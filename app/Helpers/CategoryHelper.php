<?php

namespace App\Helpers;

use App\Models\ProductCategory;

class CategoryHelper
{
    public static function renderOptions($selected = null, $parent_id = null, $sub_mark = "")
    {
        $getCat = ProductCategory::where('parent_id', $parent_id)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($getCat as $row) {
            echo '<option value="'.$row->id.'" '.($row->id == $selected ? 'selected' : '').'>'
                .$sub_mark.$row->name.'</option>';
            self::renderOptions($selected, $row->id, $sub_mark.'— ');
        }
    }
}
