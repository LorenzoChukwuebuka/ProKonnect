<?php

namespace App\Http\Controllers\User;

use App\Models\Referal;
use App\Http\Controllers\Controller;

class ReferalController extends Controller
{
    public function get_referals_for_a_user()
    {
        try {
            $referals = Referal::where('referal_id', auth()->user()->id)->get();

            if ($referals->count() == 0) {
                return response(["code" => 3, "message" => "No record found"]);
            }

            return response(["code" => 1, "data" => $referals]);

        } catch (\Throwable$th) {
            return $th;
        }
    }
}
