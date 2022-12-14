<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function create_services(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "services" => 'required|max:255|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            Service::create([
                array_merge($validator->validated()),
            ]);

            return response(["code" => 1, "message" => "Qualification created successfully"]);

        } catch (\Throwable$th) {
             return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function get_all_services()
    {
        try {
            $services = Service::all();

            if ($services->count() == 0) {
                return response(["code" => 3, "message" => "no record found"]);
            }

            return response(["code" => 1, "data" => $services]);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $services = Service::find($id);

            $services->service = $request->service ?? $qualifications->service;

            $services->save();

            return response(["code" => 1, "message" => "updated successfully"]);
        } catch (\Throwable$th) {
             return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function delete()
    {
        try {
            $services = Service::find($id)->delete();

            if ($services) {
                return response()->json(["message" => 'services has been deleted!']);
            }
        } catch (\Throwable$th) {
             return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }
}
