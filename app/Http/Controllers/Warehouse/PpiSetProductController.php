<?php

namespace App\Http\Controllers\Warehouse;
use App\Http\Controllers\Warehouse\SingleWarehouseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PpiSetProduct;
use App\Http\Controllers\Warehouse\PpiSpiStatusController;
use Carbon\Carbon;
class PpiSetProductController extends SingleWarehouseController
{
    protected $model;
    /**
     * __construct
     *
     * @param  mixed $model
     * @return void
     */
    public function __construct(PpiSetProduct $model){
        parent::__construct();
        $this->model = $model;
    }

    /**
     * add
     *
     * @return void
     */
    public function add(){
        return true;
    }
    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request){
        // dd($request->for_create_set);
        $busketInfo = $this->ppi_spi_history->arrangePpiData($request->ppi_id);
        $attributes = [
            'set_name' => $request->set_name,
            'ppi_product_id' => implode(',', $request->for_create_set),
            'ppi_id' => $request->ppi_id,
            'warehouse_id' => request()->get('warehouse_id'),
            'action_performed_by' => auth()->user()->id,
        ];
        $ppiProduct = $this->model::create($attributes);

        //Status
        $doStatus = PpiSpiStatusController::ppiActionStatus([
            'wh_id' => request()->get('warehouse_id'),
            'ppi_id' => $request->ppi_id,
            'action' => 'ppi_set_created',
            'note' => 'Set Name: '.$request->set_name,
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

        return redirect()->route('ppi_edit', [$this->wh_code, $request->ppi_id])->with(['status' => 1, 'message' => 'Successfully Set Created']);
    }

    /**
     * destroy
     *
     * @return void
     */
    public function destroy($wh_code, $id){
        //dd('set');
        $data = $this->model::find($id);
        $ppi_id = $data->ppi_id;
        $busketInfo = $this->ppi_spi_history->arrangePpiData($data->ppi_id);
        $data->delete();

        //Status
        $doStatus = PpiSpiStatusController::ppiActionStatus([
            'wh_id' => request()->get('warehouse_id'),
            'ppi_id' => $data->ppi_id,
            'action' => 'ppi_set_deleted',
            'note' => 'Set Name: '.$data->set_name,
            'redirect' => false,
            'get_status_data' => true,
        ]);

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
        return redirect()->route('ppi_edit', [$this->wh_code,  $ppi_id])->with(['status' => 0, 'message' => 'Successfully deleted']);
    }


    /**
     * destroyProductFromSet
     *
     * @return void
     */
    public function destroyProductFromSet($wh_code, $set_id, $ppi_product_id){
        //dd($product_id);
        $data = $this->model::find($set_id);
        $busketInfo = $this->ppi_spi_history->arrangePpiData($data->ppi_id);
        $product_id = $ppi_product_id;
        if($data){
            $items = [];
            $ppiProduct  = explode(',', $data->ppi_product_id);
            foreach($ppiProduct as $item){
                if($item == $product_id){

                }else {
                    $items []= $item;
                }
            }
            $data->ppi_product_id = implode(',', $items);
            $data->save();

            //Status
            $doStatus = PpiSpiStatusController::ppiActionStatus([
                'wh_id' => request()->get('warehouse_id'),
                'ppi_id' => $data->ppi_id,
                'action' => 'ppi_product_remove_from_set',
                'ppi_product_id' => $ppi_product_id,
                'note' => 'Product: '.$this->Model('PpiProduct')::ppiProductInfoByPpiProductId($ppi_product_id, ['column' => 'product_name']).'. Set Name: '.$data->set_name,
                'redirect' => false,
                'get_status_data' => true,
            ]);

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
            /** If set product is empty tha set auto delete*/
            if($data->ppi_product_id == ""){
                $this->destroy(request()->get('warehouse_code'), $data->id);
            }


            return redirect()->back()->with(['status' => 1, 'message' => 'Delete product from Set successfully']);
        } else {
            return redirect()->back()->with(['status' => 0, 'message' => 'Error']);
        }
    }//End


}
