<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($flag)
    {
        //if flog 1 show active users
        $query = User::select('email', 'name');
        if ($flag == 1) {
            $query->where('status', 1);
        } elseif ($flag == 0) {
            // $query->where('status',0);
        } else {
            return response()->json([
                'message' => 'invalid parameter pass, it can be either 1 or 0',
                'status' => 0
            ], 400);
        }
        $users = $query->get();
        // p($users);
        if (count($users) > 0) {
            // user exists
            $response = [
                'message' => count($users) . 'users found',
                'status' => 1,
                'data' => $users
            ];
        } else {
            $response = [
                'message' => count($users) . 'users found',
                'status' => 0,
            ];
        }
        return response()->json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'email' => ['required', 'email',  'unique:users,email'],
            'password' => ['required', 'min:5', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        } else {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->pasword)
            ];
            DB::beginTransaction(); {
                try {

                    $user = user::create($data);
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $user = null;
                }
                if ($user != null) {
                    //okay
                    return response()->json([
                        'message' => 'User Register Successfully'
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'User Register Unuccessfully'
                    ], 500);
                }
            }

            // p($request->all());

            // $request->validate([
            //     'name' => ['required'],
            //     'email' => ['required','email'],
            //     'password' => ['required','min:5'],

            // ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            $response = [
                'message' => 'No such resource',
                'status' => 0,
            ];
        } else {
            $response = [
                'message' => 'user found',
                'status' => 1,
                'data' => $user
            ];
        }
        return response()->json($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        $user = User::find($id);
        if (is_null($user)) {
            return response()->json(
                [
                    'status' => 0,
                    'message' => "User not exist"

                ],
                404
            );
        } else {
            DB::beginTransaction();
            try {
                $user->name = $request['name'];
                $user->email = $request['email'];
                $user->contact = $request['contact'];
                $user->pincode = $request['pincode'];
                $user->address = $request['address'];
                $user->save();
                DB::commit();
            } catch (\Exception $err) {
                DB::rollBack();
                $user = null;
            }
            if (is_null($user)) {
                return response()->json(
                    [
                        'status' => 0,
                        'message' => "internel serve error",
                        'error_msg' => $err->getMessage(),

                    ],
                    500
                );
            } else {
                return response()->json(
                    [
                        'status' => 1,
                        'message' => "User data updated successfully"

                    ],
                    200
                );
            }
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            $response = [
                'message' => "User not exist",
                'status' => 0
            ];
            $responseCode = 404;
        } else {
            DB::beginTransaction();
            try {
                $user->delete();
                DB::commit();
                $response = [
                    'message' => "User Delete Successfully",
                    'status' => 1
                ];
                $responseCode = 200;
            } catch (\Exception $err) {
                DB::rollBack();
                $response = [
                    'message' => "Internel serve error",
                    'status' => 0
                ];
                $responseCode = 500;
            }
        }
        return response()->json($response, $responseCode);
    }
    public function ChangePassword(Request $request, $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return response()->json(
                [
                    'message' => "User not exist",
                    'status' => 0
                ],
                404
            );
        } else {

            if ($user->password == $request['old_password']) {
                if ($request['new_password'] == $request['confirm_password']) {
                    DB::beginTransaction();
                    try {

$user->password=$request['new_password'];
$user->save();
DB::commit();
                    }catch(\Exception $err){
                        $user = null;
                        DB::rollBack();
                    }
                    if (is_null($user)) {
                        return response()->json(
                            [
                                'status' => 0,
                                'message' => "internel serve error",
                                'error_msg' => $err->getMessage(),

                            ],
                            500
                        );}else{
                            return response()->json([
                                'status' =>1,
                                'message' => "password update successfully"
                            ],200);
                        }

                } else {
                    return response()->json(
                        [
                            'message' => "Old password and confirm password Not match",
                            'status' => 0
                        ],
                        404
                    );
                }
            } else {
                return response()->json(
                    [
                        'message' => "Old password Not match",
                        'status' => 0
                    ],
                    404
                );
            }
        }
    }
}
