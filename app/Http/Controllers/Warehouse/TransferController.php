<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Warehouse\PpiSpiStatusController;
use App\Models\ProductStock;
use App\Models\PurchaseVendor;
use App\Models\PpiProduct;
use App\Models\PpiSpi;
use App\Models\PpiSpiHistory;
use App\Models\PpiSpiSource;
use App\Models\SpiProduct;
use Illuminate\Http\Request;
use App\Http\Controllers\Warehouse\SingleWarehouseController;
use App\Http\Controllers\Warehouse\BarcodeController;
use App\Http\Controllers\Warehouse\ProductStockController;
use App\Http\Controllers\Warehouse\PpiController;
use Carbon\Carbon;
class TransferController  extends SingleWarehouseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function generateSpiTransfer(Request $request){
        $check = $this->Model('SpiTransfer')::where('spi_id', $request->spi_id)->first() ?? null;
        if(empty($check)){
            $this->Model('SpiTransfer')::create([
                'spi_id' => $request->spi_id,
                'from_warehouse_id' => $request->from_warehouse_id,
                'ppi_id' => null,
                'to_warehouse_id' => $request->to_warehouse_id,
            ]);
        }
        (new PpiSpiStatusController)->spiActionStatus([
            'wh_id' => request()->get('warehouse_id'),
            'spi_id' => $request->spi_id,
            'action' => 'spi_sent_to_boss',
            'note' => '',
            'spi_product_id' => null,
            'redirect' => false,
        ]);
        (new PpiSpiStatusController)->spiActionStatus([
            'wh_id' => request()->get('warehouse_id'),
            'spi_id' => $request->spi_id,
            'action' => 'spi_sent_to_wh_manager',
            'note' => '',
            'spi_product_id' => null,
            'redirect' => false,
        ]);

