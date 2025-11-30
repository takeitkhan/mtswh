
DELIMITER $$
--
-- Functions
--
CREATE FUNCTION `getWhBasedData` (`prdId` INT) RETURNS VARCHAR(255) CHARSET utf8 DETERMINISTIC BEGIN
    DECLARE result VARCHAR(255);
    SET result = (
        SELECT GROUP_CONCAT(
            id, '|',
            (SELECT IFNULL(SUM(qty), 0) 
             FROM product_stocks 
             WHERE product_id = prdId AND warehouse_id = warehouses.id AND action_format = 'Ppi'), '|',
            (SELECT IFNULL(SUM(qty), 0) 
             FROM product_stocks 
             WHERE product_id = prdId AND warehouse_id = warehouses.id AND action_format = 'Spi'), '|',
            ((SELECT IFNULL(SUM(qty), 0) 
              FROM product_stocks 
              WHERE product_id = prdId AND warehouse_id = warehouses.id AND action_format = 'Ppi') - 
             (SELECT IFNULL(SUM(qty), 0) 
              FROM product_stocks 
              WHERE product_id = prdId AND warehouse_id = warehouses.id AND action_format = 'Spi'))
        ) FROM warehouses
    );
    RETURN result;
END$$

DELIMITER ;





--
-- Structure for view `stock_in_hand`
--
DROP TABLE IF EXISTS `stock_in_hand`;

CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `stock_in_hand`  AS SELECT `products`.`id` AS `id`, `products`.`name` AS `name`, `products`.`code` AS `code`, `products`.`unit_id` AS `unit_id`, `products`.`barcode_prefix` AS `barcode_prefix`, `products`.`barcode_format` AS `barcode_format`, `products`.`stock_qty_alert` AS `stock_qty_alert`, `products`.`category_id` AS `category_id`, (select ifnull(sum(`product_stocks`.`qty`),0) from `product_stocks` where ((`product_stocks`.`product_id` = `products`.`id`) and (`product_stocks`.`action_format` = 'Ppi'))) AS `stock_in`, (select sum(`temporary_stocks`.`waiting_stock_in`) from `temporary_stocks` where (`temporary_stocks`.`product_id` = `products`.`id`)) AS `waiting_stockin`, (select ifnull(sum(`product_stocks`.`qty`),0) from `product_stocks` where ((`product_stocks`.`product_id` = `products`.`id`) and (`product_stocks`.`action_format` = 'Spi'))) AS `stock_out`, (select sum(`temporary_stocks`.`waiting_stock_out`) from `temporary_stocks` where (`temporary_stocks`.`product_id` = `products`.`id`)) AS `waiting_stockout`, ((select ifnull(sum(`product_stocks`.`qty`),0) from `product_stocks` where ((`product_stocks`.`product_id` = `products`.`id`) and (`product_stocks`.`action_format` = 'Ppi'))) - (select ifnull(sum(`product_stocks`.`qty`),0) from `product_stocks` where ((`product_stocks`.`product_id` = `products`.`id`) and (`product_stocks`.`action_format` = 'Spi')))) AS `stock_in_hand`, (select `getWhBasedData`(`products`.`id`)) AS `warehouse_based_data` FROM `products` ;

-- --------------------------------------------------------

--
-- Structure for view `stock_in_hand_new`
--
DROP TABLE IF EXISTS `stock_in_hand_new`;

CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `stock_in_hand_new`  AS SELECT `products`.`id` AS `id`, `products`.`name` AS `name`, `products`.`code` AS `code`, `products`.`category_id` AS `category_id`, `products`.`barcode_format` AS `barcode_format`, `products`.`unit_id` AS `unit_id`, `products`.`barcode_prefix` AS `barcode_prefix`, `products`.`stock_qty_alert` AS `stock_qty_alert`, sum(if((`product_stocks`.`action_format` = 'Ppi'),`product_stocks`.`qty`,0)) AS `stock_in`, sum(if((`product_stocks`.`action_format` = 'Spi'),`product_stocks`.`qty`,0)) AS `stock_out`, (select sum(`temporary_stocks`.`waiting_stock_in`) from `temporary_stocks` where ((`temporary_stocks`.`product_id` = `products`.`id`) and (`temporary_stocks`.`action_format` = 'Ppi'))) AS `waiting_stock_in`, (select if(`temporary_stocks`.`waiting_stock_out`,sum(`temporary_stocks`.`waiting_stock_out`),0) from `temporary_stocks` where ((`temporary_stocks`.`product_id` = `products`.`id`) and (`temporary_stocks`.`action_format` = 'Spi'))) AS `waiting_stock_out`, ((sum(if((`product_stocks`.`action_format` = 'Ppi'),`product_stocks`.`qty`,0)) - sum(if((`product_stocks`.`action_format` = 'Spi'),`product_stocks`.`qty`,0))) - (select if(`temporary_stocks`.`waiting_stock_out`,sum(`temporary_stocks`.`waiting_stock_out`),0) from `temporary_stocks` where ((`temporary_stocks`.`product_id` = `products`.`id`) and (`temporary_stocks`.`action_format` = 'Spi')))) AS `stock_in_hand` FROM (`products` left join `product_stocks` on((`product_stocks`.`product_id` = `products`.`id`))) GROUP BY `products`.`id` ;

-- --------------------------------------------------------

--
-- Structure for view `stock_in_hand_old`
--
DROP TABLE IF EXISTS `stock_in_hand_old`;

CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `stock_in_hand_old`  AS SELECT `products`.`id` AS `id`, `products`.`name` AS `name`, `products`.`code` AS `code`, `products`.`unit_id` AS `unit_id`, `products`.`barcode_prefix` AS `barcode_prefix`, `products`.`stock_qty_alert` AS `stock_qty_alert`, `products`.`category_id` AS `category_id`, (select ifnull(sum(`product_stocks`.`qty`),0) from `product_stocks` where ((`product_stocks`.`product_id` = `products`.`id`) and (`product_stocks`.`action_format` = 'Ppi'))) AS `stock_in`, (select ifnull(sum(`product_stocks`.`qty`),0) from `product_stocks` where ((`product_stocks`.`product_id` = `products`.`id`) and (`product_stocks`.`action_format` = 'Spi'))) AS `stock_out`, ((select ifnull(sum(`product_stocks`.`qty`),0) from `product_stocks` where ((`product_stocks`.`product_id` = `products`.`id`) and (`product_stocks`.`action_format` = 'Ppi'))) - (select ifnull(sum(`product_stocks`.`qty`),0) from `product_stocks` where ((`product_stocks`.`product_id` = `products`.`id`) and (`product_stocks`.`action_format` = 'Spi')))) AS `stock_in_hand`, (select `getWhBasedData`(`products`.`id`)) AS `warehouse_based_data` FROM `products` ;

-- --------------------------------------------------------

--
-- Structure for view `tempo_import`
--
DROP TABLE IF EXISTS `tempo_import`;

CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `tempo_import`  AS SELECT `product_stocks`.`id` AS `id`, `product_stocks`.`action_format` AS `action_format`, `product_stocks`.`ppi_spi_product_id` AS `ppi_spi_product_id`, `product_stocks`.`product_id` AS `product_id`, (select `spi_products`.`ppi_product_id` from `spi_products` where (`spi_products`.`id` = `product_stocks`.`ppi_spi_product_id`)) AS `ppi_product_id`, `product_stocks`.`qty` AS `qty`, `product_stocks`.`stock_action` AS `stock_action` FROM `product_stocks` WHERE (`product_stocks`.`action_format` = 'Spi') ;
