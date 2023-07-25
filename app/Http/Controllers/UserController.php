<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;


class UserController extends Controller
{
    //

    public function create(){
        return view('users.register');

    }

    public function store(Request $request){
        $formFields = $request->validate([
            'name'=>['required', 'min:3'],
            'email'=>['required', 'email', Rule::unique('users','email')],
            'password'=>'required|confirmed|min:8'


        ]);

        $formFields['password']= bcrypt($formFields['password']);

        $user= User::create($formFields);

        auth()->login($user);

        return redirect('/')->with('message','User creation successfully!');
    }

    public function logout(Request $request) {

        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('message','Logged out successfully!');
    }

    public function login(){
        return view('users.login');
    }

    public function authenticate(Request $request) {
        $formFields = $request->validate([
            'email'=>['required', 'email'],
            'password'=>'required'


        ]);

        if(auth()->attempt($formFields)){
            $request->session()->regenerate();
            return redirect('/')->with('message','Logged in successfully!');
        }
        return back()->withErrors(['email'=>'Invalid Credentials'])->onlyInput('email');
    }

    public function manage(User $user){
        
        return view('users.manage',[
            'users' => $user
        ]);
    }
    

    public function update(Request $request, User $user_id){

        if($user_id->users != auth()->id()){
            abort(403,'Unauthorized Action');
        }

        $formFields = $request->validate([
            'name'=>['required', 'min:3'],
            'email'=>['required', 'email', Rule::unique('users','email')],
            'password'=>'required|confirmed|min:8'


        ]);

        $formFields['password']= bcrypt($formFields['password']);

        $user_id->update($formFields);

        auth()->login($user_id);


        return back()->with('message', 'User updated successfully!');

    }

}
