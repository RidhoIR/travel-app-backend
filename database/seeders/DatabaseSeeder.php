<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Destination;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo user
        User::firstOrCreate(
            ['email' => 'demo@travelapp.com'],
            [
                'name'     => 'Alex Yusuf',
                'password' => Hash::make('password123'),
            ]
        );

        // Sample destinations
        $destinations = [
            [
                'name'            => 'Hotel Dolah Amet & Suites',
                'location'        => 'London',
                'country'         => 'England',
                'description'     => 'A luxurious five-star hotel nestled in the heart of London, offering breathtaking views of the city skyline. Each suite is meticulously designed with elegant furnishings and modern amenities to ensure an unforgettable stay.',
                'price_per_night' => 100.00,
                'rating'          => 4.9,
                'distance_km'     => 5.0,
                'has_wifi'        => true,
                'has_pool'        => true,
                'has_restaurant'  => true,
                'has_parking'     => true,
                'has_spa'         => true,
                'image_url'       => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800',
                'category'        => 'hotel',
                'is_featured'     => true,
            ],
            [
                'name'            => 'Tincidunt Pool Resort',
                'location'        => 'Madrid',
                'country'         => 'Spain',
                'description'     => 'An exclusive poolside resort in the vibrant city of Madrid. Enjoy endless sunshine, world-class dining, and rejuvenating spa treatments in an atmosphere of pure luxury.',
                'price_per_night' => 120.00,
                'rating'          => 4.7,
                'distance_km'     => 3.2,
                'has_wifi'        => true,
                'has_pool'        => true,
                'has_restaurant'  => true,
                'has_parking'     => false,
                'has_spa'         => true,
                'image_url'       => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800',
                'category'        => 'resort',
                'is_featured'     => true,
            ],
            [
                'name'            => 'Curabitur Beach',
                'location'        => 'Rome',
                'country'         => 'Italy',
                'description'     => 'Discover the pristine shores of this hidden beach gem near the eternal city of Rome. Crystal-clear waters, golden sands, and breathtaking Mediterranean sunsets await you.',
                'price_per_night' => 85.00,
                'rating'          => 4.6,
                'distance_km'     => 8.5,
                'has_wifi'        => true,
                'has_pool'        => false,
                'has_restaurant'  => true,
                'has_parking'     => true,
                'has_spa'         => false,
                'image_url'       => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800',
                'category'        => 'beach',
                'is_featured'     => true,
            ],
            [
                'name'            => 'Ipsum Restaurant & Stay',
                'location'        => 'Paris',
                'country'         => 'France',
                'description'     => 'Experience the pinnacle of French elegance in this boutique hotel and gourmet restaurant. Housed in a 19th-century Haussmanian building, every corner tells a story of Parisian charm.',
                'price_per_night' => 95.00,
                'rating'          => 4.5,
                'distance_km'     => 1.8,
                'has_wifi'        => true,
                'has_pool'        => false,
                'has_restaurant'  => true,
                'has_parking'     => false,
                'has_spa'         => false,
                'image_url'       => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800',
                'category'        => 'city',
                'is_featured'     => true,
            ],
            [
                'name'            => 'Beach Mauris Blandit',
                'location'        => 'Lisbon',
                'country'         => 'Portugal',
                'description'     => 'A charming beachfront property on the stunning Atlantic coast of Lisbon. Enjoy surfing, fresh seafood, and vibrant nightlife just steps from your door.',
                'price_per_night' => 100.00,
                'rating'          => 4.8,
                'distance_km'     => 12.0,
                'has_wifi'        => true,
                'has_pool'        => true,
                'has_restaurant'  => true,
                'has_parking'     => true,
                'has_spa'         => false,
                'image_url'       => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800',
                'category'        => 'beach',
                'is_featured'     => false,
            ],
            [
                'name'            => 'Santorini Blue Dome',
                'location'        => 'Santorini',
                'country'         => 'Greece',
                'description'     => 'Perched on the cliffs of Oia, this iconic cave hotel offers the most spectacular views of the caldera and the famous Santorini sunset. A truly once-in-a-lifetime experience.',
                'price_per_night' => 250.00,
                'rating'          => 5.0,
                'distance_km'     => 2.5,
                'has_wifi'        => true,
                'has_pool'        => true,
                'has_restaurant'  => true,
                'has_parking'     => false,
                'has_spa'         => true,
                'image_url'       => 'https://images.unsplash.com/photo-1533105079780-92b9be482077?w=800',
                'category'        => 'resort',
                'is_featured'     => true,
            ],
            [
                'name'            => 'Bali Jungle Retreat',
                'location'        => 'Ubud',
                'country'         => 'Indonesia',
                'description'     => 'Immerse yourself in the lush tropical jungle of Ubud. This eco-friendly retreat offers traditional Balinese architecture, organic dining, yoga classes, and healing spa rituals.',
                'price_per_night' => 75.00,
                'rating'          => 4.8,
                'distance_km'     => 4.0,
                'has_wifi'        => true,
                'has_pool'        => true,
                'has_restaurant'  => true,
                'has_parking'     => true,
                'has_spa'         => true,
                'image_url'       => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800',
                'category'        => 'resort',
                'is_featured'     => false,
            ],
            [
                'name'            => 'Tokyo Urban Stay',
                'location'        => 'Shinjuku',
                'country'         => 'Japan',
                'description'     => 'A sleek modern hotel in the heart of Tokyo\'s neon-lit Shinjuku district. Walk to world-class shopping, dining, and entertainment. Perfect base for exploring the city.',
                'price_per_night' => 130.00,
                'rating'          => 4.6,
                'distance_km'     => 0.5,
                'has_wifi'        => true,
                'has_pool'        => false,
                'has_restaurant'  => true,
                'has_parking'     => true,
                'has_spa'         => false,
                'image_url'       => 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800',
                'category'        => 'city',
                'is_featured'     => false,
            ],
        ];

        foreach ($destinations as $data) {
            Destination::firstOrCreate(['name' => $data['name']], $data);
        }
    }
}
