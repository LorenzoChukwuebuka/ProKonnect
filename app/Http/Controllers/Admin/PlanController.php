<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Validator;

class PlanController extends Controller
{
    public function create_plan(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                "plan" => 'required|max:255|string',
                "amount" => 'required|numeric',
                "details" => [],
                "duration" => 'required',

            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            $plan = Plan::create([
                "plan" => $request->plan,
                "amount" => $request->amount,
                "details" => $request->details,
                "duration" => $request->duration,
            ]);

            return response(["code" => 1, "message" => "plan created successfully"]);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }

    }

    public function get_all_plans()
    {
        try {
            $plan = Plan::all();

            if ($plan->count() == 0) {
                return response(["code" => 3, "message" => "no record found"]);
            }

            return response(["code" => 1, "data" => $plan]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function get_plan_by_id($id)
    {
        try {
            $plan = Plan::find($id);
            if ($plan == null) {
                return response(["code" => 3, "message" => "no record found"]);
            }
            return response(["code" => 1, "data" => $plan]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function edit_plan(Request $request, $id)
    {
        try {
            $plan = Plan::find($id);

            if ($plan == null) {
                return response(["code" => 3, "message" => "no record found"]);
            }

            $plan->plan = $request->plan ?? $plan->plan;
            $plan->amount = $request->amount ?? $plan->amount;
            $plan->details = $request->details ?? $plan->details;
            $plan->duration = $request->duration ?? $plan->duration;

            $plan->save();

            return response(["code" => 1, "message" => "plan updated successfully"]);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function delete_plan($id)
    {

        try {

            $plan = Plan::find($id)->delete();

            return response(["code" => 1, "message" => "plan deleted successfully"]);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }

    }
}
