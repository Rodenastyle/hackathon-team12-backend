<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleBrowser;

class MiNubeController extends Controller
{
    //
    private $browser;

    public function __construct()
    {
        $this->browser = new SimpleBrowser();
    }

    public function getTownByName($name){
        $query = $this->browser->get(
            'http://papi.minube.com/cities',
            [
                'lang' => 'es',
                'country_id' => 63,
                'filter' => $name,
                'api_key' => env('MINUBE_APIKEY')
            ]
        );

        $data = json_decode($query, true);

        return ($data) ? array_first($data) : [];
    }

    public function getInterestPointsByTownId($townId){
        $query = $this->browser->get(
            'http://papi.minube.com/pois',
            [
                'lang' => 'es',
                'city_id' => $townId,
                'api_key' => env('MINUBE_APIKEY')
            ]
        );

        return array_map(function($value){
            $value['picture_url'] = ($value['picture_url']) ? $value['picture_url'] : "default";
            return $value;
        }, json_decode($query, true)) ?: [];
    }
}
