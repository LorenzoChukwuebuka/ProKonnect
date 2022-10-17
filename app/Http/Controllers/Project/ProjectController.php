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
                'proguide_id' => "required",

            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            $projectCreate = Project::create([
                "user_id" => auth()->user()->id,
                "project_name" => $request->project_name,
                "country_id" => $request->country_id,
                "start_date" => $request->start_date,
                "end_date" => $request->end_date,
                "overview" => $request->overview,
                "project_type" => $request->project_type,
                "proguide_id" => $request->proguide_id,
            ]);

            return \response(["code" => 1, "message" => "created project successfully"]);

        } catch (\Throwable$th) {
            return $th;
        }
    }

    public function get_all_projects()
    {
        try {

            $project = auth()->user()->project()->with('user', 'country')->latest()->get();

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
            return $project = auth()->user()->project()->with('user', 'country')->where('id', $id)->first();

            if ($project == null) {
                return response(["code" => 3, "message" => "No record found"]);
            }
        } catch (Throwable $th) {
            throw $th;
        }
    }

    public function edit_project($id, Request $request)
    {
        try {
            $project = Project::find($id);

            $project->project_name = $request->project_name ?? $project->project_name;
            $project->country_id = $request->country_id ?? $project->country_id;
            $project->start_date = $request->start_date ?? $project->start_date;
            $project->end_date = $request->end_date ?? $project->end_date;
            $project->overview = $request->overview ?? $project->overview;
            $project->project_type = $request->project_type ?? $project->project_type;
            $project->proguide_id = $request->proguide_id ?? $project->proguide_id;

            $project->save();

            return response(["code" => 1, "message" => "project updated successfully"]);

        } catch (\Throwable$th) {
            return $th;
        }
    }

    public function delete_project($id)
    {
        try {
            $project = Project::find($id)->delete();

            return response(["code" => 1, "message" => "Project deleted successfully"]);
        } catch (\Throwable$th) {
            return $th;
        }
    }

    public function toggle_status($id)
    {
        try {
            $project = Project::find($id);

            ($project->status == "active") ? "inactive" : "active";

            $project->save();

            return response(["code" => 1, "status updated"]);

        } catch (\Throwable$th) {
            return $th;
        }
    }

    #find proguides by user interests

    public function find_proguides_by_user_interests()
    {
        try {

            $interests = auth()->user();

        } catch (\Throwable$th) {
            return $th;
        }
    }
}
