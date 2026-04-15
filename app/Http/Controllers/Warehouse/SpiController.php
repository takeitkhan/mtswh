<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Warehouse\SingleWarehouseController;
use App\Http\Controllers\Controller;
use App\Models\PpiSpiNotification;
use App\Models\TemporaryStock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\PpiSpi;
use App\Helpers\Warehouse\PpiSpiHelper;
use App\Models\PpiSpiStatus;
use App\Http\Controllers\Warehouse\PpiSpiStatusController;
use App\Models\PpiSpiDispute;
use App\Models\GlobalSettings;
use DB;

class SpiController extends SingleWarehouseController
{
    protected $model;
    protected $ppiSpiStatusController;

    public function __construct(PpiSpi $model, PpiSpiStatusController $ppiSpiStatusController)
    {
        parent::__construct();
        $this->model = $model;
        $this->ppiSpiStatusController = $ppiSpiStatusController;
    }

    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        return view('admin.pages.warehouse.single.spi.index');
    }

    /**
     * create
     *
     * @param mixed $request
     * @return void
     */
    public function create(Request $request)
    {
        return view('admin.pages.warehouse.single.spi.form');
    }

    /**
     * store
     *
     * @param mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        $attributes = [
            'action_format' => 'Spi',
            'ppi_spi_type' => $request->spi_type,
            'project' => $request->project,
            'tran_type' => $request->tran_type,
            'requested_by' => $request->requested_by,
            'received_by' => $request->received_by,
            'note' => $request->note,
            'transferable' => $request->transferable ? 'yes' : null,
            'warehouse_id' => request()->get('warehouse_id'),
            'action_performed_by' => auth()->user()->id,
        ];
        //    dd($attributes);
        $spi = $this->model::create($attributes);
        /** Source Store */
        $sources = [];
        foreach ($request->main_source as $data) {
            $exWhoSource = explode('|', $data['source']);
            $sources [] = [
                'ppi_spi_id' => $spi->id,
                'action_format' => 'Spi',
                'source_type' => $data['type'],
                'who_source' => $exWhoSource[0] ?? $data['source'],
                'who_source_id' => $exWhoSource[1] ?? null,
                'levels' => count($request->main_source),
                'warehouse_id' => request()->get('warehouse_id'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        $r = $this->Model('PpiSpiSource')::insert($sources);
        $this->ppiSpiStatusController->spiActionStatus([
            //'wh_id' => $this->wh_code,
            'spi_id' => $spi->id,
            'action' => 'spi_created',
            'redirect' => false
        ]);
        try {
            /**Create Ppi Status */

            //End
            return redirect()->route('spi_edit', [$this->wh_code, $spi->id])->with(['status' => 1, 'message' => 'Successfully created']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['status' => 0, 'message' => 'Oops! Something was wrong']);
        }
    }

    /**
     * edit
     *
     * @param mixed $wh_code
     * @param mixed $id
     * @return void
     */
    public function edit($wh_code, $id)
    {
        $spi = $this->model::find($id);
        return view('admin.pages.warehouse.single.spi.form', ['spi' => $spi]);
    }

    /**
     * update
     *
     * @param mixed $request
     * @return void
     */
    public function update(Request $request)
    {
        dd($request->all());
    }

    /**
     * destroy
     *
     * @param mixed $wh_code
     * @param mixed $id
     * @return void
     */
    public function destroy($wh_code, $id)
    {
        $data = $this->model::find($id);
        $done = $data->delete();
        if ($done) {
            PpiSpiStatus::where('status_for', 'Spi')->where('ppi_spi_id', $id)->delete();

            // Delete From Temporary STock
            $this->Model('TemporaryStock')::where('action_format', 'Spi')->where('ppi_spi_id', $id)->delete() ?? null;

        }
        return redirect()->back()->with(['status' => 1, 'message' => 'Successfully deleted']);
    }
    

    public function lookup(Request $request)
    {
        $whoSource = $request->query('who_source');
        $sourceType = $request->query('source_type');
        $wh_code = $request->query('wh_code');
    
        // Subquery: latest status per ppi_spi_id
        $latestStatus = DB::table('ppi_spi_statuses as status1')
            ->select('status1.ppi_spi_id', 'status1.status_type')
            ->whereRaw('status1.id = (
                SELECT MAX(id)
                FROM ppi_spi_statuses as status2
                WHERE status2.ppi_spi_id = status1.ppi_spi_id
            )');
    
        // Main query
        $spis = \App\Models\PpiSpiSource::select('ppi_spi_sources.*', 'status_sub.status_type')
            ->leftJoinSub($latestStatus, 'status_sub', function ($join) {
                $join->on('ppi_spi_sources.ppi_spi_id', '=', 'status_sub.ppi_spi_id');
            })
            ->where('ppi_spi_sources.action_format', 'Spi')
            ->where('ppi_spi_sources.source_type', $sourceType)
            ->where('ppi_spi_sources.who_source', $whoSource)
            ->orderBy('ppi_spi_sources.id', 'desc')
            ->get();
    
        if ($spis->isNotEmpty()) {
            $links = '<div>';
            foreach ($spis as $spi) {
                $url = route('spi_edit', ['wh_code' => $wh_code, 'id' => $spi->ppi_spi_id]);
    
                $btnClass = ($spi->status_type === 'success-complete') ? 'btn-success' : 'btn-warning';
    
                $links .= '<a class="me-2 mb-1 btn btn-sm ' . $btnClass . '" href="' . $url . '" target="_blank">' . $spi->ppi_spi_id . '</a>';
            }
            $links .= '</div>';
            return $links;
        } else {
            return '<span class="text-danger">No SPI record found</span>';
        }
    }







    /**
     * apiGet
     *
     * @param mixed $request
     * @return void
     */
    public function apiGet(Request $request)
    {
        $role = request()->get('currentUserRole');
        $route = auth()->user()->checkRoute([$role], 'spi_index');

        $query = $this->model::leftjoin('users', 'users.id', 'ppi_spis.action_performed_by')
            ->leftjoin('ppi_spi_statuses', 'ppi_spi_id', 'ppi_spis.id')
            ->leftjoin('ppi_spi_sources', 'ppi_spi_sources.ppi_spi_id', 'ppi_spis.id')
            ->select(
                'ppi_spis.*',
                'users.name as user_name',
                'ppi_spi_statuses.code',
                'ppi_spi_statuses.message',
                'ppi_spi_statuses.status_type',
                'ppi_spi_sources.who_source',
                DB::raw('(SELECT who_source FROM ppi_spi_sources WHERE ppi_spi_sources.ppi_spi_id = ppi_spis.id ORDER BY ppi_spi_sources.id DESC LIMIT 1) AS root_source')
            )
            ->groupBy('ppi_spis.id')
            ->where('ppi_spis.action_format', 'Spi')
            ->where('ppi_spis.warehouse_id', request()->get('warehouse_id'));

        /** Show PPI List Based On */
        if (!empty($route) && $route->show_as == 'User') {
            $query = $query->where('ppi_spis.action_performed_by', auth()->user()->id);
        }
        if (!empty($route) && $route->show_as == 'Permission') {
            if (auth()->user()->checkRoute([$role], "spi_sent_to_wh_manager_action")) {
                $query = $query->where('ppi_spi_statuses.code', 'spi_sent_to_boss');
            }
            if (auth()->user()->checkRoute([$role], "spi_dispute_by_wh_manager_action")) {
                $query = $query->where('ppi_spi_statuses.code', 'spi_sent_to_wh_manager');
            }
        }
//        dd($query->get());
        /** End */
        /** Search Query */
        $sq = '
                $collection->where(function($q) use ($search){
                    $q->where("ppi_spis.id",  $search)
                      ->orWhere("ppi_spis.ppi_spi_type", "LIKE", "%". $search ."%")
                      ->orWhere("ppi_spis.tran_type", "LIKE", "%". $search ."%")
                      ->orWhere("ppi_spis.project", "LIKE", "%". $search ."%")
                       ->orWhere("ppi_spi_sources.who_source", "LIKE", "%". $search ."%")
                      ->orWhere("users.name", "LIKE", "%". $search ."%")
					  ->orWhere("ppi_spi_statuses.message", "LIKE", "%". $search ."%")
                      ->orWhere("ppi_spis.created_at", "LIKE", "%". $search ."%");
                });
        ';
        /** Custom Query Inside The Loop */
        $phpCode = '
                $checkDisputes = $thiss->Model("PpiSpiDispute")::where("ppi_spi_id", $data->id)->get()->count() ?? 0;
                $checkDisputes =  $checkDisputes > 0 ? " alert-danger" : false;
                $spiLastSts = $thiss->Model("PpiSpiStatus")::getSpiLastStatus($data->id);
                $role = request()->get("currentUserRole");
                $transferIcon = $data->transferable== "yes" ? "
                        <i title=\"transfer\" style=\"display: inline;border-radius: 100%;border: 1px solid #fd7e14;padding: 3px;font-size: 10px;\" class=\"fa fa-arrow-up text-orange\"></i>
                        " : null;
                $getTranslateText = $thiss->Model("Translate")::getColumn("to_text", [
                            "translate_for" => "Role",
                            "for_id" =>  $role,
                            "base_text" => $spiLastSts->code,
                        ]);
                $spiLastStatus = $getTranslateText ?? $spiLastSts->message;
                $checkSentToBoss = $thiss->Model("PpiSpiStatus")::checkSpiStatus($data->id, "spi_sent_to_boss");
                $getWarehouseCode = $thiss->Model("Warehouse")::getColumn($data->warehouse_id, "code");
        ';
        /** Filed Show for loop */
        $fields = [
            'button' => '(($checkSentToBoss && auth()->user()->checkUserRoleTypeGeneral()) ? null : $this->ButtonSet::delete("spi_destroy", [$getWarehouseCode, $data->id]))
            .$this->ButtonSet::edit("spi_edit", [$getWarehouseCode, $data->id])',
            'id' => '$data->id',
            'spi_type' => '"<span class=\"$checkDisputes\">".$data->ppi_spi_type."</span>"',
            'project' => '$data->project',
            'tran_type' => '$data->tran_type',
            'requested_by' => '$data->requested_by',
            'spi_last_status' => '$transferIcon."<span title=\"{$this->Model(\'User\')::getColumn($spiLastSts->action_performed_by, \'name\')}\" class=\"py-0 px-1 alert-{$spiLastSts->status_type}\">
                            {$spiLastStatus}
                            </span>"',
            'sources' => '$data->who_source',
            'root_source' => '$data->root_source',
            'action_performed_by' => '$this->Model("User")::getColumn($data->action_performed_by, "name")',
            'created_at' => '$data->created_at->format("d M Y H:i a")',
            'pdf' => '($spiLastSts->code == "spi_all_steps_complete") ? "<a href=\"".route("spi_delivery_challan_view", [$getWarehouseCode, $data->id, "spi_all_steps_complete"])."\" target=\"_blank\" class=\"btn btn-sm btn-success\"><i class=\"fa fa-file-pdf\"></i> PDF</a>" : "<span class=\"text-muted small\">-</span>"',
        ];

        return $this->Datatable::generate($request, $query, $fields, ['searchquery' => $sq, 'daterange' => 'ppi_spis.created_at', 'phpcode' => $phpCode, 'orderby' => 'desc']);

    }


    /**
     * @param Request $request
     * @return array
     */

    public function selectedProductDetailsInfo(Request $request)
    {
        //dd($request->all());
        $spi_type = $request->spi_type ?? 'Supply';
        $browse = $request->browse ?? $spi_type;
        $browse_ppi = $request->browse_ppi ?? null;
        $row_id = $request->row_id;
        $spi_product_id = $request->spi_product_id;
        $product_id = $request->product_id ?? null;
        $warehouse_id = $request->warehouse_id ?? request()->get('warehouse_id');
        $warehouse_code = $request->warehouse_code ?? request()->get('warehouse_code');
        $spi_project = $request->spi_project;
        $original_project = $request->original_project ?? $request->spi_project;
        $landed_project = $request->landed_project ?? null;
        $allProject = $this->Model('Project')::get();
        /*
        if($browse == 'Supply'){
            $allProject = $this->Model('Project')::where('type', 'Supply')->get();
        }else{
            $allProject = $this->Model('Project')::where('type', 'Service')->get();
        }
        */
//        return $product_id;
        if (!empty($product_id)) {
            return view('admin.pages.warehouse.single.spi.form.nselected-product-modal')->with([
                'row_id' => $row_id,
                'product_id' => $product_id,
                'browse' => $browse,
                'browse_ppi' => $browse_ppi,
                'warehouse_id' => $warehouse_id,
                'warehouse_code' => $warehouse_code,
                'spi_project' => $spi_project,
                'landed_project' => $landed_project,
                'original_project' => $original_project,
                'allProject' => $allProject,
                'spi_product_id' => $spi_product_id,
            ]);
        } else {
            return false;
        }
    }

    /**
     * Get PPI list for selected product (JSON response for new inline UI)
     */
    public function getPpiListForProduct(Request $request)
    {
        try {
            $product_id = $request->product_id;
            $spi_id = $request->spi_id;
            $warehouse_code = $request->warehouse_code ?? request()->get('warehouse_code');

            if (!$product_id) {
                return response()->json(['success' => false, 'message' => 'Product ID is required'], 400);
            }

            // Get the product details
            $product = $this->Model('Product')::find($product_id);
            if (!$product) {
                return response()->json(['success' => false, 'message' => 'Product not found'], 404);
            }

            // Get PPIs for this product from different warehouses
            // PPIs are products that are in stock in different warehouses/suppliers
            $ppis = $this->Model('PpiProduct')::where('product_id', $product_id)
                ->with([
                    'ppiSpi' => function($q) {
                        $q->select('id', 'warehouse_id');
                        $q->with(['warehouse:id,name,code']);
                    }
                ])
                ->select('id as ppi_id', 'product_id', 'ppi_id as ppi_spi_id', 'warehouse_id', 'qty as quantity_in_stock', 'unit_price', 'product_state', 'health_status')
                ->orderBy('unit_price', 'asc')
                ->get();

            $ppiList = [];
            foreach ($ppis as $ppi) {
                $warehouseName = 'N/A';
                $ppiWarehouseId = $ppi->warehouse_id;
                
                if ($ppi->ppiSpi && $ppi->ppiSpi->warehouse) {
                    $warehouseName = $ppi->ppiSpi->warehouse->name;
                }

                // For now, use warehouse as supplier (you can extend this later)
                $ppiList[] = [
                    'ppi_id' => $ppi->ppi_id,
                    'product_id' => $ppi->product_id,
                    'supplier' => $warehouseName,  // Using warehouse name as supplier
                    'warehouse' => $warehouseName,
                    'stock_in_hand' => $ppi->quantity_in_stock ?? 0,
                    'product_state' => $ppi->product_state ?? 'New',
                    'health_status' => $ppi->health_status ?? 'Useable',
                    'unit_price' => floatval($ppi->unit_price ?? 0),
                    'ppi_spi_id' => $ppi->ppi_spi_id,
                ];
            }

            return response()->json([
                'success' => true,
                'ppis' => $ppiList,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'unit' => $product->unit ?? 'pcs',
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('getPpiListForProduct Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function lendedProductsForSpi(Request $request)
    {
        $onWarehouse = request()->get('warehouse_id');

        $spis = $this->Model('SpiProduct')::where('warehouse_id', '!=', $onWarehouse)
            ->where('from_warehouse', $onWarehouse)
            ->get();

        return view('admin.pages.warehouse.single.spi.spi-lended')->with(['spis' => $spis]);
    }

    /**
     * Get all products for a specific SPI - returns JSON
     */
    public function getSpiProducts($wh_code, $spi_id)
    {
        try {
            $spi = $this->model::findOrFail($spi_id);
            $getSpiProduct = $spi->spiProducts()
                ->with('product')
                ->get();

            // Get helper instances
            $Model = app('App\Helpers\Component');
            $warehouse_code = $wh_code;

            // Render table rows HTML
            $html = '';
            foreach ($getSpiProduct as $product) {
                // Skip set products
                $checkProductIsSet = $Model('PpiSetProduct')::getSet($product->id);
                if(count($checkProductIsSet) > 0) continue;

                $html .= '<tr class="pr_row_' . $product->id . '" data-product-id="' . $product->id . '">';
                
                // Edit & Delete Buttons
                $html .= '<td>';
                $html .= '<a title="Edit" class="edit text-info font-14" href="javascript:void(0)" data-product-id="' . $product->id . '">';
                $html .= '<span class="fas fa-edit"></span>';
                $html .= '</a>';
                $html .= '&nbsp;';
                $html .= '<a title="Delete" class="delete text-danger font-14" href="javascript:void(0)" data-product-id="' . $product->id . '">';
                $html .= '<span class="fas fa-trash"></span>';
                $html .= '</a>';
                $html .= '</td>';
                
                // Correction column (empty)
                $html .= '<td class="not_print"></td>';
                
                // Product Name
                $html .= '<td class="product"><strong>' . ($product->product_name ?? 'N/A') . '</strong></td>';
                
                // QTY Input
                $html .= '<td class="qty p-1">';
                $html .= '<input type="number" class="form-control form-control-sm qty-input" value="' . $product->qty . '" min="1" data-old-value="' . $product->qty . '" data-product-id="' . $product->id . '">';
                $html .= '</td>';
                
                // Unit
                $html .= '<td class="unit">';
                if($product->product_state == 'Cut-Piece') {
                    $html .= 'Bundle';
                } else {
                    $unit = $Model('AttributeValue')::getValueById($product->product_unit_id);
                    $html .= $unit ?? 'pcs';
                }
                $html .= '</td>';
                
                // Price Input
                $html .= '<td class="price p-1 ppi_product_price_show">';
                $html .= '<input type="number" class="form-control form-control-sm unit-price-input" value="' . $product->unit_price . '" step="0.01" min="0" data-old-value="' . $product->unit_price . '" data-product-id="' . $product->id . '">';
                $html .= '</td>';
                
                // Product State
                $productState = $Model('PpiProduct')::ppiProductInfoByPpiProductId($product->ppi_product_id, ['column' => 'product_state']);
                $html .= '<td class="ppi-info-col">' . ($productState ?? '') . '</td>';
                
                // Health Status
                $healthStatus = $Model('PpiProduct')::ppiProductInfoByPpiProductId($product->ppi_product_id, ['column' => 'health_status']);
                $html .= '<td class="ppi-info-col">' . ($healthStatus ?? '') . '</td>';
                
                // Barcode Format
                $html .= '<td class="not_print ppi-info-col">' . ($product->barcode_format ?? '') . '</td>';
                
                // Notes Input
                $html .= '<td class="note p-1 not_print">';
                $html .= '<input type="text" class="form-control form-control-sm notes-input" placeholder="Notes" value="' . ($product->note ?? '') . '" data-old-value="' . ($product->note ?? '') . '" data-product-id="' . $product->id . '">';
                $html .= '</td>';
                
                // From Warehouse
                $html .= '<td class="ppi-info-col">';
                $html .= ($product->from_warehouse != $product->warehouse_id) ? 'Lended' : 'Regular';
                $html .= '<br>From ' . $Model('Warehouse')::name($product->from_warehouse);
                $html .= '</td>';
                
                // Dispute Note
                $html .= '<td class="not_print"></td>';
                
                // Physical Validation
                $html .= '<td class="text-center not_print"></td>';
                
                // Save Button
                $html .= '<td class="not_print text-center">';
                $html .= '<a title="Save" class="save text-success font-14" href="javascript:void(0)" data-product-id="' . $product->id . '" style="display: none;">';
                $html .= '<span class="fas fa-save"></span>';
                $html .= '</a>';
                $html .= '</td>';
                
                $html .= '</tr>';
            }

            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        } catch (\Exception $e) {
            \Log::error('getSpiProducts error: ' . $e->getMessage());
            \Log::error('Stack: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
