<?php

namespace App\Http\Controllers\Auth;

use App\Custom\MailMessages;
use App\Http\Controllers\Controller;
use App\Models\OTPToken;
use App\Models\Referal;
use App\Models\User;
use App\Models\UserInterests;
use App\Models\UserSpecialization;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Str;
use Validator;

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

            //handles the profile image
            if ($request->hasFile('profile_image')) {
                $validate = Validator::make($request->all(), ['profile_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048']);
                if ($validate->fails()) {
                    return response()->json(["code" => 3, 'errors' => $validate->errors()->first()]);
                }
                $profile_image = $request->profile_image->store('user_profile_images', 'public');
            }

            $userCreate = User::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'country_id' => $request->country_id,
                'user_type' => $request->user_type,
                'university_id' => $request->university_id,
                'password' => Hash::make($request->password) ?? null,
                'profile_image' => $profile_image ?? null,
                'referal_code' => "prf" . Str::random(12),

            ]);

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

            return response(['code' => 1, 'message' => 'User successfully created']);

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

            $user = User::where('email', $request['email'])->where('status', 'active')->first();

            if ($user) {
                $status = 200;
                $response = [
                    'type' => 'user',
                    // 'user_auth_type' => ($user->password != null) ? 'main' : 'google',
                    'user' => auth()->user(),
                    'token' => auth()->user()->createToken('auth_token')->plainTextToken,
                ];
                return response()->json($response, $status);
            } else {
                return response()->json(["code" => 3, 'error' => "No user with that email"], 401);
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
            return response()->json(["code"=>3,'error' => $validator->errors()], 401);
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
                        'code' => 4,
                        'message' => 'password mismatch',
                    ]);
                }
            } catch (\Exception$e) {
                return response()->json([
                    'code' => 4,
                    'message' => 'an exceptional error occured',
                ], 500);
            } catch (\Error$e) {
                return response()->json([
                    'code' => 4,
                    'message' => 'an error occured',
                ], 500);
            }
        } else {
            return response()->json([
                'code' => 4,
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
            return response()->json(['code'=>3,'error' => $validator->errors()], 401);
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
            return response()->json(['code'=>3,'error' => $validator->errors()], 401);
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

            // if ($request->hasFile('profile_image')) {
            //     $profile_image = $request->profile_image->store('user_profile_images', 'public');
            // }

            $user->full_name = $request->full_name ?? $user->full_name;
            $user->email = $request->full_name ?? $user->full_name;
            $user->bio = $request->bio ?? $user->bio;
            $user->university_id = $request->university_id ?? $user->university_id;
            $user->country_id = $request->country_id ?? $user->country_id;

            $user->save();

            return response(["code" => 1, "message" => "Credentials updated"]);
        } catch (\Throwable $th) {
            return response(["code"=>3,"error"=>$th->getMessage()]);
        }


    }

    public function update_bio()
    {
        try {
            $user = auth()->user();
        } catch (\Throwable$th) {
            //throw $th;
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

    private function generateRandom(int $length)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789', ceil($length / strlen($x)))), 1, $length);
    }

}
