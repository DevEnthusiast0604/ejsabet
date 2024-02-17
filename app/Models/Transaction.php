<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    // use EncryptedAttribute;
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        // 'is_audited' => 'boolean',
    ];

    protected $appends = [
        'is_authorizable',
        'is_editable',
        'must_be_approved_by_super_admin',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function account(){
        return $this->belongsTo(Account::class, 'from');
    }

    public function target(){
        return $this->belongsTo(Account::class, 'to');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    // public function getIsAuditedAttribute() {
    //     $audit = $this->company->audits()->orderBy('date', 'desc')->first();
    //     if (!$audit) return false;
    //     return Carbon::parse($audit->date)->greaterThanOrEqualTo(Carbon::parse($this->timestamp));
    // }

    public function getIsAuthorizableAttribute() {
        $user = User::find($this->user_id);
        return $user && $user->hasRole('sub_admin') && !$this->authorized_by_id;
    }

    public function getIsEditableAttribute() {
        if ($this->is_audited) return false;
        if (!auth()->user()->hasRole('super_admin') && $this->is_approved_by_super_admin) return false;
        $user = User::find($this->user_id);
        if ($user && $user->hasRole('auditor')) return false;
        if ($user && $user->hasRole('sub_admin') && $this->user_id == auth()->id()) return !$this->authorized_by_id;
        $category = Category::find($this->category_id);
        if ($category && $category->status === 'disabled') return false;
        return true;
    }

    public function getMustBeApprovedBySuperAdminAttribute() {
        return $this->category
             && $this->category->must_be_approved_by_super_admin
             && $this->category->must_be_approved_from
             && !$this->is_approved_by_super_admin
             && $this->created_at->greaterThanOrEqualTo(Carbon::parse($this->category->must_be_approved_from));
    }
}
