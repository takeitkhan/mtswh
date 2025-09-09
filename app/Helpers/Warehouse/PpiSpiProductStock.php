<?php

namespace App\Helpers\Warehouse;

use DB;

class PpiSpiProductStock
{
    public function ppiStockDetails($product_id)
    {
        $sql = "
        SELECT
            ppi_spis.id as ppi_id,
            ppi_spis.transferable as transferable,
            ppi_spis.purchase as purchase,
            ppi_spis.action_format,
            ppi_spis.ppi_spi_type,
            ppi_spis.project as project,
            ppi_spis.tran_type,
            ppi_spis.warehouse_id,
            ppi_spis.transferable,
            ppi_spis.created_at,
            ppi_products.id as ppi_product_id,
            ppi_products.product_id as product_id,
            products.name as product_name,
            ppi_products.product_state,
            ppi_products.health_status,
            ppi_products.unit_price as unit_price,
            ppi_products.price as price,
            ppi_bundle_products.id as bundle_id,
            ppi_bundle_products.bundle_size as bundle_size,
            (CASE
                WHEN ppi_bundle_products.bundle_size IS NOT NULL THEN ppi_bundle_products.bundle_size
                ELSE ppi_products.qty
            END) as product_qty,
            warehouses.name as warehouse_name,
            warehouses.code as warehouse_code,
            (SELECT GROUP_CONCAT(who_source)
                FROM ppi_spi_sources
                WHERE ppi_spis.id = ppi_spi_sources.ppi_spi_id
            ) as who_source,
            (
                CASE
                    WHEN
                    (SELECT SUM(qty)
                        FROM product_stocks
                        WHERE product_stocks.ppi_spi_id = ppi_spis.id
                        AND product_stocks.ppi_spi_product_id = ppi_products.id
                    )
                    THEN (
                        CASE
                            WHEN ppi_bundle_products.bundle_size IS NOT NULL THEN ppi_bundle_products.bundle_size
                            ELSE ppi_products.qty
                        END
                    )
                    ELSE NULL
                END
            ) as stock_in_qty,
            (
                CASE
                    WHEN (
                        SELECT sum(waiting_stock_in)
                        FROM temporary_stocks
                        WHERE temporary_stocks.action_format = 'Ppi'
                        AND temporary_stocks.ppi_product_id =  ppi_products.id
                    ) IS NOT NULL
                    THEN 'yes'
                    ELSE NULL
                END
            ) as is_waiting_to_stock_in,
            (
                CASE
                    WHEN (
                        SELECT MAX(id)
                        FROM ppi_spi_statuses
                        WHERE ppi_spi_statuses.ppi_spi_id = ppi_spis.id
                        AND ppi_spi_statuses.ppi_spi_product_id = ppi_products.id
                        AND ppi_spi_statuses.code = 'ppi_new_product_added_to_stock'
                    ) IS NOT NULL
                    THEN 'yes'
                    WHEN (
                        SELECT MAX(id)
                        FROM ppi_spi_statuses
                        WHERE ppi_spi_statuses.ppi_spi_id = ppi_spis.id
                        AND ppi_spi_statuses.code = 'ppi_sent_to_wh_manager'
                    ) IS NOT NULL
                    THEN 'yes'
                END
            ) as boos_approved
        FROM ppi_spis
        LEFT JOIN ppi_products ON ppi_products.ppi_id = ppi_spis.id
        LEFT JOIN ppi_bundle_products ON ppi_bundle_products.ppi_product_id = ppi_products.id
        LEFT JOIN products ON products.id = ppi_products.product_id
        LEFT JOIN warehouses ON warehouses.id = ppi_products.warehouse_id
        WHERE ppi_spis.action_format = 'Ppi'
        AND products.id = ?
    ";

        $data = DB::select($sql, [$product_id]);

        return collect($data);
    }

