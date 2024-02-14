<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $guarded = [];

    public function expenses(){
        return $this->hasMany(Transaction::class, 'from');
    }

    public function incomings(){
        return $this->hasMany(Transaction::class, 'to');
    }

    public function company(){
        return $this->belongsTo(Company::class);
    }
}
