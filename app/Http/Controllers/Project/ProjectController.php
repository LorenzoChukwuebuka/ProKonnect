<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Validator;

class ProjectController extends Controller
{
    public function create_project(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "project_name" => "required",
                "country_id" => [],
                "start_date" => [],
                "end_date" => [],
                "overview" => [],
                "project_type" => "required",

            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            $projectCreate = Project::create([
                "user_id" => auth()->user()->id,
                array_merge($validator->validated()),
            ]);

            return \response(["code" => 1, "message" => "created project successfully"]);

        } catch (\Throwable$th) {
            return $th;
        }
    }

    public function get_all_projects()
    {
        try {
            $project = auth()->user()->projects()->latest()->get();

            if ($project->count() == 0) {
                return response(["code" => 3, "message" => "No records found"]);
            }
            return response(["code" => 1, "data" => $project]);
        } catch (\Throwable$th) {
            throw $th;
        }
    }

    public function get_projects_by_id($id)
    {
        try {
            $project = Project::with(["user"]);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
