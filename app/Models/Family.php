<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    use HasFactory;
    protected $table='famillies';
    protected $primaryKey ='user_id';
    public $timestamps=false;
    protected $with=['rating'];
            function rating(){
         return $this->hasMany(Rating::class,'family_id','user_id');
    }
}
