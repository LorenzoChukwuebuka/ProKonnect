<?php

namespace App\Http\Controllers\Auth;

use App\Custom\MailMessages;
use App\Http\Controllers\Controller;
use App\Models\OTPToken;
use App\Models\Payment;
use App\Models\Referal;
use App\Models\Socials;
use App\Models\User;
use App\Models\UserInterests;
use App\Models\UserQualification;
use App\Models\UserSpecialization;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Str;
use Validator;
use WisdomDiala\Countrypkg\Models\Country;

class UserAuthController extends Controller
{
    public function create_user(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'full_name' => 'required|max:255',
                'username' => [],
                'email' => 'required|email|unique:users',
                'country_id' => [],
                'qualification' => [],
                'interest' => [],
                'specialization' => [],
                'university' => [],
                'user_type' => 'required',
                'password' => [],
                'referal_code' => [],
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            #handles the profile image
            if ($request->hasFile('profile_image')) {
                $validate = Validator::make($request->all(), ['profile_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048']);
                if ($validate->fails()) {
                    return response()->json(["code" => 3, 'error' => $validate->errors()->first()]);
                }
                $profile_image = $request->profile_image->store('user_profile_images', 'public');
            }

            $userCreate = User::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'country_id' => $request->country,
                'user_type' => $request->user_type,
                'university_id' => $request->university_id,
                'password' => Hash::make($request->password) ?? null,
                'profile_image' => $profile_image ?? null,
                'referal_code' => "prf" . Str::random(12),

            ]);

            #create qualification

            if ($request->qualification != null) {
                $qualification = json_decode($request->qualification);

                foreach ($qualification as $key => $value) {
                    UserQualification::create([
                        "user_id" => $userCreate->id,
                        "qualification_id" => $value,
                    ]);
                }

            }

            #create interest
            if ($request->interest != null) {

                $interest = json_decode($request->interest);

                foreach ($interest as $key => $value) {
                    UserInterests::create([
                        "user_id" => $userCreate->id,
                        "interest_id" => $value,
                    ]);
                }

            }

            #create specialization

            if ($request->specialization != null) {
                $specilization = json_decode($request->specialization);

                foreach ($specilization as $key => $value) {
                    UserSpecialization::create([
                        "user_id" => $userCreate->id,
                        "specialization_id" => $value,
                    ]);
                }

            }

            #referal code if any

            if ($request->referal_code != null) {
                $user = User::where('referal_code', $request->referal_code)->first();

                if ($user == null) {
                    goto create_token;
                }

                Referal::create([
                    "referal_id" => $user->id,
                    "referee_id" => $userCreate->id,
                ]);

            }
            #create otp and send mail

            create_token:
            $otp_token = $this->generateRandom(4);

            OTPToken::create([
                "token" => $otp_token,
                "user_id" => $userCreate->id,
            ]);

            MailMessages::UserVerificationMail($otp_token, $request->email);

            return response(['code' => 1, "user_id" => $userCreate->id, 'message' => 'Account  successfully created']);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function create_user_password(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [

                'password' => 'required_with:password_confirmation|same:password_confirmation|min:6',
                'password_confirmation' => 'min:6',
                'user_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(["code" => 3, 'error' => $validator->errors()], 401);
            }
            $user = User::find($request->user_id);

            $user->password = Hash::make($request->password);
            $user->save();

            return response(["message" => "Password has been updated", "code" => 1]);

        } catch (\Throwable$th) {
            return response(['code' => 3, "message" => $th->getMessage()]);
        }

    }

    public function verify_user(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'verify_token' => ['required', 'string', 'max:200'],

            ]);

            if ($validator->fails()) {
                return response()->json(["code" => 3, 'error' => $validator->errors()], 401);
            }

            $token = OTPToken::where('token', $request->verify_token)->first();

            if ($token != null) {
                $user = User::find($token->user_id);
                $user->email_verified_at = Carbon::now();
                if ($user->save()) {
                    $token->delete();
                    return response()->json([
                        'code' => 1,
                        'user_id' => $token->user_id,
                        'message' => "email verified",
                    ]);
                }
            } else {
                return response()->json([
                    'code' => 3,
                    'message' => 'token not found',
                ]);
            }

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function login_user(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(["code" => 3, 'error' => $validator->errors()], 401);
            }

            if (!auth()->attempt($request->only(['email', 'password']))) {
                return response()->json(["code" => 3, "error" => "Invalid email or passsword"], 401);
            }

            #for blocked users

            $blockedUsers = User::where('email', $request['email'])->where('status', 'blocked')->first();

            if ($blockedUsers) {
                return response(["code" => 3, "message" => "Your account has been temporarily blocked by the admin. Contact support for help"]);
            }

            # for active users
            $user = User::where('email', $request['email'])->where('status', 'active')->first();

            //   return   $user->created_at->isPast();

            if ($user) {
                if ($user) {
                    $status = 200;

                    #check if payment has exceeded the time duration

                    $payment = Payment::where('payer_id', auth()->user()->id)->latest()->first();
                    $checkExpiry = true;

                    if ($payment) {
                        $checkExpiry = Carbon::now()->gt($payment->expiry_date);
                    }

                    $country = Country::where('id', auth()->user()->country_id)->first();
                    $response = [
                        'type' => 'user',
                        // 'user_auth_type' => ($user->password != null) ? 'main' : 'google',
                        'user' => auth()->user(),
                        'country' => $country,
                        'token' => auth()->user()->createToken('auth_token')->plainTextToken,
                        'access' => ($checkExpiry) ? "Access denied " : "Access granted",

                    ];
                    return response()->json($response, $status);
                } else {
                    return response()->json(["code" => 3, 'message' => "No user with that email"], 401);
                }
            }

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }

    }

    public function user_change_password(Request $request)
    {
        # validate user inputs
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => ['required', 'string', 'min:4', 'confirmed'],
        ]);

        # check if validation fails
        if ($validator->fails()) {
            # return validation error
            return response()->json(["code" => 3, 'error' => $validator->errors()], 401);
        }
        # check if the user is authenticated
        if (auth()->user()) {
            try {
                # checking if the password matches with current password in use
                if (password_verify(request('current_password'), auth()->user()->password)) {
                    # update the new password
                    auth()->user()->update(['password' => Hash::make(request('password'))]);
                    # return success message after updating
                    return response()->json([
                        'code' => 1,
                        'data' => [
                            'message' => 'password changed.',
                        ],
                    ]);
                } else {
                    return response()->json([
                        'code' => 3,
                        'message' => 'password mismatch',
                    ]);
                }
            } catch (\Throwable$e) {
                return response()->json([
                    'code' => 3,
                    'error ' => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'code' => 3,
                'message' => 'unauthenticated user',
            ], 401);
        }
    }

    public function user_forget_password(Request $request)
    {
        $token = $this->generateRandom(4);
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 3, 'error' => $validator->errors()], 401);
        }
        //insert into password reset db
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        MailMessages::UserResetPasswordMail($token, $request->email);

        return response()->json(['message' => 'Email has been sent']);
    }

    public function user_reset_password(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 3, 'error' => $validator->errors()], 401);
        }

        $updatePassword = DB::table('password_resets')
            ->where([
                'email' => $request->email,
                'token' => $request->token,
            ])
            ->get();

        if ($updatePassword->count() > 0) {
            $user = User::where('email', $request->email)->update(['password' => Hash::make($request->password)]);
            DB::table('password_resets')->where(['email' => $request->email])->delete();

            return response()->json(["code" => 1, 'message' => "Password has been updated"]);
        } else {
            return response(["code" => 3, "error" => "Invalid token"]);
        }

    }

    public function editUserCredentials(Request $request)
    {

        try {
            $user = User::find(Auth::user()->id);

            $user->full_name = $request->full_name ?? $user->full_name;
            $user->bio = $request->bio ?? $user->bio;
            $user->university_id = $request->university_id ?? $user->university_id;
            $user->country_id = $request->country_id ?? $user->country_id;
            $user->phone_number = $request->phone_number ?? $user->phone_number;
            $user->save();

            if ($request->data) {
                $this->edit_user_qualification($request->data);
            }

            return response(["code" => 1, "message" => "Credentials updated"]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }

    }

    public function update_profile_image(Request $request)
    {
        $user = auth()->user();

        if ($request->hasFile('profile_image')) {
            $validator = Validator::make($request->all(), [

                'profile_image' => 'required|image|mimes:jpeg,webp,png,jpg,gif,svg|max:5048',

            ]);

            if ($validator->fails()) {
                return response()->json(['code' => 3, 'error' => $validator->errors()->first()], 401);
            }
            $profile_image = $request->profile_image->store('profile_images', 'public');

            $user->profile_image = $profile_image;
            $user->save();

            return response(["message" => "Profile image has been updated", "code" => 1]);
        } else {
            return response(["message" => "Profile image has not been updated", "code" => 3]);
        }

    }

    public function create_referal_code(Request $request)
    {
        $user = auth()->user();

        $user->referal_code = $request->referal_code;

        $user->save();

        return response(["code" => 1, "message" => "created referal code successfull"]);

    }

    public function get_bio()
    {
        try {
            return response(["code" => 1, "data" => auth()->user()->bio ?? "No record found"]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    private function generateRandom(int $length)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789', ceil($length / strlen($x)))), 1, $length);
    }

    private function edit_user_qualification($request)
    {
        try {
            #delete all interests where the id matches the user id

            $qualifications = auth()->user()->userqualification()->latest()->get();

            foreach ($qualifications as $qualification) {
                $qualification->delete();
            }

            $len = count($request->data);

            $data = $request->data;

            $i = 0;

            for ($i; $i < $len; $i++) {
                UserQualification::create([
                    "user_id" => auth()->user()->id,
                    "qualification_id" => $data[$i]["qualification"],
                ]);
            }

            return response(["code" => 1, "message" => "updated successfully"]);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function user_details()
    {
        try {


             $user = DB::table('countries')->join('users', 'country_id', '=', 'users.country_id')->select('countries.name','countries.short_name','users.*')->where('users.id', auth()->user()->id)->first();;
            return response(["code" => 1, "data" => $user]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function add_socials(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [

                "social" => "required",
                "link" => "required",
            ]);

            if ($validator->fails()) {
                return response()->json(["code" => 3, 'error' => $validator->errors()], 401);
            }

            $socials = Socials::create([
                "user_id" => auth()->user()->id,
                "social" => $request->social,
                "link" => $request->link,
            ]);

            return response(["code" => 1, "message" => "socials created successfully"]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function get_socials()
    {
        try {
            $socials = auth()->user()->socials()->latest()->get();

            if ($socials->count() == 0) {
                return response(["code" => 1, "message" => "No record found"]);
            }

            return response(["code" => 1, "data" => $socials]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function edit_socials(Request $request, $id)
    {
        try {
            $socials = Socials::find($id);

            $socials->link = $request->link ?? $socials->link;
            $socials->social = $request->social ?? $social->social;

            $socials->save();
            return response(["code" => 1, "message" => "socials updated successfully"]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function delete_socials($id)
    {
        try {
            $socials = Socials::find($id)->delete();

            return response(["code" => 1, "message" => "Socials deleted successfully"]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }

    }

}
