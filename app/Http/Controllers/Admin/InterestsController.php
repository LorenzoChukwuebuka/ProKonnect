<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Interests;
use Illuminate\Http\Request;
use Validator;

class InterestsController extends Controller
{
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "interests" => 'required|max:255|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            Interests::create([
                "interests" => $request->interests,
            ]);

            return response(["code" => 1, "message" => "Interest created successfully"]);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function show($id)
    {

    }

    public function findAll()
    {
        try {
            $interests = Interests::all();
            if ($interests->count() == 0) {
                return response(["code" => 3, "message" => "No record found"]);
            }

            return response(["code" => 1, "data" => $interests]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $interests = Interests::find($id);

            $interests->interests = $request->interests ?? $interests->interests;

            $interests->save();

            return response(["code" => 1, "message" => "updated successfully"]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $interests = Interests::find($id)->delete();

            if ($interests) {
                return response()->json(["code" => 1, "message" => 'Interest has been deleted!']);
            }
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }
}
