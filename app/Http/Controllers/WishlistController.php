<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class WishlistController extends Controller
{

    public function index(Request $request)
    {
        //dd($request->all());
       $items =Cart::instance('wishlist')->content();
       return view('wishlist', compact('items'));
    }

    public function add_to_wishlist(Request $request)
    {
        //dd($request->all());
       Cart::instance('wishlist')->add($request->id,$request->name, $request->quantity, $request->price)->associate('App\Models\Product');
       return redirect()->back();
    }

    public function remove_to_wishlist($rowid)
    {
        //dd($request->all());
       Cart::instance('wishlist')->remove($rowid);
       return redirect()->back();
    }

    public function empty_wishlist()
    {
        //dd($request->all());
       Cart::instance('wishlist')->destroy();
       return redirect()->back();
    }
}
