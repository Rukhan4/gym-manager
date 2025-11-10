<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = ['name', 'email', 'subscription_id'];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
