<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Personnel extends Model
{
    protected $fillable = ['date', 'activity', 'amount', 'remarks', 'file_link'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
