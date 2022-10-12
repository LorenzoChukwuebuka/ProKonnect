<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use WisdomDiala\Countrypkg\Models\State;
use WisdomDiala\Countrypkg\Models\Country;

class fetchCountriesController extends Controller
{
    public function getAllCountries()
    {
        $countries = Country::all();
        return response($countries);
    }

    public function getStateswithCountry($id)
    {
        $states = State::where('country_id', $id)->get();
        return response($states);
    }
}
