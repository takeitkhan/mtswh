<?php

namespace App\Http\Controllers\Warehouse;
use App\Http\Controllers\Warehouse\SingleWarehouseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Validator;
class ProductCategoryController extends SingleWarehouseController
{
   protected $model; 
   public function __construct(ProductCategory $model){
       parent::__construct();
       $this->model = $model;
   }

    public function index(){
        return view ('admin.pages.warehouse.single.product.category');
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
                'name' => 'required',
            ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else {
            $hash =  bin2hex(random_bytes(2));
            $attributes = [
                'name' => $request->name,
                'slug' => strtolower(str_replace(' ', '_', $request->name)).'_'.$hash,
                'description' => $request->description,
                'parent_id' => $request->parent_id,
            ];
            try {
                $data = $this->model::create($attributes);
                return redirect()->back()->with(['status' => 1, 'message' => 'Successfully created']);
            } catch (\Exception $e) {
                return redirect()->back();
            }
        }            
    }

    public function edit($wh_code,  $id){
        $category = $this->model::find($id);
        return view('admin.pages.warehouse.single.product.category', ['category' => $category]);
    }
    
    public function update(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else {
            $hash =  bin2hex(random_bytes(2));
            $attributes = [
                'name' => $request->name,
                'slug' => strtolower(str_replace(' ', '_', $request->name)).'_'.$hash,
                'description' => $request->description,
                'parent_id' => $request->parent_id,
            ];
            try {
                $data = $this->model::where('id', $request->id)->update($attributes);
                return redirect()->back()->with(['status' => 1, 'message' => 'Successfully created']);
            } catch (\Exception $e) {
                return redirect()->back();
            }
        }      
    }
    public function destroy($wh_code, $id){
        $data = $this->model::find($id);
        $data->delete(); 
        return redirect()->back()->with(['status' => 0, 'message' => 'Successfully deleted']);
    }
}
