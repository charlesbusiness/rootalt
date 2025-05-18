<?php

namespace Modules\Authentication\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Authentication\Database\Factories\EmailVerificationFactory;

class OtpManager extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   */
  protected $guarded = ['id'];

  public function user()
  {
    return $this->hasOne(User::class, 'email', 'email');
  }
}
