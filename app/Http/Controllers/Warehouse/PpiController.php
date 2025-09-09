<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Warehouse\PpiSpiStatusController;
use App\Http\Controllers\Warehouse\SingleWarehouseController;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\PpiSpi;
use App\Helpers\Warehouse\PpiSpiHelper;
use App\Models\PpiSpiStatus;
use App\Models\PpiSpiDispute;
use App\Models\GlobalSettings;
use DB;

class PpiController extends SingleWarehouseController
{
    protected $model;
    protected $ppiSpiStatusController;

    public function __construct()
    {
        parent::__construct();
        $this->model = new PpiSpi();
        $this->ppiSpiStatusController = new PpiSpiStatusController();
    }

    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        return view('admin.pages.warehouse.single.ppi.index');
    }

    /**
     * create
     *
     * @param mixed $request
     * @return void
     */
    public function create(Request $request)
    {
        return view('admin.pages.warehouse.single.ppi.form');
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
            'action_format' => 'Ppi',
            'ppi_spi_type' => $request->ppi_type,
            'project' => $request->project,
            'tran_type' => $request->tran_type,
            'note' => $request->note,
            'transferable' => $request->transferable ? 'yes' : null,
            'warehouse_id' => request()->get('warehouse_id'),
            'action_performed_by' => auth()->user()->id,
        ];
        //    dd($attributes);
        $ppi = $this->model::create($attributes);
        /** Source Store */
        $sources = [];
        foreach ($request->main_source as $data) {
            $exWhoSource = explode('|', $data['source']);
            $sources [] = [
                'ppi_spi_id' => $ppi->id,
                'action_format' => 'Ppi',
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
        try {
            /**Create Ppi Status */
            $this->ppiSpiStatusController->ppiActionStatus([
                //'wh_id' => $this->wh_code,
                'ppi_id' => $ppi->id,
                'action' => 'ppi_created',
                'redirect' => false
            ]);
            //End
            if($request->doTransfer) {

            } else {
                return redirect()->route('ppi_edit', [$this->wh_code, $ppi->id])->with(['status' => 1, 'message' => 'Successfully created']);
            }
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
        $ppi = $this->model::find($id);
        return view('admin.pages.warehouse.single.ppi.form', ['ppi' => $ppi]);
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
            PpiSpiStatus::where('status_for', 'Ppi')->where('ppi_spi_id', $id)->delete();
            $this->Model('ProductStock')::where('action_format', 'Ppi')->where('ppi_spi_id', $id)->delete();

            // Delete From Temporary STock
            $this->Model('TemporaryStock')::where('action_format', 'Ppi')->where('ppi_spi_id', $id)->delete() ?? null;

        }
        return redirect()->back()->with(['status' => 1, 'message' => 'Successfully deleted']);
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
        $route = auth()->user()->checkRoute([$role], 'ppi_index');

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
                DB::raw('(SELECT who_source FROM ppi_spi_sources WHERE ppi_spi_sources.ppi_spi_id =  ppi_spis.id ORDER BY ppi_spi_sources.id DESC LIMIT 1) AS root_source')
            )
            ->groupBy('ppi_spis.id')
            ->where('ppi_spis.action_format', 'Ppi')
            ->where('ppi_spis.warehouse_id', request()->get('warehouse_id'));
        /** Show PPI List Based On */
        if (!empty($route) && $route->show_as == 'User') {
            $query = $query->where('ppi_spis.action_performed_by', auth()->user()->id);
        }
        if (!empty($route) && $route->show_as == 'Permission') {
            if (auth()->user()->checkRoute([$role], "ppi_sent_to_wh_manager_action")) {
                $query = $query->where('ppi_spi_statuses.code', 'ppi_sent_to_boss');
            }
            if (auth()->user()->checkRoute([$role], "ppi_dispute_by_wh_manager_action")) {
                $query = $query->where('ppi_spi_statuses.code', 'ppi_sent_to_wh_manager');
            }
        }
        //dd($query->limit('2')->get());
        /** End */
        /** Search Query */
        $sq = '
                $collection->where(function($q) use ($search){
                    $q->where("ppi_spis.id", $search)
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
                $ppiLastSts = $thiss->Model("PpiSpiStatus")::getPpiLastStatus($data->id);
                $checkTransfer = $thiss->Model("SpiTransfer")::where("ppi_id", $data->id)->first() ?? null;
                $transferIcon = $checkTransfer ? "
                        <i title=\"transfer\" style=\"display: inline;border-radius: 100%;border: 1px solid #fd7e14;padding: 3px;font-size: 10px;\" class=\"fa fa-arrow-down text-orange\"></i>
                        " : null;
                $checkPurchase = $data->purchase == "yes" ? true : false;
                $purcchaseIcon =  $checkPurchase ? "<i title=\"purchase\" style=\"display: inline;border-radius: 100%;border: 1px solid #a0fd14;padding: 3px;font-size: 10px;\" class=\"fa fa-shopping-cart text-success\"></i>
                        " : null;
                $role = request()->get("currentUserRole");
                $getTranslateText = $thiss->Model("Translate")::getColumn("to_text", [
                            "translate_for" => "Role",
                            "for_id" =>  $role,
                            "base_text" => $ppiLastSts->code,
                        ]);
                $ppiLastStatus = $getTranslateText ?? $ppiLastSts->message;
                $checkSentToBoss = $thiss->Model("PpiSpiStatus")::checkPpiStatus($data->id, "ppi_sent_to_boss");
        ';
        /** Filed Show for loop */
        $fields = [
            'button' => '(($checkSentToBoss && auth()->user()->checkUserRoleTypeGeneral()) ? null : $this->ButtonSet::delete("ppi_destroy", [request()->get("warehouse_code"), $data->id]))
                           .$this->ButtonSet::edit("ppi_edit", [request()->get("warehouse_code"), $data->id])',
            'id' => '$data->id',
            'ppi_type' => '"<span class=\"$checkDisputes\">".$data->ppi_spi_type."</span>"',
            'project' => '$data->project',
            'tran_type' => '$data->tran_type',
            'ppi_last_status' => '$purcchaseIcon.$transferIcon."<span title=\"{$this->Model(\'User\')::getColumn($ppiLastSts->action_performed_by, \'name\')}\" class=\"py-0 px-1 alert-{$ppiLastSts->status_type}\">
                            {$ppiLastStatus}
                            </span>"',
            'sources' => '$data->who_source',
            'root_source' => '$data->root_source',
            'action_performed_by' => '$this->Model("User")::getColumn($data->action_performed_by, "name")',
            'created_at' => '$data->created_at->format("Y-m-d")',
        ];
        //dd($fields);

        return $this->Datatable::generate($request, $query, $fields, ['searchquery' => $sq, 'daterange' => 'ppi_spis.created_at', 'phpcode' => $phpCode, 'orderby' => 'desc']);

    }
}

/**
 * select `ppi_spis`.*, `users`.`name` as `user_name`, `ppi_spi_statuses`.`code`, `ppi_spi_statuses`.`message`, `ppi_spi_statuses`.`status_type`, `ppi_spi_sources`.`who_source` from `ppi_spis` left join `users` on `users`.`id` = `ppi_spis`.`action_performed_by` left join `ppi_spi_statuses` on `ppi_spi_id` = `ppi_spis`.`id` left join `ppi_spi_sources` on `ppi_spi_sources`.`ppi_spi_id` = `ppi_spis`.`id` where `ppi_spis`.`action_format` = ? and `(SELECT who_source FROM ppi_spi_sources WHERE ``ppi_spi_sources```.```ppi_spi_id`` =  ``ppi_spis```.```id`` ORDER BY ``ppi_spi_sources```.```id`` DESC LIMIT 1)` as `root_source` is null and `ppi_spis`.`warehouse_id` = ? group by `ppi_spis`.`id
 */
