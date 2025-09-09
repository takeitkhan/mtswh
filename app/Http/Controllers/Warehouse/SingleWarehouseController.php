<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\PpiSpiHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Warehouse;
use App\Models\User;

class SingleWarehouseController extends Controller
{
    protected $wh_code;
    protected $ppi_spi_history;

    public function __construct(){
        $this->middleware('warehouse');
        $this->wh_code = request()->wh_code;
        $this->ppi_spi_history = new PpiSpiHistory();
    }

    /**
     * Dashboard
     */
    public function index(){
        try {
            // Your logic here
            return view('admin.pages.warehouse.single.dashboard');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /** Store  */
    public function store(Request $request){

    }
}
