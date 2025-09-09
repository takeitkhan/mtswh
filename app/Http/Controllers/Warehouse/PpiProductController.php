<?php

namespace App\Http\Controllers\Warehouse;
use App\Http\Controllers\Warehouse\SingleWarehouseController;
use App\Http\Controllers\Warehouse\PpiController;
use App\Http\Controllers\Controller;
use App\Models\PpiSpiDispute;
use Illuminate\Http\Request;
use App\Models\PpiSpi;
use App\Models\PpiProduct;
use App\Models\PpiBundleProduct;
use App\Http\Controllers\Warehouse\PpiSpiStatusController;
use Carbon\Carbon;
use App\Models\TemporaryStock;

class PpiProductController extends SingleWarehouseController
{
    protected $model;
    protected $ppiController;
    protected $ppiSpiStatusController;
    protected $temporaryStock;

    /**
     * __construct
     *
     * @param  mixed $model
     * @return void
     */
    public function __construct(PpiProduct $model, PpiController $ppiController, PpiSpiStatusController $ppiSpiStatusController, TemporaryStock $temporaryStock){
        parent::__construct();
        $this->model = $model;
        $this->ppiController = $ppiController;
        $this->ppiSpiStatusController = $ppiSpiStatusController;
        $this->temporaryStock = $temporaryStock;
    }

