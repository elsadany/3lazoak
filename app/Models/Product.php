<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model {

    use HasFactory;

    protected $table = 'products';
    protected $guarded = ['id'];
    public $timestamps = false;
    protected $hidden=['image'];
    protected $with=['family'];
    protected $appends = ['imagePath','category'];

    function getImagePathAttribute() {
        if ($this->image != '') {
            if (strpos($this->image, "http") !== false)
                return $this->image;
            else if (strstr($this->image, 'uploads'))
                return url($this->image);
        }
    }
    function getCategoryAttribute(){
        $category= Category::find($this->category_id);
        if(is_object($category))
            return $category->name;
        return '';
    }
    function family(){
        return $this->belongsTo(User::class,'user_id');
    }

}
