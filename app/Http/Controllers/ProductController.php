<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use DataTables;

class ProductController extends Controller
{
    public function tableData()
    {
        return DataTables::of(Product::select('*'))
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                return '<a href="#" onclick="editData('.$row->id.')">Edit</a>';
            })
                    ->addColumn('delete', function ($row) {

                return '<a href="#" onclick="deleteData('.$row->id.')" class="text-danger">Delete</a>';
            })
            ->rawColumns(['delete' => 'delete','action' => 'action'])
                ->make(true);
    }
    private function validate_input($request) {
        $validator = Validator::make($request->all(),
                ['title'=>'required',
                 'description'=>'nullable',
                 'file'=>'required',
                 'colors.*'=>'required',
                 'sizes.*'=>'required']);
        if ($validator->fails()) {
            return ["status"=>false,
                    "message"=>$validator->errors()->first(),
                    "errors"=>$validator->errors()->all()];
       }
       $data= $validator->valid();
        return ["status"=>true,
                "data"=>$data];
   }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('product');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data= $this->validate_input($request);
        if(!$data['status'])
        {
            return response()->json($data);
        }
        $data=$data['data'];
       return    DB::transaction(function() use($data)
      {
        $product=new Product();
        $product->title=$data['title'];
        $product->description=$data['description'];
        $product->file=$data['file'];
        $product->save();
         $i=0;
         foreach ($data['sizes'] as $size) {
           $color= $data['colors'][$i];
           $variation=new Variation();
           $variation->color=$color;
           $variation->size=$size;
           $variation->product_id=$product->id;
           $variation->save();
           $i++;
         }
         return response()->json(["status"=>1,"message"=>"Inserted Successfully"]);
          });
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $data=Product::with(["Variations"])->findOrFail($product->id);
        return response()->json(["data"=>$data]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $data= $this->validate_input($request);
        if(!$data['status'])
        {
            return response()->json($data);
        }
        $data=$data['data'];
       return    DB::transaction(function() use($data,$product)
      {
        $product->title=$data['title'];
        $product->description=$data['description'];
        $product->file=$data['file'];
        $product->update();
        $product->Variations()->delete();
         $i=0;
         foreach ($data['sizes'] as $size) {
           $color= $data['colors'][$i];
           $variation=new Variation();
           $variation->color=$color;
           $variation->size=$size;
           $variation->product_id=$product->id;
           $variation->save();
           $i++;
         }
         return response()->json(["status"=>1,"message"=>"Updated Successfully"]);
          });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try{
            return   DB::transaction(function() use($product)
               {


                   $product->Variations()->delete();
                   $product->delete();
                   return response()->json(["status"=>true,
                                            "message"=>"Deleted Successfully"]);
               });
           }catch(\Exception $e){
               return response()->json(["status"=>false,
                                        "message"=>$e->getMessage()]);
           }
    }
    public function fileUpload(Request $request)
    {
        $rules=['file' => 'required|mimes:jpeg,png,jpg,gif,svg,mp4|max:2048'];

            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
              return response()->json(["status"=>0,
                      "message"=>$validator->errors()->first()
                      ]);
           }

            $image_path=$request->file('file')->store('public/product');
            $image=str_replace('public/product',"",$image_path);
            return response()->json(["status"=>1,
                                     "image"=>$image
                                    ]);
    }
}
