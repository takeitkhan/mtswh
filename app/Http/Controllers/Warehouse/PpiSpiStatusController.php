<?php

namespace App\Http\Controllers\Warehouse;
use App\Http\Controllers\Warehouse\SingleWarehouseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Warehouse\PpiSpiHelper;
use App\Models\PpiSpiStatus;
use App\Models\PpiSpiDispute;
use App\Models\PpiSpi;
use App\Models\PpiProduct;
use App\Models\PpiSpiHistory;
use App\Models\SpiProduct;
use App\Models\GlobalSettings;

class PpiSpiStatusController extends SingleWarehouseController
{
    public function __construct(){
        parent::__construct();
    }
        /**
     * actionStatus
     *
     * @param  mixed $wh_code
     * @param  mixed $request
     * @return void
     * @param $action = pass PpiSpiHelper Helper Class ppiStatusHandler() method 'key'
     */

    /**
     *  //$wh_code, $ppi_id, $action, $redirect = true
     * PPI
     * This Acction Status For use from Route
     * @param $wh_code
     * @param $ppi_id
     * @param $action
     * @return \Illuminate\Http\RedirectResponse|void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */

     public function getPpiActionStatus($wh_code,  $ppi_id, $action){
        $request = request();
        //dd($request->all());
        if(request()->get('with-note')){
            $withNote = request()->get('with-note');
        }
         if(request()->get('with-ppi_product_id')){
             $ppiProductId = request()->get('with-ppi_product_id');
         }
        return self::ppiActionStatus([
            'wh_id' => request()->get('warehouse_id'),
            'ppi_id' => $ppi_id,
            'action' => $action,
            'note' =>   $withNote ?? null,
            'ppi_product_id' => $ppiProductId ?? null,
        ]);
    }


    /**
     * PPI
     * This Action Status For PPI use from anyWhere [Method]
     * @param $options
     * @return \Illuminate\Http\RedirectResponse|void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function ppiActionStatus($options = []) {
        $request = request();
        //dd($request);
        $defaultOpt = [
            'wh_id' => request()->get('warehouse_id'), // Warehouse Code
            'ppi_id' => $request->ppi_id, //PPI id
            'action' => $request->action, // Status key
            'redirect' => true, // redirect true, false
            'message' => null, // if mesage null db saved default status msg .
            'action_performed_by' => auth()->user()->id,
            'ppi_product_id' => null,
            'get_status_data' => false,
        ];

        $merge_arr = array_merge($defaultOpt, $options);
        $action = $merge_arr['action'];
        $redirect = $merge_arr['redirect'];
        $statusArr = PpiSpiHelper::ppiStatusHandler();
        $status = array_search($action, array_keys($statusArr), true) ?? null;
        $dbStatus = PpiSpiStatus::where('ppi_spi_id', $merge_arr['ppi_id'])->orderBy('id', 'desc')->first();
        $number = $dbStatus->status_order ?? 0;
        if($status !== false){
            $attributes = [
                'ppi_spi_id' => $merge_arr['ppi_id'],
                'status_for' => 'Ppi',
                'warehouse_id' => $merge_arr['wh_id'],
                'action_performed_by' => $merge_arr['action_performed_by'],
                'code' => $statusArr[$action]['key'],
                'message' =>!empty($merge_arr['message']) ? $merge_arr['message'] : $statusArr[$action]['message'],
                'status_type' => $statusArr[$action]['status_type'],
                'status_format' => $statusArr[$action]['status_format'] ?? NULL,
                'status_order' => $number+1,
                'note'  => $merge_arr['note'] ?? NULL,
                'ppi_spi_product_id' => $merge_arr['ppi_product_id'] ?? NULl,
            ];
            //dd($attributes);
            $data = PpiSpiStatus::create($attributes);


            /**=======================
             *  If Dispute Request
             * =======================*/
            //dd($request->dispute_ele);
            if($request->dispute_ele){
                foreach($request->dispute_ele as $key => $dele){
                    //dd($dele);
                    if (array_key_exists("ppi_product_id",$dele)){
                        $deleAttr = [
                            'ppi_spi_status_id' => $data->id,
                            'ppi_spi_id' => $merge_arr['ppi_id'],
                            'status_for' => 'Ppi',
                            'ppi_spi_product_id' => $dele['ppi_product_id'],
                            'issue_column' => implode(',', $dele['issue_column'] ?? []) ?? NULL,
                            'warehouse_id' => $merge_arr['wh_id'],
                            'action_performed_by' => auth()->user()->id,
                            'note'  => $dele['note'] ?? NULL,
                            'action_format'  => $dele['action_format'] ?? NULL,
                        ];
                        //dd($deleAttr);
                        PpiSpiDispute::create($deleAttr);
                        /** Keep note in ppispistatus as note column which Product Dispute  */
                        PpiSpiStatus::where('id', $data->id)->update([
                            'note' => 'with '.PpiProduct::ppiProductInfoByPpiProductId($dele['ppi_product_id'], ['column'=>'product_name']),
                            'ppi_spi_product_id' => $dele['ppi_product_id'],
                        ]);
                    }
                }

                // History Create
                $status_id = $data->id;
                $ppiHistory = PpiSpiHistory::getInstance();
                $busketInfo = $ppiHistory->arrangePpiData($merge_arr['ppi_id']);
                $ppiHistory->createHistory([
                    'ppi_spi_id' => $merge_arr['ppi_id'],
                    'action_format' => 'Ppi',
                    'chunck_old_data' => $busketInfo,
                    'chunck_new_data' => null,
                    'status_id' => $status_id,
                ]);

            }//ENd Dispute request