    // public function ppiStockDetails($product_id){
    //        $data = DB::SELECT("
    //             SELECT
    //                 ppi_spis.id as ppi_id,
    //                 ppi_spis.transferable as transferable,
    //                 ppi_spis.purchase as purchase,
    //                 ppi_spis.action_format,
    //                 ppi_spis.ppi_spi_type,
    //                 ppi_spis.project as project,
    //                 ppi_spis.tran_type,
    //                 ppi_spis.warehouse_id,
    //                 ppi_spis.transferable,
    //                 ppi_spis.created_at,
    //                 ppi_products.id as ppi_product_id,
    //                 ppi_products.product_id as product_id,
    //                 products.name as product_name,
    //                 ppi_products.product_state,
    //                 ppi_products.health_status,
    //                 ppi_products.unit_price as unit_price,
    //                 ppi_products.price as price,
    //                 ppi_bundle_products.id as bundle_id,
    //                 ppi_bundle_products.bundle_size as bundle_size,
    //                 (CASE
    //                     WHEN ppi_bundle_products.bundle_size IS NOT NULL THEN ppi_bundle_products.bundle_size
    //                     ELSE ppi_products.qty
    //                 END) as product_qty,
    //                 warehouses.name as warehouse_name,
    //                 warehouses.code as warehouse_code,
    //                 (SELECT GROUP_CONCAT(who_source)  FROM ppi_spi_sources WHERE ppi_spis.id = ppi_spi_sources.ppi_spi_id) as who_source,
    //                 (
    //                     CASE
    //                     WHEN
    // 					(SELECT SUM(qty)  FROM product_stocks WHERE product_stocks.ppi_spi_id = ppi_spis.id AND product_stocks.ppi_spi_product_id = ppi_products.id)
    // 					THEN (
    // 						CASE
    //                          WHEN ppi_bundle_products.bundle_size IS NOT NULL THEN ppi_bundle_products.bundle_size
    //                          ELSE ppi_products.qty
    //                          END
    // 					)
    // 					ELSE NULL
    //                     END
    //                 ) as stock_in_qty,

    //                  (CASE
    //                       WHEN
    //                      	(
    //                           SELECT sum(waiting_stock_in) FROM temporary_stocks
    //                             WHERE temporary_stocks.action_format = 'Ppi'
    //                              AND temporary_stocks.ppi_product_id =  ppi_products.id
    //                           ) IS NOT NULL THEN 'yes'
    //                             ELSE
    //                             NULL
    //                              END
    //                         ) as is_waiting_to_stock_in,

    //                 (
    //                    CASE
    //                       WHEN
    //                      	( SELECT MAX(id) FROM ppi_spi_statuses
    //                        	WHERE ppi_spi_statuses.ppi_spi_id = ppi_spis.id
    //                         AND ppi_spi_statuses.ppi_spi_product_id = ppi_products.id
    //                        AND ppi_spi_statuses.code = 'ppi_new_product_added_to_stock')
    // 					   IS NOT NULL THEN 'yes'
    // 					    WHEN
    //                       (SELECT MAX(id) FROM ppi_spi_statuses
    //                        WHERE ppi_spi_statuses.ppi_spi_id = ppi_spis.id
    //                        AND ppi_spi_statuses.code = 'ppi_sent_to_wh_manager')
    // 					  IS NOT NULL THEN 'yes'
    //                      END
    //                 ) as boos_approved

    //             FROM ppi_spis
    //             LEFT JOIN ppi_products ON ppi_products.ppi_id = ppi_spis.id
    //             LEFT JOIN ppi_bundle_products ON ppi_bundle_products.ppi_product_id = ppi_products.id
    //             LEFT JOIN products ON products.id = ppi_products.product_id
    //             LEFT JOIN warehouses ON warehouses.id = ppi_products.warehouse_id
    //             WHERE ppi_spis.action_format = 'Ppi' AND products.id = {$product_id};
    //         ");
    //        return collect($data);
    // }

