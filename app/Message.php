<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $guarded=[];

    public function user(){
        return  $this->belongsTo(User::class,'from');
    }

    public function getCreatedAtAttribute($value)
    {
        return date('d-m-Y h:m:s',strtotime($value));
    }
}