    public function add(){
        //dd(request()->get('hasPermission'));
        return true;
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request){
//        dd($request->all());
//        $attributes = [];
        $bundleSizes = [];
        $busketInfo = $this->ppi_spi_history->arrangePpiData($request->ppi_id);
        $saveForTemporaryStock = [];
        foreach($request->product_id as $key => $product){
            $ppi_product = new $this->model();
            $ppi_product->ppi_id = $request->ppi_id;
            $ppi_product->product_id = $request->product_id[$key];
            $ppi_product->qty =  $request->qty[$key] ?? 0;
            $ppi_product->unit_price =  $request->unit_price[$key] ?? 0;
            $ppi_product->price =  $request->price[$key];
            $ppi_product->warehouse_id = request()->get('warehouse_id');
            $ppi_product->product_state = $request->product_state[$key];
            $ppi_product->health_status = $request->health_status[$key];
            $ppi_product->note = $request->note[$key];
            $ppi_product->action_performed_by = auth()->user()->id;
            $ppi_product->save();

            //Store data for temporary stock
            $saveForTemporaryStock []= [
                'action_format' => 'Ppi',
                'product_id' => $request->product_id[$key],
                'ppi_spi_id' =>  $request->ppi_id,
                'ppi_product_id' => $ppi_product->id,
                'spi_product_id' => null,
                'waiting_stock_in' => $request->qty[$key] ?? 0,
                'waiting_stock_out' => 0,
                'warehouse_id' => request()->get('warehouse_id'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
            //If Bundle
//            dump($request->bundle_size );
            //if($request->bundle_size && array_key_exists($request->product_id[$key] , $request->bundle_size)){
            if($request->bundle_size && array_key_exists($request->row_id[$key] , $request->bundle_size)){
//                foreach($request->bundle_size[$request->product_id[$key]] as $index => $bundle){
//                dump($request->bundle_size);
                foreach($request->bundle_size[$request->row_id[$key]] as $index => $bundle){
                    if($bundle > 0){
                        $ppi_product_bundle = new PpiBundleProduct();
                        $ppi_product_bundle->ppi_id = $request->ppi_id;
                        $ppi_product_bundle->ppi_product_id = $ppi_product->id;
                        $ppi_product_bundle->product_id = $ppi_product->product_id;
                        $ppi_product_bundle->warehouse_id = request()->get('warehouse_id');
                        $ppi_product_bundle->bundle_name = $ppi_product->product_id.$ppi_product->id.'_'.$bundle;
                        $ppi_product_bundle->bundle_size = $bundle;
//                        $ppi_product_bundle->bundle_price = $request->bundle_price[$request->product_idproduct_id[$key]][$index];
                        $ppi_product_bundle->bundle_price = $request->bundle_price[$request->row_id[$key]][$index];
                        $ppi_product_bundle->action_performed_by = auth()->user()->id;
                        $ppi_product_bundle->save();
                    }
                }
            }// End 2n

            //Insert data for temporary stock
            $this->temporaryStock->insert($saveForTemporaryStock);

            $doStatus =   $this->ppiSpiStatusController->ppiActionStatus([
                'wh_id' => request()->get('warehouse_id'),
                'ppi_id' => $request->ppi_id,
                'action' => 'ppi_product_added',
                'ppi_product_id' => $ppi_product->id,
                'note' => 'Product: '.PpiProduct::ppiProductInfoByPpiProductId($ppi_product->id, ['column' => 'product_name']),
                'redirect' => false,
                'get_status_data' => true,
            ]);

            // History Create
            $status_id = $doStatus->id;
            $newInfo = $this->ppi_spi_history->arrangePpiData($request->ppi_id);
            $this->ppi_spi_history->createHistory([
                'ppi_spi_id' => $request->ppi_id,
                'action_format' => 'Ppi',
                'chunck_old_data' => $busketInfo,
                'chunck_new_data' => $newInfo,
                'status_id' => $status_id,
            ]);
            //End
        }

        return redirect()->back()->with(['status' => 1, 'message' => 'Successfully product added']);
    }


    /**
     * edit
     *
     * @param  mixed $wh_code
     * @param  mixed $id
     * @return void
     */
    public function edit($wh_code, $id){
        $ppiEditProduct = $this->model::find($id);
        $ppiEditProductBundle = PpiBundleProduct::where('ppi_product_id', $id)->get();
        $ppi = PpiSpi::find($ppiEditProduct->ppi_id);
        return view('admin.pages.warehouse.single.ppi.form', ['ppi' => $ppi, 'ppiEditProduct' => $ppiEditProduct, 'ppiEditProductBundle' => $ppiEditProductBundle]);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @return void
     */
    public function update(Request $request){
//         dd($request->all());
        $attributes = [];
        $done = null;
        $busketInfo = $this->ppi_spi_history->arrangePpiData($request->ppi_id);
        foreach($request->product_id as $key => $product){
            $ppi_product = $this->model::find($request->ppi_product_id);
            //Prev Data
            $prevDataCollect =  $this->model::find($request->ppi_product_id);
            //Edit Actin
            $ppi_product->ppi_id = $request->ppi_id;
            $ppi_product->product_id = $request->product_id[$key];
            $ppi_product->qty =  $request->qty[$key] ?? 0;
            $ppi_product->unit_price =  $request->unit_price[$key] ?? 0;
            $ppi_product->price =  $request->price[$key];
            $ppi_product->warehouse_id = request()->get('warehouse_id');
            $ppi_product->product_state = $request->product_state[$key];
            $ppi_product->health_status = $request->health_status[$key];
            $ppi_product->note = $request->note[$key];
            $ppi_product->action_performed_by = auth()->user()->id;
            $done = $ppi_product->save();

            //Update Temporary Stock
            $updateTemporaryStock = $this->temporaryStock->where('action_format', 'Ppi')->where('ppi_product_id', $request->ppi_product_id)->first();
            if($updateTemporaryStock) {
                $updateTemporaryStock->update([
                    'product_id' => $request->product_id[$key],
                    'waiting_stock_in' => $request->qty[$key] ?? 0,
                ]);
            }else {
                $saveForTemporaryStock = [
                    'action_format' => 'Ppi',
                    'product_id' => $request->product_id[$key],
                    'ppi_spi_id' =>  $request->ppi_id,
                    'ppi_product_id' => $request->ppi_product_id,
                    'spi_product_id' => null,
                    'waiting_stock_in' => $request->qty[$key] ?? 0,
                    'waiting_stock_out' => 0,
                    'warehouse_id' => request()->get('warehouse_id'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
                $this->temporaryStock->create($saveForTemporaryStock);
            }

            //Bundle Size
            //dd($done);
            PpiBundleProduct::where('ppi_product_id', $request->ppi_product_id)->delete();
//            if($request->bundle_size && array_key_exists($request->product_id[$key] , $request->bundle_size)){
            //dd(array_key_exists($request->row_id[$key] , $request->bundle_size));
            if($request->bundle_size  && array_key_exists($request->row_id[$key] , $request->bundle_size)){
                //dd(is_array($request->bundle_size[$request->row_id[$key]]));
                if(is_array($request->bundle_size[$request->row_id[$key]])) {
//                foreach($request->bundle_size[$request->product_id[$key]] as $index => $bundle){
                    foreach ($request->bundle_size[$request->row_id[$key]] as $index => $bundle) {
                        if ($bundle > 0) {
                            $ppi_product_bundle = new PpiBundleProduct();
                            $ppi_product_bundle->ppi_id = $request->ppi_id;
                            $ppi_product_bundle->ppi_product_id = $request->ppi_product_id;
                            $ppi_product_bundle->product_id = $request->product_id[$key];
                            $ppi_product_bundle->warehouse_id = request()->get('warehouse_id');
                            $ppi_product_bundle->bundle_name = $request->product_id[$key] . $request->ppi_product_id . '_' . $bundle;
                            $ppi_product_bundle->bundle_size = $bundle;
                            $ppi_product_bundle->bundle_price = $request->bundle_price[$request->row_id[$key]][$index];
                            $ppi_product_bundle->action_performed_by = auth()->user()->id;
                            $done = $ppi_product_bundle->save();
                        }
                    }
                }
            }// End BUndle
        }
        if($done){
          $doStatus =  $this->ppiSpiStatusController->ppiActionStatus([
                'wh_id' => request()->get('warehouse_id'),
                'ppi_id' => $request->ppi_id,
                'action' => 'ppi_product_edited',
                'ppi_product_id' => $request->ppi_product_id,
                'note' => 'with '.PpiProduct::ppiProductInfoByPpiProductId($request->ppi_product_id, ['column' => 'product_name']),
                'redirect' => false,
                'get_status_data' => true,
            ]);
        }
        // History Create
        $status_id = $doStatus->id;
        $newInfo = $this->ppi_spi_history->arrangePpiData($request->ppi_id);
        $this->ppi_spi_history->createHistory([
            'ppi_spi_id' => $request->ppi_id,
            'action_format' => 'Ppi',
            'chunck_old_data' => $busketInfo,
            'chunck_new_data' => $newInfo,
            'status_id' => $status_id,
        ]);
        //End
        $ppi = PpiSpi::find($request->ppi_id);
        return redirect()->route('ppi_edit', [$this->wh_code, $ppi->id])->with(['status' => 1, 'message' => 'Successfully product updated']);
    }

    /**
     * destroy
     *
     * @return void
     */
    public function destroy($wh_code, $id){
        //dd('ok');
        //dd(PpiProduct::ppiProductInfoByPpiProductId($id, ['column' => 'product_name']));
        $data = $this->model::find($id);
        $busketInfo = $this->ppi_spi_history->arrangePpiData($data->ppi_id);
        $productName = PpiProduct::ppiProductInfoByPpiProductId($id, ['column' => 'product_name']);
        $done = $data->delete();


        /** Delete from Dispute */
        $this->Model('PpiSpiDispute')::where('ppi_spi_id', $data->ppi_id)->where('ppi_spi_product_id', $id)->delete() ?? false;


        // Delete From Temporary STock
            TemporaryStock::where('action_format', 'Ppi')->where('ppi_product_id', $id)->delete() ?? null;

        /***
         * PPI Product STock Delete
         */
        $checkStock = $this->Model('ProductStock')::where('ppi_spi_id', $data->ppi_id)->where('ppi_spi_product_id', $id)
                                    ->where('action_format', 'Ppi')->delete();
        //$done = true;
        if($done){
            $doStatus =  $this->ppiSpiStatusController->ppiActionStatus([
                'wh_id' => request()->get('warehouse_id'),
                'ppi_id' => $data->ppi_id,
                'action' => 'ppi_product_deleted',
                'note' => 'Product: '.$productName,
                'ppi_product_id' => $id,
                'redirect' => false,
                'get_status_data' => true,
            ]);
        }

        // History Create
        $status_id = $doStatus->id;
        $newInfo = $this->ppi_spi_history->arrangePpiData($data->ppi_id);
        $this->ppi_spi_history->createHistory([
            'ppi_spi_id' => $data->ppi_id,
            'action_format' => 'Ppi',
            'chunck_old_data' => $busketInfo,
            'chunck_new_data' => $newInfo,
            'status_id' => $status_id,
        ]);
        //End

        return redirect()->back()->with(['status' => 0, 'message' => 'Successfully deleted']);
    }

    /**
     * Import Product from Another PPI
     * @param Request $request
     * @return void
     */

    public function importProductFromAnotherPpi(Request $request){
//        dd($request->all());
        $fromPpiId = $request->from_ppi_id;
        $toPpiId = $request->to_ppi_id;
        $busketInfo = $this->ppi_spi_history->arrangePpiData($toPpiId);

        $fromPpiProduct = $this->model::where('ppi_id', $fromPpiId)->get()->toArray();
        $newData = [
            'ppi_id' => $toPpiId,
            'action_performed_by' => auth()->user()->id,
            'created_at'=> Carbon::now(),
            'updated_at'=> Carbon::now(),
        ];
        $ppiSetProduct = [];
        $productName =[];
        if(count($fromPpiProduct) > 0) {
            foreach ($fromPpiProduct as $p) {
                $newp = array_merge($p, $newData);
                $ppi_product = $this->model::create($newp);
                $productName [] = PpiProduct::ppiProductInfoByPpiProductId($ppi_product->id, ['column' => 'product_name']);

                //checkBundle
                $checkBundle = PpiBundleProduct::where('ppi_id', $fromPpiId)->where('ppi_product_id', $p['id'])->get()->toArray();
                if (count($checkBundle) > 0) {
                    foreach ($checkBundle as $bundle) {
                        $newBundle = [
                            'ppi_id' => $toPpiId,
                            'ppi_product_id' => $ppi_product->id,
                            'bundle_name' => $bundle['product_id'] . $ppi_product->id . '_' . $bundle['bundle_size'],
                            'action_performed_by' => auth()->user()->id,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ];
                        $nbMerge = array_replace($bundle, $newBundle);
                        PpiBundleProduct::create($nbMerge);
                    }
                }
            }

            $productName = implode('<br/>', $productName);

            //Create Status
            $doStatus = $this->ppiSpiStatusController->ppiActionStatus([
                'wh_id' => request()->get('warehouse_id'),
                'ppi_id' => $toPpiId,
                'action' => 'ppi_product_added',
                'ppi_product_id' => null,
                'note' => 'Product imported from PPI ID ' . $fromPpiId . '<br/>' . $productName,
                'redirect' => false,
                'get_status_data' => true,
            ]);

            // History Create
            $status_id = $doStatus->id;
            $newInfo = $this->ppi_spi_history->arrangePpiData($toPpiId);
            $this->ppi_spi_history->createHistory([
                'ppi_spi_id' => $toPpiId,
                'action_format' => 'Ppi',
                'chunck_old_data' => $busketInfo,
                'chunck_new_data' => $newInfo,
                'status_id' => $status_id,
            ]);
            //End
            return redirect()->back()->with(['status' => 1, 'message' => 'Successfully Product imported']);
        }else{
            return redirect()->back()->with(['status' => 1, 'message' => 'Selected PPI has no product']);
        }

    }


}
