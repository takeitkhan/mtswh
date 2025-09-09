<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePpiSiteBasedProductReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::unprepared("
            CREATE VIEW  ppi_site_based_products AS
SELECT  products.id, products.name,
	sum(ppi_products.qty) AS total_qty,
    sum(ppi_bundle_products.bundle_size) As bundle_size,

    (SELECT sum(ppd.qty) FROM ppi_products as ppd WHERE ppd.health_status = 'Faulty' AND ppd.product_id = products.id LIMIT 1) As faulty_qty,

    (SELECT sum(ppd.qty) FROM ppi_products as ppd WHERE ppd.health_status = 'Scrapped'  AND ppd.product_id = products.id LIMIT 1) As scrapped_qty,

    ppi_spi_sources.who_source as site_name

	from products
    LEFT JOIN ppi_products
    ON products.id = ppi_products.product_id

    LEFT JOIN ppi_spi_sources
    ON ppi_spi_sources.ppi_spi_id = ppi_products.ppi_id

    LEFT JOIN ppi_bundle_products
    ON ppi_bundle_products.ppi_product_id = ppi_products.id

    WHERE ppi_spi_sources.source_type = 'Site'
    AND ppi_spi_sources.action_format = 'Ppi'
    GROUP BY products.id
    ORDER BY products.id;

        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::dropIfExists('ppi_site_based_product_reports');
    }
}
