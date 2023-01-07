<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Referal;
use App\Models\Referal_transaction;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    public function confirm_payment(Request $request, string $reference)
    {

        $key = config('paystack.paystack_secret');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer  {$key}",
                "Cache-Control: no-cache",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return response(["code" => 3, "error" => "cURL Error :" . $err]);
        }

        $result = json_decode($response);

        if ($result->data->status !== 'success') {
            throw new \Exception("Transaction failed");
        }

        #pull details from the result object
        $amount = $result->data->amount / 100;
        $plan_id = $result->data->metadata->plan_id;
        $user_id = auth()->user()->id;
        $proguide_id = $result->data->metadata->proguide_id;
        $payer_email = $result->data->metadata->payer_email;
        $payer_fullname = $result->data->metadata->payer_full_name;

        #start database transaction

        DB::transaction(function () {

        });

    }

    private function referal_check(int $user_id, int | float $amount_payable)
    {

        $check_if_referred = Referal::where('referee_id', $user_id)->first();

        if ($check_if_referred == null) {
            return false;
        }

        $ref_transactions_check = Referal_transaction::where('referred_by', $check_if_referred->referal_id)->where('user_referred', $request->user_id)->first();

        if ($ref_transactions_check == null) {
            return false;
        }

        #create referal_transactions

        #credit the proguides wallet

        #send mail to proguide notifiying them
        # of a credit transaction to their wallet

        return "hello world";

    }
}
