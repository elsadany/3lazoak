<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {

    use HasFactory;

    protected $table = 'orders';
    protected $guarded = ['id'];
    public $timestamps = false;
    protected $with = ['family','items','user','address','delivery','rating'];

    function family() {
        return $this->belongsTo(User::class, 'family_id');
    }
     function delivery() {
        return $this->belongsTo(User::class, 'delivery_id');
    }
    function address() {
        return $this->belongsTo(Address::class, 'address_id');
    }
    function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    function items(){
        return $this->hasMany(OrderItems::class,'order_id');
    }
    function rating(){
        return $this->hasMany(Rating::class,'order_id');
    }
}
