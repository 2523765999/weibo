<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
class SessionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest',[
            'only' => ['create'],
        ]);
    }

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
//        dd($credentials);
        if (Auth::attempt($credentials, $request->has('remember'))){
            if (Auth::user()->activated) {
                $fallback = route('users.show', Auth::user());
                session()->flash('success','欢迎回来');
//            return redirect()->route('users.show', [Auth::user()]);//获取当前登录用户信息
                return redirect()->intended($fallback);//获取当前登录用户信息
            } else {
                Auth::logout();
                session()->flash('waring', '你的账号未激活，请检查邮箱中的注册邮件进行激活。');
                return redirect('/');
            }

        } else {
            session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
            return redirect()->back()->withInput();//失败后，withInput能将填写信息返回到old内
        }
    }

    public function destory()
    {
        Auth::logout();
        session()->flash('success', '退出成功');
        return redirect('login');
    }
}