            /**===========================
             * If Correction  Request =====
             * ===========================*/
            if($request->correction_ele) {
                $ppiHistory = PpiSpiHistory::getInstance();
                $busketInfo = $ppiHistory->arrangePpiData($merge_arr['ppi_id']);
                $ppispiDispute = PpiSpiDispute::getInstance();
                $ppi_spi_product_id = $ppispiDispute->getColumn($request->correction_ele, 'ppi_spi_product_id');
                
                $corrAttr = [
                    'ppi_spi_status_id' => $data->id,
                    'ppi_spi_id' => $merge_arr['ppi_id'],
                    'status_for' => 'Ppi',
                    'ppi_spi_product_id' => $ppi_spi_product_id,
                    'issue_column' => NULL,
                    'correction_dispute_id'  => $request->correction_ele,
                    'warehouse_id' => $merge_arr['wh_id'],
                    'action_performed_by' => auth()->user()->id,
                    'action_format'  => 'Correction',
                ];

                //dd($deleAttr);
                $correctionDoneCheck = $merge_arr['correction_done'] ?? false;
                if($correctionDoneCheck == false){
                    PpiSpiDispute::create($corrAttr);
                      /** Keep note in ppispistatus as note column which Product Correction  */
                    PpiSpiStatus::where('id', $data->id)->update([
                        'note' => 'with '.PpiProduct::ppiProductInfoByPpiProductId($ppi_spi_product_id, ['column' => 'product_name']),
                        'ppi_spi_product_id' => $ppi_spi_product_id,
                    ]);
                }
                //dump('ok');
            }//End Correction


            /**
             * if All Dispute Correction Done
             *
             * */
            if(isset($ppi_spi_product_id)){
                $correctionDone = PpiSpiDispute::disputeCorrectionAllDone('Ppi', $merge_arr['ppi_id']) ?? Null;
//                dd($correctionDoneCheck);
                if(isset($correctionDone) && !empty($correctionDone) && $correctionDoneCheck == false){
                    self::ppiActionStatus([
                        'ppi_id' => $merge_arr['ppi_id'],
                        'action' => 'ppi_correction_done_by_boss',
                        'redirect' => 'false',
                        'action_performed_by' => auth()->user()->id,
                        'correction_done'   => $correctionDoneCheck == false ? true : false,
                    ]);
                }
            }
            //End Correction

            /**=======================
             * If Auto Approve By Boss
            =========================*/
            $checkAutoApproveByBoss = GlobalSettings::getColumn('ppi_auto_approve_boss', 'meta_value');

            if($checkAutoApproveByBoss == 1 && $data->code == 'ppi_sent_to_boss'){

                self::ppiActionStatus([
                    'ppi_id' => $request->ppi_id,
                    'action' => 'ppi_sent_to_wh_manager',
                    'redirect' => 'false',
                    'action_performed_by' => GlobalSettings::getColumn('boos_user_id', 'meta_value'),
                    'message' => 'PPI sent to Warehouse Manager',
                    'note' => 'Auto approve by Boss',
                ]);

            }

