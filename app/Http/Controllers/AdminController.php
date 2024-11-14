<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;


class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    public function brands()
    {
        $brands=Brand::orderBy('id','desc')->paginate(10);
        return view('admin.brands',compact('brands'));
    }

    public function add_brand()
    {
        return view('admin.brand-add');
    }

    public function brand_store(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:255',
            'slug'=>'required|string|max:255|unique:brands,slug',
            'image'=>'required|mimes:png,jpg,jpeg|max:2048',
        ]);

        $brand=new Brand();
        $brand->name=$request->name;
        $brand->slug=Str::slug($request->name);

        $image=$request->file('image');
        $file_ext=$request->file('image')->extension();
        $file_name=Carbon::now()->timestamp.'.'.$file_ext;
        $this->GenerateBrandThumbailsImage($image,$file_name);
        $brand->image=$file_name;
        $brand->save();

        return redirect()->route('admin.brands')->with('status','Brand has been added Sucessfully');
    }

    public function brand_edit($id)
    {
        $brand=Brand::find($id);
        return view('admin.brand-edit',compact('brand'));
    }

    public function brand_update(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:255',
            'slug'=>'required|string|max:255|unique:brands,slug,'.$request->id,
            'image'=>'required|mimes:png,jpg,jpeg|max:2048',
        ]);

        $brand=Brand::find($request->id);
        $brand->name=$request->name;
        $brand->slug=Str::slug($request->name);
        if($request->hasFile('image'))
        {
            if(File::exists(public_path('uploads/brands').'/'.$brand->image))
            {
                File::delete(public_path('uploads/brands').'/'.$brand->image);
            }
            $image=$request->file('image');
            $file_ext=$request->file('image')->extension();
            $file_name=Carbon::now()->timestamp.'.'.$file_ext;
            $this->GenerateBrandThumbailsImage($image,$file_name);
            $brand->image=$file_name;
        }

        $brand->save();

        return redirect()->route('admin.brands')->with('status','Brand has been updated Sucessfully');


    }

    public function GenerateBrandThumbailsImage($image,$imageName)
    {
        //dd($image->path());
        $destinationPath=public_path('uploads/brands');
        $img=Image::read($image->path());
        $img->cover(124,124,"top");
        $img->resize(124,124,function ($constraint){
           $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);

    }

    public function brand_delete($id)
    {
        $brand=Brand::find($id);
        if(File::exists(public_path('uploads/brands').'/'.$brand->image))
        {
            File::delete(public_path('uploads/brands').'/'.$brand->image);
        }
        $brand->delete();
        return redirect()->route('admin.brands')->with('status','Brand has been deleted Sucessfully');

    }

    public function categories()
    {
        $categories=Category::orderBy('id','desc')->paginate(10);
        return view('admin.categories',compact('categories'));
    }
    public function add_category()
    {
        return view('admin.category-add');
    }

    public function category_store(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:255',
            'slug'=>'required|string|max:255|unique:categories,slug',
            'image'=>'required|mimes:png,jpg,jpeg|max:2048',
        ]);

        $category=new Category();
        $category->name=$request->name;
        $category->slug=Str::slug($request->name);

        $image=$request->file('image');
        $file_ext=$request->file('image')->extension();
        $file_name=Carbon::now()->timestamp.'.'.$file_ext;
        $this->GenerateCategoryThumbailsImage($image,$file_name);
        $category->image=$file_name;
        $category->save();

        return redirect()->route('admin.categories')->with('status','Category has been added Sucessfully');
    }

    public function category_edit($id)
    {
        $category=Category::find($id);
        return view('admin.category-edit',compact('category'));
    }

    public function category_update(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:255',
            'slug'=>'required|string|max:255|unique:categories,slug,'.$request->id,
            'image'=>'required|mimes:png,jpg,jpeg|max:2048',
        ]);

        $category=Category::find($request->id);
        $category->name=$request->name;
        $category->slug=Str::slug($request->name);
        if($request->hasFile('image'))
        {
            if(File::exists(public_path('uploads/categories').'/'.$category->image))
            {
                File::delete(public_path('uploads/categories').'/'.$category->image);
            }
            $image=$request->file('image');
            $file_ext=$request->file('image')->extension();
            $file_name=Carbon::now()->timestamp.'.'.$file_ext;
            $this->GenerateCategoryThumbailsImage($image,$file_name);
            $category->image=$file_name;
        }

        $category->save();

        return redirect()->route('admin.categories')->with('status','Category has been updated Sucessfully');


    }

    public function GenerateCategoryThumbailsImage($image,$imageName)
    {
        //dd($image->path());
        $destinationPath=public_path('uploads/categories');
        $img=Image::read($image->path());
        $img->cover(124,124,"top");
        $img->resize(124,124,function ($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);

    }

    public function category_delete($id)
    {
        $category=Category::find($id);
        if(File::exists(public_path('uploads/categories').'/'.$category->image))
        {
            File::delete(public_path('uploads/categories').'/'.$category->image);
        }
        $category->delete();
        return redirect()->route('admin.categories')->with('status','Category has been deleted Sucessfully');

    }

    public function products()
    {
        $products=Product::orderBy('created_at','desc')->paginate(10);
        return view('admin.products',compact('products'));
    }

    public function add_product()
    {
        $categories=Category::select('id','name')->orderBy('name')->get();
        $brands=Brand::select('id','name')->orderBy('name')->get();
        return view('admin.product-add',compact('categories','brands'));
    }

    public function product_store(Request $request)
    {
        //dd($request);
        $request->validate([
            'name'=>'required|string|max:255',
            'slug'=>'required|string|max:255|unique:products,slug',
            'short_description'=>'required',
            'description'=>'required',
            'regular_price'=>'required|numeric',
            'sale_price'=>'required|numeric',
            'SKU'=>'required',
            'stock_status'=>'required',
            'featured'=>'required',
            'quantity'=>'required|numeric',
            'image'=>'required|mimes:png,jpg,jpeg|max:2048',
            'category_id'=>'required',
            'brand_id'=>'required',
        ]);

        $product=new Product();
        $product->name=$request->name;
        $product->slug=Str::slug($request->slug);
        $product->short_description=$request->short_description;
        $product->description=$request->description;
        $product->regular_price=$request->regular_price;
        $product->sale_price=$request->sale_price;
        $product->SKU=$request->SKU;
        $product->stock_status=$request->stock_status;
        $product->featured=$request->featured;
        $product->quantity=$request->quantity;
        $product->category_id=$request->category_id;
        $product->brand_id=$request->brand_id;

        if($request->hasFile('image'))
        {
            $image=$request->file('image');
            $image_name=Carbon::now()->timestamp.'.'.$image->extension();
            $this->GenerateProductThumbailsImage($image,$image_name);
            $product->image=$image_name;
        }

        $gallery_arr=array();
        $gallery_images="";
        $counter=1;

        if($request->hasFile('images'))
        {
            $allowedfileExtention=['jpg','png','jpeg'];
            $files=$request->file('images');
            foreach ($files as $file)
            {
                $gextention=$file->getClientOriginalExtension();
                $gcheck=in_array($gextention,$allowedfileExtention);
                if($gcheck)
                {
                    $gfilename=Carbon::now()->timestamp.'-'.$counter.'.'.$gextention;
                    $this->GenerateProductThumbailsImage($file,$gfilename);
                    array_push($gallery_arr,$gfilename);
                    $counter++;
                }
            }

        }
        $gallery_images=implode(',',$gallery_arr);
        $product->images=$gallery_images;
        $product->save();

        return redirect()->route('admin.products')->with('status','Product has been added Sucessfully');
    }

    public function GenerateProductThumbailsImage($image,$imageName)
    {
        $destinationPathThumbnails=public_path('uploads/products/thumbnails');
        $destinationPath=public_path('uploads/products');
        $img=Image::read($image->path());

        $img->cover(540,689,"top");
        $img->resize(540,689,function ($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);

        $img->resize(104,104,function ($constraint){
            $constraint->aspectRatio();
        })->save($destinationPathThumbnails.'/'.$imageName);

    }
}
