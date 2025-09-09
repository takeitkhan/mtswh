<?php 
namespace App\Helpers\Warehouse;
use Auth;
class PpiSpiPermission {

    public static function elements(){ 
    ?>
        
        <?php if(auth()->user()->hasRoutePermission('ppi_product_price_show_element') == false): ?>
            <script>
                $('#ppi_content .ppi_product_price_show, .ppi_product_price_show').remove();
            </script>           
        <?php endif;?>

        <?php if(auth()->user()->hasRoutePermission('ppi_set_product_add') == false): ?>
            <script>
                $('#ppi_content .ppi_set_product_add').remove();
            </script>           
        <?php endif;?>
        
        <?php if(auth()->user()->hasRoutePermission('ppi_product_edit') == false): ?>
            <script>
                $('#ppi_content .ppi_product_edit').remove();
            </script>           
        <?php endif;?>

        <?php if(auth()->user()->hasRoutePermission('ppi_product_destroy') == false): ?>
            <script>
                $('#ppi_content .ppi_product_destroy').remove();
            </script>           
        <?php endif;?>

        

    <?php }


}