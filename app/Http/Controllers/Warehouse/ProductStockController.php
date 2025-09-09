<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Warehouse\SingleWarehouseController;
use App\Models\SpiProduct;
use Illuminate\Http\Request;
use App\Models\ProductStock;
use Carbon\Carbon;
use App\Models\PpiProduct;
use App\Http\Controllers\Warehouse\PpiSpiStatusController;
use App\Models\TemporaryStock;
class ProductStockController extends SingleWarehouseController
{
    protected $ppiSpiStatusController;
    //

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->ppiSpiStatusController = new PpiSpiStatusController();
    }


    /**
     * existingProductCheckByBarcode During Stock In
     * PPI
     * @param mixed $request
     * @return void
     */
    public function existingProductCheckByBarcode(Request $request)
    {
        //return request()->get('barcode');
        $r = $request->all();

        $barcode = request()->barcode;
        $productId = $request->product_id;
        //$orginalBarcode = request()->orginal_barcode;

        $existing = ProductStock::where('barcode', $barcode)
            //->where('product_id', $productId)
            ->where('stock_action', 'In')
            ->where('action_format', 'Ppi')
            ->where('warehouse_id', request()->get('warehouse_id'))
            ->first();
        //return response()->json($existing->barcode);
        if (!empty($existing)) {
            $orginalBarcode = $existing->original_barcode;
            $arr = [
                'ppi_spi_id' => $request->ppi_id,
                'action_format' => 'Ppi',
                'ppi_spi_product_id' => $request->ppi_product_id,
                'product_id' => $request->product_id,
                'barcode' => $existing->barcode,
                'product_unique_key' => $existing->product_unique_key,
                'original_barcode' => $existing->original_barcode,
                'stock_action' => 'In',
                'stock_type' => 'Existing',
                'qty' => $request->product_qty,
                'bundle_id' => $existing->bundle_id,
                'entry_date' => date('Y-m-d'),
                'warehouse_id' => request()->get('warehouse_id'),
                'action_performed_by' => auth()->user()->id,
                'note' => 'replace_with_' . $request->replace_with_barcode,
            ];
            //return response()->json($arr);
            //exit();
            $done = ProductStock::create($arr);
            if ($done) {
                $this->ppiSpiStatusController->ppiActionStatus([
                    'wh_id' => request()->get('warehouse_id'),
                    'ppi_id' => $request->ppi_id,
                    'action' => 'ppi_existing_product_added_to_stock',
                    'note' => 'with ' . PpiProduct::ppiProductInfoByPpiProductId($request->ppi_product_id, ['column' => 'product_name']) . ' Barcode: ' . $orginalBarcode,
                    'ppi_product_id' => $request->ppi_product_id,
                    'redirect' => false,
                ]);
            }
            return response()->json(['status' => '1', 'message' => 'Added to stock successfully']);
        } else {
            return false;
        }
    }


    /**
     * stockIn
     * PPI
     * @param mixed $request
     * @return void
     */
    public function stockIn(Request $request)
    {
//        dd($request->all());
        $generatedBarcode = $request->barcode_product_line_item;
        if ($generatedBarcode) {
            $checkAlreadyStockInThePpiProduct = ProductStock:: where('ppi_spi_id', $request->ppi_id)
                ->where('action_format', 'Ppi')
                ->where('ppi_spi_product_id', $request->ppi_product_id)
                ->where('product_id', $request->product_id)
                ->count();
            //dd($generatedBarcode);
            if ($checkAlreadyStockInThePpiProduct > 0) {
                return redirect()->back()->with(['status' => '1', 'message' => 'Already Stocked in']);
            } else {
                $attr = [];
                foreach ($generatedBarcode as $key => $barcode) {
                    $checkBarcodeExist = ProductStock::where('action_format', 'Ppi')
                                        ->where('ppi_spi_product_id', $request->ppi_product_id)
                                        ->where('original_barcode', $barcode)->first();
                    if($checkBarcodeExist) {

                    } else {
                        $attr [] = [
                            'ppi_spi_id' => $request->ppi_id,
                            'action_format' => 'Ppi',
                            'ppi_spi_product_id' => $request->ppi_product_id,
                            'product_id' => $request->product_id,
                            'bundle_id' => $request->bundle_id[$key] ?? null,
                            'barcode' => $request->barcode_product_unique_key[$key] ?? null,
                            'product_unique_key' => $request->product_unique_key ?? null,
                            'original_barcode' => $barcode,
                            'stock_action' => 'In',
                            'stock_type' => 'New',
                            'qty' => $request->qty[$key] ?? null,
                            'entry_date' => date('Y-m-d'),
                            'warehouse_id' => request()->get('warehouse_id'),
                            'action_performed_by' => auth()->user()->id,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ];
                    }
                }


                $done = ProductStock::insert($attr);
                //dd($attr);
                if ($done) {
                    //Delete from Temporary stock
                    TemporaryStock::where('action_format', 'Ppi')->where('ppi_product_id', $request->ppi_product_id)->delete() ?? null;
                    //Status Create
                    $this->ppiSpiStatusController->ppiActionStatus([
                        'wh_id' => request()->get('warehouse_id'),
                        'ppi_id' => $request->ppi_id,
                        'action' => 'ppi_new_product_added_to_stock',
                        'note' => 'Product ' . PpiProduct::ppiProductInfoByPpiProductId($request->ppi_product_id, ['column' => 'product_name']),
                        'ppi_product_id' => $request->ppi_product_id,
                        'redirect' => false,
                    ]);
                }
                return redirect()->route('ppi_edit', [request()->get('warehouse_code'), $request->ppi_id])->with(['status' => '1', 'message', 'Product added to stock successfully.']);
            }//End if Else checkin
        }
    }

    /**
     * stockOut
     * SPI
     * @param mixed $request
     * @return void
     */
    public function stockOut(Request $request)
    {
//        dd($request->all());
        //dd($this->Model('Product')::name($request->product_id));
        $generatedBarcode = $request->barcode_product_line_item;
//        dd($generatedBarcode);
        if ($generatedBarcode) {
            $checkAlreadyStockOutTheSpiProduct = ProductStock:: where('ppi_spi_id', $request->spi_id)
                ->where('action_format', 'Spi')
                ->where('ppi_spi_product_id', $request->spi_product_id)
                ->where('product_id', $request->product_id)
                ->count();
            if ($checkAlreadyStockOutTheSpiProduct > 0) {
                return redirect()->back()->with(['status' => '1', 'message' => 'Already Stocked Out']);
            } else {
                $attr = [];
                foreach ($generatedBarcode as $key => $barcode) {
                    $attr [] = [
                        'ppi_spi_id' => $request->spi_id,
                        'action_format' => 'Spi',
                        'ppi_spi_product_id' => $request->spi_product_id,
                        'from_ppi_product_id' => SpiProduct::where('id', $request->spi_product_id)->first()->ppi_product_id ?? null,
                        'product_id' => $request->product_id,
                        'bundle_id' => $request->bundle_id ?? null,
                        'barcode' => $request->barcode_product_unique_key[$key] ?? null,
                        'product_unique_key' => $request->product_unique_key ?? null,
                        'original_barcode' => $barcode,
                        'stock_action' => 'Out',
                        'stock_type' => null,
                        'qty' => $request->qty[$key] ?? null,
                        'entry_date' => date('Y-m-d'),
                        'warehouse_id' => request()->get('warehouse_id'),
                        'action_performed_by' => auth()->user()->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
                            //dd($attr);
                $done = ProductStock::insert($attr);
                if ($done) {
                    //Delete from Temporary stock
                    TemporaryStock::where('action_format', 'Spi')->where('spi_product_id', $request->spi_product_id)->delete() ?? null;
                    $this->ppiSpiStatusController->spiActionStatus([
                        'wh_id' => request()->get('warehouse_id'),
                        'spi_id' => $request->spi_id,
                        'action' => 'spi_product_out_from_stock',
                        'note' => 'Product ' . $this->Model('Product')::name($request->product_id),
                        'spi_product_id' => $request->spi_product_id,
                        'redirect' => false,
                    ]);
                }
                if($request->doTransfer){

                }else{
                    return redirect()->route('spi_edit', [request()->get('warehouse_code'), $request->spi_id])->with(['status' => '1', 'message' => 'Product stocked out successfully.']);
                }
            }//End if Else checkin
        }
    }


}
