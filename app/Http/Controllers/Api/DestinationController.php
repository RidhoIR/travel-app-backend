<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    public function featured(Request $request)
    {
        $userId = $request->user()->id;

        $destinations = Destination::where('is_featured', true)
            ->withCount('reviews')
            ->get()
            ->map(fn($d) => $this->format($d, $userId));

        return response()->json(['data' => $destinations]);
    }

    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $query = Destination::withCount('reviews');

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($q2) use ($q) {
                $q2->where('name', 'ilike', "%$q%")
                   ->orWhere('location', 'ilike', "%$q%")
                   ->orWhere('country', 'ilike', "%$q%");
            });
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $destinations = $query->orderBy('rating', 'desc')->get()
            ->map(fn($d) => $this->format($d, $userId));

        return response()->json(['data' => $destinations]);
    }

    public function show(Request $request, int $id)
    {
        $userId = $request->user()->id;
        $destination = Destination::withCount('reviews')->findOrFail($id);

        return response()->json(['data' => $this->format($destination, $userId)]);
    }

    private function format(Destination $d, int $userId): array
    {
        $isSaved = $d->savedByUsers()->where('user_id', $userId)->exists();

        return [
            'id'              => $d->id,
            'name'            => $d->name,
            'location'        => $d->location,
            'country'         => $d->country,
            'description'     => $d->description,
            'price_per_night' => $d->price_per_night,
            'rating'          => $d->rating,
            'reviews_count'   => $d->reviews_count,
            'distance_km'     => $d->distance_km,
            'has_wifi'        => $d->has_wifi,
            'has_pool'        => $d->has_pool,
            'has_restaurant'  => $d->has_restaurant,
            'has_parking'     => $d->has_parking,
            'has_spa'         => $d->has_spa,
            'image_url'       => $d->image_url,
            'category'        => $d->category,
            'is_saved'        => $isSaved,
        ];
    }
}
