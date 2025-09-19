<?php

namespace Modules\ProductManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Models\Upload;

// use Modules\ProductManager\Database\Factories\ProductFactory;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];

    public $uploadType = "product";

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function uploads()
    {
        return $this->hasMany(Upload::class, 'entity_id')
            ->where('upload_type', $this->uploadType);
    }
}
