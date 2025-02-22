<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    public function index()
    {
        $items = Cart::instance('cart')->content();
        return view('cart', compact('items'));
    }

    public function add_to_cart(Request $request)
    {
        $items = Cart::instance('cart')->add(
        $request->id, 
        $request->name, 
        $request->quantity, 
        $request->price
    )->associate('App\Models\Product');
        return redirect()->back();
    }

    public function increase_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty + 1;
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }

    public function decrease_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty - 1;
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }

    public function remove_item($rowId)
    {
        Cart::instance('cart')->remove($rowId);
        return redirect()->back();
    }

    public function empty_cart()
    {
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }

    public function apply_coupon_code(Request $request)
    {
        $coupon_code = $request->coupon_code;
        if(isset($coupon_code)){
            $cartSubtotal = floatval(str_replace(',', '', Cart::instance('cart')->subtotal()));
            $coupon = Coupon::where('code', $coupon_code)
                ->where('expiry_date', '>=', Carbon::today()->toDateString())
                ->where('cart_value', '<=', $cartSubtotal)
                ->first();
            if(!$coupon){
                return redirect()->back()->with('error', 'Invalide Coupon Code!');
            }else{
                Session::put('coupon', [
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'cart_value' => $coupon->cart_value,
                ]);
                $this->calculateDiscount();
                return redirect()->back()->with('success', 'Coupon Successfully Applied!');
            }
        }else{
            return redirect()->back()->with('error', 'Invalide Coupon Code!');
        }
        
    }

    public function calculateDiscount()
    {
        $discount = 0;
        $cartSubtotal = floatval(str_replace(',', '', Cart::instance('cart')->subtotal()));
        if(Session::has('coupon')){
            $discount = Session::get('coupon')['value'];
        }else{
            $discount = ($cartSubtotal * Session::get('coupon')['value'])/100;
        }

        $subtotalAfterDiscount = $cartSubtotal - $discount;
        $taxAfterDiscount = ($subtotalAfterDiscount * config('cart.tax'))/100;
        $totalAfterDiscount = $subtotalAfterDiscount + $taxAfterDiscount;

        Session::put('discount', [
            'discount' => number_format(floatval($discount), 2, '.', ','),
            'subtotal' => number_format(floatval($subtotalAfterDiscount), 2, '.', ','),
            'tax' => number_format(floatval($taxAfterDiscount), 2, '.', ','),
            'total' => number_format(floatval($totalAfterDiscount), 2, '.', ','),
        ]);
    }

    public function remove_coupon_code()
    {
        Session::forget('coupon');
        Session::forget('discount');
        return redirect()->back()->with('success', 'Coupon has been removed!');
    }
}
