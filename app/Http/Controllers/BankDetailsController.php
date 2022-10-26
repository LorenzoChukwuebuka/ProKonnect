<?php

namespace App\Http\Controllers;

use App\Models\BankDetails;
use Illuminate\Http\Request;
use Validator;

class BankDetailsController extends Controller
{
    public function create_bank_details(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "country_id" => [],
                "bank_name" => "required",
                "account_name" => "required",
                "mri_code" => [],
                "account_number" => "required|min:10",
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            $bank_details = BankDetails::updateOrCreate(

                ["user_id" => auth()->user()->id],
                [
                    "country_id" => $request->country_id,
                    "bank_name" => $request->bank_name,
                    "account_name" => $request->account_name,
                    "mri_code" => $request->mri_code,
                    "account_number" => $request->account_number,
                ]);

            return response(["code" => 1, "message" => "bank details created successfully"]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function get_bank_details()
    {
        try {
            $bank_details = BankDetails::with('user')->paginate(20);
            if ($bank_details->count() == 0) {
                return response(["code" => 3, "message"]);
            }
            return response(["code" => 1, "data" => $bank_details]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "message" => $th->getMessage()]);
        }
    }

    public function get_bank_details_for_a_particular_user()
    {
        try {

            $bank_details = auth()->user()->bank_details()->first();

            if($bank_details == null){
                return \response(["code"=>3,"message"=>"no record found"]);
            }

            return response(["code"=>1,"data"=>$bank_details]);

        } catch (\Throwable$th) {
            return  \response(["code"=>3,"error"=>$th->getMessage()]);
        }
    }


}
