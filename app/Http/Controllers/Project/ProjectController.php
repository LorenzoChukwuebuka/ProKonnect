<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
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
                'proguide_id' => [],
                "specialization_id" => [],

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
                "specialization_id" => $request->specialization_id,
            ]);

            return \response(["code" => 1, "message" => "created project successfully"]);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function get_all_projects()
    {
        try {

            $project = auth()->user()->project()->with(['user', 'country', 'proguide' => function ($query) {
                $query->with(['userspecialization' => function ($query1) {
                    $query1->with('specialization');
                }]);
            }])->latest()->get();

            if ($project->count() == 0) {
                return response(["code" => 3, "message" => "No records found"]);
            }
            return response(["code" => 1, "data" => $project]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function get_projects_by_id($id)
    {
        try {
            $project = auth()->user()->project()->with(['user', 'country', 'proguide' => function ($query) {
                $query->with(['userspecialization' => function ($query1) {
                    $query1->with('specialization');
                }]);
            }])->where('id', $id)->first();

            if ($project == null) {
                return response(["code" => 3, "message" => "No record found"]);
            }
        } catch (Throwable $th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
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
            $project->specialization_id = $request->specialization_id ?? $project->specialization_id;

            $project->save();

            return response(["code" => 1, "message" => "project updated successfully"]);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function delete_project($id)
    {
        try {
            $project = Project::find($id)->delete();

            return response(["code" => 1, "message" => "Project deleted successfully"]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
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
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    #find proguides by user interests

    public function find_proguides_by_user_interests(Request $request)
    {

        try {

            $user = auth()->user();

            $guides = User::with(['userspecialization' => function ($query) {
                $query->with('specialization');
            }])->where('user_type', 'proguide')
                ->join('user_interests', 'users.id', '=', 'user_interests.user_id')
                ->whereIn('user_interests.interest_id', $user->userinterests()->pluck('interest_id'))
                ->select('users.id', 'users.full_name', 'users.username', 'users.profile_image', 'users.status', 'users.country_id', 'users.bio')
                ->when($request->search_user, function ($query) use ($request) {
                    $query->where("users.username", "like", "%" . $request->search_user . "%");
                })
                ->get();

            if (count($guides) == 0) {
                return response(["code" => 3, "message" => "No proguide with similar interest found"]);
            }

            return response(["code" => 1, "data" => $guides]);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

}
