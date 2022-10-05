<?php

namespace App\Http\Controllers\Auth;

use App\Custom\MailMessages;
use App\Http\Controllers\Controller;
use App\Models\OTPToken;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
                'qualification_id' => 'required',
                'interests' => [],
                'specialization' => [],
                'user_type' => 'required',
                'password' => 'required|min:8',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            //handles the profile image
            if ($request->hasFile('profile_image')) {
                $validate = Validator::make($request->all(), ['profile_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048']);
                if ($validate->fails()) {
                    return response()->json(['errors' => $validate->errors()->first()]);
                }
                $profile_image = $request->profile_image->store('user_profile_images', 'public');
            }

            $userCreate = User::create(array_merge($validator->validated(), [
                'password' => Hash::make($request->password),
                'profile_image' => $profile_image ?? null,
            ]));

            if ($request->interests != null) {
                //create interests
            }

            #create specialization

            if ($request->specialization != null) {
                //create interests
            }

            #create otp and send mail

            $otp_token = $this->generateRandom(4);

            OTPToken::create([
                "token" => $otp_token,
                "user_id" => $userCreate->id,
            ]);

            MailMessages::UserVerificationMail($otp_token, $request->email);

            return response(['code' => 1, 'message' => 'User successfully created']);

        } catch (\Throwable$th) {
            return $th;
        }
    }

    public function verify_user(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'verify_token' => ['required', 'string', 'max:200'],
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
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
            return $th;
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
                return response()->json(['error' => $validator->errors()], 401);
            }

            if (!auth()->attempt($request->only(['email', 'password']))) {
                return response()->json(["error" => "Invalid email or passsword"], 401);
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
                return response()->json(['error' => "No user with that email"], 401);
            }

        } catch (\Throwable$th) {
            return $th;
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
            return response()->json(['error' => $validator->errors()], 401);
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
            return response()->json(['error' => $validator->errors()], 401);
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
            return response()->json(['error' => $validator->errors()], 401);
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
            return response(["error" => "Invalid token"]);
        }

    }

    public function editUserCredentials(Request $request)
    {
        $user = User::find(Auth::user()->id);

        $validator = Validator::make($request->all(), [
            'first_name' => 'max:255',
            'middle_name' => [],
            'last_name' => 'max:255',
            'username' => [],
            'email' => 'email',
            'phone_number' => 'max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        if ($request->hasFile('profile_image')) {
            $profile_image = $request->profile_image->store('profile_images', 'public');
        }

        $user->first_name = $request->first_name ?? $user->first_name;
        $user->middle_name = $request->middle_name ?? $user->middle_name;
        $user->last_name = $request->last_name ?? $user->last_name;
        $user->username = $request->username ?? $user->username;
        $user->email = $request->email ?? $user->email;
        $user->phone_number = $request->phone_number ?? $user->phone_number;
        $user->profile_image = $profile_image ?? $user->profile_image;

        $user->save();

        return response(["message" => "Credentials updated"]);
    }

    public function update_profile_image(Request $request)
    {
        $user = auth()->user();

        if ($request->hasFile('profile_image')) {
            $validator = Validator::make($request->all(), [

                'profile_images' => 'required|image|mimes:jpeg,webp,png,jpg,gif,svg|max:5048',

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

    private function generateRandom(int $length)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789', ceil($length / strlen($x)))), 1, $length);
    }

}
