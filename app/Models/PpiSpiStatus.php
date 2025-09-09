<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PpiSpiStatus extends Model
{
    use HasFactory;
    protected $table = 'ppi_spi_statuses';
    protected $fillable = [
        'ppi_spi_id', 'status_for', 'warehouse_id', 'code', 'action_performed_by', 'status_order', 'message', 'status_type', 'status_format', 'note', 'ppi_spi_product_id'
    ];

    public function ppispidispute(){
       return $this->hasMany('App\Models\PpiSpiDispute', 'ppi_spi_status_id', 'id');
    }


    /**===================================
     * =============== PPI================
     ====================================*/


    /**
     * @param $ppi_id
     * @return null
     */
    public static function getPpiLastStatus($ppi_id, $option = []){
        $default = [
            'ppi_spi_product_id' => null,
            'code' => null,
            'status_format' => null,
        ];
        $merge = array_merge($default, $option);
        $status = PpiSpiStatus::where('ppi_spi_id', $ppi_id)
                            ->where('status_for', 'Ppi');
        if(!empty($merge['ppi_spi_product_id'])){
            $status = $status->where('ppi_spi_product_id', $merge['ppi_spi_product_id']);
        }
        if(!empty($merge['status_format'])){
            $status = $status->where('status_format', $merge['status_format']);
        }
        if(!empty($merge['code'])){
            $status = $status->where('code', $merge['code']);
        }
        $status = $status->orderBy('status_order', 'desc')
                     ->first();


        if(!empty($status)){
            return $status ?? null;
        } else {
            return NULL;
        }
    }

    /**
     * @param $ppi_id
     * @param $options
     * @return null
     */

    public static function getPpiLastMainStatus($ppi_id, $options = []){
        $default = [
            'ppi_spi_product_id' => null,
            'code' => null,
        ];
        $merge = array_merge($default, $options);
        $status = PpiSpiStatus::where('ppi_spi_id', $ppi_id)
                            ->where('status_for', 'Ppi')
                            ->where('status_format', 'Main');
        if($merge['ppi_spi_product_id']){
            $status = $status->where('ppi_spi_product_id' , $merge['ppi_spi_product_id']);
        }
        if(!empty($merge['code'])){
            $status = $status->where('code', $merge['code']);
        }
        $status = $status->orderBy('status_order', 'desc')
                        ->first();
        if(!empty($status)){
            return $status ?? null;
        } else {
            return NULL;
        }
    }

    /**
     * @param $ppi_id
     * @param $code
     * @return bool
     */
    public static function checkPpiStatus($ppi_id, $code, $option = []){
        $default = [
            'ppi_spi_product_id' => null,
        ];
        $merge = array_merge($default, $option);
        $status = PpiSpiStatus::where('ppi_spi_id', $ppi_id)->where('status_for', 'Ppi')
                            ->where('code', $code);
        if($merge['ppi_spi_product_id']){
            $status = $status->where('ppi_spi_product_id' , $merge['ppi_spi_product_id']);
        }
        $status = $status->orderBy('status_order', 'desc')
                    ->first();

        if(!empty($status)){
            return true;
        } else {
            return false;
        }
    }



    /**
     * checkMultiLastPpiStatus
     *
     * @param  mixed $ppi_id
     * @param  mixed $code
     * @return void
     */
    public static function checkMultiLastPpiStatus($ppi_id, $code = []){
        $status =  PpiSpiStatus::where('ppi_spi_id', $ppi_id)->where('status_for', 'Ppi')
                    ->whereIn('code', $code)
                    ->orderBy('status_order', 'desc')
                    ->first();
        if(!empty($status)){
            return $status->code;
        } else {
            return Null;
        }
    }





    /**================================
     * ====== SPI ====================
     ================================*/
    public static function getSpiLastStatus($spi_id, $option=[]){
        $default = [
            'ppi_spi_product_id' => null,
            'code' => null,
            'status_format' => null,
        ];
        $merge = array_merge($default, $option);
        $status = PpiSpiStatus::where('ppi_spi_id', $spi_id)
                        ->where('status_for', 'Spi');

        if(!empty($merge['ppi_spi_product_id'])){
            $status = $status->where('ppi_spi_product_id', $merge['ppi_spi_product_id']);
        }
        if(!empty($merge['status_format'])){
            $status = $status->where('status_format', $merge['status_format']);
        }
        if(!empty($merge['code'])){
            $status = $status->where('code', $merge['code']);
        }
        $status = $status->orderBy('status_order', 'desc')->first();

        if(!empty($status)){
            return $status;
        } else {
            return NULL;
        }
    }



    /**
     * @param $spi_id
     * @param $options
     * @return null
     */

    public static function getSpiLastMainStatus($spi_id, $options = []){
        $default = [
            'ppi_spi_product_id' => null,
            'code' => null,
        ];
        $merge = array_merge($default, $options);
        $status = PpiSpiStatus::where('ppi_spi_id', $spi_id)
            ->where('status_for', 'Spi')
            ->where('status_format', 'Main');
        if($merge['ppi_spi_product_id']){
            $status = $status->where('ppi_spi_product_id' , $merge['ppi_spi_product_id']);
        }
        if(!empty($merge['code'])){
            $status = $status->where('code', $merge['code']);
        }
        $status = $status->orderBy('status_order', 'desc')
            ->first();
        if(!empty($status)){
            return $status;
        } else {
            return NULL;
        }
    }
    /**
     * @param $spi_id
     * @param $code
     * @return bool
     */
    public static function checkSpiStatus($spi_id, $code, $option = []){
        $default = [
            'ppi_spi_product_id' => null,
        ];
        $merge = array_merge($default, $option);
        $status = PpiSpiStatus::where('ppi_spi_id', $spi_id)->where('status_for', 'Spi')
            ->where('code', $code);

        if($merge['ppi_spi_product_id']){
            $status = $status->where('ppi_spi_product_id' , $merge['ppi_spi_product_id']);
        }

        $status = $status->orderBy('status_order', 'desc')->first();
        if(!empty($status)){
            return true;
        } else {
            return false;
        }
    }



    /** Motification */
    public static function notifications($options = []){            	
        $default = [
            'count' => false,
            'paginate' => false,
            'is_read' => false,
            'warehouse_id' => false,
            'query' => false,
        ];
        $merge = array_merge($default, $options);
//        $role = request()->get('currentUserRole');
        $role = User::getWarehouseRoles(auth()->user()->id)->pluck('role_id');

        if($role){
            /**
            
            $query = PpiSpiStatus::leftjoin('ppi_spi_notifications', 'ppi_spi_notifications.status_id', 'ppi_spi_statuses.id')
                ->select('ppi_spi_statuses.*', 'ppi_spi_notifications.is_read')
                ->where('ppi_spi_statuses.status_format', 'Main')
                ->orderBy('id', 'desc');
          
          	**/
          	$query = PpiSpiStatus::leftjoin('ppi_spi_notifications', 'ppi_spi_notifications.status_id', 'ppi_spi_statuses.id')
                    ->select('ppi_spi_statuses.*', 'ppi_spi_notifications.is_read')
                    ->where('ppi_spi_statuses.status_format', 'Main') // Pass the value 'Main' directly
                    ->orderBy('id', 'desc');
            if (auth()->user()->checkRoute($role, "ppi_sent_to_boss_action")) {
                $query = $query->whereNotIn('ppi_spi_statuses.code', ['ppi_sent_to_boss', 'spi_sent_to_boss',  'ppi_sent_to_wh_manager', 'spi_sent_to_wh_manager']);
            }
            if (auth()->user()->checkRoute($role, "ppi_sent_to_wh_manager_action")) {
                $query = $query->whereIn('ppi_spi_statuses.code', ['ppi_sent_to_boss', 'spi_sent_to_boss']);
            }
            if (auth()->user()->checkRoute($role, "ppi_dispute_by_wh_manager_action")) {
                $query = $query->whereIn('ppi_spi_statuses.code', ['ppi_sent_to_wh_manager', 'spi_sent_to_wh_manager']);
            }
            if ($merge['is_read']) {
                $query = $query->whereNull('ppi_spi_notifications.is_read');
            }
            if ($merge['warehouse_id']) {
                $query = $query->where('ppi_spi_statuses.warehouse_id', $merge['warehouse_id']);
            }

          
          	//\Log::info('Notifications Query', ['query' => $query->toSql()]);  // Log the query to the log file
        	//dd($query->paginate(30)); 
          
          
          
            if ($merge['query']) {
                return $query;
            }

            if ($merge['count']) {
                 $query = $query->get();
                 return count($query) ?? null;
             }

             if ($merge['paginate']) {
                 return $query->paginate($merge['paginate']) ?? null;
             } else {
                 return $query->get() ?? null;
             }
        } else{
            return null;
        }

    }

}
