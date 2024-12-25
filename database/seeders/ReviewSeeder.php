<?php

namespace Database\Seeders;

use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

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

        $reviews = [
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => '6060c477-ba9e-11ef-95b1-525400d81c3e',
                'rating' => 5.0,
                'comment' => 'Incredible tour! The guide was very knowledgeable, and we got to see all the key sights in Tokyo in one day. Highly recommend!',
                'review_date' => $randomDate,
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => '6060c477-ba9e-11ef-95b1-525400d81c3e',
                'rating' => 4.0,
                'comment' => 'Great experience, but the schedule felt a bit rushed. Still, the places we visited were fantastic!',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => '6060c477-ba9e-11ef-95b1-525400d81c3e',
                'rating' => 5.0,
                'comment' => 'Absolutely loved it! The mix of traditional and modern Tokyo was amazing. Would definitely book again.',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => '5206c939-ba9e-11ef-95b1-525400d81c3e',
                'rating' => 4.5,
                'comment' => 'Kobe is beautiful, and this cultural tour gave us a great introduction. The food was also delicious!',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => '5206c939-ba9e-11ef-95b1-525400d81c3e',
                'rating' => 3.5,
                'comment' => 'The tour was good, but I expected more cultural activities. Still, the guide was friendly and informative.',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => '5206c939-ba9e-11ef-95b1-525400d81c3e',
                'rating' => 5.0,
                'comment' => 'Wonderful experience! The traditional tea ceremony and the visit to the temples were highlights.',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => '3a519045-ba9e-11ef-95b1-525400d81c3e',
                'rating' => 5.0,
                'comment' => 'The food was outstanding! I got to try some of the best Kobe beef and other local delicacies.',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => '3a519045-ba9e-11ef-95b1-525400d81c3e',
                'rating' => 4.0,
                'comment' => 'Great food, but the tour could have included more variety. Still, I loved the local vibe.',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => '3a519045-ba9e-11ef-95b1-525400d81c3e',
                'rating' => 5.0,
                'comment' => 'Amazing tour! Every stop was better than the last. Highly recommend if you\'re a foodie!',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => '2419997f-ba9e-11ef-95b1-525400d81c3e',
                'rating' => 4.5,
                'comment' => 'Great tour! Ayutthaya is stunning, and the guide was excellent at explaining the history.',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => '2419997f-ba9e-11ef-95b1-525400d81c3e',
                'rating' => 5.0,
                'comment' => 'This was one of my favorite tours. The temples were breathtaking, and the boat ride was a nice touch.',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => '2419997f-ba9e-11ef-95b1-525400d81c3e',
                'rating' => 3.5,
                'comment' => 'The ruins were beautiful, but I felt the tour could be longer to really enjoy everything.',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => 'de701bc3-ba9d-11ef-95b1-525400d81c3e',
                'rating' => 5.0,
                'comment' => "The heritage sites in Ayutthaya were fascinating, and the guide’s knowledge made the experience memorable.",
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => 'de701bc3-ba9d-11ef-95b1-525400d81c3e',
                'rating' => 4.0,
                'comment' => 'Beautiful sites and great information, but the tour felt a bit short. Would love to see more!',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => 'de701bc3-ba9d-11ef-95b1-525400d81c3e',
                'rating' => 4.5,
                'comment' => 'A must-do tour if you\'re in Thailand! The temples and history were amazing.',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => 'ba1d8a1c-ba9d-11ef-95b1-525400d81c3e',
                'rating' => 5.0,
                'comment' => 'Stunning scenery and well-organized. The boat trip was beautiful, and the islands were breathtaking!',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => 'ba1d8a1c-ba9d-11ef-95b1-525400d81c3e',
                'rating' => 4.5,
                'comment' => 'Great day trip, but could have used more time on the islands. Still, fantastic experience overall.',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => 'ba1d8a1c-ba9d-11ef-95b1-525400d81c3e',
                'rating' => 5.0,
                'comment' => 'The best part of my vacation! The water was crystal clear, and the beaches were out of this world.',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => 'aaa7671b-ba9d-11ef-95b1-525400d81c3e',
                'rating' => 4.0,
                'comment' => 'Beautiful beaches and friendly guides. The tour was a little crowded, but still enjoyable.',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => 'aaa7671b-ba9d-11ef-95b1-525400d81c3e',
                'rating' => 5.0,
                'comment' => 'Best beach tour ever! The views were amazing, and we had plenty of time to relax on the sand.',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => 'aaa7671b-ba9d-11ef-95b1-525400d81c3e',
                'rating' => 4.5,
                'comment' => 'Lovely beaches, great guide, and the seafood lunch was incredible!',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => '9b9fb0ab-ba9d-11ef-95b1-525400d81c3e',
                'rating' => 5.0,
                'comment' => 'The nightlife in Bangkok is insane! Great bars, great vibes, and a fantastic tour guide.',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => '9b9fb0ab-ba9d-11ef-95b1-525400d81c3e',
                'rating' => 4.0,
                'comment' => 'I enjoyed the tour, but some places felt too touristy. Still, a fun night out!',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => '9b9fb0ab-ba9d-11ef-95b1-525400d81c3e',
                'rating' => 5.0,
                'comment' => 'Fantastic experience! The local bars and food spots were amazing. Definitely a must-do!',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => '8c1e29e4-ba9d-11ef-95b1-525400d81c3e',
                'rating' => 4.5,
                'comment' => "Excellent way to explore Bangkok. Saw some hidden gems and learned a lot about the city’s culture.",
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => '8c1e29e4-ba9d-11ef-95b1-525400d81c3e',
                'rating' => 5.0,
                'comment' => 'Perfect tour to discover Bangkok beyond the tourist spots. I loved the temples and local markets!',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => '8c1e29e4-ba9d-11ef-95b1-525400d81c3e',
                'rating' => 4.0,
                'comment' => 'The tour was good, but I wished we had more time at each stop. Overall, a great way to explore the city.',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => '71f48919-ba9d-11ef-95b1-525400d81c3e',
                'rating' => 4.5,
                'comment' => "Very cool tour! I learned a lot about Surabaya’s history and culture.",
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => '71f48919-ba9d-11ef-95b1-525400d81c3e',
                'rating' => 4.0,
                'comment' => 'Interesting places, but the tour could have been longer. Would definitely recommend!',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => '71f48919-ba9d-11ef-95b1-525400d81c3e',
                'rating' => 5.0,
                'comment' => 'Hidden gems indeed! Great tour with amazing local spots and fantastic guides.',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => '55228a41-ba9d-11ef-95b1-525400d81c3e',
                'rating' => 5.0,
                'comment' => 'Excellent tour! We saw all the key attractions in Surabaya, and the guide was very knowledgeable.',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => '55228a41-ba9d-11ef-95b1-525400d81c3e',
                'rating' => 4.5,
                'comment' => 'Great experience! The city is full of history, and the tour was well-paced.',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => '55228a41-ba9d-11ef-95b1-525400d81c3e',
                'rating' => 4.0,
                'comment' => 'Nice tour, but I expected more historical sites. Still, Surabaya is a beautiful city.',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => 'e109c97f-ba9c-11ef-95b1-525400d81c3e',
                'rating' => 5.0,
                'comment' => 'Best adventure tour in Yogyakarta! The guide was super friendly, and the sights were incredible.',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => 'e109c97f-ba9c-11ef-95b1-525400d81c3e',
                'rating' => 4.5,
                'comment' => 'Amazing experience! The temples and caves were beautiful, and the hike was challenging but fun.',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => 'e109c97f-ba9c-11ef-95b1-525400d81c3e',
                'rating' => 4.0,
                'comment' => 'Great tour, but I was hoping for more adventurous activities. The cultural sites were still great!',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => '8a6a9c07-ba9c-11ef-95b1-525400d81c3e',
                'rating' => 5.0,
                'comment' => 'Absolutely loved it! Yogyakarta is full of rich history, and this tour brought it all to life.',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => '8a6a9c07-ba9c-11ef-95b1-525400d81c3e',
                'rating' => 4.5,
                'comment' => 'The temples and heritage sites were breathtaking. A great way to learn about Javanese history.',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => '8a6a9c07-ba9c-11ef-95b1-525400d81c3e',
                'rating' => 4.0,
                'comment' => 'Great history tour. Some of the sites were a bit crowded, but it was still worth it.',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => 'de04f9d4-ba9b-11ef-95b1-525400d81c3e',
                'rating' => 5.0,
                'comment' => 'Fantastic food tour! I tried so many different dishes, and each one was better than the last.',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => 'de04f9d4-ba9b-11ef-95b1-525400d81c3e',
                'rating' => 4.0,
                'comment' => 'Good tour, but I expected a bit more variety in the dishes. Still, the food was amazing.',
                'review_date' => $randomDate
            ],
            [
                'user_id' => '03c7612b-178f-42d3-aac5-d8c1c780f8db',
                'product_id' => 'de04f9d4-ba9b-11ef-95b1-525400d81c3e',
                'rating' => 4.5,
                'comment' => "Wonderful food and great guide! I got to try authentic local dishes and learn about the city’s culinary history.",
                'review_date' => $randomDate
            ]
        ];

        foreach ($reviews as $review) {
            Review::create($review);
        }
    }
}
