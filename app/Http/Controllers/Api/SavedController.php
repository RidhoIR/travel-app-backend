<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SavedDestination;
use Illuminate\Http\Request;

class SavedController extends Controller
{
    public function index(Request $request)
    {
        $saved = SavedDestination::with('destination')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get()
            ->map(function ($s) {
                $d = $s->destination;
                return [
                    'id' => $s->id,
                    'destination' => [
                        'id'              => $d->id,
                        'name'            => $d->name,
                        'location'        => $d->location,
                        'country'         => $d->country,
                        'description'     => $d->description,
                        'price_per_night' => $d->price_per_night,
                        'rating'          => $d->rating,
                        'reviews_count'   => 0,
                        'distance_km'     => $d->distance_km,
                        'has_wifi'        => $d->has_wifi,
                        'has_pool'        => $d->has_pool,
                        'has_restaurant'  => $d->has_restaurant,
                        'has_parking'     => $d->has_parking,
                        'has_spa'         => $d->has_spa,
                        'image_url'       => $d->image_url,
                        'category'        => $d->category,
                        'is_saved'        => true,
                    ],
                ];
            });

        return response()->json(['data' => $saved]);
    }

    public function toggle(Request $request)
    {
        $request->validate(['destination_id' => 'required|integer|exists:destinations,id']);

        $userId        = $request->user()->id;
        $destinationId = $request->destination_id;

        $existing = SavedDestination::where('user_id', $userId)
            ->where('destination_id', $destinationId)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['saved' => false]);
        }

        SavedDestination::create([
            'user_id'        => $userId,
            'destination_id' => $destinationId,
        ]);

        return response()->json(['saved' => true]);
    }
}
