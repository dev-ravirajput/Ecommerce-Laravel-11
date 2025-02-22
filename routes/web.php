<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Http\Middleware\Authadmin;



Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/shop',[ShopController::class, 'index'])->name('shop');
Route::get('/shop/{product_slug}',[ShopController::class, 'product_details'])->name('product.details');

Route::get('/cart',[CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add',[CartController::class, 'add_to_cart'])->name('cart.add');
Route::put('/cart/increase-quantity/{rowId}',[CartController::class, 'increase_cart_quantity'])->name('cart.increase.qty');
Route::put('/cart/descrease-quantity/{rowId}',[CartController::class, 'decrease_cart_quantity'])->name('cart.decrease.qty');
Route::delete('/cart/remove/{rowId}',[CartController::class, 'remove_item'])->name('cart.item.remove');
Route::delete('/cart/clear',[CartController::class, 'empty_cart'])->name('cart.empty');

Route::post('wishlist/add', [WishlistController::class, 'add_to_wishlist'])->name('add.to.wishlist');
Route::get('wishlist', [WishlistController::class, 'index'])->name('go.to.wishlist');
Route::delete('wishlist/{id}/remove', [WishlistController::class, 'remove_to_wishlist'])->name('remove.to.wishlist');
Route::delete('wishlist/clear', [WishlistController::class, 'empty_wishlist'])->name('empty.wishlist');
Route::post('wishlist/{id}/move-to-cart', [WishlistController::class, 'move_to_cart'])->name('wishlist.move.to.cart');

Route::post('cart/apply-coupon', [CartController::class, 'apply_coupon_code'])->name('apply.coupon');
Route::delete('cart/remove-coupon', [CartController::class, 'remove_coupon_code'])->name('remove.coupon');


Route::middleware(['auth'])->group(function(){
    Route::get('/account-dashboard',[UserController::class, 'index'])->name('user.index');
});

Route::middleware(['auth', Authadmin::class])->group(function(){
    Route::get('/admin',[AdminController::class, 'index'])->name('admin.index');
    Route::get('admin/brands',[AdminController::class, 'brands'])->name('admin.brands');
    Route::get('admin/brand/add',[AdminController::class, 'addBrand'])->name('admin.brand.add');
    Route::put('admin/brand/store',[AdminController::class, 'brand_store'])->name('admin.brand.store');
    Route::get('admin/brand/edit/{id}',[AdminController::class, 'brand_edit'])->name('admin.brand.edit');
    Route::put('admin/brand/update',[AdminController::class, 'brand_update'])->name('admin.brand.update');
    Route::delete('admin/brand/update/{id}',[AdminController::class, 'brand_delete'])->name('admin.brand.delete');

    Route::get('admin/categories',[AdminController::class, 'categories'])->name('admin.categories');
    Route::get('admin/category/add',[AdminController::class, 'addCategory'])->name('admin.category.add');
    Route::post('admin/category/store',[AdminController::class, 'category_store'])->name('admin.category.store');
    Route::get('admin/category/edit/{id}',[AdminController::class, 'category_edit'])->name('admin.category.edit');
    Route::put('admin/category/update',[AdminController::class, 'category_update'])->name('admin.category.update');
    Route::delete('admin/category/{id}/delete',[AdminController::class, 'category_delete'])->name('admin.category.delete');

    Route::get('admin/products',[AdminController::class, 'products'])->name('admin.products');
    Route::get('admin/product/add',[AdminController::class, 'addProduct'])->name('admin.product.add');
    Route::post('admin/product/store',[AdminController::class, 'product_store'])->name('admin.product.store');
    Route::get('admin/product/{id}/edit',[AdminController::class, 'editProduct'])->name('admin.product.edit');
    Route::put('admin/product/update',[AdminController::class, 'product_update'])->name('admin.product.update');
    Route::delete('admin/product/{id}/delete',[AdminController::class, 'product_delete'])->name('admin.product.delete');

    Route::get('admin/coupons',[AdminController::class, 'coupons'])->name('admin.coupons');
    Route::get('admin/coupons/create',[AdminController::class, 'add_coupons'])->name('admin.coupons.add');
    Route::post('admin/coupons/store',[AdminController::class, 'store_coupons'])->name('admin.coupons.store');
    Route::get('admin/coupons/{id}/edit',[AdminController::class, 'edit_coupons'])->name('admin.coupons.edit');
    Route::post('admin/coupons/update',[AdminController::class, 'update_coupons'])->name('admin.coupons.update');
    Route::delete('admin/coupon/{id}/delete',[AdminController::class, 'delete_coupons'])->name('admin.coupons.delete');
});
