<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GeneralTagsResourse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'=> $this->id,
            'imagepath'=> $this->imagepath,
            'name'=> $this->lang($request->lang_id)->name,
            'products'=> $this->getProducts($this)
        ];
    }
    function getProducts($homepage){
        $ids= \App\Models\TagsProducts::where('tag_id', $homepage->id)->pluck('product_id')->toArray();
        $products= \App\Models\Product::whereIn('id',$ids)->orderBy('id','desc')->get();
        return ProductsResource::collection($products);
    }
}
