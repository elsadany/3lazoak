<?php

namespace App\Http\Controllers\apis;

use Elsayednofal\BackendLanguages\Models\Languages;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\BrandsCategory;
use App\Models\Brand;
use App\Models\CategoryOption;
use App\Http\Resources\OptionsResource;

class CategoriesApi extends Controller {

    function index(Request $request) {
        $categories = \App\Models\Category::with(['products']);
        if($request->family_id!=''){
            $categories_id= \App\Models\Product::where('user_id',$request->family_id)->pluck('category_id')->toArray();
            $categories=$categories->whereIn('id',$categories_id);
        }
        $categories=$categories->get();
        return response()->json(['status' => 200, 'data' => $categories->toArray()]);
    }

    function sub(Request $request) {
        $validator = \Validator::make($request->all(), [
                    'category_id' => 'required|exists:categories,id',
        ]);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $categories = \App\Models\Category::where('parent_id', $request->category_id)->where('active',1)->get();

        return response()->json(['status' => 200, 'data' => CategoryResource::collection($categories)]);
    }

    function show(Request $request) {
        $validator = \Validator::make($request->all(), [
                    'category_id' => 'exists:categories,id',
        ]);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $brandscat = BrandsCategory::orderBy('id', 'desc');
        if ($request->category_id != '') {
            $sub_category = collect($this->getSubCategory($request->category_id))->pluck('id');
            $ids = array_merge($sub_category->toArray(), [(int) $request->category_id]);

            $brandscat = $brandscat->whereIn('category_id', $ids);
        }
        $ids = $brandscat->pluck('brand_id')->toArray();
        $brands = Brand::whereIn('id', $ids)->get();
        $brandsdata = \App\Http\Resources\BrandsResource::collection($brands);
        $options = CategoryOption::where('in_filter', 1)->where('type', '!=', 4);
        if ($request->category_id != '') 
        $options = $options->where('category_id', $request->category_id);
        $options = $options->get();
        $optionsdata = OptionsResource::collection($options);
        return response()->json(['status' => 200, 'data' => $optionsdata, 'brands' => $brandsdata]);
    }
   
    function getCategory(Request $request) {
        $validator = \Validator::make($request->all(), [
                    'category_id' => 'required|exists:categories,id',
        ]);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $brandscat = BrandsCategory::orderBy('id', 'desc');
        $category= \App\Models\Category::find($request->category_id);
        $sub_category = collect($this->getSubCategory($request->category_id))->pluck('id');
        $ids = array_merge($sub_category->toArray(), [(int) $request->category_id]);

        $brandscat = $brandscat->whereIn('category_id', $ids);
        $brandsids = $brandscat->pluck('brand_id')->toArray();
        $brands = Brand::whereIn('id', $brandsids)->get();
        $brandsdata = \App\Http\Resources\BrandsResource::collection($brands);
        $options = CategoryOption::where('in_filter', 1)->where('type', '!=', 4);
        $options = $options->whereIn('category_id', $ids);
        $options = $options->get();
        $optionsdata = OptionsResource::collection($options);
        return response()->json(['status' => 200, 'data' => $optionsdata, 'brands' => $brandsdata,'category'=>new CategoryResource($category)]);
    }

    function getSubCategory($parent = 0) {
        $categories = \App\Models\Category::where('parent_id', $parent)->get();
        $result = [];
        foreach ($categories as $cat) {
            $result[] = $cat;
            $result = array_merge($result, $this->getSubCategory($cat->id));
        }
        return $result;
    }

}
