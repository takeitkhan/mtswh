<?php

namespace App\Helpers\Warehouse;

use App\Models\PpiSpi;
use App\Models\PpiSpiStatus;

class PpiSpiHelper
{
    /**
     * Status codes after which the original creator (Subordinate Manager / General role)
     * should NOT be allowed to edit/delete the PPI or its products.
     *
     * Once any of these status codes is reached, the record is considered "locked"
     * from the creator's perspective. Boss / Warehouse Manager / Global admins
     * still work through their dedicated action routes (correction_by_boss etc.).
     */
    public static function ppiCreatorLockStatuses()
    {
        return [
            'ppi_sent_to_boss',
            'ppi_sent_to_wh_manager',
            'ppi_resent_to_wh_manager',
            'ppi_ready_to_physical_validation',
            'ppi_dispute_by_wh_manager',
            'ppi_agreed_no_dispute',
            'ppi_agreed_no_existing',
            'ppi_existing_product_added_to_stock',
            'ppi_barcode_print_done',
            'ppi_new_product_added_to_stock',
            'ppi_challan_pdf_printed',
            'ppi_all_steps_complete',
        ];
    }

    public static function spiCreatorLockStatuses()
    {
        return [
            'spi_sent_to_boss',
            'spi_sent_to_wh_manager',
            'spi_resent_to_wh_manager',
            'spi_ready_to_physical_validation',
            'spi_dispute_by_wh_manager',
            'spi_all_steps_complete',
        ];
    }

    /**
     * Check if a PPI/SPI is locked for the original creator (Subordinate Manager).
     * Returns true when:
     *  - the record's last status is one of the creator-lock statuses, AND
     *  - the currently authenticated user holds a General role (i.e. a subordinate),
     *    NOT a Global admin (Boss/WH Manager flows have their own action routes).
     *
     * Global admins are never locked out by this helper.
     *
     * @param int|string $ppiSpiId
     * @param string     $actionFormat 'Ppi' or 'Spi'
     * @return bool
     */
    public static function isLockedForCreator($ppiSpiId, $actionFormat = 'Ppi')
    {
        if (empty($ppiSpiId)) {
            return false;
        }
        $user = auth()->user();
        if (empty($user)) {
            return false;
        }
        // Global admins bypass this creator-level lock.
        if (method_exists($user, 'checkUserRoleTypeGlobal') && $user->checkUserRoleTypeGlobal()) {
            return false;
        }
        // Only enforce for General-role users (Subordinate Managers).
        if (method_exists($user, 'checkUserRoleTypeGeneral') && !$user->checkUserRoleTypeGeneral()) {
            return false;
        }

        $lockStatuses = $actionFormat === 'Spi'
            ? self::spiCreatorLockStatuses()
            : self::ppiCreatorLockStatuses();

        $last = PpiSpiStatus::where('ppi_spi_id', $ppiSpiId)
            ->where('status_for', $actionFormat)
            ->where('status_format', 'Main')
            ->orderBy('id', 'desc')
            ->first();

        if (empty($last)) {
            return false;
        }

        // ✅ DRAFT MODE: Not locked if status is draft
        $draftCodes = ['ppi_draft', 'spi_draft'];
        if (in_array($last->code, $draftCodes, true)) {
            return false; // Always unlocked in DRAFT mode
        }

        return in_array($last->code, $lockStatuses, true);
    }

