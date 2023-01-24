<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Referal;
use App\Models\Referal_transaction;
class ReferalController extends Controller
{
    public function get_referals_for_a_user()
    {
        try {
            $referals = Referal::where('referal_id', auth()->user()->id)->lates()->get();

            if ($referals->count() == 0) {
                return response(["code" => 3, "message" => "No record found"]);
            }

            return response(["code" => 1, "data" => $referals]);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function get_referal_commission()
    {
        try {
            $referal_transactions = Referal_transaction::with('user_referred')->where('referred_by', auth()->user()->id)->latest()->get();
            if ($referal_transactions->count() == 0) {
                return response(["code" => 3, "message" => "No record found"]);
            }
            return response(["code" => 1, "data" => $referal_transactions]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }
}
