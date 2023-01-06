<?php

namespace App\Http\Controllers;

use App\Models\University;
use Illuminate\Http\Request;

class UniversityController extends Controller
{
    public function create_university(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "university" => "required",
            ]);

            if ($validator->fails()) {
                return response()->json(["code" => 3, 'error' => $validator->errors()], 401);
            }
            University::create([
                'university' => $request->university,
            ]);

            return response(["code" => 1, "message" => "university created successfully"]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function get_all_university()
    {
        try {
            $university = University::get();
            if (count($university) == 0) {
                return response(["code" => 3, "message" => "no record found"]);
            }

            return response(["code" => 1, "data" => $university]);
        } catch (\Throwable$th) {
            return response(["code" => "3", "error" => $th->getMessage()]);
        }
    }

    public function edit_university(Request $request, $id)
    {
        try {
            $university = University::find($id);

            $university->university = $request->university ?? $university->university;

            $university->save();

            return response(["code" => 1, "message" => "university updated successfully"]);
        } catch (\Throwable$th) {
            return response(["code" => "3", "error" => $th->getMessage()]);
        }
    }
}
