<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Translate;
class TranslateController extends Controller
{

    private $model;
    /**
     * @return string
     */
    public function __construct(Translate $model)
    {
        return $this->model = $model;
    }

    /**
     * @param Request $request
     * @return void
     * Create Or Update Data
     */
    public function storeOrUpdate(Request $request)
    {
//        dd($request->all());
        $translates = $request->translate;
        if($translates){
            foreach($translates as $key => $data){
                $arr = [
                    'translate_for' => $data['translate_for'],
                    'for_id' => $data['for_id'],
                    'base_text' => $data['base_text'],
                    'to_text' => $data['to_text'],
                ];
//                dd($arr);
                $checkExisting = $this->model::where('translate_for', $data['translate_for'])
                                            ->where('for_id', $data['for_id'])
                                            ->where('base_text', $data['base_text'])
                                            ->first();
//                dump($arr);

                if ($checkExisting) {
                    $this->model::where('id', $checkExisting->id)->update($arr);
                } else {
                    $this->model::create($arr);
                }
            }
            return redirect()->back()->with(['status' => 1, 'message' => 'Successfully Updated']);
        } else {
            return redirect()->back()->with(['status' => 0, 'message' => 'Something is wrong']);
        }

    }
}
