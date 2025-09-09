<?php

namespace App\Helpers\Warehouse;
class PpiSpiHelper
{

    public static function ppiStatusHandler()
    {
        $status = [
            'ppi_created' => [
                'key' => 'ppi_created',
                'message' => 'Ppi created',
                'status_type' => 'success',
                'status_format' => 'Main',
                'is_route' => false,
                'route_title' => '',
                'route_upload' => false,
            ],

            'ppi_created_through_transfer' => [
                'key' => 'ppi_created',
                'message' => 'Ppi created Through Transfer',
                'status_type' => 'success',
                'status_format' => 'Optional',
                'is_route' => false,
                'route_title' => '',
                'route_upload' => false,
            ],
            'ppi_created_through_purchase_from_vendor' => [
                'key' => 'ppi_created_through_purchase_from_vendor',
                'message' => 'Ppi created Through Purchase From Vendor',
                'status_type' => 'success',
                'status_format' => 'Optional',
                'is_route' => false,
                'route_title' => '',
                'route_upload' => false,
            ],

            'ppi_product_added' => [
                'key' => 'ppi_product_added',
                'message' => 'Product Added in PPI',
                'status_type' => 'success',
                'status_format' => 'Optional',
                'is_route' => false,
                'route_title' => '',
                'route_upload' => false,
            ],

            'ppi_set_created' => [
                'key' => 'ppi_set_created',
                'message' => 'Set Created in PPI',
                'status_type' => 'success',
                'status_format' => 'Optional',
                'is_route' => false,
                'route_title' => '',
                'route_upload' => false,
            ],

            'ppi_set_deleted' => [
                'key' => 'ppi_set_deleted',
                'message' => 'Set Deleted from PPI',
                'status_type' => 'danger',
                'status_format' => 'Optional',
                'is_route' => false,
                'route_title' => '',
                'route_upload' => false,
            ],

            'ppi_product_remove_from_set' => [
                'key' => 'ppi_product_remove_from_set',
                'message' => 'Product Remove from Set in PPI',
                'status_type' => 'danger',
                'status_format' => 'Optional',
                'is_route' => false,
                'route_title' => '',
                'route_upload' => false,
            ],

            'ppi_product_edited' => [
                'key' => 'ppi_product_edited',
                'message' => 'Product Edited in PPI',
                'status_type' => 'purple',
                'status_format' => 'Optional',
                'is_route' => false,
                'route_title' => '',
                'route_upload' => false,
            ],
            'ppi_product_deleted' => [
                'key' => 'ppi_product_deleted',
                'message' => 'Product Deleted from PPI',
                'status_type' => 'danger',
                'status_format' => 'Optional',
                'is_route' => false,
                'route_title' => '',
                'route_upload' => false,
            ],

            'ppi_edited' => [
                'key' => 'ppi_edited',
                'message' => 'Ppi edited',
                'status_type' => 'warning',
                'status_format' => 'Optional',
                'is_route' => false,
                'route_title' => '',
                'route_upload' => false,
            ],

            'ppi_sent_to_boss' => [
                'key' => 'ppi_sent_to_boss',
                'message' => 'Ppi sent to Boss',
                'status_type' => 'success',
                'status_format' => 'Main',
                'is_route' => true,
                'route_title' => 'Sent to Boss',
                'route_upload' => true,
            ],
            /*
            'ppi_dispute_by_boss' => [
                'key' => 'ppi_dispute_by_boss',
                'message' => 'Dispute by Boss',
                'status_type' => 'danger',
                'is_route' => true,
                'route_title' => 'Dispute by Boss',
            ],
            */

            'ppi_sent_to_wh_manager' => [
                'key' => 'ppi_sent_to_wh_manager',
                'message' => 'Ppi sent to Warehouse Manager',
                'status_type' => 'success',
                'status_format' => 'Main',
                'is_route' => true,
                'route_title' => 'Sent to Warehouse manager',
                'route_upload' => true,
            ],
            'ppi_ready_to_physical_validation' => [
                'key' => 'ppi_ready_to_physical_validation',
                'message' => 'Ppi Ready to physical validation',
                'status_type' => 'success',
                'status_format' => 'Main',
                'is_route' => true,
                'route_title' => 'Ready to physical validation',
                'route_upload' => true,
            ],
            'ppi_dispute_by_wh_manager' => [
                'key' => 'ppi_dispute_by_wh_manager',
                'message' => 'Dispute by Warehouse Manager',
                'status_type' => 'danger',
                'status_format' => 'Main',
                'is_route' => true,
                'route_title' => 'Dispute by Warehouse Manager',
                'route_upload' => true,
            ],

            'ppi_product_info_correction_by_boss' => [
                'key' => 'ppi_product_info_correction_by_boss',
                'message' => 'Ppi Product Information Correction by Boss',
                'status_type' => 'success',
                'status_format' => 'Optional',
                'is_route' => true,
                'route_title' => 'Ppi Product Information correction by boss',
                'route_upload' => true,
            ],

            'ppi_correction_done_by_boss' => [
                'key' => 'ppi_correction_done_by_boss',
                'message' => 'Ppi Correction Done by Boss',
                'status_type' => 'success',
                'status_format' => 'Main',
                'is_route' => false,
                'route_title' => 'Ppi correction Done by boss',
                'route_upload' => false,
            ],

            'ppi_resent_to_wh_manager' => [
                'key' => 'ppi_resent_to_wh_manager',
                'message' => 'Ppi Resent to Warehouse Manager',
                'status_type' => 'success',
                'status_format' => 'Main',
                'is_route' => true,
                'route_title' => 'Ppi resent to Warehouse Manager',
                'route_upload' => true,
            ],

            'ppi_agreed_no_dispute' => [
                'key' => 'ppi_agreed_no_dispute',
                'message' => 'Agreed that no dispute',
                'status_type' => 'success',
                'status_format' => 'Optional',
                'is_route' => true,
                'route_title' => 'Agreed that no dispute product',
                'route_upload' => false,
            ],

            'ppi_agreed_no_existing' => [
                'key' => 'ppi_agreed_no_existing',
                'message' => 'Agreed that no existing product',
                'status_type' => 'success',
                'status_format' => 'Optional',
                'is_route' => true,
                'route_title' => 'Agreed that no existing product',
                'route_upload' => false,
            ],

            'ppi_existing_product_added_to_stock' => [
                'key' => 'ppi_existing_product_added_to_stock',
                'message' => 'The Existing Product added to stock',
                'status_type' => 'success',
                'status_format' => 'Optional',
                'is_route' => false,
                'route_title' => 'Existing Product with added to stock',
                'route_upload' => false,
            ],

            'ppi_barcode_print_done' => [
                'key' => 'ppi_barcode_print_done',
                'message' => 'Barcode printed successfully',
                'status_type' => 'success',
                'status_format' => 'Optional',
                'is_route' => true,
                'route_title' => 'A New Product has been added to stock',
                'route_upload' => false,
            ],

            'ppi_new_product_added_to_stock' => [
                'key' => 'ppi_new_product_added_to_stock',
                'message' => 'A New Product has been added to stock',
                'status_type' => 'success',
                'status_format' => 'Optional',
                'is_route' => false,
                'route_title' => 'A New Product has been added to stock',
                'route_upload' => false,
            ],
            /*
            'ppi_all_product_added_to_stock' => [
                'key' => 'ppi_all_product_added_to_stock',
                'message' => 'All Product has been added to stock',
                'status_type' => 'success',
                'status_format' => 'Main',
                'is_route' => false,
                'route_title' => 'All Product has been added to stock',
                'route_upload' => false,
            ],
            */
            'ppi_all_steps_complete' => [
                'key' => 'ppi_all_steps_complete',
                'message' => 'PPI successfully completed',
                'status_type' => 'success-complete',
                'status_format' => 'Main',
                'is_route' => true,
                'route_title' => 'PPI success fully completed',
                'route_upload' => false,
            ],

        ];
        return $status ?? null;
    }

