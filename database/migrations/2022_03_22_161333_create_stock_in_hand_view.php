<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockInHandView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
        \DB::unprepared("
            CREATE VIEW stock_in_hand AS
            SELECT
                id,
                name,
                code,
                barcode_prefix,
                stock_qty_alert,
                category_id,
                (SELECT COUNT(*) FROM product_stocks WHERE product_id = products.id AND action_format = 'Ppi') AS stock_in,
                (SELECT COUNT(*) FROM product_stocks WHERE product_id = products.id AND action_format = 'Spi') AS stock_out,
                ((SELECT COUNT(*) FROM product_stocks WHERE product_id = products.id AND action_format = 'Ppi') - (SELECT COUNT(*) FROM product_stocks WHERE product_id = products.id AND action_format = 'Spi')) AS stock_in_hand,
                (SELECT `getWhBasedData`(products.id)) AS warehouse_based_data
            FROM products
        ");
        */


        \DB::unprepared("
            CREATE VIEW stock_in_hand AS
            SELECT
                id,
                name,
                code,
                unit_id,
                barcode_prefix,
                barcode_format,
                stock_qty_alert,
                category_id,
                (SELECT IFNULL(SUM(qty),0) FROM product_stocks WHERE product_id = products.id AND action_format = 'Ppi') AS stock_in,
                (SELECT SUM(`mtswarehouse`.`temporary_stocks`.`waiting_stock_in`) from `mtswarehouse`.`temporary_stocks`
                        where (`mtswarehouse`.`temporary_stocks`.`product_id` = `mtswarehouse`.`products`.`id`)) AS `waiting_stockin`,
                (SELECT IFNULL(SUM(qty),0) FROM product_stocks WHERE product_id = products.id AND action_format = 'Spi') AS stock_out,
                (SELECT SUM(`mtswarehouse`.`temporary_stocks`.`waiting_stock_out`) from `mtswarehouse`.`temporary_stocks`
                        where (`mtswarehouse`.`temporary_stocks`.`product_id` = `mtswarehouse`.`products`.`id`)) AS `waiting_stockout`,
                ((SELECT IFNULL(SUM(qty), 0) FROM product_stocks WHERE product_id = products.id AND action_format = 'Ppi') - (SELECT IFNULL(SUM(qty),0) FROM product_stocks WHERE product_id = products.id AND action_format = 'Spi')) AS stock_in_hand,
                (SELECT `getWhBasedData`(products.id)) AS warehouse_based_data
            FROM products
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //\DB::statement($this->dropView());


        /**
         * waiting_stock_in
         * BEGIN
        DECLARE waiting_stock_in VARCHAR(255);
        SET waiting_stock_in = (SELECT SUM(ppi_products.qty) FROM `ppi_products` LEFT JOIN `product_stocks` ON ppi_products.id = product_stocks.ppi_spi_product_id WHERE ppi_products.product_id = prdId AND product_stocks.id IS NULL);
        RETURN waiting_stock_in;
        END
         *
         *
         *
         */


        /**
         * waiting_stock_out
         * BEGIN
        DECLARE waiting_stock_out VARCHAR(255);
        SET waiting_stock_out = (SELECT SUM(spi_products.qty) FROM `spi_products` LEFT JOIN `product_stocks` ON spi_products.id = product_stocks.ppi_spi_product_id
        WHERE spi_products.product_id = prdId AND product_stocks.id IS NULL);
        RETURN waiting_stock_out;
        END
         *
         *
         *
         */

    }

}
