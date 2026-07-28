<?php

namespace App\Http\Controllers\Warehouse;
use App\Http\Controllers\Warehouse\SingleWarehouseController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Warehouse\SpiController;
use App\Models\SpiProduct;
use App\Models\PpiSpi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Models\TemporaryStock;
use App\Helpers\Warehouse\PpiSpiHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
/**
 * @Annotation
 */
class SpiProductController extends SingleWarehouseController
{
    protected $model;
    protected $spiController;
    protected $ppiSpiStatusController;

    /**
     * __construct
     *
     * @param  mixed $model
     * @return void
     */
    public function __construct(SpiProduct $model, SpiController $spiController, PpiSpiStatusController $ppiSpiStatusController){
        parent::__construct();
        $this->model = $model;
        $this->spiController = $spiController;
        $this->ppiSpiStatusController = $ppiSpiStatusController;
    }


    public function add(){
        //dd(request()->get('hasPermission'));
        return true;
    }

    /**
     * Store
     * @param Request $request
     * @return void
     */

    public function store(Request $request)
    {
        if (PpiSpiHelper::isLockedForCurrentUser($request->spi_id, 'Spi')) {
            return response()->json([
                'success' => false,
                'message' => PpiSpiHelper::lockMessage('Spi')
            ], 403);
        }
        try {
            //dd($request->all());
            $products = $request->product;
            
            // Check if products is null or empty
            if (!$products || !is_array($products)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No products provided'
                ], 400);
            }

            DB::beginTransaction();
            
    //        dd($products);
            $busketInfo = $this->ppi_spi_history->arrangeSpiData($request->spi_id);
            $saveForTemporaryStock = [];
            $requestedByPpiProductId = [];
            
