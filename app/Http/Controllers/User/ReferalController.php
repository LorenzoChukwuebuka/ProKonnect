<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Referal;

class ReferalController extends Controller
{
    public function get_referals_for_a_user()
    {
        try {
            $referals = Referal::where('referal_id', auth()->user()->id)->first();

            if ($referals == null) {
                return response(["code" => 3, "message" => "No record found"]);
            }

            return response(["code" => 1, "data" => $referals]);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }
}
