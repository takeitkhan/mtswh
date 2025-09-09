<?php

namespace App\Helpers;

use DB;
use Illuminate\Routing\Controller;
use App\Helpers\ButtonSet;
use Illuminate\Http\Request;
use App\Http\Traits\GlobalTrait;

class Datatable
{

    use GlobalTrait;

    protected $this;


    /**
     * Datatable Get Data
     * use Api
     * @param $request = Get All Form Input Request
     *
     * @param $query = Set Query Base on Database What you want data
     * During Pass Query As Parameter skip this method ( get(), offset(), limit())
     * Use query() instead of get() in $query
     *
     * @param $field = Which fields you want to show  from Query (Serialize Recommended)
     * Pass fields as Array
     */
    public function ni()
    {
        return $this;
    }


    public static function generate($request, $query, $field, $options = [])
    {
        
        //dd($query->get()->toArray());
        //unset($this);
        /**Permission to use $this */
        $ins = new self;
        $thiss = $ins->ni();
        //dd($thiss->Query);
        $field = str_ireplace('$this', '$thiss', $field);
        $fields = [];
        //End
        $customColumnFilter = [
            'daterange' => 'created_at',
            'phpcode' => null,
            'collectionquery' => null,
            'phpcodeoutside' => null,
            'searchquery' => null,
            'orderby' => 'asc',
        ];
        //dd($query->get());
        $merge_arr = array_replace($customColumnFilter, $options);
        //get array keys from $field array;
        //dd(array_keys($field) );

        if($request->order || $request->search['value'] ) {
            $q = $query->cursor()->take(1)->toArray() ?? false;
            foreach (array_keys($field) as $key) {
//                $q = $query->get()->toArray();
                if ($q) {
                    $check = array_key_exists($key, $q[0]);
                    if ($check == true && is_array($q[0][$key])) {

                        $fields [] = 'id';

                    } elseif ($check == true) {

                        $fields [] = $key;

                    } else {
                        $fields [] = 'id';
                    }
                }

            }

        } else {
            $fields [] = 'id';
        }

        // $fields = array_keys($field);
        $start = $request->start ?? 0; //Start show data from request count
        $length = $request->length ?? 50; //How much show data
        $search = $request->search['value'] ?? Null; //Search field
        $column = $request->order ? $fields[$request->order[0]['column']] : 'id'; // column Filter
        $dir = $request->order ? $request->order[0]['dir'] : $merge_arr['orderby']; //Order Descending/Ascending

        //Daterange
        $from_date = date($request->from_date);
        $to_date = date($request->to_date);

        //Total Row Number of Query
        $countTotal = count($query->get());
        // dd($countTotal);
        if ($search) { //For Search
            if (!empty($merge_arr["searchquery"])) {
                $collection = $query;
                eval('  ' . $merge_arr["searchquery"] . ';');
                $collection = $collection->orderBy($column, $dir);//->get();
                $countTotal = count($collection->get());
                if($request->length == '-1') {

                }else {
                    $collection = $collection->offset($start)->limit($length);
                }
                $collection = $collection->get();
            } else {
                foreach ($fields as $i => $d) {
                    $collection = $query->orWhere($d, 'LIKE', '%' . $search . '%')->orderBy($column, $dir);
                    /**
                     * if we would to use Any Extra Query here
                     * We can easily that Through collectionQuery
                     * Reminder: Query Code Must be passed here String Type and EleQuent Method
                     */
                    eval(' ' . $merge_arr["collectionquery"] . ';');
                }
                $countTotal = $collection->count();
                $collection = $collection->offset($start)->limit($length);
                $collection = $collection->get();

            }

            /**Daterange Search */
        } elseif ($request->from_date && $request->to_date) { //For Daterange
            if (!empty($merge_arr["searchquery"])) {
                $collection = $query;
                eval('  ' . $merge_arr["searchquery"] . ';');
                $collection = $collection->where(function ($q) use ($merge_arr, $request) {
                    $q->WhereBetween($merge_arr['daterange'], [$request->from_date, $request->to_date])
                        ->orWhereDate($merge_arr['daterange'], [$request->from_date, $request->to_date]);
                });
                $countTotal = count($collection->get());
                $collection = $collection->offset($start)->limit($length);
                $collection = $collection->get();
            } else {
                $collection = $query
                    ->where(function ($q) use ($merge_arr, $request) {
                        $q->WhereBetween($merge_arr['daterange'], [$request->from_date, $request->to_date])
                            ->orWhereDate($merge_arr['daterange'], [$request->from_date, $request->to_date]);
                    });
                eval(' ' . $merge_arr["collectionquery"] . ';');
                $countTotal = count($collection->get());
                $collection = $collection->offset($start)->limit($length);
                $collection = $collection->get();
            }
            //End dateRange Search
        } elseif ($request->length == '-1') { //Show all page

            $collection = $query->orderBy($column, $dir);
            eval(' ' . $merge_arr["collectionquery"] . ';');
            $collection = $collection->get();
            $countTotal = count($collection);

        } else { //Default

            $collection = $query->orderBy($column, $dir);
            eval(' ' . $merge_arr["collectionquery"] . ';');
            $countTotal = count($collection->get());

            $collection = $collection->offset($start)->limit($length)->get();
        }


        /**
         * if we would to use Any PhPcode here
         * We can easily that Through phocodeOutSide
         * Reminder: PHP Code Must be passed here String Type
         */
        eval(' ' . $merge_arr["phpcodeoutside"] . ';');


        /**Loop */
        $arr = [];
        
        //dd($collection);
        foreach ($collection as $key => $data) {
            //dump($data);
            eval(' ' . $merge_arr["phpcode"] . ';');
            //Evaluted Field
            $val = [];
            foreach ($field as $k => $f) {
                $val[$k] = eval('return ' . $f . ';');
            }
            $arr [] = $val;
        }
        
        //dd($collection);


        $draw_val = $request->draw;

        $results = array(
            "draw" => intval($draw_val),
            'start' => $start,
            'length' => $length,
            "recordsTotal" => intval($countTotal),
            "recordsFiltered" => intval($countTotal),
            "dir" => $dir,
            "data" => $arr,
        );
        return $results;
    }//End
    

}
