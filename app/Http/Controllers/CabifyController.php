<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleBrowser;

class CabifyController extends Controller
{
    //
    private $browser;

    public function __construct()
    {
        $this->browser = new SimpleBrowser();
        $this->browser->addHeader('Accept-Language: en');
        $this->browser->addHeader('Authorization: Bearer '.env('CABIFY_APIKEY'));
    }

    public function getEstimationByCoordinates($latitude, $longitude){
        $mockIntervalMonth = new \DateInterval("P1M");
        $mockDateStart = (new \DateTime())->add($mockIntervalMonth)->format('Y-m-d H:i:s ');

        $query = $this->browser->post(
            'https://test.cabify.com/api/v2/estimate',
            [
                json_encode([
                    'stops' => [
                        [
                            "name" => "Puerta del Sol",
                            "addr" => "Plaza de la Puerta del Sol",
                            "num" => "s/n",
                            "city" => "Madrid",
                            "country" => "Spain",
                            "hit_at" => $mockDateStart
                        ],
                        [
                            'loc' => [
                                $latitude,
                                $longitude
                            ]
                        ]
                    ]
                ])
            ]
        );

        return json_decode($query, true) ?: [];
    }
}
