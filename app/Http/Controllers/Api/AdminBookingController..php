<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
{
    // GET /api/admin/bookings
    public function index(Request $request)
    {
        $q = Booking::with(['user', 'destination', 'schedule']);

        if ($request->filled('status')) $q->where('status', $request->status);
        if ($request->filled('search')) {
            $s = $request->search;

            $q->where(function ($query) use ($s) {
                $query->whereHas('user', function ($u) use ($s) {
                    $u->where('name', 'ilike', "%$s%")
                        ->orWhere('email', 'ilike', "%$s%");
                })
                    ->orWhereHas('destination', function ($d) use ($s) {
                        $d->where('name', 'ilike', "%$s%");
                    });
            });
        }

        $bookings = $q->latest()->paginate(20);
        return response()->json([
            'data' => collect($bookings->items())->map(fn($b) => $this->fmt($b)),
            'meta' => ['total' => $bookings->total(), 'last_page' => $bookings->lastPage()],
        ]);
    }

    // PUT /api/admin/bookings/{id}
    public function update(Request $request, int $id)
    {
        $request->validate(['status' => 'required|in:confirmed,completed,cancelled']);
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => $request->status]);
        return response()->json([
            'message' => 'Status booking diperbarui.',
            'data'    => $this->fmt($booking->fresh()->load(['user', 'destination', 'schedule'])),
        ]);
    }

    // DELETE /api/admin/bookings/{id}
    public function destroy(int $id)
    {
        Booking::findOrFail($id)->delete();
        return response()->json(['message' => 'Booking dihapus.']);
    }

    private function fmt(Booking $b): array
    {
        return [
            'id'             => $b->id,
            'user_id'        => $b->user_id,
            'user'           => $b->user ? ['name' => $b->user->name, 'email' => $b->user->email] : null,
            'destination_id' => $b->destination_id,
            'destination'    => $b->destination ? [
                'name' => $b->destination->name,
                'location' => $b->destination->location,
                'country' => $b->destination->country,
                'image_url' => $b->destination->image_url,
            ] : null,
            'schedule'       => $b->schedule ? [
                'id' => $b->schedule->id,
                'date' => $b->schedule->date->format('Y-m-d'),
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
