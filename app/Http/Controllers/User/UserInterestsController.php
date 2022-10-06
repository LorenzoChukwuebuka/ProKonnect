<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserInterests;
use Illuminate\Http\Request;
use Validator;

class UserInterestsController extends Controller
{
    public function create_user_interests(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "interest_id" => [],
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            foreach ($variable as $key => $value) {
                $userInterests = UserInterests::create([
                    "user_id" => auth()->user()->id,
                    "interest_id" => $value,
                ]);
            }

            return response(["code" => 1, "message" => "created successfully"]);

        } catch (\Throwable$th) {

        }
    }


    
}
