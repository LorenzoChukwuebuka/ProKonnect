<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Validator;

class ServiceCategoryController extends Controller
{
    public function create_service_category(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "service_id" => 'required|max:255|string',
                "service_category" => 'required|string|max:255',
                "review" => [],
                'service_category_file' => 'mimes:jpeg,png,jpg,gif,svg,pdf,docx|max:10000',
            ]);

            if ($validator->fails()) {
                return response()->json(['code' => 3, 'error' => $validator->errors()], 401);
            }
            if ($request->hasFile('service_category_file')) {
                $files = $request->file('service_category_file')->store('service_category_files', 'public');
            }

            $service_category = ServiceCategory::create([
                "service_category" => $request->service_category,
                "service_id" => $request->service_id,
                "review" => $request->review,
                "image" => $files ?? null,
            ]);

            return response(["code" => 1, "message" => "service category has been created successfully"]);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function get_service_category()
    {
        try {
            $service_category = ServiceCategory::with('services')->latest()->get();

            if ($service_category->count() == 0) {
                return response(["code" => 3, "message" => "no record found"]);
            }
            return response(["code" => 1, "data" => $service_category]);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function update_service_category(Request $request, $id)
    {
        try {
            $service_category = ServiceCategory::find($id);

            $service_category->service_id = $request->service_id ?? $service_category->service_id;
            $service_category->service_category = $request->service_category ?? $service_category->service_category;
            $service_category->review = $request->review ?? $service_category->review;

            if ($request->hasFile('service_category_file')) {
                $files = $request->file('service_category_file')->store('service_category_files', 'public');
            }

            $service_category->image = $files ?? $service_category->image;

            $service_category->save();

            return response(["code" => 1, "message" => "updated successfully"]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }

    }

    public function get_service_category_with_service_id($id)
    {
        try {
            $service_category = ServiceCategory::where('service_id', $id)->latest()->paginate(10);
            if ($service_category->count() == 0) {
                return response(["code" => 3, "message" => "no record found"]);
            }
            return response(["code" => 1, "data" => $service_category]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function delete_service_category($id)
    {
        try {
            $service_category = ServiceCategory::find($id)->delete();
            return response(["code" => "1", "message" => "Service category deleted successfully"]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

}
