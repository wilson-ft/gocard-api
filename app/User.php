<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

use App\Category;
use App\UserCategory;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone_no',
        'balance',
        'ext_account_id',
        'photo'
    ];

    protected $hidden = [
        'api_token',
    ];

    public function getCreatedAtAttribute($date)
    {
        if($date){
            return Carbon::createFromFormat('Y-m-d H:i:s', $date)->setTimezone('GMT+8');
        }
    }

    public function getUpdatedAtAttribute($date)
    {
        if($date){
            return Carbon::createFromFormat('Y-m-d H:i:s', $date)->setTimezone('GMT+8');
        }
    }

    public static function boot()
    {
        parent::boot();

        static::created(function (User $user){
            $categories = Category::get();

            $insertVals = [];
            foreach ($categories as $key => $category) {
                $insertVals[] = [
                    'user_id'           => $user->id,
                    'category_id'       => $category->id,
                    'level'             => 1,
                    'experience'        => 0,
                    'total_experience'  => 0
                ];
            }

            UserCategory::insert($insertVals);
        });
    }

    public function userCategories()
    {
        return $this->hasMany('App\UserCategory');
    }
}
