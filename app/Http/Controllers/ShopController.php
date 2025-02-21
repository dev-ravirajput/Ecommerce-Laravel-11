<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;

class ShopController extends Controller
{
    public function index(Request $request)
{ 
    $size = $request->query('size', 12);
    $order = $request->query('order', -1);
    $f_brands = $request->query('brands'); // Selected brands from URL
    $f_categories = $request->query('categories'); // Selected categories from URL
    $min_price = $request->query('min', 1);
    $max_price = $request->query('max', 10000);

    // Sorting logic
    switch ($order) {
        case 1:
            $o_column = 'created_at';
            $o_order = 'DESC';
            break;
        case 2:
            $o_column = 'created_at';
            $o_order = 'ASC';
            break;
        case 3:
            $o_column = 'sale_price';
            $o_order = 'ASC';
            break;
        case 4:
            $o_column = 'sale_price';
            $o_order = 'DESC';
            break;
        default:
            $o_column = 'id';
            $o_order = 'DESC';
            break;
    }

    // Fetch all categories
    $categories = Category::orderBy('name', 'ASC')->get();

    // Initialize Product Query
    $query = Product::whereBetween('sale_price', [$min_price, $max_price])
                    ->orderBy($o_column, $o_order);

    // Apply Category Filter
    if (!empty($f_categories)) {
        $categoryArray = explode(',', $f_categories);
        $query->whereIn('category_id', $categoryArray);
    }

    // Apply Brand Filter
    if (!empty($f_brands)) {
        $brandArray = explode(',', $f_brands);
        $query->whereIn('brand_id', $brandArray);
    }

    // Fetch Products
    $products = $query->paginate($size);

    // Fetch Brands Based on Selected Categories & Price Range
    $brandQuery = Brand::whereHas('products', function ($q) use ($min_price, $max_price, $f_categories) {
        $q->whereBetween('sale_price', [$min_price, $max_price]);

        if (!empty($f_categories)) {
            $categoryArray = explode(',', $f_categories);
            $q->whereIn('category_id', $categoryArray);
        }
    });

    $brands = $brandQuery->orderBy('name', 'ASC')->get();

    return view('shop', compact('products', 'size', 'order', 'brands', 'f_brands', 'categories', 'f_categories', 'min_price', 'max_price'));
}
   

    public function product_details($product_slug)
    {
        $product = Product::where('slug',$product_slug)->first();
        $products = Product::where('slug', '!=', $product_slug)->get()->take(8);
        return view('details', compact('product', 'products'));
    }
}
