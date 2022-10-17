<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Specialization;

class SpecializationController extends Controller
{
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "specialization" => 'required|max:255|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            Specialization::create([
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
            $qualifications = Specialization::all();
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
            $qualifications = Specialization::find($id);

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
            $qualifications = Specialization::find($id)->delete();

            if ($qualifications) {
                return response()->json(["message" => 'Qualification has been deleted!']);
            }
        } catch (\Throwable$th) {
            return $th;
        }
    }
}
