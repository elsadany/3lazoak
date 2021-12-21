<?php

namespace App\Http\Controllers\apis;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductsResource;
use Elsayednofal\BackendLanguages\Models\Languages;

class ProductsApi extends Controller {

    function index(Request $request) {
        $validator = \Validator::make($request->all(), [
                    'category_id' => 'exists:categories,id',
        ]);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $products =  Product::where('active',1);
        $products = $this->search($products, $request);
        $max_price = $products->max('price_after_discount');
        $min_price = $products->min('price_after_discount');

        $products = $products->orderBy('id', 'desc');
        $products = $products->get();
        return response()->json(['status' => 200, 'data' => ProductsResource::collection($products),'max_price' => (string)$max_price, 'min_price' => (string)$min_price]);
    }
    
  

    function getSubCategory($parent = 0) {
        $categories = Category::where('parent_id', $parent)->get();
        $result = [];
        foreach ($categories as $cat) {
            $result[] = $cat;
            $result = array_merge($result, $this->getSubCategory($cat->id));
        }
        return $result;
    }

    function search($products, $request) {
        if ($request->category_id != '') {
            $sub_category = collect($this->getSubCategory($request->category_id))->pluck('id');
            $ids = array_merge($sub_category->toArray(), [(int) $request->category_id]);
            $products = $products->where('active',1)->whereIn('category_id', $ids);
        }
        if($request->search!=''){
            $pro_ids= \App\Models\ProductLang::where('name','like','%'.$request->search.'%')->orWhere('description','like','%'.$request->search.'%')->pluck('product_id')->toArray();
            $products=$products->where('active',1)->whereIn('id',$pro_ids);
        }

        if ($request->brand_id!='')
            $products = $products->where('active',1)->where('brand_id', $request->brand_id);

        if ($request->value_id != '' && count($request->value_id) > 0) {
            $ids = \App\Models\ProductOption::whereIn('value_id', $request->value_id)->pluck('product_id')->toArray();
            $products = $products->where('active',1)->whereIn('id', $ids);
        }

        if ($request->tag_id != '') {
            $pro_ids = \App\Models\TagsProducts::where('tag_id', $request->tag_id)->pluck('product_id')->toArray();
            $products = $products->where('active',1)->whereIn('id', $pro_ids);
        }

        if ($request->price_from > 0)
            $products = $products->where('price', '>=', $request->price_from);

        if ($request->price_to > 0)
            $products = $products->where('price', '<=', $request->price_to);

        return $products;
    }

    function show(Request $request) {
        $validator = \Validator::make($request->all(), [
                    'product_id' => 'required|exists:products,id',
        ]);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $product = Product::find($request->product_id);

        $related = Product::where('id', '!=', $product->id)->where('active',1)->where('stock', '>', 0)->where('category_id', $product->category_id)->orderBy('id', 'desc')->limit(6)->get();
        return response()->json(['status' => 200, 'data' => new ProductsResource($product), 'related' => ProductsResource::collection($related)]);
    }

    function getids($category) {
        $arr = [];
        array_push($arr, $category->children()->pluck('id')->toArray());
        foreach ($category->children as $one) {
            if ($one->children()->count() > 0) {
                array_push($arr, $this->getids($one));
            }
        }
        return $arr;
    }

}
