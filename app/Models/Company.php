<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $guarded = [];

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }

    public function expenses() {
        return $this->transactions()->where('type', 'expense');
    }

    public function incomings() {
        return $this->transactions()->where('type', 'incoming');
    }

    public function transfers() {
        return $this->transactions()->where('type', 'transfer');
    }

    public function accounts() {
        return $this->hasMany(Account::class);
    }

    public function users() {
        return $this->hasMany(User::class);
    }

    public function audits() {
        return $this->hasMany(Audit::class);
    }

    public function categories(){
        $users = $this->users->pluck('id');
        return Category::whereIn('user_id', $users)->get();
    }

    public function getBalance($date) {
        $date = Carbon::parse($date);
        $expenses = $this->expenses()->withTrashed()->whereDate('timestamp', '<=', $date)->sum('amount');
        $incoming = $this->incomings()->withTrashed()->whereDate('timestamp', '<=', $date)->sum('amount');

        $current_balance = $incoming - $expenses;

        return $current_balance;
    }

    public function getLatestAuditDate() {
        $latest_audit = $this->audits()->orderBy('date', 'desc')->first();
        if (!$latest_audit) return null;
        return Carbon::parse($latest_audit->date);
    }
}
