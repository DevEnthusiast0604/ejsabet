<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];

    protected $appends = ['name_with_company'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }

    public function from_account(){
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function to_account(){
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function getNameWithCompanyAttribute() {
        $company = $this->user->company ?? null;
        if ($company) {
            return $company->name . ' - ' . $this->name;
        } else {
            return $this->name;
        }
    }
}
