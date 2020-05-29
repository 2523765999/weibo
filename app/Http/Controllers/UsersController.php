<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;//
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',[
            'except' => ['create','show','store', 'index', 'confirmEmail'],
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
        $statuses = $user->statuses()
                            ->orderBy('created_at')
                            ->paginate(10);
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
        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');
//        Auth::login($user);
//        session()->flash('seccess', '欢迎, 您将在这里开启一段新的旅程。');//success 拼写错误，造成没显示出来。
//        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
//        return redirect()->route('users.show', [$user]);//写法注意
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

    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activation_token = null;
        $user->activated = true;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }

    public function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'aufree@yousails.com';
        $name = 'Aufree';
        $to = $user->email;
        $subject = "感谢注册 Sample 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
            $message->from($from, $name)->to($to)->subject($subject);
        });
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
