<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model {

    use HasFactory;

    protected $table = 'categories';
    protected $guarded = ['id'];
    public $timestamps = false;
    protected $hidden=['image'];
    protected $appends = ['imagePath'];

    function getImagePathAttribute() {
        if ($this->image != '') {
            if (strpos($this->image, "http") !== false)
                return $this->image;
            else if (strstr($this->image, 'uploads'))
                return url($this->image);
        }
    }
    function products(){
        return $this->hasMany(Product::class,'category_id')->orderBy('id','desc')->limit(5);
    }

}
