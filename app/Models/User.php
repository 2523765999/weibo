<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
/**
 * Class User
 * @package App\Models
 */
class User extends Authenticatable
{
    use Notifiable;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->activation_token = str_random(30);
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * @param string $size
     * @return string
     */
    public function gravatar($size='100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public  function statuses()
    {
        return $this->hasMany(Status::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function feed()
    {
//        return $this->statuses()
//            ->orderBy('created_at', 'desc');
        $user_ids = Auth::user()->followings->pluck('id')->toArray();
        array_push($user_ids, Auth::user()->id);
        return Status::whereIn('user_id', $user_ids)
            ->with('user')
            ->orderBy('created_at', 'desc');
//        通过 followings 方法取出所有关注用户的信息，再借助 pluck 方法将 id 进行分离并赋值给 user_ids；
//        将当前用户的 id 加入到 user_ids 数组中；
//        使用 Laravel 提供的 查询构造器 whereIn 方法取出所有用户的微博动态并进行倒序排序；
//        我们使用了 Eloquent 关联的 预加载 with 方法，预加载避免了 N+1 查找的问题，大大提高了查询效率。N+1 问题 的例子可以阅读此文档 Eloquent 模型关系预加载 。
        ## 我们在 User 模型里定义了关联方法 followings()，关联关系定义好后，我们就可以通过访问 followings 属性直接获取到关注用户的 集合
        ## $user->followings == $user->followings()->get() // 等于 true
    }
    //获取粉丝关系列表
    public function followers()
    {
        return $this->belongsToMany(User::class,'followers','user_id', 'follower_id');
        //第三个参数：模型外键名，  第四个参数：要合并的模型外键名
    }
    //获取用户关注人列表
    public function followings()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }
    //加关注
    public function follow($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->sync($user_ids, false);
        # 我们并没有给 sync 和 detach 指定传递参数为用户的 id，这两个方法会自动获取数组中的 id。
    }
    //取消关注
    public function unfollow($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
        # 我们并没有给 sync 和 detach 指定传递参数为用户的 id，这两个方法会自动获取数组中的 id。
    }

//    public function isFollowing($user_id)
//    {
//        $this->followings()->contains($user_id);
//    }
    public function isFollowing($user_id)
    {
        return $this->followings->contains($user_id);
    }

}
