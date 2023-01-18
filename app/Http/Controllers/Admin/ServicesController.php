<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Validator;

class ServicesController extends Controller
{
    public function create_services(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "service" => 'required|max:255|string',
                "description" => "max:1000|string",
                "service_icon" => "mimes:jpeg,png,jpg,gif,svg|max:10000",
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            if ($request->hasFile('service_icon')) {

                $files = $request->file('service_icon')->store('service_files', 'public');

            }

            Service::create([
                "service" => $request->service,
                "service_icon" => $files ?? null,
                "description" => $request->description,
            ]);

            return response(["code" => 1, "message" => "Service created successfully"]);

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

            $services->service = $request->service ?? $services->service;

            $services->save();

            return response(["code" => 1, "message" => "updated successfully"]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $services = Service::find($id)->delete();

            if ($services) {
                return response()->json(["code" => 1, "message" => 'services has been deleted!']);
            }
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

}
