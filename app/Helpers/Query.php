<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use DB;
use App\Helpers\ButtonSet;
use App\Http\Traits\GlobalTrait;
use Spatie\Html\Html;

class Query
{

    use GlobalTrait;


    //Get Data
    public static function getData($table)
    {
        return DB::table($table)->get();
    }

    public static function delete($route, $id, $arrEle = [])
    {
        $default = [
            'title' => 'Delete',
            'id' => 'delete-form-' . $id,
        ];
        $merge = array_merge($default, $arrEle);
        $formId = $merge['id'];

        /** @var \Spatie\Html\Html $html */
        $html = app(\Spatie\Html\Html::class);

        // Build form
        $form = $html->form()
            ->method('POST')  // POST for HTML
            ->action(route($route, $id))
            ->id($formId);

        // Add CSRF token
        $csrf = $html->input('hidden', '_token')->value(csrf_token());

        // Add _method=DELETE
        $method = $html->input('hidden', '_method')->value('DELETE');

        // Add button
        $button = $html->button('<span class="icon-trash is-red"></span>')
            ->type('button')
            ->attribute('title', $merge['title'])
            ->addClass('border-0')
            ->style('background:none;color:red;font-size:14px;padding:0 3px 0 0;')
            ->attribute('onclick', "DeleteconfirmAlertCustom('{$formId}')");

        return $form->open() . $csrf->toHtml() . $method->toHtml() . $button->toHtml() . $form->close();
    }



    //Delete Data
    // public static function delete($route, $id, $arrEle = [])
    // {
    //     $default = [
    //         'title' => 'Delete',
    //         'class' => null,
    //         'id' => null,
    //     ];
    //     $merge = array_merge($default, $arrEle);
    //     $fromId = $merge["id"];
    //     //dd($fromId);
    //     $html = \Form::open(array('url' => route($route, $id), 'id' => $fromId, 'method' => 'DELETE', 'style' => ''));
    //     $html .= '<button style="background: none;color: red;font-size: 14px;padding: 0 3px 0 0;" onclick="DeleteconfirmAlertCustom(`' . $fromId . '`)"  title="' . $merge['title'] . '" type="button" class="border-0">
    //     <span class="icon-trash is-red"></span></button>';
    //     $html .= \Form::close();

    //     return $html;
    // }

    /**
     * Access Any Model
     * From Models Directory
     */
    public static function accessModel($modelName)
    {
        $modelPath = '\App\Models' . '\\' . $modelName;
        return $modelPath;
    }


    /**
     * getEnumValues
     * From DB Table
     * @param  mixed $table
     * @param  mixed $column
     * @return void
     */
    public static function getEnumValues($table, $column)
    {
        $type = DB::select("SHOW COLUMNS FROM {$table} WHERE Field = '{$column}'")[0]->Type;

        preg_match('/^enum\((.*)\)$/', $type, $matches);

        $enum = [];
        foreach (explode(',', $matches[1]) as $value) {
            $v = trim($value, "(')");
            $enum[strtolower($v)] = $v;
        }

        return $enum;
    }

    // public static function getEnumValues($table, $column) {
    //     $type = DB::select(DB::raw("SHOW COLUMNS FROM $table WHERE Field = '{$column}'"))[0]->Type ;
    //     preg_match('/^enum((.*))$/', $type, $matches);
    //     $enum = array();
    //     foreach( explode(',', $matches[1]) as $value )
    //     {
    //       $v = trim( $value, "(')" );
    //       $enum[strtolower($v)] = $v;
    //     }
    //     return $enum;
    // }



    /**
     * Update SQL table column enum
     *
     */
    public static function changeColumnEnumValues($options = [])
    {
        $default = [
            'table_name'  => null,
            'column_name' => null,
            'enum_values' => [],
        ];

        $arr = array_merge($default, $options);

        if (!$arr['table_name'] || !$arr['column_name'] || empty($arr['enum_values'])) {
            throw new \InvalidArgumentException("Table name, column name, and enum values are required.");
        }

        $enum = "'" . implode("','", $arr['enum_values']) . "'";

        $query = "ALTER TABLE {$arr['table_name']} MODIFY COLUMN {$arr['column_name']} ENUM({$enum})";

        return DB::statement($query);
    }

    // public static function changeColumnEnumValues($options = [])
    // {
    //     $default = [
    //         'table_name' => null,
    //         'column_name' => null,
    //         'enum_values' => [],
    //     ];
    //     $arr = array_merge($default, $options);
    //     $enum = "'" . implode("','", $arr['enum_values']) . "'";


    //     $query = DB::select(DB::raw("ALTER TABLE " . $arr['table_name'] . " MODIFY COLUMN " . $arr['column_name'] . " ENUM(" . $enum . ")"));

    //     return $query;
    // }

    public static function getDateTimeFormat($date, $format = 'Y-m-d h:i a')
    {
        $date = new \DateTime($date);
        return $date->format($format);
    }

    /**
     * barcodeGenerator
     *
     * @param  mixed $string
     * @return string
     */
    public static function barcodeGenerator($string, $options = [])
    {
        //$generator = new \Picqer\Barcode\BarcodeGeneratorHTML();
        //$img = move_uploaded_file(asset('public')."/barcode{$string}.jpg", $generator->getBarcode($string, $generator::TYPE_CODABAR));
        //return $img;
        //return $generator->getBarcode($string, $generator::TYPE_CODE_128, 1, 30);
        $default = [
            'show_digit' => $string,
            'show_digit_title' => $string,
        ];
        $merge = array_merge($default, $options);
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $showDigit = $merge['show_digit'];
        $showDigitTitle = $merge['show_digit_title'];
        return '<img title="Digit: ' . $showDigitTitle . '" style="width: 100px;" src="data:image/png;base64,' . base64_encode($generator->getBarcode($string, $generator::TYPE_CODE_128_B, 1, 40)) . '">' . '<br><span style="font-size: 11px; font-weight: bold;" title="Digit: ' . $showDigitTitle . '">' . $showDigit . '<span>';
    }

    /**
    TYPE_CODE_39
    TYPE_CODE_39_CHECKSUM
    TYPE_CODE_39E
    TYPE_CODE_39E_CHECKSUM
    TYPE_CODE_93

    TYPE_CODE_128
    TYPE_CODE_128_A
    TYPE_CODE_128_B

     */
}
