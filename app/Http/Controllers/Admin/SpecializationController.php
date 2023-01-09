<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specialization;
use Illuminate\Http\Request;
use Validator;

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
                "specialization" => $request->specialization,
            ]);

            return response(["code" => 1, "message" => "Specialization created successfully"]);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
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
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $specialization = Specialization::find($id);

            $specialization->specialization = $request->specialization ?? $specialization->specialization;

            $specialization->save();

            return response(["code" => 1, "message" => "updated successfully"]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $qualifications = Specialization::find($id)->delete();

            if ($qualifications) {
                return response()->json(["code" => 1, "message" => 'specilization has been deleted!']);
            }
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }
}
