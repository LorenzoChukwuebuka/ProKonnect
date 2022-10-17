<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Models\UserSpecialization;
use App\Http\Controllers\Controller;

class UserSpecializationController extends Controller
{
    public function create_user_qualification(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                "qualification_id" => [],
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            foreach ($variable as $key => $value) {
                $userInterests = UserSpecialization::create([
                    "user_id" => auth()->user()->id,
                    "qualification_id" => $value,
                ]);
            }

            return response(["code" => 1, "message" => "created successfully"]);

        } catch (\Throwable$th) {
            return $th;
        }

    }

    public function get_all_user_user_interests()
    {
        try {
            $qualifications = auth()->user()->user_qualifications()->latest()->get();

            if ($interests->count() == 0) {
                return response(["code" => 3, "message" => "No record found"]);
            }

            return response(["code" => 1, "data" => $qualifications]);
        } catch (\Throwable$th) {
            return $th;
        }
    }

    public function delete_interests($id)
    {
        try {
            $deleteInterests = UserSpecialization::find($id)->delete();

            return response(["code" => 1, "message" => "interest deleted "]);
        } catch (\Throwable$th) {
            return $th;
        }

    }
}
