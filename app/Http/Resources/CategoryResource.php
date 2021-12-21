<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return[
            'id'=> $this->id,
            'name'=> $this->lang($request->lang_id)->name,
            'image'=> $this->imagepath,
            'has_children'=> $this->checkchilderen($this),
                'children'=> $this->getChildren($this, $request)
        ];
    }
    function checkchilderen($category){
      
        if($category->children()->count()>0)
            return 1;
        return 0;
    }
    function getChildren($category,$request){
        if($category->children()->count()>0)
            return self::collection ($category->children()->get());
    }
}
