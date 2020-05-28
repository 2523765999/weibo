<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;//
use Illuminate\Support\Facades\Auth;
class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',[
            'except' => ['create','show','store', 'index']
        ]);
        $this->middleware('guest',[
            'only' => ['create']
        ]);
    }

    public function index()
    {
//        $users = User::all();
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
	{
		return view('users.create');
	}

	public function show(User $user)
	{
//	    dd(is_object($user));
//        dd($user);
//	    dd(compact('user'));
        $statuses = $user->statuses()
                            ->orderBy('created_at')
                            ->paginate(10);
//        dd(compact('user','statuses'));
//        dd($user['attributes']);
//		return view('users.show',compact('user'));
		return view('users.show',compact('user','statuses'));
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

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit',compact('user'));
    }

    public function update(User $user,Request $request)
    {
        $this->validate($request,[
            'name' => 'required|max:50',
//            'password' => 'required|confirmed|min:6'
            'password' => 'nullable|confirmed|min:6'
        ]);
        /*$user->update([
            'name' => $request->name,
            'password' => bcrypt($request->password)
        ]);*/
        $this->authorize('update', $user);
        $data = [];
        $data['name'] = $request->name;
//        if ($request->has('password')) {
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);
        session()->flash('success', '更新成功~');

//        return redirect()->route('users.show',$user->id);
        return redirect()->route('users.show',$user);
    }

    public function destroy(User $user)
    {
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();//最后将用户重定向到上一次进行删除操作的页面，即用户列表页。
    }

    public function followings(User $user)
    {
        $users = $user->followings()->paginate(30);
        $title = $user->name . '关注的人';
        return view('users.show_follow', compact('users', 'title'));
    }

    public function followers(User $user)
    {
        $users = $user->followers()->paginate(30);
        $title = $user->name . '的粉丝';
        return view('users.show_follow', compact('users', 'title'));
    }




}