    public function spiStockDetails($product_id)
    {
        $sql = "
        SELECT mm.*,
               (IFNULL(mm.lended_project, mm.original_project)) as project
        FROM (
            SELECT
                ppi_spis.id as spi_id,
                ppi_spis.action_format,
                ppi_spis.ppi_spi_type,
                ppi_spis.project as original_project,
                (CASE
                    WHEN (
                        SELECT landed_project
                        FROM spi_product_loan_from_projects
                        WHERE spi_product_loan_from_projects.spi_product_id = spi_products.id
                          AND spi_product_loan_from_projects.status = 'processing'
                          AND spi_product_loan_from_projects.product_id = ?
                    ) IS NOT NULL
                    THEN (
                        SELECT landed_project
                        FROM spi_product_loan_from_projects
                        WHERE spi_product_loan_from_projects.spi_product_id = spi_products.id
                          AND spi_product_loan_from_projects.status = 'processing'
                          AND spi_product_loan_from_projects.product_id = ?
                    )
                    ELSE NULL
                 END
                ) as lended_project,
                ppi_spis.tran_type,
                ppi_spis.transferable,
                ppi_spis.created_at,
                ppi_products.id as ppi_product_id,
                ppi_products.product_id as product_id,
                products.name as product_name,
                ppi_products.product_state,
                ppi_products.ppi_id as ppi_id,
                ppi_products.health_status,
                spi_products.id as spi_product_id,
                spi_products.unit_price,
                spi_products.qty as product_qty,
                spi_products.warehouse_id,
                spi_products.from_warehouse,
                warehouses.name as warehouse_name,
                warehouses.code as warehouse_code,
                (
                    CASE
                        WHEN (spi_products.warehouse_id != spi_products.from_warehouse) THEN 'yes'
                        ELSE NULL
                    END
                ) as is_lended,
                (CASE
                     WHEN (spi_products.warehouse_id != spi_products.from_warehouse)
                          THEN (SELECT name FROM warehouses WHERE warehouses.id = spi_products.from_warehouse)
                     ELSE NULL
                 END) as from_warehouse_name,
                (SELECT GROUP_CONCAT(who_source)
                   FROM ppi_spi_sources
                  WHERE ppi_spis.id = ppi_spi_sources.ppi_spi_id
                ) as who_source,
                (
                    CASE
                        WHEN (
                            SELECT sum(waiting_stock_out)
                              FROM temporary_stocks
                             WHERE temporary_stocks.action_format = 'Spi'
                               AND temporary_stocks.spi_product_id = spi_products.id
                        ) IS NOT NULL
                        THEN 'yes'
                        ELSE NULL
                    END
                ) as is_waiting_to_stock_out,
                (
                    CASE
                        WHEN (
                            SELECT MAX(id)
                              FROM ppi_spi_statuses
                             WHERE ppi_spi_statuses.ppi_spi_id = ppi_spis.id
                               AND ppi_spi_statuses.ppi_spi_product_id = spi_products.id
                               AND ppi_spi_statuses.code = 'spi_product_out_from_stock'
                        ) IS NOT NULL
                        THEN 'yes'
                        WHEN (
                            SELECT MAX(id)
                              FROM ppi_spi_statuses
                             WHERE ppi_spi_statuses.ppi_spi_id = ppi_spis.id
                               AND ppi_spi_statuses.code = 'spi_sent_to_wh_manager'
                        ) IS NOT NULL
                        THEN 'yes'
                    END
                ) as boos_approved
            FROM ppi_spis
            LEFT JOIN spi_products ON spi_products.spi_id = ppi_spis.id
            LEFT JOIN ppi_products ON spi_products.ppi_product_id = ppi_products.id
            LEFT JOIN products ON products.id = spi_products.product_id
            LEFT JOIN warehouses ON warehouses.id = spi_products.from_warehouse
            WHERE ppi_spis.action_format = 'Spi'
              AND products.id = ?
        ) mm
    ";

        // 3 placeholders → pass 3 bindings
        $data = DB::select($sql, [$product_id, $product_id, $product_id]);

        return collect($data);
    }


    // public function spiStockDetails($product_id)
    // {
    //     $data = DB::select("
    //         SELECT mm.*,
    //         (IFNULL(mm.lended_project, mm.original_project)) as project
    //         FROM
    //             (SELECT
    //                 ppi_spis.id as spi_id,
    //                 ppi_spis.action_format,
    //                 ppi_spis.ppi_spi_type,
    //                 ppi_spis.project as original_project,
    //                 (CASE
    //                     WHEN (
    //                         SELECT landed_project FROM spi_product_loan_from_projects
    //                         WHERE spi_product_loan_from_projects.spi_product_id = spi_products.id
    //                         AND spi_product_loan_from_projects.status = 'processing'
    //                         AND spi_product_loan_from_projects.product_id = {$product_id}
    //                     ) IS NOT NULL THEN (
    //                         SELECT landed_project FROM spi_product_loan_from_projects
    //                         WHERE spi_product_loan_from_projects.spi_product_id = spi_products.id
    //                          AND spi_product_loan_from_projects.status = 'processing'
    //                         AND spi_product_loan_from_projects.product_id = {$product_id}
    //                     )
    //                     ELSE
    //                     NULL
    //                     END
    //                 ) as lended_project,
    //                 ppi_spis.tran_type,
    //                 ppi_spis.transferable,
    //                 ppi_spis.created_at,
    //                 ppi_products.id as ppi_product_id,
    //                 ppi_products.product_id as product_id,
    //                 products.name as product_name,
    //                 ppi_products.product_state,
    //                 ppi_products.ppi_id as ppi_id,
    //                 ppi_products.health_status,
    // 				spi_products.id as spi_product_id,
    //                 spi_products.unit_price,
    //                 spi_products.qty as product_qty,

