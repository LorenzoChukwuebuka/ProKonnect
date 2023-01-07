<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Referal;
use App\Models\Referal_transaction;
use DB;
use Illuminate\Http\Request;
use Validator;

class PaymentController extends Controller
{

    public function initialize_payment(Request $request)
    {

        try {

            $validator = Validator::make($request->all(), [
                "amount" => "required",
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 401);
            }

            $key = config('paystack.paystack_secret');

            $url = "https://api.paystack.co/transaction/initialize";

            $fields = [
                'email' => auth()->user()->email,
                'amount' => $request->amount * 100,
                'metadata' => [
                    "plan_id" => $request->plan_id,
                    "user_id" => auth()->user()->id,
                    "proguide_id" => $request->proguide_id,
                    "payer_email" => auth()->user()->email ?? null,
                    "payer_fullname" => auth()->user()->full_name ?? null,
                ],

            ];

            $fields_string = json_encode($fields);

            #open connection
            $ch = curl_init();

            #set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Authorization: Bearer {$key}",
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
                "Authorization: Bearer {$key}",
                "Cache-Control: no-cache",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return response(["code" => 3, "error" => "cURL Error :" . $err]);
        }

        return $result = json_decode($response);

        if ($result->data->status !== 'success') {
            throw new \Exception("Transaction failed");
        }

        #pull details from the result object
        $amount = $result->data->amount / 100;
        $plan_id = $result->data->metadata->plan_id;
        $user_id = $result->data->metadata->id;
        $proguide_id = $result->data->metadata->proguide_id;
        $payer_email = $result->data->metadata->payer_email ?? null;
        $payer_fullname = $result->data->metadata->payer_full_name ?? null;

        #check if transaction reference already exists

        $checkRefernce = Payment::where('reference', $reference)->first();

        if ($checkRefernce != null) {
            return response(["code" => 1, "message" => "possible duplicate transaction"]);
        }

        #start database transaction

        DB::transaction(function () use ($reference, $amount, $plan_id, $user_id, $proguide_id, $payer_email, $payer_fullname) {

            #pull the duration from the plan_id

            $planDuration = Plan::find($plan_id)->duration;

            $refPercent = config('paystack.referal_percentage');

            $referalPercentage = $this->percentage($refPercent, $amount);

            #referal

            $referalEarnings = $amount - $referalPercentage;

            $referal = $this->referal_check($user_id, $referalEarnings, $payment_id);

            #create payment

            $payment = Payment::create([
                "payer_id" => $user_id,
                "proguide_id" => $proguide_id,
                "plan_id" => $plan_id,
                "amount_paid" => ($referal == false) ? $amount : $referalEarnings,
                "payer_email" => $payer_email,
                "payer_fullname" => $payer_fullname,
                "duration" => $planDuration,
            ]);

            if ($payment) {
                #check the balance of the proguide

                $previous_balance = DB::select('SELECT ifnull((select available_balance from wallets where user_id = ?  order by id desc limit 1), 0 ) AS prevbal', [$proguide_id]);

                #fund proguide's wallet

                $wallet = Wallet::updateOrCreate(['user_id' => $proguide_id],
                    [
                        "available_balance" => $amount + $previous_balance[0]->prevbal,
                    ]);
            }

        });

    }

    private function referal_check(int $user_id, int | float $amount_payable, int $payment_id)
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

        $ref_transactions = Referal_transaction::create([
            "referred_by" => $user_id,
            "user_referred" => $check_if_referred->referee_id,
            "payment_id" => $payment_id,
            "amount_earned" => $amount_payable,
        ]);

        if (!$ref_transactions) {
            return false;
        }

        return $ref_transactions;

    }

    private function percentage(int $firstNumb, int $secondNumb)
    {
        return ($firstNumb / 100) * $secondNumb;
    }
}
