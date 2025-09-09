<?php
/**
 * Ppi SPi Statuses Status Type Enum Value
 */
  \App\Helpers\Query::changeColumnEnumValues([
     'table_name' => 'ppi_spi_statuses',
     'column_name' => 'status_type',
     'enum_values' => [
         'success',
         'danger',
         'warning',
         'info',
         'purple',
         'success-complete',
     ],
 ]);

/*
$attributes_items = [
    [
        'unique_name' => 'raw_items',
        'label' => 'Raw Items',
        'with_attribute_relation' => 'unit',
    ],
    [
        'unique_name' => 'finished_items',
        'label' => 'Finished Items',
    ],
    [
        'unique_name' => 'drink_items',
        'label' => 'Drink Items',
        'with_attribute_relation' => 'unit',
    ],
    [
        'unique_name' => 'unit',
        'label' => 'Unit',
        'with_attribute_relation' => null,
    ],
];
// $saveAttr = [];
foreach($attributes_items as $key => $item){
        $check = $this->Model('AttributeItem')::where('unique_name', $item['unique_name'])->first();
        if(empty($check)){
            $saveAttr = $attributes_items[$key];
            $this->Model('AttributeItem')::create($saveAttr);
        }
}

*/

?>
