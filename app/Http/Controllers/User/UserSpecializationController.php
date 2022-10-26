<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserSpecialization;
use Illuminate\Http\Request;
use Validator;

class UserSpecializationController extends Controller
{
    public function create_user_specialization(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                "data" => [],
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            if (!$request->data) {
                return response(["code" => 3, "message" => "No request was sent"]);
            }

            $len = count($request->data);

            $data = $request->data;

            $i = 0;

            for ($i; $i < $len; $i++) {
                UserSpecialization::create([
                    "user_id" => auth()->user()->id,
                    "specialization_id" => $data[$i]["specialization"],
                ]);
            }

            return response(["code" => 1, "message" => "created successfully"]);

        } catch (\Throwable$th) {
            return $th;
        }

    }

    public function get_all_user_specialization()
    {
        try {
            $specialization = auth()->user()->userspecialization()->latest()->get();

            if ($specialization->count() == 0) {
                return response(["code" => 3, "message" => "No record found"]);
            }

            return response(["code" => 1, "data" => $specialization]);
        } catch (\Throwable$th) {
            return $th;
        }
    }

    public function delete_specialization($id)
    {
        try {
            $deleteInterests = UserSpecialization::find($id)->delete();

            return response(["code" => 1, "message" => "interest deleted "]);
        } catch (\Throwable$th) {
            return $th;
        }

    }

    public function edit_user_specialization(Request $request)
    {
        try {
            #delete all interests where the id matches the user id

            $specialization = auth()->user()->userspecialization()->latest()->get();

            foreach ($specialization as $specialization) {
                $specialization->delete();
            }

            $len = count($request->data);

            $data = $request->data;

            $i = 0;

            for ($i; $i < $len; $i++) {
                UserSpecialization::create([
                    "user_id" => auth()->user()->id,
                    "specialization_id" => $data[$i]["specialization"],
                ]);
            }

            return response(["code" => 1, "message" => "updated successfully"]);
        } catch (Throwable $th) {
            return $th;
        }
    }
}
