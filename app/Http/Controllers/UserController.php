<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Input;
use Hash;
use Validator;

class UserController extends Controller
{
    /**
     * Show the edit user modal
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showEditUser()
    {
        $data = [
            'user' => Auth::user(),
        ];

        return view('ManageUser.Modals.EditUser', $data);
    }

    /**
     * Updates the current user
     *
     * @param Request $request
     * @return mixed
     */
    public function postEditUser(Request $request)
    {
        $rules = [
            'email'        => ['required', 'email', 'exists:users,email,account_id,'.Auth::user()->account_id],
            'new_password' => ['min:5', 'confirmed', 'required_with:password'],
            'password'     => 'passcheck',
            'first_name'   => ['required'],
            'last_name'    => ['required'],
        ];

        $messages = [
            'email.email'         => 'Please enter a valid E-mail address.',
            'email.required'      => 'E-mail address is required.',
            'password.passcheck'  => 'This password is incorrect.',
            'email.exists'        => 'This E-mail has is already in use.',
            'first_name.required' => 'Please enter your first name.',
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            return response()->json([
                'status'   => 'error',
                'messages' => $validation->messages()->toArray(),
            ]);
        }

        $user = Auth::user();

        if ($request->get('password')) {
            $user->password = Hash::make(Input::get('new_password'));
        }

        $user->first_name = $request->get('first_name');
        $user->last_name  = $request->get('last_name');

        $user->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Successfully Edited User',
        ]);
    }
}
