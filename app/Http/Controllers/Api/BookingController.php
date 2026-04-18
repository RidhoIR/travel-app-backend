<?php
// app/Http/Controllers/Api/BookingController.php

namespace App\Http\Controllers\Api;

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\{Booking, Destination, Schedule, Wallet};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    // GET /api/bookings
    public function index(Request $request)
    {
        $bookings = Booking::with(['destination', 'schedule'])
            ->where('user_id', $request->user()->id)
            ->latest()->get()->map(fn($b) => $this->fmt($b));
        return response()->json(['data' => $bookings]);
    }

    // GET /api/bookings/{id}
    public function show(Request $request, int $id)
    {
        $b = Booking::with(['destination','schedule'])
            ->where('user_id', $request->user()->id)->findOrFail($id);
        return response()->json(['data' => $this->fmt($b)]);
    }

    // POST /api/bookings  — booking reguler, bayar wallet
    public function store(Request $request)
    {
        $request->validate([
            'destination_id' => 'required|integer|exists:destinations,id',
            'check_in'       => 'required|date|after_or_equal:today',
            'check_out'      => 'required|date|after:check_in',
        ]);

        $dest   = Destination::findOrFail($request->destination_id);
        $nights = (int) \Carbon\Carbon::parse($request->check_in)->diffInDays($request->check_out);
        $total  = $dest->price_per_night * $nights;

        $booking = DB::transaction(function () use ($request, $dest, $nights, $total) {
            // Debit wallet
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $request->user()->id], ['balance' => 0]);
            $wallet->debit($total, "Pembayaran booking: {$dest->name}");

            return Booking::create([
                'user_id'        => $request->user()->id,
                'destination_id' => $request->destination_id,
                'check_in'       => $request->check_in,
                'check_out'      => $request->check_out,
                'nights'         => $nights,
                'guests'         => 1,
                'total_price'    => $total,
                'status'         => 'confirmed',
                'payment_status' => 'paid',
                'payment_method' => 'wallet',
            ]);
        });

        return response()->json([
            'message' => 'Booking berhasil! Dibayar via E-Wallet.',
            'data'    => $this->fmt($booking->load(['destination','schedule'])),
        ], 201);
    }

    // POST /api/schedules/{id}/book  — booking dari schedule, bayar wallet
    public function bookSchedule(Request $request, int $scheduleId)
    {
        $request->validate(['guests' => 'required|integer|min:1|max:20']);

        $schedule = Schedule::with('destination')->lockForUpdate()->findOrFail($scheduleId);
        if (!$schedule->is_available) return response()->json(['message' => 'Slot tidak tersedia.'], 422);
        if ($schedule->getRemaining() < $request->guests)
            return response()->json(['message' => "Hanya {$schedule->getRemaining()} tempat tersisa."], 422);

        $total = $schedule->price * $request->guests;

        $booking = DB::transaction(function () use ($request, $schedule, $total) {
            // Debit wallet
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $request->user()->id], ['balance' => 0]);
            $wallet->debit($total,
                "Booking slot {$schedule->time_slot} - {$schedule->destination->name}");

            // Tambah booked count
            $schedule->increment('booked', $request->guests);
            if ($schedule->fresh()->getRemaining() === 0)
                $schedule->update(['is_available' => false]);

            return Booking::create([
                'user_id'        => $request->user()->id,
                'destination_id' => $schedule->destination_id,
                'schedule_id'    => $schedule->id,
                'check_in'       => $schedule->date,
                'check_out'      => $schedule->date,
                'nights'         => 1,
                'guests'         => $request->guests,
                'total_price'    => $total,
                'status'         => 'confirmed',
                'payment_status' => 'paid',
                'payment_method' => 'wallet',
            ]);
        });

        return response()->json([
            'message' => 'Booking slot berhasil! Dibayar via E-Wallet.',
            'data'    => $this->fmt($booking->load(['destination','schedule'])),
        ], 201);
    }

    // POST /api/bookings/{id}/cancel
    public function cancel(Request $request, int $id)
    {
        $booking = Booking::where('user_id', $request->user()->id)->findOrFail($id);
        if ($booking->status !== 'confirmed')
            return response()->json(['message' => 'Hanya booking confirmed yang bisa dibatalkan.'], 422);

        DB::transaction(function () use ($booking, $request) {
            $booking->update(['status' => 'cancelled']);

            // Refund ke wallet jika sudah dibayar
            if ($booking->payment_status === 'paid') {
                $wallet = Wallet::firstOrCreate(['user_id' => $request->user()->id], ['balance' => 0]);
                $wallet->credit($booking->total_price, 'refund',
                    "Refund booking #{$booking->id}: {$booking->destination->name}");
                $booking->update(['payment_status' => 'refunded']);
            }

            // Kembalikan kuota schedule jika booking dari slot
            if ($booking->schedule_id) {
                $schedule = Schedule::find($booking->schedule_id);
                if ($schedule) {
                    $schedule->decrement('booked', $booking->guests);
                    $schedule->update(['is_available' => true]);
                }
            }
        });

        return response()->json(['message' => 'Booking dibatalkan dan saldo dikembalikan.']);
    }

    private function fmt(Booking $b): array
    {
        return [
            'id'             => $b->id,
            'user_id'        => $b->user_id,
            'user'           => $b->user ? ['name' => $b->user->name, 'email' => $b->user->email] : null,
            'destination_id' => $b->destination_id,
            'destination'    => $b->destination ? [
                'name'      => $b->destination->name,
                'location'  => $b->destination->location,
                'country'   => $b->destination->country,
                'image_url' => $b->destination->image_url,
            ] : null,
            'schedule'       => $b->schedule ? [
                'id'        => $b->schedule->id,
                'date'      => $b->schedule->date->format('Y-m-d'),
                'time_slot' => $b->schedule->time_slot,
            ] : null,
            'check_in'       => $b->check_in?->format('Y-m-d'),
            'check_out'      => $b->check_out?->format('Y-m-d'),
            'nights'         => $b->nights,
            'guests'         => $b->guests,
            'total_price'    => $b->total_price,
            'status'         => $b->status,
            'payment_status' => $b->payment_status,
            'payment_method' => $b->payment_method,
            'created_at'     => $b->created_at->toDateTimeString(),
        ];
    }
}
