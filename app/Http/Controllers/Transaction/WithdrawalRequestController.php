<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Validator;

class WithdrawalRequestController extends Controller
{
    public function create_withdrawal_request(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "amount" => "required",
            ]);

            if ($validator->fails()) {
                return response()->json(['code' => 3, 'error' => $validator->errors()->first()], 401);
            }

            $wallet = Wallet::where("user_id", auth()->user()->id)->first();

            if ($wallet->count() == 0) {
                return response(["code" => 3, "message" => "You can't make withdrawals at this point as you have zero balance in your wallet"]);
            }

            if ($wallet->available_balance < 1000) {
                return response(["code" => 3, "message" => "you have insufficient balance"]);
            }

            $withdrawal = WithdrawalRequest::create([
                "user_id" => auth()->user()->id,
                "amount" => $request->amount,
            ]);

            return response(["code" => "1", "message" => "withdrawal request sent"]);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function view_withdrawal_requests()
    {
        try {
            $withdrawal = WithdrawalRequest::where('user_id', auth()->user()->id)->get();

            if ($withdrawal->count() == 0) {
                return \response(["code" => 3, "message" => "no records found"]);
            }

            return response(["code" => 1, "data" => $withdrawal]);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function cancel_withdrawal_request($id)
    {
        try {
            $withdrawal = WithdrawalRequest::find($id);

            if ($withdrawal->status == 'approved') {
                return response(["code" => 3, "message" => "You can't cancel this withdrawal. As it has already been approved"]);
            }

            if ($withdrawal->status == 'cancelled') {
                return response(["code" => 3, "message" => "You have already cancelled this withdrawal"]);
            }
            $withdrawal->status = "cancelled" ?? $withdrawal->status;
            $withdrawal->save();

            return \response(["code" => 1, "message" => "withdrawal cancelled"]);

        } catch (\Throwable$th) {
            return response(["code" => "3", "error" => $th->getMessage()]);
        }
    }

    #-------------------------------- FOR ADMIN --------------------

    public function view_pending_withdrawals()
    {
        try {
            $withdrawals = WithdrawalRequest::with('users')->where('status', 'pending')->latest()->get();

            if ($withdrawals->count() == 0) {

                return response(["code" => 3, "message" => "No record found"]);
            }

            return response(["code" => 1, "data" => $withdrawals]);
        } catch (\Throwable$th) {
            return response(["code" => "3", "error" => $th->getMessage()]);
        }
    }

    public function approve_withdrawal($id)
    {
        try {
            #get the withdrawal and check if amount is in wallet

            $withdrawal = WithdrawalRequest::find($id);

            $wallet = Wallet::where('user_id', $withdrawal->user_id)->first();

            if ($withdrawal->amount > $wallet->available_balance) {
                return response(["code" => 3, "message" => "Amount is greater than available balance"]);
            }

            if ($withdrawal->status == 'approved') {
                return response(["code" => 3, "message" => "Withdrawal has already been approved"]);
            }

            $withdrawal->status = 'approved';

            $withdrawal->save();

            #deduct amount from wallet

            $balance = (int) $wallet->available_balance - (int) $withdrawal->amount;

            #update wallet

            $wallet = Wallet::updateOrCreate(['user_id' => $withdrawal->user_id],
                [
                    "available_balance" => $balance,
                ]);

            return response(["code" => 1, "message" => "Withdrawal has been approved"]);

        } catch (\Throwable$th) {
            return response(["code" => "3", "error" => $th->getMessage()]);
        }
    }
}
