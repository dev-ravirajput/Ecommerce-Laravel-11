<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Intervention\Image\Laravel\Facades\Image;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    public function brands(){
        $brands = Brand::latest()->paginate(10);
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

    public function GEnerateBrandThumbnail($image, $imagename){
        $destinationPath = public_path('uploads/brands');
        $img = Image::read($image->getPathname());
        $img->cover(124,124,"top");
        $img->resize(124,124,function($constraints){
            $constraints->aspectRation();
        })->save($destinationPath.'/'.$imagename);
    }
}