    /**
     * Check if a PPI/SPI is locked for ANY user based on their role and the current status.
     * Implements hierarchical lock logic:
     * - Subordinate Manager: locked after sending to Boss
     * - Boss: locked after sending to Warehouse Manager (unless Dispute)
     * - Warehouse Manager: only locked if their action is reverted
     * - Global Admin: never locked
     *
     * @param int|string $ppiSpiId
     * @param string     $actionFormat 'Ppi' or 'Spi'
     * @return bool true if locked for current user, false otherwise
     */
    public static function isLockedForCurrentUser($ppiSpiId, $actionFormat = 'Ppi')
    {
        if (empty($ppiSpiId)) {
            return false;
        }

        $user = auth()->user();
        if (empty($user)) {
            return false;
        }

        // Global admins and super admins bypass all locks
        if (method_exists($user, 'checkUserRoleTypeGlobal') && $user->checkUserRoleTypeGlobal()) {
            return false;
        }

        $record = PpiSpi::find($ppiSpiId);
        if (empty($record)) {
            return false;
        }

        $lastStatus = PpiSpiStatus::where('ppi_spi_id', $ppiSpiId)
            ->where('status_for', $actionFormat)
            ->where('status_format', 'Main')
            ->orderBy('id', 'desc')
            ->first();

        if (empty($lastStatus)) {
            return false;
        }

        // ✅ DRAFT MODE: Not locked if status is draft
        $draftCodes = ['ppi_draft', 'spi_draft'];
        if (in_array($lastStatus->code, $draftCodes, true)) {
            return false; // Always unlocked in DRAFT mode
        }

        $currentUserId = $user->id;
        $recordCreatorId = $record->action_performed_by;
        $lastStatusCode = $lastStatus->code;
        $lastStatusPerformedBy = $lastStatus->action_performed_by;

        // Define lock statuses for each role
        $subordinateCreatorLock = $actionFormat === 'Spi'
            ? self::spiCreatorLockStatuses()
            : self::ppiCreatorLockStatuses();

        // Boss lock statuses: locked if sent to WH Manager (unless Dispute)
        $bossLockStatuses = [
            'ppi_sent_to_wh_manager',
            'ppi_resent_to_wh_manager',
            'ppi_ready_to_physical_validation',
            'ppi_agreed_no_dispute',
            'ppi_agreed_no_existing',
            'ppi_existing_product_added_to_stock',
            'ppi_barcode_print_done',
            'ppi_new_product_added_to_stock',
            'ppi_challan_pdf_printed',
            'ppi_all_steps_complete',
        ];

        if ($actionFormat === 'Spi') {
            $bossLockStatuses = [
                // Boss locked after sending to WH Manager
                // UNLESS there's a Dispute (exception handled below)
                'spi_sent_to_wh_manager',
                'spi_resent_to_wh_manager',
                'spi_ready_to_physical_validation',
                'spi_all_steps_complete',
            ];
        }

        // LOCK LOGIC FOR SUBORDINATE MANAGER (Creator)
        // Lock if: user is creator AND status is in creator lock list
        if (method_exists($user, 'checkUserRoleTypeGeneral') && $user->checkUserRoleTypeGeneral()) {
            if ($currentUserId === $recordCreatorId && in_array($lastStatusCode, $subordinateCreatorLock, true)) {
                return true;
            }
            return false;
        }

        // LOCK LOGIC FOR BOSS
        // Lock if: status is in bossLockStatuses (meaning it's been sent to WH Manager)
        // Unless: there's an active Dispute that Boss needs to correct
        $userHasBossRole = $user->hasRole(['boss', 'Boss', 'boss_permission'])
            || (method_exists($user, 'checkUserRole') && in_array('boss', (array)$user->checkUserRole()));

        if ($userHasBossRole) {
            // Check if there's a Dispute - if so, Boss is NOT locked (can correct)
            $hasDispute = $lastStatusCode === 'ppi_dispute_by_wh_manager'
                || $lastStatusCode === 'spi_dispute_by_wh_manager'
                || $lastStatusCode === 'ppi_correction_done_by_boss'
                || $lastStatusCode === 'spi_correction_done_by_boss';

            if ($hasDispute) {
                return false; // Boss can correct disputed records
            }

            // If no dispute and status is in boss lock list, Boss is locked
            if (in_array($lastStatusCode, $bossLockStatuses, true)) {
                return true;
            }

            return false;
        }

        // LOCK LOGIC FOR WAREHOUSE MANAGER - typically not locked (they handle their stage)
        // Only locked if we explicitly set them as locked
        return false;
    }

