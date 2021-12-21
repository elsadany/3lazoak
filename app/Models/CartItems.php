<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItems extends Model
{
    use HasFactory;
    protected $table='cart_items';
    protected $guarded=['id'];
    public $timestamps=false;
    protected $casts=['product_details'=>'array'];
    protected $with=['product'];
            function product(){
        return $this->belongsTo(Product::class,'product_id');
    }
    
}
