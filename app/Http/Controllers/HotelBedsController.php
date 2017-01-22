<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleBrowser;
use SimpleHttpHeaders;

class HotelBedsController extends Controller
{
    //
    private $browser;

    private $apiSignature;

    public function __construct()
    {
        $this->browser = new SimpleBrowser();
        $this->apiSignature = hash("sha256", env('HOTELBEDS_APIKEY').env('HOTELBEDS_SECRET').time());

        $this->browser->addHeader('X-Signature: '.$this->apiSignature);
        $this->browser->addHeader('Api-key: '.env('HOTELBEDS_APIKEY'));
        $this->browser->addHeader('Content-Type: application/json');
        $this->browser->addHeader('Accept: application/json');
    }

    public function getHotelsByTownCoordinates($latitude, $longitude){
        $dayInterval = new \DateInterval('P1D');
        $monthInterval = new \DateInterval('P1M');
        $mockDate1 = (new \DateTime())->add($monthInterval)->format('Y-m-d');
        $mockDate2 = (new \DateTime())->add($monthInterval)->add($dayInterval)->format('Y-m-d');

        $hotels = $this->browser->post('https://api.test.hotelbeds.com/hotel-api/1.0/hotels',
            json_encode([
                'stay' => [
                    'checkIn' => $mockDate1,
                    'checkOut' => $mockDate2,
                    'shiftDays' => 1
                ],
                'occupancies' => [
                    [
                        'rooms' => 1,
                        'adults' => 2,
                        'children' => 0,
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
                    ]
                ],
                'geolocation' => [
                    'longitude' => $longitude,
                    'latitude' => $latitude,
                    'radius' => '20',
                    'unit' => 'km'
                ]
            ])
        );

        return @json_decode($hotels, true)['hotels']['hotels'] ?: [];
    }
}
