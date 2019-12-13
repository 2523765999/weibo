<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
class SessionsController extends Controller
{
    public function create()
    {
        return view('sessions.create');
    }

    public function store(Request $request)
    {
        $credentials = $this->validate($request,[
            'email' => 'required|max:255',
            'password' => 'required'
        ]);
        if (Auth::attempt($credentials)){
            session()->flash('success','欢迎回来');
            return redirect('users.show',[Auth::user()]);//获取当前登录用户信息
        } else {
            session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
            return redirect()->back()->withInput();//失败后，withInput能将填写信息返回到old内
        }
    }
}