    /**
     * Human readable message shown when a write is blocked by the creator lock.
     */
    public static function lockMessage($actionFormat = 'Ppi')
    {
        $label = $actionFormat === 'Spi' ? 'SPI' : 'PPI';
        return "This {$label} has already been submitted and is read-only for you. Please contact the Boss for any correction request.";
    }

    public static function ppiStatusHandler()
    {
        $status = [
            'ppi_draft' => [
                'key' => 'ppi_draft',
                'message' => '✏️ PPI in Draft Mode - Edit/Delete Allowed',
                'status_type' => 'info',
                'status_format' => 'Main',
                'is_route' => false,
                'route_title' => 'Draft Mode',
                'route_upload' => false,
            ],
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
            'ppi_challan_pdf_printed' => [
                'key' => 'ppi_challan_pdf_printed',
                'message' => 'Challan PDF printed',
                'status_type' => 'success',
                'status_format' => 'Optional',
                'is_route' => true,
                'route_title' => 'Challan PDF printed',
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
            'spi_draft' => [
                'key' => 'spi_draft',
                'message' => '✏️ SPI in Draft Mode - Edit/Delete Allowed',
                'status_type' => 'info',
                'status_format' => 'Main',
                'is_route' => false,
                'route_title' => 'Draft Mode',
                'route_upload' => false,
            ],
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
            'spi_delivery_challan_generated' => [
                'key' => 'spi_delivery_challan_generated',
                'message' => 'SPI Delivery Challan has been generated',
                'status_type' => 'success',
                'status_format' => 'Main',
                'is_route' => true,
                'route_title' => 'Generate Delivery Challan',
                'route_upload' => false,
            ],
            'spi_all_steps_complete' => [
                'key' => 'spi_all_steps_complete',
                'message' => 'SPI successfully completed',
                'status_type' => 'success-complete',
                'status_format' => 'Main',
                'is_route' => true,
                'route_title' => 'Close this SPI',
                'route_upload' => false,
            ],
        ];

        return $status;
    }

    /**================================
     * ===== DRAFT MODE HELPERS =======
     ================================*/

    /**
     * Check if PPI/SPI is in DRAFT mode
     * @param int|string $ppiSpiId
     * @param string $actionFormat 'Ppi' or 'Spi'
     * @return bool
     */
    public static function isDraftMode($ppiSpiId, $actionFormat = 'Ppi')
    {
        $lastStatus = PpiSpiStatus::where('ppi_spi_id', $ppiSpiId)
            ->where('status_for', $actionFormat)
            ->where('status_format', 'Main')
            ->orderBy('id', 'desc')
            ->first();

        if (empty($lastStatus)) {
            return true; // New record is considered as DRAFT
        }

        $draftCodes = ['ppi_draft', 'spi_draft'];
        return in_array($lastStatus->code, $draftCodes, true);
    }

    /**
     * Check if edit/delete is allowed (only in DRAFT mode)
     * @param int|string $ppiSpiId
     * @param string $actionFormat 'Ppi' or 'Spi'
     * @return bool
     */
    public static function canEditDeleteInDraft($ppiSpiId, $actionFormat = 'Ppi')
    {
        return self::isDraftMode($ppiSpiId, $actionFormat);
    }

    /**
     * Get Draft Status Message for UI display
     * @param int|string $ppiSpiId
     * @param string $actionFormat 'Ppi' or 'Spi'
     * @return string
     */
    public static function getDraftStatusMessage($ppiSpiId, $actionFormat = 'Ppi')
    {
        if (self::isDraftMode($ppiSpiId, $actionFormat)) {
            return '✏️ DRAFT - Edit/Delete Allowed until submission';
        }
        return '🔒 SUBMITTED - Locked (No Edit/Delete)';
    }

}

