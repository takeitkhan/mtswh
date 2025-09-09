<?php
namespace App\Helpers\Warehouse;
use App\Models\PpiBundleProduct;
use App\Models\PpiSpi;
use App\Models\Warehouse;
use DB;
class ProductStockHelper {
    public function getProductStock($product_id){
        /*
        $data = DB::select("
                    SELECT
                        ppi_product_stock.ppi_spi_id,
                        ppi_spis.project,
                        ppi_spis.ppi_spi_type,
                        ppi_product_stock.action_format,
                        ppi_product_stock.ppi_spi_product_id  as ppi_product_id,
                        ppi_product_stock.product_id,
                        ppi_product_stock.bundle_id,
                        ppi_product_stock.stock_action,
                        ppi_products.product_state,
                        ppi_products.health_status,
                        ppi_products.unit_price,
                        ppi_product_stock.qty as stock_in,
                        CONVERT((
                         SELECT (sum(qty)+0) FROM product_stocks as spi_product_stock_out
                         WHERE spi_product_stock_out.product_id = $product_id
                         AND spi_product_stock_out.action_format = 'Spi'
                         AND from_ppi_product_id = ppi_product_stock.ppi_spi_product_id
                        ),UNSIGNED INTEGER) as stock_out,
                        ppi_product_stock.warehouse_id,
                       (
                        SELECT IF(stock_in > stock_out, 'yes', 'no')
                        ) as have_stock_status,
                        ppi_product_stock.entry_date,
                       (
                            SELECT (stock_in - CONVERT(stock_out, UNSIGNED INTEGER) )
                        )as stock_in_hand

                    FROM product_stocks as ppi_product_stock

                    LEFT JOIN ppi_spis ON ppi_spis.id = ppi_product_stock.ppi_spi_id
                    LEFT JOIN ppi_products ON ppi_products.id = ppi_product_stock.ppi_spi_product_id
                    WHERE ppi_spis.action_format = 'Ppi'
                    AND ppi_product_stock.product_id = $product_id;
            ");

        */

        $data = DB::select("
                    SELECT
                        ppi_product_stock.ppi_spi_id,
                        ppi_spis.project,
                        ppi_spis.ppi_spi_type,
                        ppi_product_stock.action_format,
                        ppi_product_stock.ppi_spi_product_id  as ppi_product_id,
                        ppi_product_stock.product_id,
                        ppi_product_stock.bundle_id,
                        ppi_product_stock.stock_action,
                        ppi_products.product_state,
                        ppi_products.health_status,
                        ppi_products.unit_price,
                        ppi_product_stock.entry_date,
                        ppi_product_stock.warehouse_id,

                        sum(stock_in_product.qty) as stock_in,

                      	(
                            SELECT IFNULL(sum(qty), 0) FROM product_stocks as spi_product_stock_out
                            WHERE spi_product_stock_out.product_id = $product_id
                            AND spi_product_stock_out.action_format = 'Spi'
                            AND from_ppi_product_id = ppi_product_stock.ppi_spi_product_id
                        ) as stock_out,

                        (
                            SELECT IF(sum(stock_in_product.qty) > stock_out, 'yes', 'no')
                        ) as have_stock_status


                    FROM product_stocks as ppi_product_stock


					INNER JOIN product_stocks as stock_in_product ON stock_in_product.id = ppi_product_stock.id

                    LEFT JOIN ppi_spis ON ppi_spis.id = ppi_product_stock.ppi_spi_id
                    LEFT JOIN ppi_products ON ppi_products.id = ppi_product_stock.ppi_spi_product_id
                    WHERE ppi_spis.action_format = 'Ppi'
                    AND ppi_product_stock.product_id = $product_id
                    GROUP BY IF(ppi_product_stock.bundle_id, ppi_product_stock.bundle_id, ppi_product_stock.ppi_spi_product_id);
					#GROUP BY ppi_product_stock.ppi_spi_id;
            ");


        $data = collect($data);
        $arr = [];
        $i=0;
        foreach($data as $item){
            $stock_out = $item->stock_out ?? 0;
            $arr [$i]= (object)[
                'ppi_spi_id' => $item->ppi_spi_id,
                'project' => $item->project,
                'ppi_spi_type' => $item->ppi_spi_type,
                'action_format' => $item->action_format,
                'ppi_product_id' => $item->ppi_product_id,
                'product_id' => $item->product_id,
                'bundle_id' => $item->bundle_id,
                'bundle' => $item->bundle_id ? PpiBundleProduct::where('id', $item->bundle_id)->first() : false,
                'stock_action' => $item->stock_action,
                'product_state' => $item->product_state,
                'health_status' => $item->health_status,
                'unit_price' => $item->unit_price,
                'stock_in' => $item->stock_in,
                'stock_out' => $stock_out,
                'warehouse_id' => $item->warehouse_id,
                'have_stock_status' =>$item->stock_in > $stock_out ? 'yes' : 'no',
                'entry_date' => $item->entry_date,
                'stock_in_hand' => $item->stock_in - $stock_out,
                'sources' => \App\Models\PpiSpiSource::where('ppi_spi_id', $item->ppi_spi_id)->where('action_format', 'Ppi')->get(),
            ];
            $i++;
        }

        return collect($arr);
    }


    public function getSpiTemporaryStock($product_id){
        $data = DB::select("
                SELECT temporary_stocks.action_format,
                    temporary_stocks.ppi_spi_id,
                    temporary_stocks.product_id,
                    temporary_stocks.waiting_stock_out,
                    temporary_stocks.spi_product_id,
                    spi_products.ppi_product_id,
                    spi_products.bundle_id,
                    spi_products.warehouse_id
                    FROM `temporary_stocks`
                    LEFT JOIN spi_products ON spi_products.id = temporary_stocks.spi_product_id
                    WHERE temporary_stocks.action_format = 'Spi'
                    AND temporary_stocks.product_id=$product_id;
            ");
        return collect($data);
    }



    public function hasSpiProductDispute($spi_product_id){
            $data = DB::select("
                        SELECT
                            ppi_spi_disputes.id,
                            spi_products.id as spi_product_id,
                            spi_products.spi_id,
                            spi_products.ppi_product_id,
                            spi_products.product_id,
                            CONVERT(spi_products.qty, UNSIGNED INTEGER) as qty,
                            spi_products.bundle_id,
                            spi_products.warehouse_id,
                            spi_products.from_warehouse,
                            ppi_spi_disputes.action_format as dispute_status,
                            (
                                SELECT dispute_correction.action_format
                                FROM ppi_spi_disputes as dispute_correction
                                WHERE dispute_correction.status_for = 'Spi'
                                AND dispute_correction.ppi_spi_product_id = spi_products.id
                                AND dispute_correction.correction_dispute_id = ppi_spi_disputes.id
                            )as correction_status
                        FROM ppi_spi_disputes
                        LEFT JOIN spi_products ON spi_products.id = ppi_spi_disputes.ppi_spi_product_id
                        WHERE ppi_spi_disputes.status_for = 'Spi'
                        AND ppi_spi_disputes.action_format = 'Dispute'
                        AND ppi_spi_disputes.ppi_spi_product_id = $spi_product_id
                        ORDER BY ppi_spi_disputes.id DESC
                        LIMIT 0,1;
            ");

            $data = collect($data);
            return $data ?? null;
    }


    public function getSpiProductBasedOnPpiProductId($ppi_product_id){
        $data = DB::select("
            SELECT
                ppi_spis.id as spi_id,
                ppi_spis.project,
                ppi_spis.ppi_spi_type,
                ppi_spis.action_format,
                spi_products.warehouse_id,
                spi_products.from_warehouse,
                spi_products.product_id,
                spi_products.id as spi_product_id,
                spi_products.ppi_id,
                spi_products.ppi_product_id,
                spi_products.bundle_id,
                spi_products.unit_price,
                spi_products.qty,
                spi_products.created_at
            FROM spi_products
            LEFT JOIN ppi_spis ON ppi_spis.id = spi_products.spi_id
            WHERE ppi_product_id = $ppi_product_id;
        ");

        return collect($data);
    }


    //Vendor of PPi
    public function getPpiVendorBasedProduct($vendor_name, $option =[]){
        $default = [
            'from_date' => false,
            'to_date' => false,
        ];
        $merge =  array_merge($default, $option);
        $from_date = $merge['from_date'];
        $to_date = $merge['to_date'];
        $daterange = $from_date ? " AND ppi_spi_sources.created_at BETWEEN '$from_date' AND '$to_date'" : false;
        $data = DB::select("
            SELECT
            id,
            name,
                (
                SELECT GROUP_CONCAT(ppi_products.ppi_id SEPARATOR ',')
                FROM ppi_products
                LEFT JOIN ppi_spi_sources ON ppi_spi_sources.ppi_spi_id = ppi_products.ppi_id
                WHERE ppi_products.product_id = products.id
                AND ppi_spi_sources.who_source LIKE '%$vendor_name%'
                $daterange
            ) as ppi_id
            FROM products;
        ");
        $datas = collect($data)->whereNotNull('ppi_id');
        $arr = [];
        foreach($datas as $item){
            $ppi_ids =  array_unique( explode(',', $item->ppi_id) );
            $links = [];
            $ppiProductQty = [];
            foreach($ppi_ids as $id){
                $ppis = \App\Models\PpiSpi::where('id', $id)->first();
                $ppiProductQty []= \App\Models\PpiProduct::Where('ppi_id', $ppis->id)->where('product_id', $item->id)->get()->sum('qty');
                $warehouseCode = Warehouse::getColumn($ppis->warehouse_id, 'code');
                $link = route('ppi_edit', [$warehouseCode, $id]);
                $hasSpiClose = \App\Models\PpiSpiStatus::where('status_for', 'Ppi')->where('ppi_spi_id', $id)->where('status_format', 'Main')->where('code', 'ppi_all_steps_complete')->first();
                $colorCls = $hasSpiClose ? 'text-green fw-bold': 'text-primary';
                $links []= '<a class="d-inline-block '.$colorCls.'" target="_blank" href="'.$link.'">'.$id.'</a>';
            }
            $arr []= (object)[
                'id' => $item->id,
                'name' => $item->name,
                'ppi_id' => implode(' ', $links),
                'qty' => array_sum($ppiProductQty),
            ];

        }

        return collect($arr);

    }




    //Vendor of SPI

    public function getSpiVendorBasedProduct($vendor_name, $option =[]){
        $default = [
            'from_date' => false,
            'to_date' => false,
        ];
        $merge =  array_merge($default, $option);
        $from_date = $merge['from_date'];
        $to_date = $merge['to_date'];
        $daterange = $from_date ? " AND ppi_spi_sources.created_at BETWEEN '$from_date' AND '$to_date'" : false;
        $data = DB::select("
            SELECT
            id,
            name,
                (
                SELECT GROUP_CONCAT(spi_products.spi_id SEPARATOR ',')
                FROM spi_products
                LEFT JOIN ppi_spi_sources ON ppi_spi_sources.ppi_spi_id = spi_products.spi_id
                WHERE spi_products.product_id = products.id
                AND ppi_spi_sources.who_source LIKE '%$vendor_name%'
                $daterange
            ) as spi_id
            FROM products;
        ");
        $datas = collect($data)->whereNotNull('spi_id');
        $arr = [];
        foreach($datas as $item){
            $spi_ids =  array_unique( explode(',', $item->spi_id) );
            $links = [];
            $ppiProductQty = [];
            foreach($spi_ids as $id){
                $spis = \App\Models\PpiSpi::where('id', $id)->first();
                $spiProductQty []= $spis ? \App\Models\SpiProduct::Where('spi_id', $spis->id)->where('product_id', $item->id)->get()->sum('qty') : false;
                $warehouseCode = $spis ? Warehouse::getColumn($spis->warehouse_id, 'code') : false;
                $link = route('spi_edit', [$warehouseCode, $id]);
                $hasSpiClose = \App\Models\PpiSpiStatus::where('status_for', 'Spi')->where('ppi_spi_id', $id)->where('status_format', 'Main')->where('code', 'spi_all_steps_complete')->first();
                $colorCls = $hasSpiClose ? 'text-green fw-bold': 'text-primary';
                $links []= '<a class="d-inline-block '.$colorCls.'" target="_blank" href="'.$link.'">'.$id.'</a>';
            }
            $arr []= (object)[
                'id' => $item->id,
                'name' => $item->name,
                'spi_id' => implode(' ', $links),
                'qty' => array_sum($spiProductQty),
            ];

        }

        return collect($arr);

    }








    //Spi
    public function getSpiSiteBasedProduct($site_code, $option =[]){
        /*
        $data = DB::select("
            SELECT
                products.id,
                products.name,
                ppi_spi_sources.who_source,
                ppi_spis.id
                FROM products
                LEFT JOIN spi_products ON spi_products.product_id =products.id
                LEFT JOIN ppi_spis ON ppi_spis.id = spi_products.spi_id
                LEFT JOIN ppi_spi_sources ON  ppi_spis.id = ppi_spi_sources.ppi_spi_id
                WHERE ppi_spi_sources.action_format = 'Spi'
                AND ppi_spi_sources.source_type = 'Site';
        ");
          (
                SELECT GROUP_CONCAT(spi_products.spi_id SEPARATOR ',')
                FROM spi_products
                LEFT JOIN ppi_spi_sources ON ppi_spi_sources.ppi_spi_id = spi_products.spi_id
                WHERE spi_products.product_id = products.id
                AND ppi_spi_sources.source_type = 'Site'
                AND ppi_spi_sources.who_source LIKE '%$site_code%'
                GROUP BY ppi_spi_sources.ppi_spi_id
            ) as spi_id
        */
        $default = [
            'from_date' => false,
            'to_date' => false,
        ];
        $merge =  array_merge($default, $option);
        $from_date = $merge['from_date'];
        $to_date = $merge['to_date'];
        $daterange = $from_date ? " AND ppi_spi_sources.created_at BETWEEN '$from_date' AND '$to_date'" : false;

        $data = DB::select("
            SELECT
            id,
            name,
                (
                SELECT GROUP_CONCAT(spi_products.spi_id SEPARATOR ',')
                FROM spi_products
                LEFT JOIN ppi_spi_sources ON ppi_spi_sources.ppi_spi_id = spi_products.spi_id
                WHERE spi_products.product_id = products.id
                AND ppi_spi_sources.who_source LIKE '%$site_code%'
                AND ppi_spi_sources.source_type = 'Site'
                $daterange
            ) as spi_id
            FROM products;
        ");
        $datas = collect($data)->whereNotNull('spi_id');
        $arr = [];
        foreach($datas as $item){
            $spi_ids =  array_unique( explode(',', $item->spi_id) );
            $links = [];
            $spiProductQty = [];
            foreach($spi_ids as $id){
                $spis = \App\Models\PpiSpi::where('id', $id)->first();
                $spiProductQty []= \App\Models\SpiProduct::Where('spi_id', $spis->id)->where('product_id', $item->id)->get()->sum('qty');
                $warehouseCode = Warehouse::getColumn($spis->warehouse_id, 'code');
                $link = route('spi_edit', [$warehouseCode, $id]);
                $hasSpiClose = \App\Models\PpiSpiStatus::where('status_for', 'Spi')->where('ppi_spi_id', $id)->where('status_format', 'Main')->where('code', 'spi_all_steps_complete')->first();
                $colorCls = $hasSpiClose ? 'text-green fw-bold': 'text-primary';
                $links []= '<a class="d-inline-block '.$colorCls.'" target="_blank" href="'.$link.'">'.$id.'</a>';
            }
            $arr []= (object)[
                'id' => $item->id,
                'name' => $item->name,
                'spi_id' => implode(' ', $links),
                'qty' => array_sum($spiProductQty),
            ];

        }

        return collect($arr);

    }
    
    
    
    public function getPpiSpisWithProjects()
    {
        return DB::table('ppi_spis as ps')
            ->leftJoin('projects as p', 'p.name', '=', 'ps.project')
            ->groupBy('ps.project')
            ->select([
                'ps.id',
                'ps.action_format',
                'ps.ppi_spi_type',
                'ps.project',
                // 'ps.tran_type',
                // 'ps.warehouse_id',
                // 'p.id as project_id',
                // 'p.name as project_name',
                // 'p.code as project_code',
                // 'p.type as project_type',
                // 'p.customer',
                // 'p.vendor'
            ])
            ->get();
    }
    
    public function getPpiDataAccumulated(array $options = [])
    {
        $defaultOptions = [
            'project'    => null,
            'whoSource'  => null,
            'startDate'  => null,
            'endDate'    => null,
        ];
    
        // Merge provided options with defaults
        $options = array_merge($defaultOptions, $options);
    
        $conditions = [
            ['ppi_spis.action_format', '=', 'Ppi'],
        ];
    
        // Add the project condition if provided
        if (!empty($options['project'])) {
            $conditions[] = ['ppi_spis.project', '=', $options['project']];
        }
    
        // Add the whoSource condition if provided
        if (!empty($options['whoSource'])) {
            $conditions[] = ['ppi_spi_sources.who_source', '=', $options['whoSource']];
        }
    
        // Add the date range condition if startDate and endDate are provided
        if (!empty($options['startDate']) && !empty($options['endDate'])) {
            // Convert start and end date from dd/mm/yyyy to yyyy-mm-dd
            $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', $options['startDate'])->format('Y-m-d');
            $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', $options['endDate'])->format('Y-m-d');
    
            $conditions[] = ['ppi_spis.created_at', '>=', $startDate];
            $conditions[] = ['ppi_spis.created_at', '<=', $endDate];
        }
    
        return PpiSpi::select(
            'ppi_spis.id',
            'ppi_spis.action_format',
            'ppi_spis.ppi_spi_type',
            'ppi_spis.project',
            'ppi_spis.tran_type',
            'ppi_spis.warehouse_id',
            'ppi_products.product_id',
            \DB::raw('COUNT(DISTINCT ppi_products.ppi_id) AS total_count_of_ppi_id'),
            \DB::raw('GROUP_CONCAT(DISTINCT ppi_products.ppi_id ORDER BY ppi_products.ppi_id ASC SEPARATOR ", ") AS ppi_ids'),
            'projects.id AS project_id',
            'projects.name AS project_name',
            'projects.code',
            'projects.type',
            'projects.customer',
            'projects.vendor',
            'products.name AS product_name',
            'ppi_spi_sources.source_type',
            'ppi_spi_sources.who_source'
        )
        ->leftJoin('projects', 'projects.name', '=', 'ppi_spis.project')
        ->leftJoin('ppi_products', 'ppi_products.ppi_id', '=', 'ppi_spis.id')
        ->leftJoin('products', 'products.id', '=', 'ppi_products.product_id')
        ->leftJoin('ppi_spi_sources', 'ppi_spi_sources.ppi_spi_id', '=', 'ppi_spis.id')
        ->where($conditions)
        ->groupBy('ppi_products.product_id')
        ->get();
    }







    public function getLatestRootSourceByProject($project)
    {
        return PpiSpi::select(
            'ppi_spis.project',
            DB::raw('(SELECT who_source FROM ppi_spi_sources WHERE ppi_spi_sources.ppi_spi_id = ppi_spis.id ORDER BY ppi_spi_sources.id DESC LIMIT 1) AS root_source')
        )
        ->leftJoin('ppi_spi_sources', 'ppi_spi_sources.ppi_spi_id', '=', 'ppi_spis.id')
        ->where('ppi_spis.project', $project)
        ->whereIn('ppi_spis.action_format', ['Ppi', 'Spi'])
        ->groupBy('root_source')
        ->get();
    }



}

?>
