<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Qualifications;
use Illuminate\Http\Request;
use Validator;

class QualificationsController extends Controller
{
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "qualification" => 'required|max:255|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            Qualifications::create([
                array_merge($validator->validated()),
            ]);

            return response(["code" => 1, "message" => "Qualification created successfully"]);

        } catch (\Throwable$th) {
            return $th;
        }
    }

    public function show($id)
    {

    }

    public function findAll()
    {
        try {
            $qualifications = Qualifications::all();
            if ($qualifications->count() == 0) {
                return response(["code" => 3, "message" => "No record found"]);
            }

            return response(["code" => 1, "data" => $qualifications]);
        } catch (\Throwable$th) {
            return $th;
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $qualifications = Qualifications::find($id);

            $qualifications->qualification = $request->qualification ?? $qualifications->qualification;

            $qualifications->save();

            return response(["code" => 1, "message" => "updated successfully"]);
        } catch (\Throwable$th) {
            return $th;
        }
    }

    public function delete()
    {
        try {
            $qualifications = Qualifications::find($id)->delete();

            if ($qualifications) {
                return response()->json(["message" => 'Qualification has been deleted!']);
            }
        } catch (\Throwable$th) {
            return $th;
        }
    }
}