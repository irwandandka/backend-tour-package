<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $startDate = Carbon::parse('2024-01-01'); // Start date
        $endDate = Carbon::parse('2024-12-31');   // End date

        // Generate a random date between the start and end date
        $randomDate = Carbon::createFromTimestamp(rand($startDate->timestamp, $endDate->timestamp));

        $products = Product::all();

        $users = User::all();

        $reviews = [
            [
                // 'user_name' => 'Emily Johnson',
                'rating' => 5,
                'review_date' => $randomDate,
                'product_id' => $products->where('slug', 'tokyo-city-highlights')->first()->id,
                'user_id' => $users->where('email', 'irwndandka@gmail.com')->first()->id,
                'comment' => 'An unforgettable introduction to Tokyo! From the busy streets of Shibuya to the peaceful Senso-ji Temple, every stop was amazing. The Skytree view was breathtaking. Highly recommend!',
            ],
            [
                // 'user_name' => 'Michael Smith',
                'rating' => 4,
                'review_date' => $randomDate,
                'product_id' => $products->where('slug', 'tokyo-city-highlights')->first()->id,
                'user_id' => $users->where('email', 'irwndandka@gmail.com')->first()->id,
                'comment' => 'A great way to see the highlights of Tokyo in a day. The guide was knowledgeable and friendly. I loved the mix of modern and traditional sights.',
            ],
            [
                // 'user_name' => 'Sarah Brown',
                'rating' => 5,
                'review_date' => $randomDate,
                'product_id' => $products->where('slug', 'tokyo-city-highlights')->first()->id,
                'user_id' => $users->where('email', 'irwndandka@gmail.com')->first()->id,
                'comment' => 'This tour was fantastic! The cherry blossoms were in full bloom, and the guide shared so much interesting history. I would do it again in a heartbeat!',
            ],
            [
                // 'user_name' => 'Daniel Kim',
                'rating' => 4,
                'review_date' => $randomDate,
                'product_id' => $products->where('slug', 'busan-beach-escape')->first()->id,
                'user_id' => $users->where('email', 'irwndandka@gmail.com')->first()->id,
                'comment' => 'Loved the mix of beach time and cultural exploration. The Korean BBQ dinner was delicious, and the spa experience was so relaxing!',
            ],
            [
                // 'user_name' => 'Jessica Lee',
                'rating' => 5,
                'review_date' => $randomDate,
                'product_id' => $products->where('slug', 'busan-beach-escape')->first()->id,
                'user_id' => $users->where('email', 'irwndandka@gmail.com')->first()->id,
                'comment' => 'Busan is beautiful! The beaches were stunning, and the tour guide was very friendly. I especially loved the visit to the Gamcheon Culture Village.',
            ],
            [
                // 'user_name' => 'David Wilson',
                'rating' => 4,
                'review_date' => $randomDate,
                'product_id' => $products->where('slug', 'busan-beach-escape')->first()->id,
                'user_id' => $users->where('email', 'irwndandka@gmail.com')->first()->id,
                'comment' => 'A great way to explore Busan! The beach time was perfect, and the cultural sites were fascinating. I would recommend this tour to anyone visiting Korea.',
            ],
            [
                // 'user_name' => 'Sophia Lee',
                'rating' => 5,
                'review_date' => $randomDate,
                'product_id' => $products->where('slug', 'yogyakarta-heritage-journey')->first()->id,
                'user_id' => $users->where('email', 'irwndandka@gmail.com')->first()->id,
                'comment' => 'Truly immersive! The temples were awe-inspiring and the Ramayana Ballet was a magical experience. I learned so much about Javanese culture.',
            ],
            [
                // 'user_name' => 'James Smith',
                'rating' => 4,
                'review_date' => $randomDate,
                'product_id' => $products->where('slug', 'yogyakarta-heritage-journey')->first()->id,
                'user_id' => $users->where('email', 'irwndandka@gmail.com')->first()->id,
                'comment' => 'A well-organized tour with a great balance of sightseeing and cultural experiences. The Borobudur Temple was a highlight for me.',
            ],
            [
                // 'user_name' => 'Olivia Brown',
                'rating' => 5,
                'review_date' => $randomDate,
                'product_id' => $products->where('slug', 'yogyakarta-heritage-journey')->first()->id,
                'user_id' => $users->where('email', 'irwndandka@gmail.com')->first()->id,
                'comment' => 'An unforgettable experience! The temples were stunning, and the local guide was very knowledgeable. I loved every moment of this tour.',
            ],
            [
                // 'user_name' => 'Michael Tan',
                'rating' => 4,
                'review_date' => $randomDate,
                'product_id' => $products->where('slug', 'bangkok-city-temple-tour')->first()->id,
                'user_id' => $users->where('email', 'irwndandka@gmail.com')->first()->id,
                'comment' => 'The temples were stunning and the river cruise was very peaceful. The street food in Chinatown was a highlight! Great tour overall.',
            ],
            [
                // 'user_name' => 'Emma Wong',
                'rating' => 5,
                'review_date' => $randomDate,
                'product_id' => $products->where('slug', 'bangkok-city-temple-tour')->first()->id,
                'user_id' => $users->where('email', 'irwndandka@gmail.com')->first()->id,
                'comment' => 'A fantastic way to explore Bangkok! The temples were beautiful, and the local guide was very informative. I loved the boat ride on the Chao Phraya River.',
            ],
            [
                // 'user_name' => 'Liam Johnson',
                'rating' => 4,
                'review_date' => $randomDate,
                'product_id' => $products->where('slug', 'bangkok-city-temple-tour')->first()->id,
                'user_id' => $users->where('email', 'irwndandka@gmail.com')->first()->id,
                'comment' => 'A great introduction to Bangkok! The temples were stunning, and the street food was delicious. I would recommend this tour to anyone visiting Thailand.',
            ],
            [
                // 'user_name' => 'Rachel Nguyen',
                'rating' => 5,
                'review_date' => $randomDate,
                'product_id' => $products->where('slug', 'hanoi-halong-bay-discovery')->first()->id,
                'user_id' => $users->where('email', 'irwndandka@gmail.com')->first()->id,
                'comment' => 'Fantastic experience! The Cu Chi Tunnels were so interesting, and the Mekong Delta boat ride was serene. Loved the local village lunch too.',
            ],
            [
                // 'user_name' => 'Ethan Tran',
                'rating' => 4,
                'review_date' => $randomDate,
                'product_id' => $products->where('slug', 'hanoi-halong-bay-discovery')->first()->id,
                'user_id' => $users->where('email', 'irwndandka@gmail.com')->first()->id,
                'comment' => 'A great way to see the Mekong Delta! The boat ride was relaxing, and the local villages were charming. I enjoyed the lunch at a local home.',
            ],
            [
                // 'user_name' => 'Sophia Nguyen',
                'rating' => 5,
                'review_date' => $randomDate,
                'product_id' => $products->where('slug', 'hanoi-halong-bay-discovery')->first()->id,
                'user_id' => $users->where('email', 'irwndandka@gmail.com')->first()->id,
                'comment' => 'An amazing tour! The Mekong Delta was beautiful, and the Cu Chi Tunnels were fascinating. The guide was very knowledgeable and friendly.',
            ],
            [
                // 'user_name' => 'Lucas Pham',
                'rating' => 4,
                'review_date' => $randomDate,
                'product_id' => $products->where('slug', 'hanoi-halong-bay-discovery')->first()->id,
                'user_id' => $users->where('email', 'irwndandka@gmail.com')->first()->id,
                'comment' => 'A well-organized tour with a great mix of history and culture. The Mekong Delta boat ride was peaceful, and the local lunch was delicious.',
            ],
            [
                // 'user_name' => 'David Romano',
                'rating' => 5,
                'review_date' => $randomDate,
                'product_id' => $products->where('slug', 'rome-ancient-wonders')->first()->id,
                'user_id' => $users->where('email', 'irwndandka@gmail.com')->first()->id,
                'comment' => 'Walking through history! The Colosseum and Vatican were mind-blowing. Our guide was super knowledgeable. A must-do in Rome.',
            ],
            [
                // 'user_name' => 'Isabella Rossi',
                'rating' => 4,
                'review_date' => $randomDate,
                'product_id' => $products->where('slug', 'rome-ancient-wonders')->first()->id,
                'user_id' => $users->where('email', 'irwndandka@gmail.com')->first()->id,
                'comment' => 'A great way to see Rome! The Colosseum was impressive, and the Vatican was breathtaking. The guide was very informative.',
            ],
            [
                // 'user_name' => 'Isabella Costa',
                'rating' => 4,
                'review_date' => $randomDate,
                'product_id' => $products->where('slug', 'lisbon-city-highlights')->first()->id,
                'user_id' => $users->where('email', 'irwndandka@gmail.com')->first()->id,
                'comment' => 'Lisbon is such a charming city. The tram ride was so fun and the historical sites were very impressive. A great mix of past and present!',
            ],
            [
                // 'user_name' => 'Lorenzo Silva',
                'rating' => 5,
                'review_date' => $randomDate,
                'product_id' => $products->where('slug', 'lisbon-city-highlights')->first()->id,
                'user_id' => $users->where('email', 'irwndandka@gmail.com')->first()->id,
                'comment' => 'A fantastic tour! The guide was very knowledgeable and the sights were stunning. I loved the visit to Belem Tower and the Jeronimos Monastery.',
            ],
            [
                // 'user_name' => 'Sofia Pereira',
                'rating' => 4,
                'review_date' => $randomDate,
                'product_id' => $products->where('slug', 'lisbon-city-highlights')->first()->id,
                'user_id' => $users->where('email', 'irwndandka@gmail.com')->first()->id,
                'comment' => 'A great way to explore Lisbon! The tram ride was fun, and the local food was delicious. I would recommend this tour to anyone visiting Portugal.',
            ],
        ];

        DB::transaction(function () use ($reviews) {
            foreach ($reviews as $review) {
                Review::create($review);
            }
        });
    }
}
