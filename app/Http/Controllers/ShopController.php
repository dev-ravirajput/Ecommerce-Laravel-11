<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ShopController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(12);
        return view('shop', compact('products'));
    }

    public function product_details($product_slug)
    {
        $product = Product::where('slug',$product_slug)->first();
        $products = Product::where('slug', '!=', $product_slug)->get()->take(8);
        return view('details', compact('product', 'products'));
    }
}
