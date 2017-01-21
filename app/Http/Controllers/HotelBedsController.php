<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleBrowser;

class HotelBedsController extends Controller
{
    //
    private $browser;

    private $apiSignature;

    public function __construct()
    {
        $this->browser = new SimpleBrowser();
        $this->apiSignature = hash("sha256", env('HOTELBEDS_APIKEY').env('HOTELBEDS_SECRET').time());
    }

    public function getHotelsByTownCoordinates($latitude, $longitude){
        $dayInterval = new \DateInterval('P1D');
        $monthInterval = new \DateInterval('P1M');
        $mockDate1 = (new \DateTime())->add($monthInterval)->format('Y-m-d');
        $mockDate2 = (new \DateTime())->add($monthInterval)->add($dayInterval)->format('Y-m-d');

        $hotels = $this->browser->post('https://api.test.hotelbeds.com/hotel-api/1.0/hotels',
            [
                'stay' => [
                    'checkIn' => $mockDate1,
                    'checkOut' => $mockDate2,
                    'shiftDays' => 1
                ],
                'occupancies' => [
                    'rooms' => 1,
                    'adults' => 2,
                    'paxes' => [
                        [
                            'type' => 'AD',
                            'age' => 30
                        ],
                        [
                            'type' => 'AD',
                            'age' => 30
                        ]
                    ]
                ],
                'geolocation' => [
                    'longitude' => $longitude,
                    'latitud' => $latitude,
                    'radius' => '20',
                    'unit' => 'km'
                ]
            ]
        );
    }
}
