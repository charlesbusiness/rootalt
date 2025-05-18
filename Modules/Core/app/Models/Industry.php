<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Core\Database\Factories\IndustryFactory;

class Industry extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'description'];

   /**
    * These are just temporal data.
    */
    protected $industries = [
      ['name' => 'mining', 'description' => 'Solid minirals mining'],
      ['name' => 'commerce', 'description' => 'Trade and investment'],
      ['name' => 'IT', 'description' => 'Information Technology'],
    ];

    public function getIndustries(){
      return $this->industries;
    }
}
