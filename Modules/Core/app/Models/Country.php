<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Core\Database\Factories\CountryFactory;

class Country extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];
    
    public function getCountries()
    {
        $path = storage_path('files/countries.txt');
        $countriesArray = convertFileToAssociativeArray($path);
        return $countriesArray;
    }
}