            foreach($products as $key => $product){
                $ppiProductId = (int)($product['ppi_product_id'] ?? 0);
                $ppiId = (int)($product['ppi_id'] ?? 0);
                $productId = (int)($product['product_id'] ?? 0);
                $requestedQty = (float)($product['qty'] ?? 0);

                $ppiProduct = DB::table('ppi_products')
                    ->where('id', $ppiProductId)
                    ->where('ppi_id', $ppiId)
                    ->where('product_id', $productId)
                    ->whereNull('deleted_at')
                    ->lockForUpdate()
                    ->first();

                if (!$ppiProduct) {
                    throw ValidationException::withMessages([
                        'product' => 'The selected PPI product is invalid or no longer available.'
                    ]);
                }

                if ($requestedQty <= 0) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Please enter a valid quantity.'
                    ]);
                }

                $hasActionPpi = DB::table('temporary_stocks')
                    ->where('ppi_spi_id', $ppiProduct->ppi_id)
                    ->where('action_format', 'Ppi')
                    ->exists();

                if ($hasActionPpi) {
                    throw ValidationException::withMessages([
                        'product' => 'This PPI cannot be selected because it has pending PPI stock changes.'
                    ]);
                }

                $waitingQty = (float)DB::table('temporary_stocks as temporary_stock')
                    ->join('spi_products as spi_product', 'spi_product.id', '=', 'temporary_stock.spi_product_id')
                    ->where('spi_product.ppi_id', $ppiProduct->ppi_id)
                    ->where('spi_product.product_id', $ppiProduct->product_id)
                    ->where('temporary_stock.action_format', 'Spi')
                    ->sum('temporary_stock.waiting_stock_out');

                $stockedOutQty = (float)DB::table('product_stocks as product_stock')
                    ->join('spi_products as spi_product', 'spi_product.id', '=', 'product_stock.ppi_spi_product_id')
                    ->where('spi_product.ppi_id', $ppiProduct->ppi_id)
                    ->where('spi_product.product_id', $ppiProduct->product_id)
                    ->where('product_stock.action_format', 'Spi')
                    ->where('product_stock.stock_action', 'Out')
                    ->sum('product_stock.qty');

                $availableQty = max(0, (float)$ppiProduct->qty - $waitingQty - $stockedOutQty);
                $requestedByPpiProductId[$ppiProduct->id] = ($requestedByPpiProductId[$ppiProduct->id] ?? 0) + $requestedQty;

                if ($requestedByPpiProductId[$ppiProduct->id] > $availableQty) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Your requested quantity exceeds the available quantity (' . $availableQty . ') for PPI ' . $ppiProduct->ppi_id . '.'
                    ]);
                }

                $attr =  [
                    'spi_id' => $request->spi_id,
                    'warehouse_id' => request()->get('warehouse_id'),
                    'from_warehouse' => $product['from_warehouse'] ?? null,
                    'product_id' => $ppiProduct->product_id,
                    'ppi_product_id' => $ppiProduct->id,
                    'ppi_id' => $ppiProduct->ppi_id,
                    'bundle_id' => $product['bundle_id'] ?? null,
                    'qty' => $product['qty'],
                    'unit_price' => $product['unit_price'] ?? 0,
    //                'price' => $product['price'],
                    'price' => $product['qty']*($product['unit_price'] ?? 0),
                    'note' => $product['note'] ?? $product['notes'] ?? null,
                    'action_performed_by' => auth()->user()->id,
                    'any_warning_cls' => null,
                ];
    //            dd($attr);
                $spi_product = $this->model::create($attr);

                //Store data for temporary stock
                $saveForTemporaryStock []= [
                    'action_format' => 'Spi',
                    'product_id' => $ppiProduct->product_id,
                    'ppi_spi_id' =>  $request->spi_id,
                    'ppi_product_id' => $attr['ppi_product_id'],  // ✓ Use actual ppi_product_id from attributes
                    'spi_product_id' => $spi_product->id,
                    'waiting_stock_in' => 0,
                    'waiting_stock_out' => $product['qty'] ?? 0,
                    'warehouse_id' => request()->get('warehouse_id'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];

                $doStatus = $this->ppiSpiStatusController->spiActionStatus([
                    'wh_id' => request()->get('warehouse_id'),
                    'spi_id' => $request->spi_id,
                    'action' => 'spi_product_added',
                    'spi_product_id' => $spi_product->id,
                    'note' => 'Product: '.$this->Model('Product')::name($product['product_id']),
                    'redirect' => false,
                    'get_status_data' => true,
                ]);

                //check Land From Project - safely access optional fields
                $landedProject = $product['landed_project'] ?? null;
                $originalProject = $product['originalProject'] ?? null;
                
                if(!empty($landedProject) && !empty($originalProject) && $landedProject != $originalProject){
                    $ProjectLendedData = [
                        'spi_id' =>  $request->spi_id,
                        'spi_product_id' => $spi_product->id,
                        'product_id' => $product['product_id'],
                        'ppi_id' => $product['ppi_id'],
                        'ppi_product_id' => $product['ppi_product_id'],
                        'original_project' =>  $product['originalProject'],
                        'original_project_id' =>  $this->Model('Project')::where('name', 'LIKE', '%'.$product['originalProject'].'%')->first()->id ?? null,
                        'landed_project' =>  $product['landed_project'],
                        'landed_project_id' =>  $this->Model('Project')::where('name', 'LIKE', '%'.$product['landed_project'].'%')->first()->id ?? null,
                        'qty' => $product['qty'],
                        'status' => 'processing'
                    ];
                    $lendDataStore = $this->Model('SpiProductLoanFromProject')::create($ProjectLendedData);
                    if($lendDataStore) {
                        $this->ppiSpiStatusController->spiActionStatus([
                            'wh_id' => request()->get('warehouse_id'),
                            'spi_id' => $request->spi_id,
                            'action' => 'spi_product_lended_from_project',
                            'spi_product_id' => $spi_product->id,
                            'note' => 'Product: ' . $this->Model('Product')::name($product['product_id']) . ' lended from ' . $product['landed_project'] . ' Project.',
                            'redirect' => false,
                            'get_status_data' => false,
                        ]);
                    }
                }

                // History Create
                $newInfo = $this->ppi_spi_history->arrangeSpiData($request->spi_id);
                $this->ppi_spi_history->createHistory([
                    'ppi_spi_id' => $request->spi_id,
                    'action_format' => 'Spi',
                    'chunck_old_data' => $busketInfo,
                    'chunck_new_data' => $newInfo,
                    'status_id' => $doStatus->id ?? null,
                ]);
            }
    //        dd($attr);
            if (count($saveForTemporaryStock) > 0) {
                TemporaryStock::insert($saveForTemporaryStock);
            }

            // Generate HTML for newly added products to return immediately
            $spi = PpiSpi::findOrFail($request->spi_id);
            // Use leftjoin instead of with() to properly load product_name
            $getSpiProduct = SpiProduct::leftJoin('products', 'products.id', 'spi_products.product_id')
                ->select(
                    'spi_products.*',
                    'spi_products.id as spi_product_id',
                    'products.id as product_id',
                    'products.name as product_name',
                    'products.unit_id as product_unit_id',
                    'products.barcode_format as barcode_format'
                )
                ->where('spi_products.spi_id', $request->spi_id)
                ->orderBy('spi_products.created_at', 'desc')
                ->get();

            $wh_code = request()->get('warehouse_code');
            
            // Render table rows HTML
            $html = '';
            foreach ($getSpiProduct as $product) {
                // Skip set products
                $checkProductIsSet = $this->Model('PpiSetProduct')::getSet($product->spi_product_id);
                if(count($checkProductIsSet) > 0) continue;

                $html .= '<tr class="pr_row_' . $product->spi_product_id . '" data-product-id="' . $product->spi_product_id . '">';
                
                // Edit & Delete Buttons
                $html .= '<td>';
                $html .= '<a title="Edit" class="edit text-info font-14" href="javascript:void(0)" data-product-id="' . $product->spi_product_id . '">';
                $html .= '<span class="fas fa-edit"></span>';
                $html .= '</a>';
                $html .= '&nbsp;';
                $html .= '<a title="Delete" class="delete text-danger font-14" href="javascript:void(0)" data-product-id="' . $product->spi_product_id . '">';
                $html .= '<span class="fas fa-trash"></span>';
                $html .= '</a>';
                $html .= '</td>';
                
                // Correction column (empty)
                $html .= '<td class="not_print"></td>';
                
                // Product Name
                $html .= '<td class="product"><strong>' . ($product->product_name ?? 'N/A') . '</strong></td>';
                
                // QTY Input
                $html .= '<td class="qty p-1">';
                $html .= '<input type="number" class="form-control form-control-sm qty-input" value="' . $product->qty . '" min="1" data-old-value="' . $product->qty . '" data-product-id="' . $product->spi_product_id . '">';
                $html .= '</td>';
                
                // Unit
                $html .= '<td class="unit">';
                if($product->product_state == 'Cut-Piece') {
                    $html .= 'Bundle';
                } else {
                    $unit = $this->Model('AttributeValue')::getValueById($product->product_unit_id);
                    $html .= $unit ?? 'pcs';
                }
                $html .= '</td>';
                
                // Price Input
                $html .= '<td class="price p-1 ppi_product_price_show">';
                $html .= '<input type="number" class="form-control form-control-sm unit-price-input" value="' . $product->unit_price . '" step="0.01" min="0" data-old-value="' . $product->unit_price . '" data-product-id="' . $product->spi_product_id . '">';
                $html .= '</td>';
                
                // Product State
                $productState = $this->Model('PpiProduct')::ppiProductInfoByPpiProductId($product->ppi_product_id, ['column' => 'product_state']);
                $html .= '<td class="ppi-info-col">' . ($productState ?? '') . '</td>';
                
                // Health Status
                $healthStatus = $this->Model('PpiProduct')::ppiProductInfoByPpiProductId($product->ppi_product_id, ['column' => 'health_status']);
                $html .= '<td class="ppi-info-col">' . ($healthStatus ?? '') . '</td>';
                
                // Barcode Format
                $html .= '<td class="not_print ppi-info-col">' . ($product->barcode_format ?? '') . '</td>';
                
                // Notes Input
                $html .= '<td class="note p-1 not_print">';
                $html .= '<input type="text" class="form-control form-control-sm notes-input" placeholder="Notes" value="' . ($product->note ?? '') . '" data-old-value="' . ($product->note ?? '') . '" data-product-id="' . $product->spi_product_id . '">';
                $html .= '</td>';
                
                // From Warehouse
                $html .= '<td class="ppi-info-col">';
                $html .= ($product->from_warehouse != $product->warehouse_id) ? 'Lended' : 'Regular';
                $html .= '<br>From ' . $this->Model('Warehouse')::name($product->from_warehouse);
                $html .= '</td>';
                
                // Dispute Note
                $html .= '<td class="not_print"></td>';
                
                // Physical Validation
                $html .= '<td class="text-center not_print"></td>';
                
                // Save Button
                $html .= '<td class="not_print text-center">';
                $html .= '<a title="Save" class="save text-success font-14" href="javascript:void(0)" data-product-id="' . $product->spi_product_id . '" style="display: none;">';
                $html .= '<span class="fas fa-save"></span>';
                $html .= '</a>';
                $html .= '</td>';
                
                $html .= '</tr>';
            }

            DB::commit();

            return response()->json([
                'success' => true, 
                'status' => 1, 
                'message' => 'Successfully product added',
                'html' => $html
            ]);
        } catch (ValidationException $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first()
            ], 422);
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            \Log::error('SPI Product Store Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * edit
     *
     * @param  mixed $wh_code
     * @param  mixed $id
     * @return void
     */
    public function edit($wh_code, $id){
        $spiEditProduct = $this->model::find($id);
        if ($spiEditProduct && PpiSpiHelper::isLockedForCurrentUser($spiEditProduct->spi_id, 'Spi')) {
            return redirect()->route('spi_index', [$wh_code])
                ->with(['status' => 0, 'message' => PpiSpiHelper::lockMessage('Spi')]);
        }
//        $spiEditProductBundle = spiBundleProduct::where('ppi_product_id', $id)->get();
        $spiEditProductBundle = null;
        $spi = PpiSpi::find($spiEditProduct->spi_id);
        return view('admin.pages.warehouse.single.spi.form', ['spi' => $spi, 'spiEditProduct' => $spiEditProduct, 'spiEditProductBundle' => $spiEditProductBundle]);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @return void
     */
    public function update(Request $request){
        try {
            // Handle AJAX request for single product update
            $spi_product_id = $request->spi_product_id;
            $qty = $request->qty;
            $unit_price = $request->unit_price;
            $notes = $request->notes ?? '';

            // Find and update the product
            $spiProduct = $this->model::findOrFail($spi_product_id);

            if (PpiSpiHelper::isLockedForCurrentUser($spiProduct->spi_id, 'Spi')) {
                return response()->json([
                    'success' => false,
                    'message' => PpiSpiHelper::lockMessage('Spi')
                ], 403);
            }

            DB::beginTransaction();
            $spiProduct = $this->model::lockForUpdate()->findOrFail($spi_product_id);

            $requestedQty = (float)($qty ?? 0);
            if ($requestedQty <= 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Please enter a valid quantity.'
                ], 422);
            }

            $ppiProduct = DB::table('ppi_products')
                ->where('id', $spiProduct->ppi_product_id)
                ->where('ppi_id', $spiProduct->ppi_id)
                ->where('product_id', $spiProduct->product_id)
                ->whereNull('deleted_at')
                ->lockForUpdate()
                ->first();

            if (!$ppiProduct) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'The source PPI product is invalid or no longer available.'
                ], 422);
            }

            $waitingQty = (float)DB::table('temporary_stocks as temporary_stock')
                ->join('spi_products as spi_product', 'spi_product.id', '=', 'temporary_stock.spi_product_id')
                ->where('spi_product.ppi_id', $ppiProduct->ppi_id)
                ->where('spi_product.product_id', $ppiProduct->product_id)
                ->where('temporary_stock.action_format', 'Spi')
                ->where('temporary_stock.spi_product_id', '!=', $spiProduct->id)
                ->sum('temporary_stock.waiting_stock_out');

            $stockedOutQty = (float)DB::table('product_stocks as product_stock')
                ->join('spi_products as spi_product', 'spi_product.id', '=', 'product_stock.ppi_spi_product_id')
                ->where('spi_product.ppi_id', $ppiProduct->ppi_id)
                ->where('spi_product.product_id', $ppiProduct->product_id)
                ->where('product_stock.action_format', 'Spi')
                ->where('product_stock.stock_action', 'Out')
                ->sum('product_stock.qty');

            $availableQty = max(0, (float)$ppiProduct->qty - $waitingQty - $stockedOutQty);

            if ($requestedQty > $availableQty) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Your requested quantity exceeds the available quantity (' . $availableQty . ') for PPI ' . $ppiProduct->ppi_id . '.'
                ], 422);
            }

            $spiProduct->update([
                'qty' => $qty,
                'unit_price' => $unit_price,
                'price' => $qty * $unit_price,
                'note' => $notes,
                'action_performed_by' => auth()->user()->id,
            ]);

            // Update temporary_stocks to ensure stock_in_hand_new view has correct data
            // This is critical because stock_in_hand_new view depends on waiting_stock_out in temporary_stocks
            TemporaryStock::where('action_format', 'Spi')
                ->where('spi_product_id', $spi_product_id)
                ->update([
                    'waiting_stock_out' => $qty,
                    'updated_at' => Carbon::now()
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully'
            ]);
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            \Log::error('SpiProduct update error: ' . $e->getMessage());
            \Log::error('Stack: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * destroy
     *
     * @return JsonResponse|RedirectResponse
     */
    public function destroy($wh_code, $id){
//        dd($id);

        $data = $this->model::find($id);
        if ($data && PpiSpiHelper::isLockedForCurrentUser($data->spi_id, 'Spi')) {
            return redirect()->back()
                ->with(['status' => 0, 'message' => PpiSpiHelper::lockMessage('Spi')]);
        }
//        dd($data->spi_id);
        $productName = $this->Model('Product')::name($data->product_id);
//        dd($productName);
        $busketInfo = $this->ppi_spi_history->arrangeSpiData($data->spi_id);

        $done = $data->delete();

        /** Delete from Dispute */
            $this->Model('PpiSpiDispute')::where('ppi_spi_id', $data->spi_id)->where('ppi_spi_product_id', $id)->delete() ?? false;

            // Delete From Temporary STock
        TemporaryStock::where('action_format', 'Spi')->where('spi_product_id', $id)->delete() ?? null;
        /***
         * SPI Product STock Delete
         */
        $checkStock = $this->Model('ProductStock')::where('ppi_spi_id', $data->spi_id)->where('ppi_spi_product_id', $id)
            ->where('action_format', 'Spi')->delete();

        //$done = true;
        $status_id = null;
        if($done){
            $doStatus =  $this->ppiSpiStatusController->spiActionStatus([
                'wh_id' => request()->get('warehouse_id'),
                'spi_id' => $data->spi_id,
                'action' => 'spi_product_deleted',
                'note' => 'Product: '.$productName,
                'spi_product_id' => $id,
                'redirect' => false,
                'get_status_data' => true,
            ]);
            $status_id = $doStatus->id;
        }

        // History Create
        if($status_id){
            $newInfo = $this->ppi_spi_history->arrangeSpiData($data->spi_id);
            $this->ppi_spi_history->createHistory([
                'ppi_spi_id' => $data->spi_id,
                'action_format' => 'Spi',
                'chunck_old_data' => $busketInfo,
                'chunck_new_data' => $newInfo,
                'status_id' => $status_id,
            ]);
        }
        //End

        // Return JSON response for AJAX requests or redirect for traditional requests
        if (request()->expectsJson() || request()->is('*/spi/product/delete/*')) {
            return response()->json([
                'success' => true,
                'status' => 0,
                'message' => 'Successfully deleted'
            ]);
        }

        return redirect()->back()->with(['status' => 0, 'message' => 'Successfully deleted']);

    }



    /**
     * Import Product from Another SPI
     * @param Request $request
     * @return void
     */

    public function importProductFromAnotherSpi(Request $request){
//        dd($request->all());
        $fromSpiId = $request->from_spi_id;
        $toSpiId = $request->to_spi_id;
        $fromSpiProduct = $this->model::where('spi_id', $fromSpiId)->get()->toArray();
        $busketInfo = $this->ppi_spi_history->arrangeSpiData($toSpiId);
        $newData = [
            'spi_id' => $toSpiId,
            'action_performed_by' => auth()->user()->id,
            'created_at'=> Carbon::now(),
            'updated_at'=> Carbon::now(),
        ];
        $productName =[];
        if(count($fromSpiProduct) > 0) {
            foreach ($fromSpiProduct as $p) {
                $newData = array_merge($newData, ['qty' => 0, 'any_warning_cls' => 'alert-purple']);
                $newp = array_merge($p, $newData);
                $spi_product = $this->model::create($newp);
                $productName [] = $this->Model('PpiProduct')::ppiProductInfoByPpiProductId($newp['ppi_product_id'], ['column' => 'product_name']);
                //dump($newp);
            }

            $productName = implode('<br/>', $productName);

            //Create Status
            $doStatus = $this->ppiSpiStatusController->spiActionStatus([
                'wh_id' => request()->get('warehouse_id'),
                'spi_id' => $toSpiId,
                'action' => 'spi_product_added',
                'spi_product_id' => null,
                'note' => 'Product imported from SPI ID ' . $fromSpiId . '<br/>' . $productName,
                'redirect' => false,
                'get_status_data' => true,
            ]);

            // History Create
            $status_id = $doStatus->id;
            $newInfo = $this->ppi_spi_history->arrangeSpiData($toSpiId);
            $this->ppi_spi_history->createHistory([
                'ppi_spi_id' => $toSpiId,
                'action_format' => 'Spi',
                'chunck_old_data' => $busketInfo,
                'chunck_new_data' => $newInfo,
                'status_id' => $status_id,
            ]);
            //End
            return redirect()->back()->with(['status' => 1, 'message' => 'Successfully Product imported']);
        }else{
            return redirect()->back()->with(['status' => 1, 'message' => 'Selected SPI has no product']);
        }
    }

}
