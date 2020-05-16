<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserPayment extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'event_id',
        'grand_total',
        'status',
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

        static::created(function (UserPayment $userPayment){
            $userCategory   = UserCategory::where([
                                'user_id'       => $userPayment->user_id,
                                'category_id'   => $userPayment->event->category_id
                            ])->first();

            if($userCategory != null){
                $expValue   = $userPayment->event->experience;
                $totalExp   = $userCategory->total_experience;
                $experience = $userCategory->experience;
                $level      = $userCategory->level;

                $expGained  = $expValue + $experience;

                if($expGained > 50){
                    $level      = $level + 1;
                    $expGained  = $expGained - 50;
                }

                $userCategory->level            = $level;
                $userCategory->experience       = $expGained;
                $userCategory->total_experience = $totalExp + $expValue;
                $userCategory->save();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function event()
    {
        return $this->belongsTo('App\Event');
    }
}
