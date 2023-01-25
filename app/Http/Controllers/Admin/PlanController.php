<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanOption;
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
            $plan = Plan::with('plan_options')->latest()->get();

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

    public function create_plan_options(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "plan_id" => "required",
                "option_name" => "required",
                "amount" => ["required"],
                "previous_amount" => [],
                "description" => [],
                "number_of_proguides" => "required",
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            $plan_options = PlanOption::create([
                "plan_id" => $request->plan_id,
                "option_name" => $request->option_name,
                "amount" => $request->amount,
                "previous_amount" => $request->previous_amount,
                "description" => $request->description,
                "number_of_proguides" => $request->number_of_proguides,
            ]);

            return response(["code" => 1, "message" => "plan options created successfully"]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function get_all_plan_options()
    {
        try {
            $plan_options = PlanOption::with('plan')->latest()->get();

            if ($plan_options->count() == 0) {
                return response(["code" => 3, "message" => "No record found"]);
            }

            return response(["code" => 1, "data" => $plan_options]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function get_all_plan_options_with_plan_id($id)
    {
        try {
            $plan_options = PlanOption::where('plan_id', $id)->latest()->paginate(10);
            if ($plan_options->count() == 0) {
                return response(["code" => 3, "message" => "No record found"]);
            }

            return response(["code" => 1, "data" => $plan_options]);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function edit_plan_options(Request $request, $id)
    {
        try {
            $plan_options = PlanOption::find($id);

            $plan_options->plan_id = $request->plan_id ?? $plan_options->plan_id;
            $plan_options->option_name = $request->option_name ?? $plan_options->option_name;
            $plan_options->amount = $request->amount ?? $plan_options->amount;
            $plan_options->previous_amount = $request->previous_amount ?? $plan_options->previous_amount;
            $plan_options->description = $request->description ?? $plan_options->description;

            $plan_options->save();

            return \response(["code" => 1, "message" => "plan option edited successfully"]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }

    }

    public function delete_plan_options($id)
    {
        try {
            $plan_options = PlanOption::find($id)->delete();

            return response(["code" => 1, "message" => "options deleted successfully"]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }
}
