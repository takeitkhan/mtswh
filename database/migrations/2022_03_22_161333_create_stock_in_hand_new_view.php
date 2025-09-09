<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockInHandViewNew extends Migration
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
    CREATE VIEW stock_in_hand_new AS

         SELECT products.id, products.name, products.code, products.category_id, products.barcode_format, products.unit_id, products.barcode_prefix,products.stock_qty_alert,
            SUM(IF(product_stocks.action_format = 'Ppi', product_stocks.qty, 0)) as stock_in,
            SUM(IF(product_stocks.action_format = 'Spi', product_stocks.qty, 0)) as stock_out,
            (
                SELECT sum(waiting_stock_in) FROM temporary_stocks WHERE product_id = products.id AND action_format='Ppi'
            )as waiting_stock_in,
            (
                SELECT IF(waiting_stock_out, sum(waiting_stock_out), 0) FROM temporary_stocks WHERE product_id = products.id AND action_format='Spi'
            )as waiting_stock_out,

            (
                SUM(IF(product_stocks.action_format = 'Ppi', product_stocks.qty, 0))
                -
                SUM(IF(product_stocks.action_format = 'Spi', product_stocks.qty, 0))
                -(SELECT IF(waiting_stock_out, sum(waiting_stock_out), 0) FROM temporary_stocks WHERE temporary_stocks.product_id = products.id AND temporary_stocks.action_format='Spi'))AS stock_in_hand

    FROM products

    LEFT JOIN product_stocks ON product_stocks.product_id = products.id

    GROUP By products.id;
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
