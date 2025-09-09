<?php

namespace App\Http\Controllers\Warehouse;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Warehouse\SingleWarehouseController;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PpiSpiHistoryController extends SingleWarehouseController
{
    /**
     * @param PpiSpiHistory $model
     */

    public function history(Request $request){
        $id = $request->id;
        $history = $this->Model('PpiSpiHistory')::find($id);
        $this->grantPermission();
        return view('admin.pages.warehouse.single.ppi_spi_history')->with(['history' => $history]);
    }
}
