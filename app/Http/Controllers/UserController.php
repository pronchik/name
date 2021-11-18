<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return User::find($id);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $fields = $request->validate([
            'name' => 'required|string',
            'password' => 'string|confirmed'
        ]);

        $user->name = $fields['name'];
        if($request->password){
            $user->password = bcrypt($fields['password']);
        }
        $user->save();

        $response = [
            'user' => $user,
        ];

        return response($response,201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function myData(){
        return Auth::user();
    }

    public function balance(Request $request){
        $fields = $request->validate([
            'amount' => 'required|int',
        ]);

        $user = Auth::user();
        $user->balance += $fields['amount'];
        $user->save();
        $response = [
            'balance' => $user->balance
        ];
        return response($response,201);
    }
}
