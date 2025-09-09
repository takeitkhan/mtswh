<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PpiSpiDispute extends Model
{
    use HasFactory;
    protected $table = 'ppi_spi_disputes';
    protected $fillable = [
        'ppi_spi_status_id', 'ppi_spi_id', 'ppi_spi_product_id', 'status_for', 'issue_column', 'note', 'warehouse_id', 'action_performed_by', 'correction_dispute_id',  'action_format'
    ];

    public static function getInstance() {
        return new self();
    }
    
    public function ppispistatus(){
        return $this->hasOne('App\Models\PpiSpiStatus', 'id', 'ppi_spi_status_id');
    }

    public static function getColumn($id, $columnName){
        $value = PpiSpiDispute::where('id', $id)->first();
        return $value->$columnName ?? NUll;
    }

    public static function checkProductForDispute($status_for, $ppi_spi_id, $ppi_spi_product_id, $issue_column){
        $checkCorrection = PpiSpiDispute::where('ppi_spi_id', $ppi_spi_id)
                            ->where('status_for', $status_for)
                            ->where('ppi_spi_product_id', $ppi_spi_product_id)
                            ->orderBy('id', 'desc')
                            ->first();
        if(!empty($checkCorrection) && $checkCorrection->action_format == 'Correction'){
            return null;
        } else{
            $data =  PpiSpiDispute::where('ppi_spi_id', $ppi_spi_id)
                        ->where('ppi_spi_product_id', $ppi_spi_product_id)
                        ->where('status_for', $status_for)
                        ->whereRaw("find_in_set('".$issue_column."',issue_column)")
                        ->orderBy('id', 'desc')
                        ->first();
            return  $data ?? null;
        }
    }

    /**
     * disputeData
     * get Data where Staus For Ppi
     * Action formate Dispute
     * @param  mixed $ppi_spi_id = ppi_id
     * @param  mixed $ppi_spi_product_id = ppi_produtc_id
     * @return void
     */
    public static function disputeData($status_for, $ppi_spi_id, $ppi_spi_product_id){
        $data =  PpiSpiDispute::where('ppi_spi_id', $ppi_spi_id)
                      ->where('status_for', $status_for)
                      ->where('ppi_spi_product_id', $ppi_spi_product_id)
                      ->where('action_format', 'Dispute')
                      ->orderBy('id', 'DESC')
                      ->first();
       return  $data ?? null;
    }
    /**
     * Check this Dispute  has been correction
     */
    public static function checkDisputeCorrection($status_for, $dispute_id){
        $data =  PpiSpiDispute::where('action_format', 'Correction')
                    ->where('correction_dispute_id', $dispute_id)
                    ->where('status_for', $status_for)
                    ->orderBy('id', 'DESC')
                    ->first();
        return  $data ?? null;

    }
    /**
     * Ppi Correction Dispute Status List
     * Based on Ppi Spi Product ID
     */
    public static function ppiDisputeCorrectionList($status_for, $ppi_product_id){
        $query = PpiSpiDispute::with('ppispistatus')
                            ->where('ppi_spi_product_id', $ppi_product_id)
                            ->where('status_for', $status_for)
                            ->where('action_format', 'Dispute')
                            ->get();
        $data = [];
        if($query){
        foreach($query as $item){
                $chekCorrection = PpiSpiDispute::where('ppi_spi_product_id', $ppi_product_id)
                                            ->where('status_for', $status_for)
                                            ->where('action_format', 'Correction')
                                            ->where('correction_dispute_id', $item->id)
                                            ->first() ?? null;
                $data []= (object) [
                    'dispute_status_for' => $item->action_format,
                    'correction_status_for' => $item->action_format,
                    'dispute_note' => $item->note,
                    'dispute_action_by' => $item->action_performed_by,
                    'correction_action_by' => $chekCorrection->action_performed_by ?? null,
                    'dispute_date'  => $item->created_at->format('d M Y H:i a'),
                    'correction_note' => $chekCorrection ? $chekCorrection->note : null,

                    'correction_dispute_id' => $chekCorrection ? $chekCorrection->correction_dispute_id : null,

                    'correction_date'  => $chekCorrection ? $chekCorrection->created_at->format('d M Y H:i a') : null,

                ];
                //dump($chekCorrection);
            }
        }
        return (object) $data;
    }

    /**
     * check the ppi product is dispute
     *
     */
    public static function thisPpiProductDisputeOrCoorection($status_for, $ppi_spi_product_id){
        $data = PpiSpiDispute::where('status_for', $status_for)
                    ->where('ppi_spi_product_id', $ppi_spi_product_id)
                    ->orderBy('id', 'DESC')
                    ->first();
        $actionFormat =  $data->action_format ?? null;
        return $actionFormat ?? null;
    }


    /**
     * Check Dispute Corrction is Equal
     * Depends PPI
     * All Dispute Is Correction Done
     */
    public static function disputeCorrectionAllDone($status_for, $ppi_spi_id){
        $dispute = PpiSpiDispute::where('action_format', 'Dispute')
            ->where('status_for', $status_for)
            ->where('ppi_spi_id', $ppi_spi_id)
            ->orderBy('id', 'DESC')
            ->get()->count();

        $correction = PpiSpiDispute::where('action_format', 'Correction')
            ->where('status_for', $status_for)
            ->where('ppi_spi_id', $ppi_spi_id)
            ->orderBy('id', 'DESC')
            ->get()->count();

        if($dispute > 0 && $dispute == $correction){
            return true;
        }else {
            return null;
        }
    }


    /**
     * Check Dispute Corrction is Equal
     * All Dispute Is Correction Done
     */

    public static function disputeCorrectionDone($status_for, $ppi_spi_id, $ppi_spi_product_id, $option=[]){
        $default = [
            'action_performed_by' => null,
            'route_permission' => null,
        ];
        $merge = array_merge($default, $option);
        $dispute = PpiSpiDispute::where('action_format', 'Dispute')
                    ->where('status_for', $status_for)
                    ->where('ppi_spi_product_id', $ppi_spi_product_id)
                    ->orderBy('id', 'DESC')
                    ->get()->count();

        $correction = PpiSpiDispute::where('action_format', 'Correction')
                        ->where('status_for', $status_for)
                        ->where('ppi_spi_product_id', $ppi_spi_product_id);

        if(!empty($merge['action_performed_by'])){
            $correction = $correction->where('action_performed_by', $merge['action_performed_by']);
        }
        $correction = $correction->orderBy('id', 'DESC')
                        ->get()->count();

//       dump($dispute);
//       dump($correction);
//       exit();

        if($dispute > 0 && $dispute == $correction){
            $data = 'true';
        }
        elseif($dispute > 0){
            $data = 'correction-not-done';
        }
        else {
            $data = null;
        }

        if(!empty($merge['route_permission'])){
            if(auth()->user()->hasRoutePermission($merge['route_permission'])){
                $data = $data;
            }else {
                $data = false;
            }
        }else {
            $data = $data;
        }
        return $data;
    }
}
