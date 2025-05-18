<?php

namespace Modules\Core\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BusinessManager\Models\Business;

// use Modules\Core\Database\Factories\BusinessRoleUserFactory;

class BusinessRoleUser extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function businessRole()
    {
        return $this->belongsTo(BusinessRole::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
