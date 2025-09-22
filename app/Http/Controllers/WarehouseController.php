<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Warehouse\PpiSpiStatusController;
use App\Models\PpiProduct;
use App\Models\PpiSpi;
use Illuminate\Http\Request;
use Validator;
use App\Models\Warehouse;
use App\Models\Roleuser;
use App\Models\PpiSpiNotification;
use Carbon\Carbon;
use App\Models\PpiSpiHistory;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    private $model;
    private $roleuser;

    public function __construct(Warehouse $model, Roleuser $roleuser)
    {
        $this->model = $model;
        $this->roleuser = $roleuser;
    }

    /**
     * @index
     */
    public function index()
    {
        //$wh = $this->model::get();
        $wh = auth()->user()->getUserWarehouse();
        return view('admin.pages.warehouse.index', compact('wh'));
    }

    /**
     * @Create
     */
    public function create()
    {
        return view('admin.pages.warehouse.form');
    }

    /**
     * Data insert to DB
     */
    public function store(Request $request)
    {
        //dd($request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
            ]
        );
        // process the login
        if ($validator->fails()) {
            return redirect('warehouse.create')
                ->withErrors($validator)
                ->withInput();
        } else {
            // store
            $hash = bin2hex(random_bytes(2));
            $attributes = [
                'name' => $request->name,
                'code' => strtolower(str_replace(' ', '_', $request->name)) . '_' . $hash,
                'email' => $request->email,
                'phone' => $request->phone,
                'location' => $request->location,
                'is_active' => $request->is_active
            ];
            //dd($attributes);
            $warehouse = $this->model::create($attributes);

            //Insert roleuser table
            $assignAttr = [];
            if ($request->assign_user != null) {
                foreach ($request->assign_user as $key => $assignUser) {
                    $assignAttr[] = $attributes = [
                        'user_id' => $assignUser['user_id'],
                        'role_id' => $assignUser['role_id'],
                        'warehouse_id' => $warehouse->id,
                    ];
                } //End Foreach
                //dd($assignAttr);
                $whr = $this->roleuser::insert($assignAttr);
            } //End if

            try {
                return redirect()->route('warehouse_index')->with(['status' => 1, 'message' => 'Successfully created user']);
            } catch (\Exception $e) {
                //dd($e->errorInfo[2]);
                $errormsg = $e->errorInfo[2];
            }
        }
    }

    /**
     * Edit a Single Warehouse
     * by Id
     */
    public function edit($id)
    {
        $wh = $this->model::find($id);
        $assignedUser = $this->roleuser::where('warehouse_id', $id)->get();
        return view('admin.pages.warehouse.form', compact('wh', 'assignedUser'));
    }

    /**
     * Update Data of DB
     *
     */
    // public function update(Request $request)
    // {
    //     //Catch Warehouse Information
    //     $attributes = [
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'phone' => $request->phone,
    //         'location' => $request->location,
    //         'is_active' => $request->is_active
    //     ];
    //     $wh = $this->model::where('id', $request->id)->update($attributes); //End Warehouse Inormation Update

    //     //Assign User to Warehouse
    //     $existing = $this->roleuser::where('warehouse_id', $request->id)->delete() ?? Null; //Delete Existing Data        
    //     //Store to RoleUser
    //     $assignAttr = [];
    //     if ($request->assign_user != null) {
    //         foreach ($request->assign_user as $key => $assignUser) {
    //             $assignAttr [] = $attributes = [
    //                 'user_id' => $assignUser['user_id'],
    //                 'role_id' => $assignUser['role_id'],
    //                 'warehouse_id' => $request->id,
    //             ];
    //         }
    //         //End Foreach
    //         dd($assignAttr);
    //         $whr = $this->roleuser::insert($assignAttr);
    //     }//End if

    //     try {
    //         return redirect()->back()->with(['status' => 1, 'message' => 'Successfully updated']);
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with(['status' => 0, 'message' => 'Error']);
    //     }
    // }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:warehouses,id',
            'name' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'location' => 'nullable|string',
            'is_active' => 'required|in:Yes,No',
            'assign_user' => 'nullable|array',
            'assign_user.*.user_id' => 'required|integer|exists:users,id',
            'assign_user.*.role_id' => 'required|integer|exists:roles,id',
        ]);

        $warehouseAttributes = $request->only(['name', 'email', 'phone', 'location', 'is_active']);
        $assignUsers = $request->input('assign_user', []);

        DB::beginTransaction();
        try {
            $warehouse = $this->model::findOrFail($request->id);
            $warehouse->update($warehouseAttributes);

            $toInsert = [];
            foreach ($assignUsers as $au) {
                $exists = $this->roleuser::where('warehouse_id', $warehouse->id)
                    ->where('user_id', $au['user_id'])
                    ->where('role_id', $au['role_id'])
                    ->exists();

                if (!$exists) {
                    $toInsert[] = [
                        'user_id' => $au['user_id'],
                        'role_id' => $au['role_id'],
                        'warehouse_id' => $warehouse->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($toInsert)) {
                $this->roleuser::insert($toInsert);
            }

            DB::commit();
            return redirect()->back()->with(['status' => 1, 'message' => 'Successfully updated']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(['status' => 0, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete Data from DB
     */
    public function destroy($id)
    {
        $wh = $this->model::find($id);
        $wh->delete();
        return redirect()->back()->with(['status' => 1, 'message' => 'Successfully deleted']);
    }


    public function notofication($status_id)
    {
        $cn = $this->Model('PpiSpiNotification')::where('status_id', $status_id)->first();
        $status = $this->Model('PpiSpiStatus')::where('id', $status_id)->first();
        $warehouse_code = $this->Model('Warehouse')::getColumn($status->warehouse_id, 'code');

        if (!empty($cn) && ($cn->is_read == 1 || $cn->is_read == 0)) {
        } else {
            $attr = [
                'status_id' => $status_id,
                'is_read' => 1,
                'action_performed_by' => auth()->user()->id,
            ];
            PpiSpiNotification::create($attr);
        }


        if ($status->status_for == 'Ppi') {
            return redirect(route('ppi_edit', [$warehouse_code, $status->ppi_spi_id]));
        }
        if ($status->status_for == 'Spi') {
            return redirect()->route('spi_edit', [$warehouse_code, $status->ppi_spi_id]);
        }
        return null;
    }

    /**
     * @param Request $request
     * @return void
     * Clear All Notification
     */

    public function notoficationClearAll(Request $request)
    {
        //        dd($request->all());
        $exId = explode('|', $request->clearAll);

        $upDateId = null;
        $attr = null;

        foreach ($exId as $id) {
            $check = PpiSpiNotification::where('status_id', $id)->first();
            if ($check) {
                $upDateId[] = $check->id;
                //                PpiSpiNotification::where('id', $check->id)->update(['is_read' => 2]);
            } else {
                $attr[] = [
                    'status_id' => $id,
                    'is_read' => 2,
                    'action_performed_by' => auth()->user()->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }
        if ($upDateId) {
            $done = PpiSpiNotification::whereIn('id', $upDateId)->update(['is_read' => 2]);
        }

        if (!empty($attr)) {
            PpiSpiNotification::insert($attr);
        }


        //PpiSpiNotification::where('is_read', '!=', '2')->update(['is_read' => 2]) ?? null;
        return redirect()->back();
    }


    /**
     *=================
     *=== Report ======
     *=================
     */

    public function productStock(Request $request)
    {
        if ($request->product_id) {
            $product = $this->Model('ProductStockReport')::find($request->product_id);
            //dd($request);
            return view('admin.pages.warehouse.report.product-stock-details')->with(['product' => $product]);
        } else {
            return view('admin.pages.warehouse.report.product-stock');
        }
    }

    public function apiGetProductStock(Request $request)
    {

        $query = $this->Model('ProductStockReportNew')::query();
        /** Filed Show for loop */
        $phpCode = '
            $link = route("report_product_stock_details", $data->id);
        ';
        /*
        $fields = [
            'id' => '$data->id',
            'name' => '"<a target=\"_blank\" class=\"text-primary\" href=\"{$link}\">{$data->name}</a>"',
            'code' => '$data->code',
            'stock_in' => '$data->stock_in',
            'waiting_stock_in' => '$data->waiting_stockin ?? 0',
            'stock_out' => '$data->stock_out',
            'waiting_stock_out' => '$data->waiting_stockout ?? 0',
            'stock_in_hand' => '$data->stock_in_hand-($data->waiting_stockout ?? 0)',
            'unit' => '$this->Model("AttributeValue")::getValueById($data->unit_id)',
        ];
        */

        $fields = [
            'id' => '$data->id',
            'name' => '"<a target=\"_blank\" class=\"text-primary\" href=\"{$link}\">{$data->name}</a>"',
            'code' => '$data->code',
            'stock_in' => '$data->stock_in',
            'waiting_stock_in' => '$data->waiting_stock_in ?? 0',
            'stock_out' => '$data->stock_out',
            'waiting_stock_out' => '$data->waiting_stock_out ?? 0',
            'stock_in_hand' => '$data->stock_in_hand',
            'unit' => '$this->Model("AttributeValue")::getValueById($data->unit_id)',
        ];
        return $this->Datatable::generate($request, $query, $fields, ['orderby' => 'asc', 'phpcode' => $phpCode]);
    }

    public function ppiSiteBasedProductReport(Request $request)
    {
        return view('admin.pages.warehouse.report.ppi-site-based-product-report');
    }

    public function spiSiteBasedProductReport()
    {
        return view('admin.pages.warehouse.report.spi-site-based-product-report');
    }


    public function ppiSpiAccumulated()
    {
        return view('admin.pages.warehouse.report.ppi-spi-accumulated-report');
    }

    public function apiGetPpiSiteBasedProductReport(Request $request)
    {
        $query = $this->Model('PpiSiteBasedProductReport')::query();
        /** Filed Show for loop */
        $phpCode = '

        ';
        $fields = [
            'id' => '$data->id',
            'name' => '$data->name',
            'qty' => '$data->total_qty + $data->bundle_size',
            'site_name' => '$data->site_name',
        ];
        return $this->Datatable::generate($request, $query, $fields, ['orderby' => 'asc', 'phpcode' => $phpCode]);
    }





    //Scraped Product
    public  function scrappedProduct()
    {
        return view('admin.pages.warehouse.report.scrapped-product');
    }
    public  function scrappedProductDetails($product_id)
    {
        $product = $this->Model('Product')::where('id', $product_id)->first();
        return view('admin.pages.warehouse.report.scrapped-product-details', compact('product'));
    }

    public  function apiGetScrappedProduct(Request $request)
    {
        $query = $this->Model('ScrappedProduct')::query()->where('scrapped_product', '>', 0);
        /** Filed Show for loop */
        $phpCode = '
            $link = route("report_scrapped_product_details", $data->id);
        ';
        $fields = [
            'id' => '$data->id',
            'name' => '"<a target=\"_blank\" class=\"text-primary\" href=\"{$link}\">{$data->name}</a>"',
            'code' => '$data->code',
            'scrapped_product' => '($data->scrapped_product + $data->scrapped_product_bundle) ?? 0',
            'unit' => '$this->Model("AttributeValue")::getValueById($data->unit_id)',
        ];
        return $this->Datatable::generate($request, $query, $fields, ['orderby' => 'asc', 'phpcode' => $phpCode]);
    }


    //Faulty Product
    public  function faultyProduct()
    {
        return view('admin.pages.warehouse.report.faulty-product');
    }
    public  function faultyProductDetails($product_id)
    {
        $product = $this->Model('Product')::where('id', $product_id)->first();
        return view('admin.pages.warehouse.report.faulty-product-details', compact('product'));
    }

    public  function apiGetFaultyProduct(Request $request)
    {
        $query = $this->Model('FaultyProduct')::query()->where('faulty_product', '>', 0);
        /** Filed Show for loop */
        $phpCode = '
            $link = route("report_faulty_product_details", $data->id);
        ';
        $fields = [
            'id' => '$data->id',
            'name' => '"<a target=\"_blank\" class=\"text-primary\" href=\"{$link}\">{$data->name}</a>"',
            'code' => '$data->code',
            'scrapped_product' => '($data->faulty_product + $data->faulty_product_bundle) ?? 0',
            'unit' => '$this->Model("AttributeValue")::getValueById($data->unit_id)',
        ];
        return $this->Datatable::generate($request, $query, $fields, ['orderby' => 'asc', 'phpcode' => $phpCode]);
    }

    public function ppiProductToSpi($ppi_product_id)
    {
        return view('admin.pages.warehouse.report.ppi_product_to_spi', compact('ppi_product_id'));
    }


    //Vendor Report
    public function vendorReport()
    {
        return view('admin.pages.warehouse.report.vendor_report');
    }

    public function purchaseVendorReport()
    {
        return view('admin.pages.warehouse.report.purchase_vendor_report');
    }


    //project Lended Report
    public function lendedFromReport(Request $request)
    {
        $project_name = $request->project;
        $stock_in_hand = $request->stock_in_hand;
        return view('admin.pages.warehouse.report.project-lended')->with([
            'project_name' => $project_name,
            'stock_in_hand' => $stock_in_hand,
        ]);
    }


    public function startlendProjectReturn(Request $request)
    {
        $lended_id = explode(',', $request->lended_id) ?? false;
        //        dd($lended_id);
        $getData = $this->Model('SpiProductLoanFromProject')::whereIn('id', $lended_id)->get();
        $makeArr = [];
        foreach ($getData as $data) {
            $forWarehouseId = $this->Model('SpiProduct')::where('id', $data->spi_product_id)->first()->from_warehouse;
            $makeArr[$forWarehouseId][$data->landed_project_id][] = [
                'project_name' => $data->landed_project,
                'project_type' => $this->Model('Project')::where('id', $data->landed_project_id)->first()->type ?? null,
                'product_id' => $data->product_id,
                'spi_product_id' => $data->spi_product_id,
                'ppi_product_id' => $data->ppi_product_id,
                'qty' => $data->qty,
                'lended_id' => $data->id,
                'for_warehouse_id' => $forWarehouseId
            ];
        }

        $arrs = [];

        //Process ppi

        foreach ($makeArr as $whid => $items) {
            //dump($items);
            foreach ($items as $pid => $item) {
                $makePpi = [
                    'action_format' => 'Ppi',
                    'ppi_spi_type' => $item[0]['project_type'],
                    'project' => $item[0]['project_name'],
                    'tran_type' => 'Without Money',
                    'note' => 'Lended product return from ' . $item[0]['project_name'],
                    'transferable' => null,
                    'warehouse_id' => $whid,
                    'action_performed_by' => auth()->user()->id,
                ];
                //dump($makePpi);
                $ppi = PpiSpi::create($makePpi);
                PpiSpiStatusController::ppiActionStatus([
                    'wh_id' => $whid,
                    'ppi_id' => $ppi->id ?? null,
                    'action' => 'ppi_created',
                    'redirect' => false
                ]);
                // Process PPI Product
                foreach ($item as $key => $p) {
                    $ppiProduct = $this->Model('PpiProduct')::where('id', $p['ppi_product_id'])->first();
                    $createPpiProduct = [
                        'ppi_id' => $ppi->id,
                        'warehouse_id' => $whid,
                        'product_id' => $p['product_id'],
                        'unit_price' => 0,
                        'qty' => $p['qty'],
                        'price' => 0,
                        'product_state' => $ppiProduct->product_state,
                        'health_status' => $ppiProduct->health_status,
                        'note' => null,
                        'action_performed_by' => auth()->user()->id,
                    ];


                    $ppiProductIDCreate = $this->Model('PpiProduct')::create($createPpiProduct);

                    //Update Lended Table
                    $this->Model('SpiProductLoanFromProject')::where('id', $p['lended_id'])->update([
                        'status' => 'done',
                        'generate_ppi_id' => $ppi->id,
                        'updated_at' => $ppi->updated_at,
                    ]);

                    //Store data for temporary stock
                    $saveForTemporaryStock = [
                        'action_format' => 'Ppi',
                        'product_id' => $p['product_id'],
                        'ppi_spi_id' =>  $ppi->id,
                        'ppi_product_id' =>  $ppiProductIDCreate->id,
                        'spi_product_id' => null,
                        'waiting_stock_in' => $p['qty'] ?? 0,
                        'waiting_stock_out' => 0,
                        'warehouse_id' => $whid,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];

                    if ($ppiProduct->product_state == 'Cut-Piece') {
                        $newPpiProductId = $ppiProductIDCreate->id;
                        $newbundleName = $ppiProduct->product_id . $newPpiProductId . '_' . $ppiProduct->qty;
                        $oldbundleName = $ppiProduct->product_id . $ppiProduct->ppi_product_id . '_' . $ppiProduct->qty;
                        $getBundle = $this->Model('PpiBundleProduct')::where('ppi_product_id', $p['ppi_product_id'])->where('bundle_name', $oldbundleName)->first();
                        $makeBundle = [
                            'ppi_id' => $pid,
                            'ppi_product_id' => $newPpiProductId,
                            'product_id' => $ppiProduct->product_id,
                            'warehouse_id' => $whid,
                            'bundle_name' => $newbundleName,
                            'bundle_size' => $ppiProduct->qty,
                            'bundle_price' => $getBundle->bundle_price,
                            'action_performed_by' => auth()->user()->id,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ];
                        $this->Model('PpiBundleProduct')::create($makeBundle);
                    }

                    //Insert data for temporary stock
                    $this->Model('TemporaryStock')::create($saveForTemporaryStock);
                    //dump($saveForTemporaryStock);


                    $doStatus =   PpiSpiStatusController::ppiActionStatus([
                        'wh_id' => $whid,
                        'ppi_id' => $ppi->id,
                        'action' => 'ppi_product_added',
                        'ppi_product_id' => $ppiProductIDCreate->id,
                        'note' => 'Product: ' . PpiProduct::ppiProductInfoByPpiProductId($ppiProductIDCreate->id, ['column' => 'product_name']),
                        'redirect' => false,
                        'get_status_data' => true,
                    ]);

                    // History Create
                    $status_id = $doStatus->id;
                    $busketInfo = (new PpiSpiHistory())->arrangePpiData($ppi->id);
                    $newInfo = (new PpiSpiHistory())->arrangePpiData($ppi->id);
                    (new PpiSpiHistory())->createHistory([
                        'ppi_spi_id' => $ppi->id,
                        'action_format' => 'Ppi',
                        'chunck_old_data' => $busketInfo,
                        'chunck_new_data' => $newInfo,
                        'status_id' => $status_id,
                    ]);
                }
            }
        }

        return redirect()->back();
    }
}
