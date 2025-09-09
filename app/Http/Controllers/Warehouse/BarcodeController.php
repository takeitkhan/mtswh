<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Warehouse\SingleWarehouseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PpiProduct;
use App\Models\SpiProduct;
use DB;

class BarcodeController extends SingleWarehouseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function indexOf(Request $request)
    {
        if ($request->submit) {
            //$barcode = DNS1D::getBarcodeHTML('4445645656', 'PHARMA2T', 3, 33, 'green', true);
            return view('admin.pages.warehouse.single.ppi.barcode')->with(['pid' => $request->product_code]);
        } else {
            return view('admin.pages.warehouse.single.ppi.barcode');
        }

    }

    /**
     * PPI Line Item
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function getLineItem(Request $request)
    {
        $ppiProductId = $request->id;
        $ppiProduct = PpiProduct::find($ppiProductId);
        $ppiId = $ppiProduct->ppi_id;
        $product = $this->Query::accessModel('PpiProduct')::where('id', $ppiProductId)->first();
        $productBarcodeFormat = $this->Query::accessModel('Product')::getColumn($product->product_id, 'barcode_format');
        $productBarcodePrefix = $this->Query::accessModel('Product')::getColumn($product->product_id, 'barcode_prefix');
        $productUniqueKey = $this->Query::accessModel('Product')::getColumn($product->product_id, 'unique_key');

        //dd($productBarcodePrefix);
        /** Set Product */
        $setProduct = null;
        if($request->get('set-name')){
            $setProduct = $request->get('set-name');
        }
        /** Bundle Product */
        $bundleProduct = null;
        if($request->get('bundle')){

            //dd($ppiProductId);
           //$bundleSizes = explode('|', $request->get('bundle'));
           $bundleSizes = $this->Model('PpiBundleProduct')::where('ppi_product_id', $ppiProductId)->get()->toArray();
           $bundleProduct = $bundleSizes;
           $arr1 = $product->toArray();
           $arr2 = ['qty' => count($bundleSizes)];
           $bundes = array_merge($arr1, $arr2);
           $product = (object) $bundes;
           $totalRow =  $product->qty;
           $productBarcodeFormat = 'Bundle-Tag';
        }
        /** ENd */


        /** Qty  */
        if($productBarcodeFormat == 'Without-Tag'){
            $lineItemQty = $product->qty;
            $totalItemQty = 1;
            $totalRow = $totalRow ?? 1;
        }elseif($productBarcodeFormat == 'Tag'){
            $lineItemQty = 1;
            $totalItemQty = $product->qty;
            $totalRow = $totalRow ?? $product->qty;
        }elseif($productBarcodeFormat == 'Bundle-Tag'){
            $lineItemQty = 1;
            $totalItemQty = $product->qty;
            $totalRow = $totalRow ?? $product->qty;
        }
        //dd($bundleProduct);
        //dd($product);
        return view('admin.pages.warehouse.single.ppi.barcode')->with(
            [
                'product' => $product,
                'barcode_format' => $productBarcodeFormat,
                'barcode_prefix' => $productBarcodePrefix,
                'unique_key' => $productUniqueKey,
                'ppi_product' => $ppiProduct,
                'ppi_product_id' => $ppiProductId,
                'ppi_id'    => $ppiId,
                'set_product'  => $setProduct,
                'bundle_product' => $bundleProduct,
                'line_item_qty' => $lineItemQty,
                'total_item_qty' => $totalItemQty,
                'total_row' => $totalRow,
            ]
        );
    }



    /**
     * SPI LIne Item
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function getSpiLineItem(Request $request)
    {
//        dd($request->id);
        $spiProductId = $request->id;
        $spiProduct = SpiProduct::find($spiProductId);
//        dd($spiProduct);
        $spiId = $spiProduct->spi_id;
        $spi = $this->Model('PpiSpi')::where('id', $spiProduct->spi_id)->first();
        $product = $spiProduct;
        $productUniqueKey = $this->Model('Product')::getColumn($product->product_id, 'unique_key');
        $barcode_format = $this->Model('Product')::getColumn($product->product_id, 'barcode_format');
        if(!empty($product->bundle_id)){
            $barcode_format = 'Bundle-Tag';
        }

        //dd($produc)
        $getLineItem =  [
            'ppi_spi_product_id' => $product->ppi_product_id,
            'ppi_id' => $product->ppi_id,
            'bundle_id' => $product->bundle_id,
            'product_id' => $product->product_id,
            'data' => true,
        ];


       $checkAlreadyStockOut = $this->Model('PpiSpiStatus')::where('ppi_spi_id', $product->spi_id)
                                    ->where('status_for', 'Spi')
                                    ->where('ppi_spi_product_id', $spiProductId)
                                    ->where('code', 'spi_product_out_from_stock')
                                    ->first();

        $alreadyStockOut = $this->Model('ProductStock')::where('stock_action', 'Out')
                                        ->where('action_format', 'Spi')
                                        ->where('product_id', $product->product_id)
                                        ->where('bundle_id', $product->bundle_id)
                                        ->where('warehouse_id', $product->from_warehouse)
                                        ->pluck('original_barcode')
                                        ->toArray();


        if($checkAlreadyStockOut){
            $getLineItem = $this->Model('ProductStock')::where('action_format', 'Spi')
                ->where('bundle_id', $product->bundle_id)
                ->where('product_id', $product->product_id)
                ->where('stock_action', 'Out')
                ->where('action_format', 'Spi')
                ->where('ppi_spi_product_id', $spiProductId)
                ->orderBy('id', 'desc')
                ->where('warehouse_id', $product->from_warehouse)
                ->take($product->qty);
            $getLineItem = $getLineItem->whereIn('original_barcode', $alreadyStockOut);
        }else{
            $getLineItem = $this->Model('ProductStock')::where('action_format', 'Ppi')
                        ->where('bundle_id', $product->bundle_id)
                        ->where('product_id', $product->product_id)
                        ->where('stock_action', 'In')
                        ->where('action_format', 'Ppi')
                        //->where('ppi_spi_product_id', $product->ppi_product_id)
                        ->orderBy('id', 'desc')
                        ->where('warehouse_id', $product->from_warehouse)
                        ->take($product->qty);
            if($barcode_format == 'Without-Tag'){
                $getLineItem= $getLineItem->take(1);
            }else {
                $getLineItem = $getLineItem->whereNotIn('original_barcode', $alreadyStockOut);
            }
        }

        $getLineItem = $getLineItem->get();

        $datas =  [
            'product' => $product,
            'getLineItem' => $getLineItem,
            'spi_id'    => $spiId,
            'bundle_product' => $product->bundle_id,
            'unique_key' => $productUniqueKey,
            'barcode_format' => $barcode_format,
            'spi_product_id' => $spiProductId,
            'spi' => $spi
        ];
        if($request->doTransfer){ // if call from transferController -> spiTransfer
            return $datas;
        }
        //dump($getLineItem);
//        dump($alreadyStockOut);
//        die();
        //dd($product->from_warehouse);
//        dd($datas);
        return view(    'admin.pages.warehouse.single.spi.validation')->with($datas);

        exit();

        /*
        $product = $this->Model('SpiProduct')::where('id', $spiProductId)->first();
        $productBarcodeFormat = $this->Model('Product')::getColumn($product->product_id, 'barcode_format');
        $productBarcodePrefix = $this->Model('Product')::getColumn($product->product_id, 'barcode_prefix');
        $productUniqueKey = $this->Model('Product')::getColumn($product->product_id, 'unique_key');


        // Set Product
        $setProduct = null;
        if($request->get('set-name')){
            $setProduct = $request->get('set-name');
        }
        // Bundle Product
        $bundleProduct = null;
        if($request->get('bundle')){

            //dd($ppiProductId);
            //$bundleSizes = explode('|', $request->get('bundle'));
            $bundleId = $request->get('bundle');
            $bundleSizes = $this->Model('PpiBundleProduct')::where('id', $bundleId)->get()->toArray();
            $bundleProduct = $bundleSizes;
            $arr1 = $product->toArray();
            $arr2 = ['qty' => count($bundleSizes)];
            $bundes = array_merge($arr1, $arr2);
            $product = (object) $bundes;
            $totalRow =  $product->qty;
            $productBarcodeFormat = 'Bundle-Tag';
        }
        // ENd


        // Qty
        if($productBarcodeFormat == 'Without-Tag'){
            $lineItemQty = $product->qty;
            $totalItemQty = 1;
            $totalRow = $totalRow ?? 1;
        }elseif($productBarcodeFormat == 'Tag'){
            $lineItemQty = 1;
            $totalItemQty = $product->qty;
            $totalRow = $totalRow ?? $product->qty;
        }elseif($productBarcodeFormat == 'Bundle-Tag'){
            $lineItemQty = 1;
            $totalItemQty = $product->qty;
            $totalRow = $totalRow ?? $product->qty;
        }
        //dd($bundleProduct);
        //dd($product);
        return view('admin.pages.warehouse.single.spi.validation')->with(
            [
                'product' => $product,
                'barcode_format' => $productBarcodeFormat,
                'barcode_prefix' => $productBarcodePrefix,
                'unique_key' => $productUniqueKey,
                'spi_product' => $spiProduct,
                'spi_product_id' => $spiProductId,
                'spi_id'    => $spiId,
                'set_product'  => $setProduct,
                'bundle_product' => $bundleProduct,
                'line_item_qty' => $lineItemQty,
                'total_item_qty' => $totalItemQty,
                'total_row' => $totalRow,
            ]
        );
        */
    }

}
