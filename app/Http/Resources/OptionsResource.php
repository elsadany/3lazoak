<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OptionsResource extends JsonResource
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
            'name'=> $this->lang($request->lang_id)->name,
            'typename'=> $this->typename,
            'type'=> $this->type,
            'values'=>$this->getValues($this,$request)
        ];
    }
    function getValues($option,$request){
        $values= \App\Models\CategoryOptionValue::where('option_id',$option->id)->get();
        $valuesarr=[];
        foreach($values as $key=>$one){
            $valuesarr[$key]['id']=$one->id;
            $valuesarr[$key]['code']=$one->code;
            $valuesarr[$key]['value']=$one->lang($request->lang_id)->value;
            
        }
        return $valuesarr;
    }
}