            //END
            if($redirect == true){
                if($data){
                    return redirect()->back()->with(['status' => 1, 'message' => 'Action successfully saved']);
                }else{
                    //return redirect()->back()->with(['status' => 0, 'message' => 'So']);
                    //return null;
                }
            }

            if($merge_arr['get_status_data']){
                return $data;
            }
        }

    }

        //End PPi Status

        /**
         * PPI ELEMENTS
         */
        public function ppiElements(){
            return true;
        }

        /**
         * =============================================
         * =============SPI=============================
         * =============STATUS==========================
         * =============ACTION==========================
         */

    /**
     * actionStatus
     * SPI
     * @param  mixed $wh_code
     * @param  mixed $request
     * @return void
     * @param $action = pass PpiSpiHelper Helper Class ppiStatusHandler() method 'key'
     */
    //$wh_code, $ppi_id, $action, $redirect = true,
    //This Acction Status For use from Route

    public function getSpiActionStatus($wh_code,  $spi_id, $action){
        $request = request();
//        dd($action);
        if(request()->get('with-note')){
            $withNote = request()->get('with-note');
        }
        if(request()->get('with-spi_product_id')){
            $spiProductId = request()->get('with-spi_product_id');
        }
        return $this->spiActionStatus([
            'wh_id' => request()->get('warehouse_id'),
            'spi_id' => $spi_id,
            'action' => $action,
            'note' =>   $withNote ?? null,
            'spi_product_id' => $spiProductId ?? null,
        ]);
    }

    /**
     * SPI Action Status
     * @param $options
     * @return \Illuminate\Http\RedirectResponse|void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     *
     */
    //This Action Status For SPI use from anyWhere [Method]
    public function spiActionStatus($options = []){
        $request = request();

        $defaultOpt = [
            'wh_id' => request()->get('warehouse_id'), // Warehouse Code
            'spi_id' => $request->spi_id, //SPI id
            'action' => $request->action, // Status key
            'redirect' => true, // redirect true, false
            'message' => null, // if mesage null db saved default status msg .
            'action_performed_by' => auth()->user()->id,
            'spi_product_id' => null,
            'get_status_data' => false,
        ];
        $merge_arr = array_merge($defaultOpt, $options);
        $action = $merge_arr['action'];
        $redirect = $merge_arr['redirect'];
        $statusArr = PpiSpiHelper::spiStatusHandler();
        $status = array_search($action, array_keys($statusArr), true) ?? null;
        $dbStatus = PpiSpiStatus::where('ppi_spi_id', $merge_arr['spi_id'])->orderBy('id', 'desc')->first();
        $number = $dbStatus->status_order ?? 0;
        //dd($status);
        if($status !== false){
            $attributes = [
                'ppi_spi_id' => $merge_arr['spi_id'],
                'status_for' => 'Spi',
                'warehouse_id' => $merge_arr['wh_id'],
                'action_performed_by' => $merge_arr['action_performed_by'],
                'code' => $statusArr[$action]['key'],
                'message' =>!empty($merge_arr['message']) ? $merge_arr['message'] : $statusArr[$action]['message'],
                'status_type' => $statusArr[$action]['status_type'],
                'status_format' => $statusArr[$action]['status_format'] ?? NULL,
                'status_order' => $number+1,
                'note'  => $merge_arr['note'] ?? NULL,
                'ppi_spi_product_id' => $merge_arr['spi_product_id'] ?? NULl,
            ];
            //dd($attributes);
            $data = PpiSpiStatus::create($attributes);

            /**=======================
             *  If Dispute Request
             * =======================*/
            //dd($request->dispute_ele);
            if($request->dispute_ele){
                foreach($request->dispute_ele as $key => $dele){
//                    dd($dele);
                    if (array_key_exists("spi_product_id",$dele)){
                        $deleAttr = [
                            'ppi_spi_status_id' => $data->id,
                            'ppi_spi_id' => $merge_arr['spi_id'],
                            'status_for' => 'Spi',
                            'ppi_spi_product_id' => $dele['spi_product_id'],
                            'issue_column' => implode(',', $dele['issue_column'] ?? []) ?? NULL,
                            'warehouse_id' => $merge_arr['wh_id'],
                            'action_performed_by' => auth()->user()->id,
                            'note'  => $dele['note'] ?? NULL,
                            'action_format'  => $dele['action_format'] ?? NULL,
                        ];
                        //dd($deleAttr);
                        PpiSpiDispute::create($deleAttr);

                        /** Keep note in ppispistatus as note column which Product Dispute  */
                        PpiSpiStatus::where('id', $data->id)->update([
                            'note' => 'with '.SpiProduct::spiProductInfoBySpiProductId($dele['spi_product_id'], ['column'=>'product_name']),
                           // 'note' => 'with '.$this->Model('Product')::name($dele['spi_product_id']),
                            'ppi_spi_product_id' => $dele['spi_product_id'],
                        ]);
                    }
                }

                // History Create
                $status_id = $data->id;
                $ppiHistory = PpiSpiHistory::getInstance();
                $busketInfo = $ppiHistory->arrangeSpiData($merge_arr['spi_id']);
                $ppiHistory->createHistory([
                    'ppi_spi_id' => $merge_arr['spi_id'],
                    'action_format' => 'Spi',
                    'chunck_old_data' => $busketInfo,
                    'chunck_new_data' => null,
                    'status_id' => $status_id,
                ]);
            }//ENd Dispute request

            /**===========================
             * If Correction  Request =====
             * ===========================*/
            //dd($request->correction_ele);
            if($request->correction_ele){
                $ppi_spi_product_id = $this->Model('PpiSpiDispute')::getColumn($request->correction_ele, 'ppi_spi_product_id');
                $corrAttr = [
                    'ppi_spi_status_id' => $data->id,
                    'ppi_spi_id' => $merge_arr['spi_id'],
                    'status_for' => 'Spi',
                    'ppi_spi_product_id' => $ppi_spi_product_id,
                    'issue_column' => NULL,
                    'correction_dispute_id'  => $request->correction_ele,
                    'warehouse_id' => $merge_arr['wh_id'],
                    'action_performed_by' => auth()->user()->id,
                    'action_format'  => 'Correction',
                ];

                //dd($deleAttr);
                $correctionDoneCheck = $merge_arr['correction_done'] ?? false;
                if($correctionDoneCheck == false){
                    PpiSpiDispute::create($corrAttr);
                    /** Keep note in ppispistatus as note column which Product Correction  */
                    PpiSpiStatus::where('id', $data->id)->update([
                        'note' => 'with '.SpiProduct::spiProductInfoBySpiProductId($ppi_spi_product_id, ['column' => 'product_name']),
                        //'note' => 'with '.$this->Model('Product')::name($ppi_spi_product_id),
                        'ppi_spi_product_id' => $ppi_spi_product_id,
                    ]);
                }
                //dump('ok');
            }//End Correction


            /**
             * if All Dispute Correction Done
             *
             * */
            if(isset($ppi_spi_product_id)){
                $correctionDone = PpiSpiDispute::disputeCorrectionAllDone('Spi', $merge_arr['spi_id']) ?? Null;
                if(isset($correctionDone) && !empty($correctionDone) && $correctionDoneCheck == false){
//                    dd($correctionDone);
//                    dd( $request->spi_id);
//                    dd($merge_arr['spi_id']);
                    $this->spiActionStatus([
                        'spi_id' => $merge_arr['spi_id'],
                        'action' => 'spi_correction_done_by_boss',
                        'redirect' => 'false',
                        'action_performed_by' => auth()->user()->id,
                        'correction_done'   => $correctionDoneCheck == false ? true : false,
                    ]);
                }
            }
            //End Correction

            /**=======================
             * If Auto Approve By Boss
            =========================*/
            $checkAutoApproveByBoss = GlobalSettings::getColumn('spi_auto_approve_boss', 'meta_value');

            if($checkAutoApproveByBoss == 1 && $data->code == 'spi_sent_to_boss'){

                self::ppiActionStatus([
                    'spi_id' => $request->spi_id,
                    'action' => 'spi_sent_to_wh_manager',
                    'redirect' => 'false',
                    'action_performed_by' => GlobalSettings::getColumn('boos_user_id', 'meta_value'),
                    'message' => 'SPI sent to Warehouse Manager',
                    'note' => 'Auto approve by Boss',
                ]);

            }

            //END
            if($redirect == true){
                if($data){
                    return redirect()->back()->with(['status' => 1, 'message' => 'Action successfully saved']);
                }else{
                    //return redirect()->back()->with(['status' => 0, 'message' => 'So']);
                    //return null;
                }
            }

            if($merge_arr['get_status_data']){
                return $data;
            }
        }
        //return null;
    }



}
