<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function view_all_students_and_filter(Request $request)
    {
        try {
            $students = User::with('userinterests', 'userqualification', 'userspecialization')
                ->where('user_type', 'student')
                ->when($request->status, function ($query) use ($request) {
                    $query->where('status', $request->status);
                })
                ->latest()
                ->get();

            if ($students->count() == 0) {
                return response(["code" => 3, "message" => "No record found"]);
            }
            return response(["code" => 1, "data" => $students]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function view_all_proguides_and_filter(Request $request)
    {
        try {
            $proguides = User::with('userinterests', 'userqualification', 'userspecialization')
                ->where('user_type', 'proguide')
                ->when($request->status, function ($query) use ($request) {
                    $query->where('status', $request->status);
                })
                ->latest()
                ->get();

            if ($proguides->count() == 0) {
                return response(["code" => 3, "message" => "No record found"]);
            }
            return response(["code" => 1, "data" => $proguides]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function unblock_blocked_users($id)
    {
        try {
            $blocked_user = User::find($id);

            if ($blocked_user->status == 'blocked') {
                $blocked_user->status = 'active';
                $blocked_user->bad_word_count = 0;

                $blocked_user->save();
            }

            return response(["code" => 1, "message" => "User has been unblocked"]);
        } catch (\Throwable$th) {
            return reponse(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function search_students(Request $request)
    {
        try {
            $students = User::where('user_type', 'student')
                ->orWhere('username', "like", "%" . $request->search . "%")
                ->orWhere('full_name', "like", "%" . $request->search . "%")
                ->latest()
                ->get();

            if ($students->count() == 0) {
                return response(["code" => 3, "message" => "No record found"]);
            }
            return response(["code" => 1, "data" => $students]);

        } catch (\Throwable$th) {
            return reponse(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function search_proguides(Request $request)
    {
        try {
            $proguides = User::where('user_type', 'proguide')
                ->orWhere('username', "like", "%" . $request->search . "%")
                ->orWhere('full_name', "like", "%" . $request->search . "%")
                ->latest()
                ->get();

            if ($proguides->count() == 0) {
                return response(["code" => 3, "message" => "No record found"]);
            }
            return response(["code" => 1, "data" => $proguides]);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

}
