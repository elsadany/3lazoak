<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model {

    use HasFactory;

    protected $table = 'banners';
    protected $guarded = ['id'];
    public $timestamps = false;
    protected $hidden=['image'];
    protected $appends = ['imagePath'];
    protected $with=['city'];
            function getImagePathAttribute() {
        if ($this->image != '') {
            if (strpos($this->image, "http") !== false)
                return $this->image;
            else if (strstr($this->image, 'uploads'))
                return url($this->image);
        }
    }
    function city(){
        return $this->belongsTo(City::class,'city_id');
    }

}
