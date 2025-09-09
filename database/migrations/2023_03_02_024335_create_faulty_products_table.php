<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaultyProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('faulty_products', function (Blueprint $table) {
//            $table->id();
//            $table->timestamps();
//        });
        /*
        \DB::unprepared("
            CREATE VIEW faulty_products AS
                SELECT
                *,
                (SELECT SUM(ppi_products.qty)  FROM `ppi_products` WHERE `health_status` = 'Faulty' AND product_id = products.id) AS faulty_product

                FROM products
                ORDER BY `products`.`id` DESC
        ");
        */
        \DB::unprepared("
        CREATE VIEW faulty_products AS
SELECT *,
                 (SELECT SUM(ppi_products.qty)  FROM `ppi_products` WHERE `health_status` = 'Faulty' AND product_id = products.id) AS faulty_product,
                 (SELECT SUM(ppi_bundle_products.bundle_size)
                        FROM `ppi_bundle_products`
                        LEFT JOIN ppi_products ON ppi_bundle_products.ppi_product_id = ppi_products.id
                        WHERE ppi_products.product_id = products.id
                        AND ppi_products.health_status = 'Faulty'
                 ) AS faulty_product_bundle
                 FROM products
                ORDER BY `products`.`id` DESC;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::dropIfExists('faulty_products');
    }
}