    //                 spi_products.warehouse_id,
    //                 spi_products.from_warehouse,
    //                	warehouses.name as warehouse_name,
    //                	warehouses.code as warehouse_code,
    //                 (
    // 					CASE
    //                     WHEN
    //                     (spi_products.warehouse_id != spi_products.from_warehouse)  THEN 'yes'
    //                     ELSE
    // 					NULL
    //                     END
    // 				) as is_lended,
    //                 (CASE
    //                  	WHEN
    //                     (spi_products.warehouse_id != spi_products.from_warehouse)
    //                  		THEN (SELECT name FROM warehouses WHERE warehouses.id = spi_products.from_warehouse)
    //                  	ELSE
    //                  	NULL
    //                  	END) as from_warehouse_name,

    // 			 (SELECT GROUP_CONCAT(who_source)  FROM ppi_spi_sources WHERE ppi_spis.id = ppi_spi_sources.ppi_spi_id) as who_source,

    // 			  (
    //                    CASE
    //                       WHEN
    //                      	(
    //                             #SELECT MAX(id) FROM ppi_spi_statuses
    //                           #WHERE ppi_spi_statuses.ppi_spi_id = ppi_spis.id
    //                           #AND ppi_spi_statuses.ppi_spi_product_id = spi_products.id
    //                           #AND ppi_spi_statuses.code = 'spi_product_out_from_stock'
    //                           SELECT sum(waiting_stock_out) FROM temporary_stocks
    //                             WHERE temporary_stocks.action_format = 'Spi'
    //                              AND temporary_stocks.spi_product_id =  spi_products.id
    //                           )
    //                                  IS NOT NULL THEN 'yes'
    //                             ELSE
    //                             NULL
    //                              END
    //                         ) as is_waiting_to_stock_out,

    //                 (
    //                    CASE
    //                       WHEN
    //                      	( SELECT MAX(id) FROM ppi_spi_statuses
    //                        	WHERE ppi_spi_statuses.ppi_spi_id = ppi_spis.id
    //                         AND ppi_spi_statuses.ppi_spi_product_id = spi_products.id
    //                        AND ppi_spi_statuses.code = 'spi_product_out_from_stock')
    // 					   IS NOT NULL THEN 'yes'
    // 					    WHEN
    //                       (SELECT MAX(id) FROM ppi_spi_statuses
    //                        WHERE ppi_spi_statuses.ppi_spi_id = ppi_spis.id
    //                        AND ppi_spi_statuses.code = 'spi_sent_to_wh_manager')
    // 					  IS NOT NULL THEN 'yes'
    //                      END
    //                 ) as boos_approved

    //             FROM ppi_spis
    //             LEFT JOIN spi_products ON spi_products.spi_id = ppi_spis.id
    //             LEFT JOIN ppi_products ON spi_products.ppi_product_id = ppi_products.id
    //             LEFT JOIN products ON products.id = spi_products.product_id
    //             LEFT JOIN warehouses ON warehouses.id = spi_products.from_warehouse
    //             WHERE ppi_spis.action_format = 'Spi' AND products.id = {$product_id}) mm
    //     ");
    //     return collect($data);
    // }

