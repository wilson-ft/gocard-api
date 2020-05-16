<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Category extends Model
{
    protected $fillable = [
        'name'
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

    public function userCategories()
    {
        return $this->hasMany('App\UserCategory');
    }
}
