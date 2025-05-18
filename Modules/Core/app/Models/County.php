<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Core\Database\Factories\CountyFactory;

class County extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['county_name'];

    protected $counties = [
        'Bomi',
        'Bong',
        'Gbarpolu',
        'Grand Bassa',
        'Grand Cape Mount',
        'Grand Gedeh',
        'Grand Kru',
        'Lofa',
        'Margibi',
        'Maryland',
        'Montserrado',
        'Nimba',
        'River Gee',
        'Rivercess',
        'Sinoe'
    ];

    public function getCounties()
    {
        return $this->counties;
    }
}
