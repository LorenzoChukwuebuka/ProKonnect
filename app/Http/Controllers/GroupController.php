<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Validator;

class GroupController extends Controller
{
    public function create_group(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                "group_name" => "required",
                "group_description" => [],

            ]);

            if ($validator->fails()) {
                return response()->json(["code" => 3, 'error' => $validator->errors()], 401);
            }

            $group = Group::create([
                'group_name' => $request->group_name,
                'group_description' => $request->group_description,
                'user_id' => auth()->user()->id,

            ]);

            return response(["code" => 1, "message" => "group created successfully"]);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function users_with_similar_interests(Request $request)
    {
        try {

            $user = auth()->user();

            $guides = User::where('user_type', 'student')
                ->join('user_interests', 'users.id', '=', 'user_interests.user_id')
                ->whereIn('user_interests.interest_id', $user->userinterests()->pluck('interest_id'))
                ->select('users.id', 'users.full_name', 'users.username', 'users.email', 'users.bio', 'users.profile_image', 'users.status', 'users.country_id', 'users.phone_number')
                ->when($request->search_user, function ($query) use ($request) {
                    $query->where("users.username", "like", "%" . $request->search_user . "%");
                })
                ->get();

            if (count($guides) == 0) {
                return response(["code" => 3, "message" => "No student  with similar interest or username found"]);
            }

            return response(["code" => 1, "data" => $guides]);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function add_users(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                "group_id" => "required",
                "user_id" => "required",

            ]);

            if ($validator->fails()) {
                return response()->json(["code" => 3, 'error' => $validator->errors()], 401);
            }

            $add_users = UserGroup::create([
                "group_id" => $request->group_id,
                "user_id" => $request->user_id,
            ]);

            #update the group member count

            $group = Group::find($request->group_id);

            $group->number_of_participants++;

            $group->save();

            return response(["code" => 1, "message" => "user add to group successfully"]);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function get_all_groups_created_by_a_particular_user()
    {

        try {
            $group = Group::with('user')->where('status', 'active')->where('user_id', auth()->user()->id)->latest()->get();

            if ($group->count() == 0) {
                return response(["code" => 3, "message" => "No record found"]);
            }

            return response(["code" => 1, "data" => $group]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }

    }

    /**
     * @var int $id
     * gets a group created by a particular user
     * @return Group
     * @throws Exception
     */

    public function get_a_particular_group_for_a_user($id)
    {
        try {
            $group = Group::find($id)->where('user_id', auth()->user()->id)->first();
            if ($group == null) {
                return response(["code" => 3, "message" => "No record found"]);
            }

            return response(["code" => 1, "data" => $group]);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function delete_group($id)
    {
        try {
            $group = Group::find($id)->delete();

            return response(["code" => 1, "message" => "group deleted successfully"]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function edit_group(Request $request, $id)
    {
        try {
            $group = Group::find($id);

            $group->group_name = $request->group_name ?? $group->group_name;
            $group->group_description = $request->group_description ?? $group->group_description;

            $group->save();

            return response(["code" => 1, "message" => "update successfully"]);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function delete_users_from_group($user_id, $group_id)
    {
        try {
            $usergroup = UserGroup::where('user_id', $user_id)->where('group_id', $group_id)->delete();

            $group = Group::find($group_id);

            $group->number_of_participants--;

            $group->save();
            return response(["code" => 1, "message" => "user deleted from group successfully"]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function change_group_status($id)
    {
        try {
            $group = Group::find($id);

            ($group->status == 'active') ? $group->status = 'inactive' : $group->status = 'inactive';

            $group->save();

            return response(["code" => 1, "message" => "status changed successfully"]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function get_groups_with_users()
    {
        try {
            $group = Group::with(['user_group' => function ($query) {
                $query->with('user');
            }])->where('user_id', auth()->user()->id)->get();

            if ($group->count() == 0) {
                return response(["code" => 3, "message" => "No record found"]);
            }

            return response(["code" => 1, "data" => $group]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function get_user_groups()
    {

        try {
            $userGroups = UserGroup::with(['group' => function ($query) {
                $query->with('user');
            }])->where('user_id', auth()->user()->id)->latest()->get();

            if ($userGroups->count() == 0) {
                return response(["code" => 3, "message" => "No record found"]);
            }

            return response(["code" => 1, "data" => $userGroups]);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function exit_group_by_participant($group_id)
    {
        try {
            $usergroup = UserGroup::where('user_id', auth()->user()->id)->where('group_id', $group_id)->delete();

            $group = Group::find($group_id);

            $group->number_of_participants--;

            $group->save();
            return response(["code" => 1, "message" => "user deleted from group successfully"]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }
}
