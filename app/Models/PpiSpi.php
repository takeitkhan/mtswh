<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class PpiSpi extends Model
{
    use HasFactory;
    protected $table = 'ppi_spis';
    protected $fillable = [
        'action_format', 'ppi_spi_type', 'project', 'tran_type', 'note','warehouse_id', 'action_performed_by' , 'transferable', 'purchase'
    ];

    public function stock(){
        return $this->hasMany('\App\Models\ProductStock','ppi_spi_id', 'id');
    }
    public function source(){
        return $this->hasMany('\App\Models\PpiSpiSource','ppi_spi_id', 'id');
    }
  
  
  
  /**
     * Get the count of PpiSpi records based on action_format and date filter.
     *
     * @param  string  $actionFormat
     * @param  bool  $dateFilter
     * @return int
     */
      public static function getCountByActionFormat(string $actionFormat, bool $dateFilter = false, string $grabStatus = '') : int
      {
          // Set the default value for $code based on the lowercase of $actionFormat
          $code = strtolower($actionFormat);

          $query = self::leftJoin('ppi_spi_statuses', 'ppi_spis.id', '=', 'ppi_spi_statuses.ppi_spi_id')
              ->select(
                  'ppi_spis.*',
                  'ppi_spi_statuses.code',
                  'ppi_spi_statuses.message',
                  'ppi_spi_statuses.status_type'
              )
              ->where('action_format', $actionFormat)
              ->groupBy('ppi_spis.id');

          if ($dateFilter) {
              $query->whereDate('ppi_spis.created_at', Carbon::today());
          }

        // If $getPending is true, only include records with 'pending' status type
        if ($grabStatus === 'created') {
            // Count records where code is '_created'
            $query->where('ppi_spi_statuses.code', '=', $code . '_created');
        } elseif ($grabStatus === 'pending') {
            // Exclude '_all_steps_complete' records
            $query->where('ppi_spi_statuses.code', '=', $code . '_all_steps_complete');
        } elseif ($grabStatus === 'completed') {
            // Count records where code is '_all_steps_complete'
            $query->where('ppi_spi_statuses.code', '!=', $code . '_all_steps_complete');
        } else {
            // If no specific status is given, check for both '_created' and '_all_steps_complete'
            $query->where(function ($query) use ($code) {
                $query->where('ppi_spi_statuses.code', '=', $code . '_created')
                      ->orWhere('ppi_spi_statuses.code', '=', $code . '_all_steps_complete');
            });
        }


          // Return the count of the query
          //return $query->toSql();
          return $query->get()->count();
      }



  
  
  /**
  	$count = PpiSpi::leftJoin('ppi_spi_statuses', 'ppi_spis.id', '=', 'ppi_spi_statuses.ppi_spi_id')
                ->select(
                    'ppi_spis.*',
                    'ppi_spi_statuses.code',
                    'ppi_spi_statuses.message',
                    'ppi_spi_statuses.status_type'
                )
          		->where('action_format', 'Spi')
                ->whereDate('ppi_spis.created_at', Carbon::today())
                ->groupBy('ppi_spis.id')
          		->get()
                ->count();
                
                **/
}
