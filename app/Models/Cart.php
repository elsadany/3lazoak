<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model {

    use HasFactory;

    protected $table = 'cart';
    protected $guarded = ['id'];
    public $timestamps = false;
    protected $with = ['family','items'];

    function family() {
        return $this->belongsTo(User::class, 'family_id');
    }
    function items(){
        return $this->hasMany(CartItems::class,'cart_id');
    }

}