    public static function ppiElements()
    {
        $elements = [
            'ppi_product_price_show' => [
                'route_name' => 'ppi_product_price_show',
                'route_title' => 'Ppi Product Price Show',
                'is_route' => true,
            ],


        ];

        return $elements;
    }

    public static function button()
    {
        $html = '<button type="button">Send to Boss</button>';
        $html .= '<button type="button">Send to Warehouse Manager</button>';
    }


    /**
     * SPI Status Handler
     */
    public static function spiStatusHandler()
    {
        $status = [
            'spi_created' => [
                'key' => 'spi_created',
                'message' => 'Spi created',
                'status_type' => 'success',
                'status_format' => 'Main',
                'is_route' => false,
                'route_title' => '',
                'route_upload' => false,
            ],
            'spi_product_added' => [
                'key' => 'spi_product_added',
                'message' => 'Product Added in SPI',
                'status_type' => 'success',
                'status_format' => 'Optional',
                'is_route' => false,
                'route_title' => '',
                'route_upload' => false,
            ],
            'spi_product_edited' => [
                'key' => 'spi_product_edited',
                'message' => 'Product Edited in SPI',
                'status_type' => 'purple',
                'status_format' => 'Optional',
                'is_route' => false,
                'route_title' => '',
                'route_upload' => false,
            ],
            'spi_product_deleted' => [
                'key' => 'spi_product_deleted',
                'message' => 'Product Deleted from SPI',
                'status_type' => 'danger',
                'status_format' => 'Optional',
                'is_route' => false,
                'route_title' => '',
                'route_upload' => false,
            ],
            'spi_product_lended_from_project' => [
                'key' => 'spi_product_lended_from_project',
                'message' => 'Product lended from project in SPI',
                'status_type' => 'info',
                'status_format' => 'Optional',
                'is_route' => false,
                'route_title' => '',
                'route_upload' => false,
            ],
            'spi_edited' => [
                'key' => 'spi_edited',
                'message' => 'Spi edited',
                'status_type' => 'warning',
                'status_format' => 'Optional',
                'is_route' => false,
                'route_title' => '',
                'route_upload' => false,
            ],
            'spi_sent_to_boss' => [
                'key' => 'spi_sent_to_boss',
                'message' => 'Spi sent to Boss',
                'status_type' => 'success',
                'status_format' => 'Main',
                'is_route' => true,
                'route_title' => 'Sent to Boss',
                'route_upload' => true,
            ],
            'spi_sent_to_wh_manager' => [
                'key' => 'spi_sent_to_wh_manager',
                'message' => 'Spi sent to Warehouse Manager',
                'status_type' => 'success',
                'status_format' => 'Main',
                'is_route' => true,
                'route_title' => 'Sent to Warehouse manager',
                'route_upload' => true,
            ],
            'spi_ready_to_physical_validation' => [
                'key' => 'spi_ready_to_physical_validation',
                'message' => 'Spi Ready to physical validation',
                'status_type' => 'success',
                'status_format' => 'Main',
                'is_route' => true,
                'route_title' => 'Ready to physical validation',
                'route_upload' => true,
            ],
            'spi_dispute_by_wh_manager' => [
                'key' => 'spi_dispute_by_wh_manager',
                'message' => 'Dispute by Warehouse Manager',
                'status_type' => 'danger',
                'status_format' => 'Main',
                'is_route' => true,
                'route_title' => 'Dispute by Warehouse Manager',
                'route_upload' => true,
            ],
            'spi_product_info_correction_by_boss' => [
                'key' => 'spi_product_info_correction_by_boss',
                'message' => 'Spi Product Infomation Correction by Boss',
                'status_type' => 'success',
                'status_format' => 'Optional',
                'is_route' => true,
                'route_title' => 'Spi Product Information correction by boss',
                'route_upload' => true,
            ],
            'spi_correction_done_by_boss' => [
                'key' => 'spi_correction_done_by_boss',
                'message' => 'Spi Correction Done by Boss',
                'status_type' => 'success',
                'status_format' => 'Main',
                'is_route' => false,
                'route_title' => 'Spi correction Done by boss',
                'route_upload' => false,
            ],
            'spi_resent_to_wh_manager' => [
                'key' => 'spi_resent_to_wh_manager',
                'message' => 'Spi Resent to Warehouse Manager',
                'status_type' => 'success',
                'status_format' => 'Main',
                'is_route' => true,
                'route_title' => 'Spi resent to Warehouse Manager',
                'route_upload' => true,
            ],
            'spi_agreed_no_dispute' => [
                'key' => 'spi_agreed_no_dispute',
                'message' => 'Agreed that no dispute',
                'status_type' => 'success',
                'status_format' => 'Optional',
                'is_route' => true,
                'route_title' => 'Agreed that no dispute product',
                'route_upload' => false,
            ],
            'spi_product_out_from_stock' => [
                'key' => 'spi_product_out_from_stock',
                'message' => 'A Product out from stock',
                'status_type' => 'success',
                'status_format' => 'Optional',
                'is_route' => false,
                'route_title' => 'Spi Product Out From stock',
                'route_upload' => false,
            ],
            /*
            'spi_all_product_out_from_stock' => [
                'key' => 'spi_all_product_out_from_stock',
                'message' => 'All Product out from stock',
                'status_type' => 'success',
                'status_format' => 'Main',
                'is_route' => false,
                'route_title' => 'All Product out from stock',
                'route_upload' => false,
            ],
            */
            'spi_transfer_complete' => [
                'key' => 'spi_transfer_complete',
                'message' => 'SPI transfer successfully',
                'status_type' => 'success',
                'status_format' => 'Optional',
                'is_route' => false,
                'route_title' => 'SPI transfer successfully',
                'route_upload' => false,
            ],
            'purchase_from_vendor' => [
                'key' => 'purchase_from_vendor',
                'message' => 'Product Purchase From Vendor successfully',
                'status_type' => 'success',
                'status_format' => 'Optional',
                'is_route' => false,
                'route_title' => 'SPI transfer successfully',
                'route_upload' => false,
            ],
            'spi_all_steps_complete' => [
                'key' => 'spi_all_steps_complete',
                'message' => 'SPI successfully completed',
                'status_type' => 'success-complete',
                'status_format' => 'Main',
                'is_route' => true,
                'route_title' => 'SPI successfully completed',
                'route_upload' => false,
            ],
        ];

        return $status;
    }

}
