<?php

namespace App\Helpers;

use App\Models\ProductCategory;

class CategoryTableHelper
{
    public static function renderRows($parent_id = null, $sub_mark = "")
    {
        $categories = ProductCategory::where('parent_id', $parent_id)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($categories as $data) {
            echo '<tr>';
            echo '<td class="align-middle">'
                . \App\Helpers\ButtonSet::delete("product_category_destroy", [request()->get("warehouse_code"), $data->id])
                . \App\Helpers\ButtonSet::edit("product_category_edit", [request()->get("warehouse_code"), $data->id])
                . '</td>';
            echo '<td class="align-middle">'.e($data->id).'</td>';
            echo '<td class="align-middle">'.e($sub_mark . $data->name).'</td>';
            echo '</tr>';

            self::renderRows($data->id, $sub_mark . '— ');
        }
    }
}