        return redirect()->route('spi_edit', [$request->get('warehouse_code'), $request->spi_id]);
    }

    public function spiTransfer(Request $request){

//        dd($request->all());
        $spiProduct = SpiProduct::where('spi_id', $request->spi_id)->get();
//        dd($spiProduct);
//        $datas = [];
        $makeForPpiProduct = [];
        foreach($spiProduct as $product){
            $merge = [
                'id' => $product->id,
                'doTransfer' => true
            ];
            $makeForPpiProduct[$product->ppi_product_id]= $product;
            $datas []= (new BarcodeController)->getSpiLineItem($request->merge($merge));
        }
//        dd($makeForPpiProduct);
//        dd($datas);
//        $merge = [];
        foreach($datas as $data){
            $product = $data['product'];
            $getLineItem = $data['getLineItem'];
            $spi_id    = $data['spi_id'];
            $bundle_product = $data['bundle_product'];
            $unique_key = $data['unique_key'];
            $barcode_format = $data['barcode_format'];
            $spi_product_id = $data['spi_product_id'];
            if($product){
                $barcode_product_line_items = [];
                $barcode_product_unique_keys = [];
                $qtys = [];
                foreach ($getLineItem as $lineItem){

                    $barCodeDigit = $lineItem->barcode;

                    $orginalBarCodeDigit = $lineItem->original_barcode;

                    if($barcode_format == 'Without-Tag'){
                        $qty =  $product->qty;
                    }else{
                        $qty =  $lineItem->qty;
                    }

                    $barcode_product_line_items[]= $orginalBarCodeDigit;
                    $barcode_product_unique_keys[]= $barCodeDigit;
                    $qtys[] = $qty;

                }//End

                $merge =  [
                    'spi_id' => $product->spi_id,
                    'spi_product_id' => $product->id,
                    'product_id' => $product->product_id,
                    'product_unique_key' => $unique_key,
                    'warehouse_id' => $product->warehouse_id ,
                    'bundle_id' => $bundle_product ?? null,
                    'barcode_product_line_item' => $barcode_product_line_items,
                    'barcode_product_unique_key' => $barcode_product_unique_keys,
                    'qty' => $qtys,
                    'doTransfer' => true
                ];
                (new ProductStockController)->stockOut($request->merge($merge));
            }

        }
//        dd($merge);

        //Status Transfer
        (new PpiSpiStatusController)->spiActionStatus([
            'wh_id' => request()->get('warehouse_id'),
            'spi_id' => $request->spi_id,
            'action' => 'spi_transfer_complete',
            'note' => 'From ' . $this->Model('Warehouse')::name($request->from_warehouse_id).' To '.$this->Model('Warehouse')::name($request->to_warehouse_id),
            'spi_product_id' => null,
            'redirect' => false,
        ]);
        //Status Complete
        (new PpiSpiStatusController)->spiActionStatus([
            'wh_id' => request()->get('warehouse_id'),
            'spi_id' => $request->spi_id,
            'action' => 'spi_all_steps_complete',
            'note' => '',
            'spi_product_id' => null,
            'redirect' => false,
        ]);



        //make PPI
        $getSpi = $this->Model('PpiSpi')::where('id', $request->spi_id)->first();
        $getSpiSource = $this->Model('PpiSpiSource')::where('ppi_spi_id', $request->spi_id)->get();
        $makePpi = [
            'action_format' => 'Ppi',
            'ppi_spi_type' => $getSpi->ppi_spi_type,
            'project' => $getSpi->project,
            'tran_type' => $getSpi->tran_type,
            'note' => $getSpi->note,
            'transferable' => 'yes',
            'warehouse_id' => $request->to_warehouse_id,
            'action_performed_by' => auth()->user()->id,
        ];
        $ppi = PpiSpi::create($makePpi);
//        dd($getSpiSource);
        //make ppi SOurce
        $makePpiSources = $getSpiSource->map(function($va) use ($request, $ppi){
            $val = [
                'ppi_spi_id' => $ppi->id ?? null,
                'action_format' => 'Ppi',
                'source_type' => $va->source_type,
                'who_source' => $va->who_source,
                'who_source_id' => $va->who_source_id,
                'levels' => $va->levels,
                'warehouse_id' => $request->to_warehouse_id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            return $val;
        });

        $this->Model('PpiSpiSource')::insert($makePpiSources->toArray());

        /**Create Ppi Status */
        PpiSpiStatusController::ppiActionStatus([
            //'wh_id' => $this->wh_code,
            'ppi_id' => $ppi->id ?? null,
            'action' => 'ppi_created',
            'redirect' => false
        ]);

//        dd($makePpiSources->toArray());
        //

        //ready ppi product

        $saveForTemporaryStock = [];

        foreach($makeForPpiProduct as $index => $ppiProduct){
            $getPpiProduct = $this->Model('PpiProduct')::where('id', $index)->first();

            $createPpiProduct = [
                'ppi_id' => $ppi->id ,
                'warehouse_id' =>  $request->to_warehouse_id,
                'product_id' =>  $getPpiProduct->product_id,
                'unit_price' =>  $ppiProduct->unit_price,
                'qty' =>  $ppiProduct->qty,
                'price' =>  $ppiProduct->price,
                'product_state' =>  $getPpiProduct->product_state,
                'health_status' =>  $getPpiProduct->health_status,
                'note' =>  $ppiProduct->note,
                'action_performed_by' =>  auth()->user()->id,
            ];


            $ppiProductIDCreate = $this->Model('PpiProduct')::create($createPpiProduct);


            //Store data for temporary stock
            $saveForTemporaryStock []= [
                'action_format' => 'Ppi',
                'product_id' => $getPpiProduct->product_id,
                'ppi_spi_id' =>  $ppi->id,
                'ppi_product_id' => $ppiProductIDCreate->id,
                'spi_product_id' => null,
                'waiting_stock_in' => $ppiProduct->qty ?? 0,
                'waiting_stock_out' => 0,
                'warehouse_id' => request()->get('warehouse_id'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];


            if($getPpiProduct->product_state == 'Cut-Piece'){
                $newPpiProductId = $ppiProductIDCreate->id;
                $newbundleName = $ppiProduct->product_id.$newPpiProductId.'_'.$ppiProduct->qty;
                $oldbundleName = $ppiProduct->product_id.$ppiProduct->ppi_product_id.'_'.$ppiProduct->qty;
                $getBundle = $this->Model('PpiBundleProduct')::where('ppi_product_id', $index)->where('bundle_name', $oldbundleName)->first();
//                dd($bundleName);
                $makeBundle = [
                    'ppi_id' => $ppi->id ,
                    'ppi_product_id' => $newPpiProductId,
                    'product_id' => $ppiProduct->product_id,
                    'warehouse_id' => $request->to_warehouse_id,
                    'bundle_name' => $newbundleName,
                    'bundle_size' => $ppiProduct->qty,
                    'bundle_price' => $getBundle->bundle_price,
                    'action_performed_by' => auth()->user()->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $this->Model('PpiBundleProduct')::create($makeBundle);

            }// End if

            //Insert data for temporary stock
            $this->Model('TemporaryStock')::insert($saveForTemporaryStock);

            $doStatus =   PpiSpiStatusController::ppiActionStatus([
                'wh_id' => $request->to_warehouse_id,
                'ppi_id' => $ppi->id,
                'action' => 'ppi_product_added',
                'ppi_product_id' => $ppiProductIDCreate->id,
                'note' => 'Product: '.PpiProduct::ppiProductInfoByPpiProductId($ppiProductIDCreate->id, ['column' => 'product_name']),
                'redirect' => false,
                'get_status_data' => true,
            ]);

            // History Create
            $status_id = $doStatus->id;
            $busketInfo = $this->ppi_spi_history->arrangePpiData($ppi->id);
            $newInfo = $this->ppi_spi_history->arrangePpiData($ppi->id);
            $this->ppi_spi_history->createHistory([
                'ppi_spi_id' => $ppi->id,
                'action_format' => 'Ppi',
                'chunck_old_data' => $busketInfo,
                'chunck_new_data' => $newInfo,
                'status_id' => $status_id,
            ]);
        }
        //End
        /**Create Ppi Status */
        PpiSpiStatusController::ppiActionStatus([
            //'wh_id' => $this->wh_code,
            'ppi_id' => $ppi->id ?? null,
            'action' => 'ppi_created_through_transfer',
            'note' =>  'From ' . $this->Model('Warehouse')::name($request->from_warehouse_id).' To '.$this->Model('Warehouse')::name($request->to_warehouse_id). ' Spi ID: '.$request->spi_id,
            'redirect' => false
        ]);
        $this->Model('SpiTransfer')::create([
            'spi_id' => $request->spi_id,
            'from_warehouse_id' => $request->from_warehouse_id,
            'ppi_id' => $ppi->id,
            'to_warehouse_id' => $request->to_warehouse_id,
        ]);

        return redirect()->route('spi_edit', [$request->get('warehouse_code'), $request->spi_id]);

    }




    //Vendor Purchase
    public function buyProductFromVendor(Request $request){
        $attr = [
            'spi_id' => $request->spi_id,
            'spi_product_id' => $request->spi_product_id,
            'vendor_id' => $request->vendor_id,
            'vendor_name' => $request->vendor_name,
            'qty' => $request->qty,
            'price' => $request->price,
            'product_id' => $request->product_id,
            'warehouse_id' => request()->get('warehouse_id'),
            'action_performed_by' => auth()->user()->id,
        ];
//        dd($attr);
        $purchaseData = PurchaseVendor::create($attr);
        //Spi Source
        $getSpi = $this->Model('PpiSpi')::where('id', $request->spi_id)->first();
        $spiSources = PpiSpiSource::where('ppi_spi_id', $request->spi_id)->get();
        $spiProduct = SpiProduct::where('id', $request->spi_product_id)->first();
        //Make PPI
        $makePpi = [
            'action_format' => 'Ppi',
            'ppi_spi_type' => $getSpi->ppi_spi_type,
            'project' => $getSpi->project,
            'tran_type' => $getSpi->tran_type,
            'note' => null,
            'transferable' => null,
            'purchase' => 'yes',
            'warehouse_id' => request()->get('warehouse_id'),
            'action_performed_by' => auth()->user()->id,
        ];
        $ppi = PpiSpi::create($makePpi);


        $makePpiSource = [];
        foreach($spiSources as $item){
            $makePpiSource []= [
                'ppi_spi_id' => $ppi->id,
                'action_format' => 'Ppi',
                'who_source' => $item->who_source,
                'source_type' => $item->source_type,
                'who_source_id' => $item->who_source_id,
                'levels' => $item->level,
                'warehouse_id' => request()->get('warehouse_id'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        PpiSpiSource::insert($makePpiSource);


        $createPpiProduct = [
            'ppi_id' => $ppi->id ,
            'warehouse_id' =>  request()->get('warehouse_id'),
            'product_id' =>  $request->product_id,
            'unit_price' =>  $request->unit_price ?? 0,
            'qty' =>  $request->qty,
            'price' =>  ($request->price ?? 0)*$request->qty,
            'product_state' =>  'New',
            'health_status' =>  $request->health_status,
            'note' =>  null,
            'action_performed_by' =>  auth()->user()->id,
        ];

        $ppiProductIDCreate = $this->Model('PpiProduct')::create($createPpiProduct);

        /**Create Ppi Status */
        (new PpiSpiStatusController)->ppiActionStatus([
            //'wh_id' => $this->wh_code,
            'ppi_id' => $ppi->id ?? null,
            'action' => 'ppi_created',
            'redirect' => false
        ]);
        /**Create Ppi Status */
        (new PpiSpiStatusController)->ppiActionStatus([
            //'wh_id' => $this->wh_code,
            'ppi_id' => $ppi->id ?? null,
            'action' => 'ppi_created_through_purchase_from_vendor',
            'note' =>  '',
            'redirect' => false
        ]);
        $doStatus =  (new PpiSpiStatusController)->ppiActionStatus([
            'wh_id' => request()->get('warehouse_id'),
            'ppi_id' => $ppi->id,
            'action' => 'ppi_product_added',
            'ppi_product_id' => $ppiProductIDCreate->id,
            'note' => 'Product: '.PpiProduct::ppiProductInfoByPpiProductId($ppiProductIDCreate->id, ['column' => 'product_name']),
            'redirect' => false,
            'get_status_data' => true,
        ]);

        //stock In
        $stockedProduct = ProductStock::where('action_format', 'Ppi')->where('ppi_spi_product_id', $spiProduct->ppi_product_id)->first()->toArray();
        $restock = [
            'ppi_spi_id' => $ppi->id,
            'action_format' => 'Ppi',
            'ppi_spi_product_id' => $ppiProductIDCreate->id,
            'product_id' =>$stockedProduct['product_id'],
            'bundle_id' => NULL,
            'barcode' => $stockedProduct['product_unique_key'].$ppiProductIDCreate->id.'0',
            'original_barcode' => $ppiProductIDCreate->id.'0',
            'product_unique_key' => $stockedProduct['product_unique_key'],
            'stock_action' => 'In',
            'stock_type' => 'Purchase',
            'qty' => $request->qty,
            'entry_date' => date('Y-m-d'),
            'warehouse_id' => request()->get('warehouse_id'),
            'action_performed_by' => auth()->user()->id,
            'note' => null,
        ];

        $doStock = ProductStock::create($restock);


        (new PpiSpiStatusController)->ppiActionStatus([
            'wh_id' => request()->get('warehouse_id'),
            'ppi_id' => $ppi->id,
            'action' => 'ppi_new_product_added_to_stock',
            'note' => 'Product ' . PpiProduct::ppiProductInfoByPpiProductId($ppiProductIDCreate->id, ['column' => 'product_name']),
            'ppi_product_id' => $ppiProductIDCreate->id,
            'redirect' => false,
        ]);

        (new PpiSpiStatusController)->ppiActionStatus([
             //'wh_id' => $this->wh_code,
             'ppi_id' => $ppi->id ?? null,
             'action' => 'ppi_all_steps_complete',
             'note' =>  '',
             'redirect' => false
         ]);

        //Update Purchase Data
        PurchaseVendor::where('id', $purchaseData->id)->update([
            'create_ppi_id' =>$ppi->id,
            'create_ppi_product_id' =>$ppiProductIDCreate->id,
        ]);

        (new PpiSpiStatusController)->spiActionStatus([
            'wh_id' => request()->get('warehouse_id'),
            'spi_id' => $request->spi_id,
            'action' => 'purchase_from_vendor',
            'note' => "{$request->qty} {$request->unit} of {$request->product_name} have been purchased from {$request->vendor_name}. Ppi Create ID: {$ppi->id}",
            'spi_product_id' => null,
            'redirect' => false,
        ]);

        return redirect()->route('spi_edit', [$request->get('warehouse_code'), $request->spi_id]);
    }
}
