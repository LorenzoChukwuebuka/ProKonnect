<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\User;
use App\Models\Payment;
use App\Custom\MailMessages;
use Illuminate\Http\Request;
use App\Models\StudentsProguide;

class StudentsProguideController extends Controller
{
    public function create_students_proguide(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "proguide_id" => "required",
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            #check if student has exceeded the max number of proguides

            $student_max = Payment::where('payer_id', auth()->user()->id)->first();

            if ($student_max->number_of_proguides === 0) {
                return response(["code" => 3, "message" => "Max number of prguides exceeded "]);
            }

            $studentProguide = StudentsProguide::create([
                "user_id" => auth()->user()->id,
                "proguide_id" => $request->proguide_id,
            ]);

            $student = Payment::where('payer_id', auth()->user()->id)->first();

            $student_max->number_of_proguides--;

            $student->save();

            $this->send_notification_mail($request->proguide_id, auth()->user()->full_name);

            return response(["code" => 1, "message" => "created successfully"]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function get_all_students_proguides()
    {
        try {
            $studentProguide = StudentsProguide::with('proguide', 'student')->latest()->where('proguide_id', auth()->user()->id)->get();

            if ($studentProguide->count() == 0) {
                return response(["code" => 3, "message" => "No record found"]);
            }

            return response(["code" => 1, "data" => $studentProguide]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function get_all_proguides_students()
    {
        try {
            $studentProguide = StudentsProguide::with('proguide', 'student')->latest()->where('user_id', auth()->user()->id)->get();

            if ($studentProguide->count() == 0) {
                return response(["code" => 3, "message" => "No record found"]);
            }

            return response(["code" => 1, "data" => $studentProguide]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function delete_student_proguide($id)
    {
        try {
            $student = StudentsProguide::find($id)->delete();
            return response(["code" => 1, "message" => "deleted successfully"]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    private function send_notification_mail($proguide_id, $student)
    {
        $proguide_email = User::where('id', $proguide_id)->first();

        MailMessages::SendNotificationMailToProguide($student, $proguide_email->email);

    }
}
