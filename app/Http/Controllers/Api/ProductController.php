<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use GuzzleHttp\Psr7\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function getProducts(){
        $products = Product::all();

        return response()->json($products);
    }

    public function addProduct(Request $request){
        $request_data = $request->only(["product_name","product_price","product_weight","product_compound","category_id"]);

        $validator = Validator::make($request_data,[
            "product_name"=>["required","string"],
            "product_price"=>["required","integer"],
            "product_weight"=>["required",'regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
            "product_compound"=>["required","string"],
            "category_id"=>["required","integer"],
        ]);

        if($validator->fails()){
            return response()->json([
                "status"=>"false",
                "errors"=>$validator->messages()
            ],422 );
        }

        $product = Product::create([
            "product_name"=>$request->product_name,
            "product_price"=>$request->product_price,
            "product_weight"=>$request->product_weight,
            "product_compound"=>$request->product_compound,
        ]);

        return response()->json([
            "status"=>"true",
            "product"=>$product
        ]);
    }
}
