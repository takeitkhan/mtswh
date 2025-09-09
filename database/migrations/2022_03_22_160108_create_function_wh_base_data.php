<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
//use DB;
class CreateFunctionWhBaseData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        \DB::unprepared("
               CREATE DEFINER=`root`@`localhost` FUNCTION `getWhBasedData`(
                    `prdId` INT
                )
                RETURNS varchar(255) CHARSET latin1
                LANGUAGE SQL
                DETERMINISTIC
                CONTAINS SQL
                SQL SECURITY DEFINER
                COMMENT ''
                BEGIN
                DECLARE result VARCHAR(255);
                SET result = (
                    SELECT GROUP_CONCAT(
                        id, '|',
                        (SELECT IFNULL(SUM(qty),0) FROM product_stocks WHERE product_id = prdId AND warehouse_id = warehouses.id AND action_format = 'Ppi'), '|',
                        (SELECT IFNULL(SUM(qty),0) FROM product_stocks WHERE product_id = prdId AND warehouse_id = warehouses.id AND action_format = 'Spi'), '|',
                        ((SELECT IFNULL(SUM(qty), 0) FROM product_stocks WHERE product_id = prdId AND warehouse_id = warehouses.id AND action_format = 'Ppi') - (SELECT IFNULL(SUM(qty),0) FROM product_stocks WHERE product_id = prdId AND warehouse_id = warehouses.id AND action_format = 'Spi'))
                    ) FROM warehouses);
                RETURN result;
                END
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::dropIfExists('function_wh_base_data');
    }
}
