<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;//
use Illuminate\Support\Facades\Auth;
class UsersController extends Controller
{
	public function create()
	{
		return view('users.create');
	}

	public function show(User $user)
	{
		return view('users.show',compact('user'));
	}

	public function store(Request $request)
    {
        $this->validate($request,[
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:255',//这 里是针对于数据表 users 做验证
            'password' => 'required|confirmed|min:6'
        ]);
        $user = User::create([ //Eloquent 模型提供的 create 方法
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        Auth::login($user);
//        session()->flash('seccess', '欢迎, 您将在这里开启一段新的旅程。');//success 拼写错误，造成没显示出来。
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('users.show',[$user]);//写法注意
    }

}
