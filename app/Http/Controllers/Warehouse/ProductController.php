<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Warehouse\SingleWarehouseController;
use Illuminate\Http\Request;
use App\Models\Product;
use Validator;
use DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;

class ProductController extends SingleWarehouseController
{
    protected $model;

    public function __construct(Product $model)
    {
        parent::__construct();
        $this->model = $model;
    }

    public function index()
    {
        return view('admin.pages.warehouse.single.product.index');
    }

    /**
     * Create Form
     */
    public function create()
    {
        return view('admin.pages.warehouse.single.product.form');
    }

    /**
     * Store Data
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'name' => 'required',
                'code' => 'unique:products,code|required'
            ]
        );
//        dd($request->all());
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        } else {
            $hash = bin2hex(random_bytes(2));
            $warehouse_id = implode(',', $request->warehouse_id);
            $unit = $this->Query::accessModel('AttributeValue')::getValueById($request->unit_id);
            //$slug = strtolower(str_replace(' ', '_', $request->name)) . '_' . $hash;
            $barcode_prefix = $request->barcode_format == 'Tag' ? 'MTS' . str_replace(' ', '', $request->code) : null;
            $attribute = [
                'user_id' => auth()->user()->id,
                'name' => $request->name,
                'code' => str_replace(' ', '', $request->code),
                //'slug' => $slug,
                'description' => $request->description,
                'brand_id' => $request->brand_id,
                'unit_id' => $request->unit_id,
                'warehouse_id' => $warehouse_id,
                'product_type' => implode(',', $request->product_type),
                'barcode_format' => $request->barcode_format,
                'barcode_prefix' => $barcode_prefix,
                'unique_key' => 'MTS' . bin2hex(random_bytes(2)),
                'stock_qty_alert' => $request->stock_qty_alert,
                'category_id' => $request->category_id,
                'price_range' => $request->price_range,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
            try {
                $product = $this->model::create($attribute);
                return redirect()->back()->with(['status' => 1, 'message' => 'Successfully created']);
            } catch (\Exception $e) {
                return redirect()->back();
            }
        }
    }

    /**
     * Edit Form
     */
    public function edit($wh_code, $id)
    {
        $product = $this->model::find($id);
        return view('admin.pages.warehouse.single.product.form', ['product' => $product]);
    }

    /**
     * Update Data
     */

    public function update(Request $request)
    {
        // dd($request->all());
        $warehouse_id = implode(',', $request->warehouse_id);
        $unit = $this->Model('AttributeValue')::getValueById($request->unit_id);
        $attributes = [
            'user_id' => auth()->user()->id,
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'brand_id' => $request->brand_id,
            'unit_id' => $request->unit_id,
            'warehouse_id' => $warehouse_id,
            //'product_type' => $request->product_type,
            //'used_for' => $request->used_for,
            //'barcode_format' => $request->barcode_format,
            //'barcode_prefix' => $request->barcode_prefix == 'Tag' ? 'MTS_'.$request->barcode_prefix : NULL,
            'stock_qty_alert' => $request->stock_qty_alert,
            'category_id' => $request->category_id,
        ];

        //dd($attributes);
        try {
            $product = $this->model::where('id', $request->id)->update($attributes);
            return redirect()->route('product_index', $this->wh_code)->with(['status' => 1, 'message' => 'Successfully updated']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['status' => 0, 'message' => 'Error']);
        }

    }

    /**
     * Delete
     */
    public function destroy($wh_code, $id)
    {
        $data = $this->model::find($id);
        $data->delete();
        return redirect()->back()->with(['status' => 0, 'message' => 'Successfully deleted']);
    }


    /**
     * Api for Datatable
     */
    public function apiGet(Request $request)
    {

        $query = $this->model::query()
            ->leftjoin('product_categories as categories', 'categories.id', 'products.category_id')
            ->leftjoin('attribute_values as unit', 'unit.id', 'products.unit_id')
            ->leftjoin('attribute_values as brand', 'brand.id', 'products.brand_id')
            ->leftjoin('warehouses', DB::raw("FIND_IN_SET(warehouses.id,products.warehouse_id)"), ">", DB::raw("'0'"))
            ->select('products.*', 'categories.name as category',
                DB::raw("GROUP_CONCAT(warehouses.name) as warehouse_name"),
                'unit.value as unit_name', 'brand.value as brand_name')
            ->groupBy("products.id")
            ->where(function ($c) {
                $c->whereRaw("FIND_IN_SET('" . request()->get('warehouse_id') . "',warehouse_id)");
            });
        /**
         * search Query
         */
        $sq = '
            $collection->where(function($q) use ($search){
                            $q->where("products.name", "LIKE", "%". $search ."%")
                            ->orWhere("products.unique_key", "LIKE", "%". $search ."%")
                            ->orWhere("products.code", "LIKE", "%". $search ."%")
                            ->orWhere("products.unit_id", "LIKE", "%". $search ."%")
                            ->orWhere("products.brand_id", "LIKE", "%". $search ."%")
                            ->orWhere("products.warehouse_id", "LIKE", "%". $search ."%")
                            ->orWhere("warehouses.name", "LIKE", "%". $search ."%")
                            ->orWhere("categories.name", "LIKE", "%". $search ."%")
                            ->orWhere("unit.value", "LIKE", "%". $search ."%")
                            ->orWhere("brand.value", "LIKE", "%". $search ."%")
                            ->orWhere("products.product_type", "LIKE", "%". $search ."%")
                            ->orWhere("products.stock_qty_alert", "LIKE", "%". $search ."%")
                            ->orWhere("products.barcode_format", "LIKE", "%". $search ."%");
            });
        ';
        $phpCode = '';
        $fields = [
            'button' => '
                $this->ButtonSet::delete("product_destroy", [request()->get("warehouse_code"), $data->id], ["id" => "p".$data->id]).
                $this->ButtonSet::edit("product_edit", [request()->get("warehouse_code"), $data->id])
            ',
            'name' => '$data->name',
            'code' => '$data->code',
            'unit_id' => '$data->unit_name',
            'brand_id' => '$data->brand_name',
            'warehouse_code' => '$data->warehouse_name',
            'category' => '$data->category',
            'product_type' => '$data->product_type',
            'stock_qty_alert' => '$data->stock_qty_alert',
            'barcode_format' => '$data->barcode_format',
            'created_at' => '$data->created_at ? $data->created_at->format(\'Y-m-d h:i a\') : null'
        ];
        return $this->Datatable::generate($request, $query, $fields, ['searchquery' => $sq, 'daterange' => 'products.created_at', 'phpcode' => $phpCode]);
    }


