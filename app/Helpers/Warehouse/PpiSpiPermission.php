<?php 
namespace App\Helpers\Warehouse;
use Auth;
class PpiSpiPermission {

    public static function elements(){ 
    ?>
        
        <?php if(auth()->user()->hasRoutePermission('ppi_product_price_show_element') == false): ?>
            <script>
                document.querySelectorAll('#ppi_content .ppi_product_price_show, .ppi_product_price_show').forEach(el => el.remove());
            </script>           
        <?php endif;?>

        <?php if(auth()->user()->hasRoutePermission('ppi_set_product_add') == false): ?>
            <script>
                document.querySelectorAll('#ppi_content .ppi_set_product_add').forEach(el => el.remove());
            </script>           
        <?php endif;?>
        
        <?php if(auth()->user()->hasRoutePermission('ppi_product_edit') == false): ?>
            <script>
                document.querySelectorAll('#ppi_content .ppi_product_edit').forEach(el => el.remove());
            </script>           
        <?php endif;?>

        <?php if(auth()->user()->hasRoutePermission('ppi_product_destroy') == false): ?>
            <script>
                document.querySelectorAll('#ppi_content .ppi_product_destroy').forEach(el => el.remove());
            </script>           
        <?php endif;?>

        

    <?php }


}