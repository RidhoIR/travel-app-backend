<?php
// app/Http/Controllers/Api/BookingController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Destination;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::with('destination')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get()
            ->map(fn($b) => $this->format($b));

        return response()->json(['data' => $bookings]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'destination_id' => 'required|integer|exists:destinations,id',
            'check_in'       => 'required|date|after_or_equal:today',
            'check_out'      => 'required|date|after:check_in',
        ]);

        $destination = Destination::findOrFail($request->destination_id);

        $checkIn  = \Carbon\Carbon::parse($request->check_in);
        $checkOut = \Carbon\Carbon::parse($request->check_out);
        $nights   = (int) $checkIn->diffInDays($checkOut);

        $booking = Booking::create([
            'user_id'        => $request->user()->id,
            'destination_id' => $request->destination_id,
            'check_in'       => $request->check_in,
            'check_out'      => $request->check_out,
            'nights'         => $nights,
            'total_price'    => $destination->price_per_night * $nights,
            'status'         => 'confirmed',
        ]);

        return response()->json([
            'message' => 'Booking confirmed!',
            'data'    => $this->format($booking->load('destination')),
        ], 201);
    }

    public function show(Request $request, int $id)
    {
        $booking = Booking::with('destination')
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json(['data' => $this->format($booking)]);
    }

    public function cancel(Request $request, int $id)
    {
        $booking = Booking::where('user_id', $request->user()->id)->findOrFail($id);

        if ($booking->status !== 'confirmed') {
            return response()->json(['message' => 'Only confirmed bookings can be cancelled.'], 422);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Booking cancelled successfully.']);
    }

    private function format(Booking $b): array
    {
        $d = $b->destination;
        return [
            'id'             => $b->id,
            'user_id'        => $b->user_id,
            'destination_id' => $b->destination_id,
            'check_in'       => $b->check_in->format('Y-m-d'),
            'check_out'      => $b->check_out->format('Y-m-d'),
            'nights'         => $b->nights,
            'total_price'    => $b->total_price,
            'status'         => $b->status,
            'created_at'     => $b->created_at->toDateTimeString(),
            'destination'    => $d ? [
                'id'         => $d->id,
                'name'       => $d->name,
                'location'   => $d->location,
                'country'    => $d->country,
                'image_url'  => $d->image_url,
            ] : null,
        ];
    }
}
