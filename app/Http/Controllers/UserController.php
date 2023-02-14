<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('superadmin');
        return view('dashboard.superadmin.user', [
            'users' => User::where('id', '!=', auth()->user()->id)->get()
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize('superadmin');
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username'
        ]);

        $password = '';
        if ($request->password == null) {
            $password = $request->username;
        }
        else {
            $password = $request->password;
        }

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => bcrypt($password),
            'is_reviewer' => true
        ]);

        return redirect()->back()->with('success', 'User Successfully Created');
    }

    public function update(Request $request)
    {
        $this->authorize('superadmin');
        $user = User::find($request->user_id);
        $rules = ([
            'name' => 'required'
        ]);

        if ($user->username != $request->username) {
            $rules['username'] = 'required|unique:users,username';
        }

        $validatedData = $request->validate($rules);
        if ($request->password == null) {
            $validatedData['password'] = $user->password;
        }
        else {
            $validatedData['password'] = bcrypt($request->password);
        }

        User::where('id', $request->user_id)->update($validatedData);

        return redirect()->back()->with('success', 'User Successfully Updated');
    }
}
