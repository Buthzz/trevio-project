<?php

class SearchController
{
    public function index(): void
    {
        $filters = [
            'query' => $_GET['q'] ?? '',
            'city' => $_GET['city'] ?? '',
            'price' => $_GET['price'] ?? '',
            'rating' => $_GET['rating'] ?? '',
        ];

        $searchResults = $this->filterHotels($this->mockHotels(), $filters);

        require __DIR__ . '/../../views/customer/search.php';
    }

    private function mockHotels(): array
    {
        return [
            [
                'name' => 'The Langham Jakarta',
                'city' => 'Jakarta',
                'rating' => 4.9,
                'reviews' => 412,
                'price' => 'IDR 2.850.000',
                'price_value' => 2850000,
                'image' => 'https://images.unsplash.com/photo-1551776235-dde6d4829808?auto=format&fit=crop&w=1200&q=80',
                'highlights' => ['Infinity pool', 'Sky bar', 'City view']
            ],
            [
                'name' => 'Padma Resort Ubud',
                'city' => 'Bali',
                'rating' => 4.8,
                'reviews' => 289,
                'price' => 'IDR 3.450.000',
                'price_value' => 3450000,
                'image' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=80',
                'highlights' => ['Trekking', 'Wellness spa', 'Jungle view']
            ],
            [
                'name' => 'GAIA Hotel Bandung',
                'city' => 'Bandung',
                'rating' => 4.7,
                'reviews' => 198,
                'price' => 'IDR 2.150.000',
                'price_value' => 2150000,
                'image' => 'https://images.unsplash.com/photo-1505691723518-36a5ac3be353?auto=format&fit=crop&w=1200&q=80',
                'highlights' => ['Heated pool', 'Kids club', 'Scenic deck']
            ],
            [
                'name' => 'Hotel Tentrem Yogyakarta',
                'city' => 'Yogyakarta',
                'rating' => 4.9,
                'reviews' => 354,
                'price' => 'IDR 1.980.000',
                'price_value' => 1980000,
                'image' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=1200&q=80',
                'highlights' => ['Spa', 'Cultural tour', 'Fine dining']
            ],
        ];
    }

    private function filterHotels(array $hotels, array $filters): array
    {
        return array_values(array_filter($hotels, static function (array $hotel) use ($filters): bool {
            $matchQuery = $filters['query'] === '' || stripos($hotel['name'] . ' ' . $hotel['city'], $filters['query']) !== false;
            $matchCity = $filters['city'] === '' || $filters['city'] === 'Semua Kota' || strcasecmp($hotel['city'], $filters['city']) === 0;
            $matchRating = $filters['rating'] === '' || $filters['rating'] === 'Semua Rating' || $hotel['rating'] >= (float) str_replace('+', '', $filters['rating']);
            if ($filters['price'] === '< 1 juta') {
                $matchPrice = $hotel['price_value'] < 1000000;
            } elseif ($filters['price'] === '1 - 2 juta') {
                $matchPrice = $hotel['price_value'] >= 1000000 && $hotel['price_value'] <= 2000000;
            } elseif ($filters['price'] === '2 - 3 juta') {
                $matchPrice = $hotel['price_value'] > 2000000 && $hotel['price_value'] <= 3000000;
            } elseif ($filters['price'] === '> 3 juta') {
                $matchPrice = $hotel['price_value'] > 3000000;
            } else {
                $matchPrice = true;
            }
            return $matchQuery && $matchCity && $matchRating && $matchPrice;
        }));
    }
}
