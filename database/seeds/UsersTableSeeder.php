<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = factory(User::class)->times(50)->make();
//        User::insert($users->toArray());
        User::insert($users->makeVisible(['password', 'remember_token'])->toArray());//makeVisible 方法临时显示 User 模型里指定的隐藏属性 $hidden

        //更新第一个账户，方便登录
        $user = User::find(1);
        $user->name = 'songdexin';
        $user->email = '2523765999@qq.com';
        $user->password = bcrypt('123123');
        $user->is_admin = true;
//        $user->activated = true;
        $user->save();
    }
}
