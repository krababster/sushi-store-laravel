<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function registration(Request $request){
        $responses = [];
        $errors= [];


        if(User::where('user_login', $request->user_login)->first() || User::where('user_email', $request->user_email)->first()) {
            if (User::where('user_login', $request->user_login)->first()) {
                array_push($errors, 'Такий логін вже існує');
            }
            if (User::where('user_email', $request->user_email)->first()) {
                array_push($errors, 'Такий e-mail вже існує');
            }

            return response()->json([
                "status" => false,
                "message" => $errors
            ], 404);
        }

        if($request->user_login && $request->user_password && $request->user_email) {
            if(strlen($request->user_password)<8)
            {
                return response()->json([
                    "status"=>false,
                    "message"=>"Ваш пароль має буди довший 8 символів"
                ],400);
            }

            $user = User::create([
                "user_login" => $request->user_login,
                "user_password" => Hash::make($request->user_password),
                "user_email" => $request->user_email,
                "user_token"=>''
            ]);
            $token = Auth::guard('api')->login($user);
            $user->user_token = $token;
            $user->save();
            return response()->json([
                "status" => true,
                'user'=> $user,
//                "token"=>$token,
            ],200);
        }

        if(!$request->user_login ){
            $response = 'Поле Логін не може лишатися порожнім';
            array_push($responses,$response);
        }
        if(!$request->user_password){
            $response = 'Поле Пароль не може лишатися порожнім';
            array_push($responses,$response);
        }
        if(!$request->user_email ){
            $response = 'Поле E-Mail не може лишатися порожнім';
            array_push($responses,$response);
        }
        if(strlen($request->user_password)<8){
            $response = 'Ваш пароль має буди довший 8 символів';
            array_push($responses, $response);
        }

        return response()->json([
            "status"=>false,
            "message"=>$responses
        ],404);

    }

    public function login(Request $request){
        $responses = [];

        $user = User::where('user_login',$request->user_login)->first();

        if(!$request->user_login || !$request->user_password){
            if(!$request->user_login ){
                $response = 'Поле Логін не може лишатися порожнім';
                array_push($responses,$response);
            }
            if(!$request->user_password){
                $response = 'Поле Пароль не може лишатися порожнім';
                array_push($responses,$response);
            }
            return response()->json([
                "status"=>false,
                "message"=>$responses
            ],404);
        }
        if((!$user||(!Hash::check($request->user_password,$user->user_password)))){
            if(!$user){
                return response()->json([
                    'status'=>false,
                    'message'=>'Невірний логін'
                ]);
            }
            if(!$user||(!Hash::check($request->user_password,$user->user_password))){
                return response()->json([
                    'status'=>false,
                    'message'=>'Невірний пароль'
                ]);
            }
        }

        if(Hash::check($request->user_password,$user->user_password)) {
            $token = Auth::guard('api')->login($user);
            $user->user_token = $token;
            $user->save();
            return response()->json([
                "status"=>true,
                "token"=>$token,
                'user'=>$user
            ]);
        }else{
            return response()->json([
                "status"=>false
            ],401);
        }
    }


    public function logout(Request $request){
        $user = User::where('user_login',$request->user_login)->first();
        if(!$user){
            return response()->json([
                "status"=>false,
                "message"=>'Такого користувача не існує'
            ],401);
        }
        $user->user_token = '';
        $user->save();

        return response()->json([
            "status"=>true,
            "message"=>"User log out"
        ],200);
    }

    public function deleteUser($id){
        $delete_user = User::find($id);


        if(is_null($delete_user)){
            return response()->json([
                "status"=>false
            ],400);
        }
        $delete_user->delete();
        return response()->json([
            "status"=>true,
            "message"=>"User was deleted"
        ],200);

    }

    public function getUsers(){
        $users = User::all();

        return response()->json($users);
    }
    public function getUserById(string $user_id){
        $user = User::find($user_id);

        if(!$user){
            return response()->json([
                "status"=>"false",
                "message"=>"User not found"
            ],404);
        }
        return response()->json($user);
    }

}
