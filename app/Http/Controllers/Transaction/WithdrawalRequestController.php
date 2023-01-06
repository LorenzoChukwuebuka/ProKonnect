<?php

namespace App\Http\Controllers\Transaction;

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

            $wallet = Wallet::where("user_id", auth()->user()->id);

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

    public function view_withdrawal_requests(Request $request)
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
            $withdrawal->status = "cancelled" ?? $withdrawal->status;
            $withdrawal->save();

            \response(["code" => 1, "message" => "withdrawal cancelled"]);

        } catch (\Throwable$th) {
            return response(["code" => "3", "error" => $th->getMessage()]);
        }
    }
}
