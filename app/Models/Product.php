<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'manufacturer_id',
        'name',
        'code',
        'description',
        'amount',
        'garancy',
        'in_stock'
    ];
    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function category()
    {
        return $this->belongsTo(Categories::class);
    }

    public function setInStockAttribute($value)
    {
        $this->attributes['in_stock'] = ($value == 'есть в наличие');
    }

    public function setGarancyAttribute($value)
    {
        $this->attributes['garancy'] = is_int($value) ? $value : 0;
    }
}
