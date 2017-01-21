<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Town;
use SimpleBrowser;

class TownsController extends Controller
{

    private $browser;
    private $miNube;
    private $hotelBeds;

    public function __construct()
    {
        $this->browser = new SimpleBrowser();
        $this->miNube = new MiNubeController();
        $this->hotelBeds = new HotelBedsController();
    }

    /**
     * Display a listing of the resource.
     *
     *
     */
    public function index($province = null)
    {
        //
        return Town::query()->where("province", $province)->get()->toJson();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $town = Town::query()->findOrFail($id)->toArray();

        $miNubeTown = array_except($this->miNube->getTownByName($town['name']),
            [
                'city_name',
                'zone_id',
                'zone_name',
                'country_id',
                'country_name'
            ]
        ) ?: [];

        $townInterests = ( ! empty($miNubeTown)) ?
            $this->miNube->getInterestPointsByTownId($miNubeTown['city_id']) :
            []
        ;

        return json_encode($town + $miNubeTown + ["pois" => $townInterests], true);
    }

}
