<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    public function initialize_transaction(Request $request)
    {


        try {

            $validator = Validator::make($request->all(), [
                "amount" => "required",
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 401);
            }

            $url = "https://api.paystack.co/transaction/initialize";

            $fields = [
                'email' => auth()->user()->email,
                'amount' => $request->amount * 100,

            ];

            $fields_string = http_build_query($fields);

            #open connection
            $ch = curl_init();

            #set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Authorization: Bearer sk_test_d8c4d9fd18651c4f7dbff20eca41475b17881042",
                "Cache-Control: no-cache",
            ));

            #So that curl_exec returns the contents of the cURL; rather than echoing it
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            #execute post
            $result = curl_exec($ch);
            return $result;
        } catch (Throwable $th) {
            return $th;
        }

    }

    public function confirm_payment()
    {

    }
}
