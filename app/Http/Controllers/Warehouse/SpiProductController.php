<?php

namespace App\Http\Controllers\Warehouse;
use App\Http\Controllers\Warehouse\SingleWarehouseController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Warehouse\SpiController;
use App\Models\SpiProduct;
use App\Models\PpiSpi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\TemporaryStock;
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
        //dd($request->all());
        $products = $request->product;
//        dd($products);
        $busketInfo = $this->ppi_spi_history->arrangeSpiData($request->spi_id);
        $saveForTemporaryStock = [];
        foreach($products as $key => $product){
            $attr =  [
                'spi_id' => $request->spi_id,
                'warehouse_id' => request()->get('warehouse_id'),
                'from_warehouse' => $product['from_warehouse'],
                'product_id' => $product['product_id'],
                'ppi_product_id' => $product['ppi_product_id'],
                'ppi_id' => $product['ppi_id'],
                'bundle_id' => $product['bundle_id'] ?? null,
                'qty' => $product['qty'],
                'unit_price' => $product['unit_price'],
//                'price' => $product['price'],
                'price' => $product['qty']*$product['unit_price'],
                'note' => $product['note'],
                'action_performed_by' => auth()->user()->id,
                'any_warning_cls' => null,
            ];
//            dd($attr);
            $spi_product = $this->model::create($attr);

            //Store data for temporary stock
            $saveForTemporaryStock []= [
                'action_format' => 'Spi',
                'product_id' => $product['product_id'],
                'ppi_spi_id' =>  $request->spi_id,
                'ppi_product_id' => null,
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

            //check Land From Project
            if(!empty($product['landed_project'] && $product['originalProject'] && $product['landed_project']  != $product['originalProject'])){
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
            $status_id = $doStatus->id;
            $newInfo = $this->ppi_spi_history->arrangeSpiData($request->spi_id);
            $this->ppi_spi_history->createHistory([
                'ppi_spi_id' => $request->spi_id,
                'action_format' => 'Spi',
                'chunck_old_data' => $busketInfo,
                'chunck_new_data' => $newInfo,
                'status_id' => $status_id,
            ]);
        }
//        dd($attr);
        TemporaryStock::insert($saveForTemporaryStock);

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
        $spiEditProduct = $this->model::find($id);
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
//         dd($request->all());
        $products = $request->product;
        $busketInfo = $this->ppi_spi_history->arrangeSpiData($request->spi_id);
//        dd($products);
        foreach($products as $key => $product){
            $attr =  [
                'spi_id' => $request->spi_id,
                'warehouse_id' => request()->get('warehouse_id'),
                'from_warehouse' => $product['from_warehouse'],
                'product_id' => $product['product_id'],
                'ppi_product_id' => $product['ppi_product_id'],
                'ppi_id' => $product['ppi_id'],
                'bundle_id' => $product['bundle_id'] ?? null,
                'qty' => $product['qty'],
                'unit_price' => $product['unit_price'],
//                'price' => $product['price'],
                'price' => $product['qty']*$product['unit_price'],
                'note' => $product['note'],
                'action_performed_by' => auth()->user()->id,
                'any_warning_cls' => null,
            ];
//            dd($attr);
            $this->model::where('id', $request->spi_product_id)->update($attr);


            //check Land From Project
            $checklend = $this->Model('SpiProductLoanFromProject')::where('spi_id', $request->spi_id)->where('spi_product_id', $request->spi_product_id)->delete() ?? false;
            if(!empty($product['landed_project'] && $product['originalProject'] && $product['landed_project']  != $product['originalProject'])){
                $ProjectLendedData = [
                    'spi_id' =>  $request->spi_id,
                    'spi_product_id' =>  $request->spi_product_id,
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
                        'spi_product_id' => $request->spi_product_id,
                        'note' => 'Product: ' . $this->Model('Product')::name($product['product_id']) . ' lended from ' . $product['landed_project'] . ' Project.',
                        'redirect' => false,
                        'get_status_data' => false,
                    ]);
                }
            }


            //Update Temporary Stock
            $updateTemporaryStock = TemporaryStock::where('action_format', 'Spi')->where('spi_product_id', $request->spi_product_id)->first();
            if($updateTemporaryStock) {
                $updateTemporaryStock->update([
                    'product_id' => $product['product_id'],
                    'waiting_stock_out' => $product['qty'] ?? 0,
                ]);
            }else {
                $saveForTemporaryStock = [
                    'action_format' => 'Spi',
                    'product_id' => $product['product_id'],
                    'ppi_spi_id' =>  $request->spi_id,
                    'ppi_product_id' => null,
                    'spi_product_id' => $request->spi_product_id,
                    'waiting_stock_in' => 0,
                    'waiting_stock_out' => $product['qty'] ?? 0,
                    'warehouse_id' => request()->get('warehouse_id'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
                TemporaryStock::create($saveForTemporaryStock);
            }

            $doStatus = $this->ppiSpiStatusController->spiActionStatus([
                'wh_id' => request()->get('warehouse_id'),
                'spi_id' => $request->spi_id,
                'action' => 'spi_product_edited',
                'spi_product_id' => $request->spi_product_id,
                'note' => 'Product: '.$this->Model('Product')::name($product['product_id']),
                'redirect' => false,
                'get_status_data' => true,
            ]);



            // History Create
            $status_id = $doStatus->id;
            $newInfo = $this->ppi_spi_history->arrangeSpiData($request->spi_id);
            $this->ppi_spi_history->createHistory([
                'ppi_spi_id' => $request->spi_id,
                'action_format' => 'Spi',
                'chunck_old_data' => $busketInfo,
                'chunck_new_data' => $newInfo,
                'status_id' => $status_id,
            ]);
        }

        $spi = PpiSpi::find($request->spi_id);
        return redirect()->route('spi_edit', [$this->wh_code, $spi->id])->with(['status' => 1, 'message' => 'Successfully product updated']);
    }

    /**
     * destroy
     *
     * @return void
     */
    public function destroy($wh_code, $id){
//        dd($id);

        $data = $this->model::find($id);
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
        }

        // History Create
        $status_id = $doStatus->id;
        $newInfo = $this->ppi_spi_history->arrangeSpiData($data->spi_id);
        $this->ppi_spi_history->createHistory([
            'ppi_spi_id' => $data->spi_id,
            'action_format' => 'Spi',
            'chunck_old_data' => $busketInfo,
            'chunck_new_data' => $newInfo,
            'status_id' => $status_id,
        ]);
        //End

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
