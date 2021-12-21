<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model {
    protected $with=['order'];
            function order(){
        return $this->belongsTo(Order::class,'order_id');
    }
    

}
