<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $appends=['id_imagePath','carIdImagePath'];
    use HasFactory;
    protected $table='delivery';
    public $timestamps=false;
     protected $primaryKey ='user_id';
    function getIdImagePathAttribute(){
        if($this->id_image!='')
            url($this->image_id);
        return '';
    }
    function getCarIdImagePathAttribute(){
        if($this->car_id_image!='')
            url($this->image_id);
        return '';
    }
}
