<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
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

}
