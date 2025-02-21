<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Intervention\Image\Laravel\Facades\Image;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    public function brands(Request $request){
    $query = Brand::latest();

    if ($request->has('search')) {
        $query->where('name', 'like', '%' . $request->name . '%');
    }
        $brands = $query->paginate(10);
        return view('admin.brands', compact('brands'));
    }

    public function addBrand(){
        return view('admin.brand-add');
    }

    public function brand_store(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'mimes:png,jpg,jpeg:max:2048',
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->slug);
        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $filename = Carbon::now()->timestamp.'.'.$file_extension;
        $this->GEnerateBrandThumbnail($image, $filename);
        $brand->image = $filename;
        $brand->save();

        return redirect()->route('admin.brands')->with('status', 'Brand Added Successfully!');
    }

    public function brand_edit($id){
        $brand = Brand::find($id);
        return view('admin.brand-edit', compact('brand'));
    }

    public function brand_update(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug'.$request->id,
            'image' => 'mimes:png,jpg,jpeg:max:2048',
        ]);
        $brand = Brand::find($request->brandId);
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->slug);
        if($request->file('image')){
            if(File::exists(public_path('uploads/brands/'.$brand->image)))
            {
                File::delete(public_path('uploads/brands/'.$brand->image));
            }
        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $filename = Carbon::now()->timestamp.'.'.$file_extension;
        $this->GEnerateBrandThumbnail($image, $filename);
        $brand->image = $filename;
        }
        
        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'Brand Updated Successfully!');
    }

    public function brand_delete($id){
        $brand = Brand::find($id);
        if(File::exists(public_path('uploads/brands/'.$brand->image)))
        {
            File::delete(public_path('uploads/brands/'.$brand->image));
        }
        $brand->delete();
        return redirect()->route('admin.brands')->with('status', 'Brand Deleted Successfully!');
    }

    public function GEnerateBrandThumbnail($image, $imagename){
        $destinationPath = public_path('uploads/brands');
        $img = Image::read($image->getPathname());
        $img->cover(124,124,"top");
        $img->resize(124,124,function($constraints){
            $constraints->aspectRation();
        })->save($destinationPath.'/'.$imagename);
    }

    public function categories(Request $request){
    $query = Category::latest();

    if ($request->has('search')) {
        $query->where('name', 'like', '%' . $request->name . '%');
    }

    $categories = $query->paginate(10);
    
    return view('admin.categories', compact('categories'));
   }


   public function addCategory(){
        return view('admin.category-add');
    }

    public function category_store(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'mimes:png,jpg,jpeg:max:2048',
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->slug);
        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $filename = Carbon::now()->timestamp.'.'.$file_extension;
        $this->GEnerateCategoryThumbnail($image, $filename);
        $category->image = $filename;
        $category->save();

        return redirect()->route('admin.categories')->with('status', 'Category Added Successfully!');
    }

    public function category_edit($id){
        $category = Category::find($id);
        return view('admin.category-edit', compact('category'));
    }

    public function category_update(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug'.$request->id,
            'image' => 'mimes:png,jpg,jpeg:max:2048',
        ]);
        $category = Category::find($request->categoryId);
        $category->name = $request->name;
        $category->slug = Str::slug($request->slug);
        if($request->file('image')){
            if(File::exists(public_path('uploads/categories/'.$category->image)))
            {
                File::delete(public_path('uploads/categories/'.$category->image));
            }
        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $filename = Carbon::now()->timestamp.'.'.$file_extension;
        $this->GEnerateCategoryThumbnail($image, $filename);
        $category->image = $filename;
        }
        
        $category->save();
        return redirect()->route('admin.categories')->with('status', 'Category Updated Successfully!');
    }

        public function category_delete($id){
        $category = Category::find($id);
        if(File::exists(public_path('uploads/categories/'.$category->image)))
        {
            File::delete(public_path('uploads/categories/'.$category->image));
        }
        $category->delete();
        return redirect()->route('admin.categories')->with('status', 'Category Deleted Successfully!');
    }

        public function GEnerateCategoryThumbnail($image, $imagename){
        $destinationPath = public_path('uploads/categories');
        $img = Image::read($image->getPathname());
        $img->cover(124,124,"top");
        $img->resize(124,124,function($constraints){
            $constraints->aspectRation();
        })->save($destinationPath.'/'.$imagename);
    }


   public function products(Request $request){
    $query = Product::latest();

    if ($request->has('search')) {
        $query->where('name', 'like', '%' . $request->name . '%');
    }

    $products = $query->paginate(10);

    return view('admin.products', compact('products'));
   }


    public function addProduct()
    {

    $categories = Category::all();
    $brands = Brand::all();
    return view('admin.product-add', compact('categories', 'brands'));

    }

    public function product_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug',
            'category_id' => 'required',
            'brand_id' => 'required',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'images.*' => 'mimes:png,jpg,jpeg|max:2048',
            'image' => 'required|mimes:png,jpg,jpeg:max:2048',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->slug);
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        
        if($request->hasFile('image'))
        {

        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $filename = Carbon::now()->timestamp.'.'.$file_extension;
        $this->GenerateProductThumbnail($image, $filename);
        $product->image = $filename; 

        }
        

        $gallery_arr = array();
        $gallery_images = "";
        $counter = 1;
        $currentTimestamp = Carbon::now()->format('Ymd_His');

        if($request->hasFile('images'))
        {
            $allowedExtentions = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');
            foreach ($files as $file)
             {
                $gextension = $file->getClientOriginalExtension();
                $gcheck = in_array($gextension, $allowedExtentions);
                if($gcheck)
                {
                    $gfileName = $currentTimestamp.'-'.$counter.'.'.$gextension;
                    $this->GenerateProductThumbnail($file, $gfileName);
                    array_push($gallery_arr, $gfileName);
                    $counter = $counter + 1;
                }
            }
            $gallery_images = implode(',', $gallery_arr);
        }
        $product->images = $gallery_images; 
        $product->save();

        return redirect()->route('admin.products')->with('status', 'Product Added Successfully!');
    }


    public function editProduct($id)
    {

    $categories = Category::all();
    $brands = Brand::all();
    $product = Product::find($id);
    return view('admin.product-edit', compact('categories', 'brands', 'product'));

    }


    public function product_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug,' . $request->id,
            'category_id' => 'required',
            'brand_id' => 'required',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'images.*' => 'mimes:png,jpg,jpeg|max:2048',
            'image' => 'mimes:png,jpg,jpeg:max:2048',
        ]);

        $product = Product::find($request->id);
        $product->name = $request->name;
        $product->slug = Str::slug($request->slug);
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        
        if($request->hasFile('image'))
        {
            if(File::exists(public_path('uploads/products/thumbnails'.$product->image)))
            {
               File::delete(public_path('uploads/products/thumbnails'.$product->image));
            }
            if(File::exists(public_path('uploads/products/'.$product->image)))
            {
               File::delete(public_path('uploads/products/'.$product->image));
            }

        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $filename = Carbon::now()->timestamp.'.'.$file_extension;
        $this->GenerateProductThumbnail($image, $filename);
        $product->image = $filename; 

        }
        

        $gallery_arr = array();
        $gallery_images = "";
        $counter = 1;
        $currentTimestamp = Carbon::now()->format('Ymd_His');

        if($request->hasFile('images'))
        {
            foreach (explode(',', $product->images) as $ofile)
            {
                if(File::exists(public_path('uploads/products/'.$ofile)))
                {
                    File::delete(public_path('uploads/products/'.$ofile));
                }
                if(File::exists(public_path('uploads/products/thumbnails'.$ofile)))
                {
                    File::delete(public_path('uploads/products/thumbnails'.$ofile));
                }

            }

            $allowedExtentions = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');
            foreach ($files as $file)
             {
                $gextension = $file->getClientOriginalExtension();
                $gcheck = in_array($gextension, $allowedExtentions);
                if($gcheck)
                {
                    $gfileName = $currentTimestamp.'-'.$counter.'.'.$gextension;
                    $this->GenerateProductThumbnail($file, $gfileName);
                    array_push($gallery_arr, $gfileName);
                    $counter = $counter + 1;
                }
            }
            $gallery_images = implode(',', $gallery_arr);
            $product->images = $gallery_images;
        } 
        $product->save();

        return redirect()->route('admin.products')->with('status', 'Product Updated Successfully!');
    }


     public function product_delete($id){
        $product = Product::find($id);
        if(File::exists(public_path('uploads/products/thumbnails/'.$product->image)))
        {
            File::delete(public_path('uploads/categories/thumbnails/'.$product->image));
        }
        if(File::exists(public_path('uploads/products/'.$product->image)))
        {
            File::delete(public_path('uploads/categories/'.$product->image));
        }

        foreach (explode(',', $product->images) as $ofile)
            {
                if(File::exists(public_path('uploads/products/'.$ofile)))
                {
                    File::delete(public_path('uploads/products/'.$ofile));
                }
                if(File::exists(public_path('uploads/products/thumbnails'.$ofile)))
                {
                    File::delete(public_path('uploads/products/thumbnails'.$ofile));
                }

            }
            
        $product->delete();
        return redirect()->route('admin.products')->with('status', 'Product Deleted Successfully!');
    }

       public function GenerateProductThumbnail($image, $imagename)
       {
        $destinationPathThumbnails = public_path('uploads/products/thumbnails');
        $destinationPath = public_path('uploads/products');
        $img = Image::read($image->getPathname());

        $img->cover(540,689,"top");
        $img->resize(540,689,function($constraints){
            $constraints->aspectRation();
        })->save($destinationPath.'/'.$imagename);

        $img->resize(104,104,function($constraints){
            $constraints->aspectRation();
        })->save($destinationPathThumbnails.'/'.$imagename);
    }


}