    /**
     * Upload Product via Excel
     */
    public function uploadViaExcel(Request $request)
    {
        # code...
        //dd($request->start_import);
        if ($request->start_import) {
            $requestImport = json_decode($request->start_import, true);
            //dd($requestImport);
            $attributes = [];
            foreach ($requestImport as $key => $r) {
                $r = (object)$r;
                $hash = bin2hex(random_bytes(2));
                $warehouse_id = $this->Model('Warehouse')::pluck('id')->implode(',');
                $unit = null;
                $slug = strtolower(str_replace(' ', '_', $r->product_name)) . '_' . $hash;

                /** Category get or Store */
                //dd($r);
                if (!empty($r->product_category)) {
                    $getCat = $this->Model('ProductCategory')::where('name', $r->product_category)->first();
                    if ($getCat) {
                        $category_id = $getCat->id;
                    } else {
                        $catArr = [
                            'name' => $r->product_category,
                            'slug' => strtolower(str_replace(' ', '', $r->product_category)),
                        ];
                        $catInsert = $this->Model('ProductCategory')::create($catArr);
                        $category_id = $catInsert->id;
                    }
                }
                /** End */

                /** Unit get or Store */
                if (!empty($r->product_unit)) {
                    $getUnit = $this->Model('AttributeValue')::where('unique_name', 'Unit')->where('value', $r->product_unit)->first();
                    if ($getUnit) {
                        $unit_id = $getUnit->id;
                    } else {
                        // dd($r->product_unit);
                        $unitArr = [
                            'slug' => strtolower(str_replace(' ', '', $r->product_unit)),
                            'value' => $r->product_unit,
                            'unique_name' => 'Unit',
                            'status' => 'Active'
                        ];
                        $unitInsert = $this->Model('AttributeValue')::create($unitArr);
                        //    dd($unitInsert);
                        $unit_id = $unitInsert->id;
                    }
                }
                /** End */
                $product_type = !empty($r->product_type) ? $r->product_type : 'Supply,Service';
                $code = str_replace(' ', '', $r->product_code);
                $checkHasDbCode = $this->Model('Product')::where('code', $code)->first();
                $barcode_prefix = $r->product_barcode == 'Tag' ? 'MTS' . str_replace(' ', '', $r->product_code) : null;
                if(empty($checkHasDbCode)) {
                    $duplicateCodeInExcel = array_keys(array_column($requestImport, 'product_code'), $code);
                    $duplicateCodeInExcelCheck = count($duplicateCodeInExcel) > 1 ? false : true;
                    if($duplicateCodeInExcelCheck) {
                        $attributes [] = [
                            'user_id' => auth()->user()->id,
                            'name' => $r->product_name,
                            //'slug' => $slug,
                            'code' => $code,
                            'product_type' => $product_type,
                            'category_id' => $category_id ?? null,
                            'brand_id' => $r->product_brand ?? null,
                            'unit_id' => $unit_id ?? null,
                            'warehouse_id' => $r->warehouse_id ?? $warehouse_id,
                            'barcode_prefix' => $barcode_prefix,
                            'barcode_format' => $r->product_barcode,
                            'unique_key' => 'MTS' . bin2hex(random_bytes(2)) . $key,
                            'stock_qty_alert' => 20,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ];
                    }
                }

            }
            //dump($attributes);
//            dd($attributes);
            $done = $this->model::insert($attributes);
            if ($done) {
                return redirect()->back()->with([
                    'status' => 1,
                    'message' => 'Product has been uploaded successfully.'
                ]);
            }
            //exit();
        } else {
            Excel::import(new ProductsImport, $request->file('excel_file')->store('temp'));
            return back();
        }
        //
    }

}


// store segment

//            foreach($request->product_type as $type) {
//                $code = $request->code.'_'.substr($type, 0, 3);
//                //$makebpf = str_pad(substr($request->code, 0, 4), 4, '0', STR_PAD_LEFT).substr($type, 1,2);
//                $makebpf = str_pad(substr($request->code, 0, 4), 4, '0', STR_PAD_LEFT).substr($type, 1,2);
//                $barcode_prefix= $request->barcode_format == 'Tag' ? 'MTS'.preg_replace('/\s+/', '' ,$makebpf) : null;
//                $arr1 = $attribute;
//                $arr2 = [
//                    'code' => $code,
//                    'product_type' => $type,
//                    'barcode_prefix' => $barcode_prefix,
//                    'slug'  => $slug.substr($type, 0, 3),
//                    'unique_key' => 'MTS'. bin2hex(random_bytes(2)),
//                ];
//                 $attributes []= array_merge($arr1, $arr2);
//            }