    public function spiStockDetailsWithProjectName($product_id, $project_name)
    {
        $sql = "
        SELECT mm.*,
               (IFNULL(mm.lended_project, mm.original_project)) as project
        FROM
        (
            SELECT
                ppi_spis.id as spi_id,
                ppi_spis.action_format,
                ppi_spis.ppi_spi_type,
                ppi_spis.project as original_project,
                (
                    CASE
                        WHEN (
                            SELECT landed_project
                            FROM spi_product_loan_from_projects
                            WHERE spi_product_loan_from_projects.spi_product_id = spi_products.id
                              AND spi_product_loan_from_projects.status = 'processing'
                              AND spi_product_loan_from_projects.product_id = ?
                        ) IS NOT NULL
                        THEN (
                            SELECT landed_project
                            FROM spi_product_loan_from_projects
                            WHERE spi_product_loan_from_projects.spi_product_id = spi_products.id
                              AND spi_product_loan_from_projects.status = 'processing'
                              AND spi_product_loan_from_projects.product_id = ?
                        )
                        ELSE NULL
                    END
                ) as lended_project,
                ppi_spis.tran_type,
                ppi_spis.transferable,
                ppi_spis.created_at,
                ppi_products.id as ppi_product_id,
                ppi_products.product_id as product_id,
                products.name as product_name,
                ppi_products.product_state,
                ppi_products.ppi_id as ppi_id,
                ppi_products.health_status,
                spi_products.id as spi_product_id,
                spi_products.unit_price,
                spi_products.qty as product_qty,

                spi_products.warehouse_id,
                spi_products.from_warehouse,
                warehouses.name as warehouse_name,
                warehouses.code as warehouse_code,

                (
                    CASE
                        WHEN (spi_products.warehouse_id != spi_products.from_warehouse) THEN 'yes'
                        ELSE NULL
                    END
                ) as is_lended,

                (
                    CASE
                        WHEN (spi_products.warehouse_id != spi_products.from_warehouse)
                             THEN (SELECT name FROM warehouses WHERE warehouses.id = spi_products.from_warehouse)
                        ELSE NULL
                    END
                ) as from_warehouse_name,

                (SELECT GROUP_CONCAT(who_source)
                 FROM ppi_spi_sources
                 WHERE ppi_spis.id = ppi_spi_sources.ppi_spi_id) as who_source,

                (
                    CASE
                        WHEN (
                            SELECT SUM(waiting_stock_out)
                            FROM temporary_stocks
                            WHERE temporary_stocks.action_format = 'Spi'
                              AND temporary_stocks.spi_product_id = spi_products.id
                        ) IS NOT NULL
                        THEN 'yes'
                        ELSE NULL
                    END
                ) as is_waiting_to_stock_out,

                (
                    CASE
                        WHEN (
                            SELECT MAX(id)
                            FROM ppi_spi_statuses
                            WHERE ppi_spi_statuses.ppi_spi_id = ppi_spis.id
                              AND ppi_spi_statuses.ppi_spi_product_id = spi_products.id
                              AND ppi_spi_statuses.code = 'spi_product_out_from_stock'
                        ) IS NOT NULL
                        THEN 'yes'

                        WHEN (
                            SELECT MAX(id)
                            FROM ppi_spi_statuses
                            WHERE ppi_spi_statuses.ppi_spi_id = ppi_spis.id
                              AND ppi_spi_statuses.code = 'spi_sent_to_wh_manager'
                        ) IS NOT NULL
                        THEN 'yes'
                    END
                ) as boos_approved

            FROM ppi_spis
            LEFT JOIN spi_products ON spi_products.spi_id = ppi_spis.id
            LEFT JOIN ppi_products ON spi_products.ppi_product_id = ppi_products.id
            LEFT JOIN products ON products.id = spi_products.product_id
            LEFT JOIN warehouses ON warehouses.id = spi_products.from_warehouse
            WHERE ppi_spis.action_format = 'Spi'
              AND products.id = ?
              AND ppi_spis.project = ?
        ) mm
    ";

        $data = DB::select($sql, [$product_id, $product_id, $product_id, $project_name]);
        return collect($data);
    }


