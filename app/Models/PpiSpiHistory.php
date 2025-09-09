<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PpiSpiHistory extends Model
{
    use HasFactory;
    protected $table = 'ppi_spi_histories';
    protected  $fillable = [
        'ppi_spi_id', 'action_format', 'chunck_old_data', 'chunck_new_data', 'status_id', 'action_performed_by', 'action_time'
    ];


    public static function getInstance() {
        return new self();
    }

    public function arrangePpiData($ppi_spi_id){
        $ppi = PpiSpi::where('action_format', 'Ppi')->where('id', $ppi_spi_id)->first()->toArray();
        $ppiSource = PpiSpiSource::where('ppi_spi_id', $ppi_spi_id)->get()->toArray();
        $ppiProducts = PpiProduct::where('ppi_id', $ppi_spi_id)->get()->toArray();
        $ppiSetProduct = PpiSetProduct::where('ppi_id', $ppi_spi_id)->get()->toArray();
        $ppiBundleProduct = PpiBundleProduct::where('ppi_id', $ppi_spi_id)->get()->toArray();

        $toArr = [
            'ppi_basic_info' =>  $ppi,
            'ppi_source' =>  $ppiSource,
            'ppi_products' =>  $ppiProducts,
            'ppi_set_products' =>  $ppiSetProduct,
            'ppi_bundle_products' =>  $ppiBundleProduct,
        ];
        return $toArr;
    }

    public function arrangeSpiData($ppi_spi_id){
        $ppi = PpiSpi::where('action_format', 'Spi')->where('id', $ppi_spi_id)->first()->toArray();
        $ppiSource = PpiSpiSource::where('ppi_spi_id', $ppi_spi_id)->get()->toArray();
        $ppiProducts = SpiProduct::where('spi_id', $ppi_spi_id)->get()->toArray();
//        $ppiSetProduct = PpiSetProduct::where('ppi_id', $ppi_spi_id)->get()->toArray();
//        $ppiBundleProduct = PpiBundleProduct::where('spi_id', $ppi_spi_id)->get()->toArray();

        $toArr = [
            'ppi_basic_info' =>  $ppi,
            'ppi_source' =>  $ppiSource,
            'ppi_products' =>  $ppiProducts,
            'ppi_set_products' =>  [],
            'ppi_bundle_products' =>  [],
        ];
        return $toArr;
    }

    /**
     * @param $option
     * @return void
     */
    public  function createHistory($option = []){
        $default = [
            'ppi_spi_id' => null,
            'action_format' => null,
            'chunck_old_data' => null,
            'chunck_new_data' => null,
            'action_performed_by' => auth()->user()->id,
            'action_time' => Carbon::now(),
            'status_id' => null,
        ];
        $merge = array_merge($default, $option);
        $attr = [
            'ppi_spi_id' => $merge['ppi_spi_id'],
            'action_format' => $merge['action_format'],
            'chunck_old_data' => json_encode($merge['chunck_old_data']),
            'chunck_new_data' => json_encode($merge['chunck_new_data']),
            'action_performed_by' => auth()->user()->id,
            'action_time' => Carbon::now(),
            'status_id' => $merge['status_id'],
        ];
        PpiSpiHistory::create($attr);
    }
}