    // public function spiStockDetailsWithProjectName($product_id, $project_name)
    // {
    //     $data = DB::select("
    //         SELECT mm.*,
    //         (IFNULL(mm.lended_project, mm.original_project)) as project
    //         FROM
    //             (SELECT
    //                 ppi_spis.id as spi_id,
    //                 ppi_spis.action_format,
    //                 ppi_spis.ppi_spi_type,
    //                 ppi_spis.project as original_project,
    //                 (CASE
    //                     WHEN (
    //                         SELECT landed_project FROM spi_product_loan_from_projects
    //                         WHERE spi_product_loan_from_projects.spi_product_id = spi_products.id
    //                         AND spi_product_loan_from_projects.status = 'processing'
    //                         AND spi_product_loan_from_projects.product_id = {$product_id}
    //                     ) IS NOT NULL THEN (
    //                         SELECT landed_project FROM spi_product_loan_from_projects
    //                         WHERE spi_product_loan_from_projects.spi_product_id = spi_products.id
    //                          AND spi_product_loan_from_projects.status = 'processing'
    //                         AND spi_product_loan_from_projects.product_id = {$product_id}
    //                     )
    //                     ELSE
    //                     NULL
    //                     END
    //                 ) as lended_project,
    //                 ppi_spis.tran_type,
    //                 ppi_spis.transferable,
    //                 ppi_spis.created_at,
    //                 ppi_products.id as ppi_product_id,
    //                 ppi_products.product_id as product_id,
    //                 products.name as product_name,
    //                 ppi_products.product_state,
    //                 ppi_products.ppi_id as ppi_id,
    //                 ppi_products.health_status,
    // 				spi_products.id as spi_product_id,
    //                 spi_products.unit_price,
    //                 spi_products.qty as product_qty,

    //                 spi_products.warehouse_id,
    //                 spi_products.from_warehouse,
    //                	warehouses.name as warehouse_name,
    //                	warehouses.code as warehouse_code,
    //                 (
    // 					CASE
    //                     WHEN
    //                     (spi_products.warehouse_id != spi_products.from_warehouse)  THEN 'yes'
    //                     ELSE
    // 					NULL
    //                     END
    // 				) as is_lended,
    //                 (CASE
    //                  	WHEN
    //                     (spi_products.warehouse_id != spi_products.from_warehouse)
    //                  		THEN (SELECT name FROM warehouses WHERE warehouses.id = spi_products.from_warehouse)
    //                  	ELSE
    //                  	NULL
    //                  	END) as from_warehouse_name,

    // 			 (SELECT GROUP_CONCAT(who_source)  FROM ppi_spi_sources WHERE ppi_spis.id = ppi_spi_sources.ppi_spi_id) as who_source,

    // 			  (
    //                    CASE
    //                       WHEN
    //                      	(
    //                             #SELECT MAX(id) FROM ppi_spi_statuses
    //                           #WHERE ppi_spi_statuses.ppi_spi_id = ppi_spis.id
    //                           #AND ppi_spi_statuses.ppi_spi_product_id = spi_products.id
    //                           #AND ppi_spi_statuses.code = 'spi_product_out_from_stock'
    //                           SELECT sum(waiting_stock_out) FROM temporary_stocks
    //                             WHERE temporary_stocks.action_format = 'Spi'
    //                              AND temporary_stocks.spi_product_id =  spi_products.id
    //                           )
    //                                  IS NOT NULL THEN 'yes'
    //                             ELSE
    //                             NULL
    //                              END
    //                         ) as is_waiting_to_stock_out,

    //                 (
    //                    CASE
    //                       WHEN
    //                      	( SELECT MAX(id) FROM ppi_spi_statuses
    //                        	WHERE ppi_spi_statuses.ppi_spi_id = ppi_spis.id
    //                         AND ppi_spi_statuses.ppi_spi_product_id = spi_products.id
    //                        AND ppi_spi_statuses.code = 'spi_product_out_from_stock')
    // 					   IS NOT NULL THEN 'yes'
    // 					    WHEN
    //                       (SELECT MAX(id) FROM ppi_spi_statuses
    //                        WHERE ppi_spi_statuses.ppi_spi_id = ppi_spis.id
    //                        AND ppi_spi_statuses.code = 'spi_sent_to_wh_manager')
    // 					  IS NOT NULL THEN 'yes'
    //                      END
    //                 ) as boos_approved

    //             FROM ppi_spis
    //             LEFT JOIN spi_products ON spi_products.spi_id = ppi_spis.id
    //             LEFT JOIN ppi_products ON spi_products.ppi_product_id = ppi_products.id
    //             LEFT JOIN products ON products.id = spi_products.product_id
    //             LEFT JOIN warehouses ON warehouses.id = spi_products.from_warehouse
    //             WHERE ppi_spis.action_format = 'Spi' AND products.id = {$product_id} AND ppi_spis.project = '{$project_name}') mm
    //     ");
    //     return collect($data);
    // }
}
