<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Redirector;
use Illuminate\Http\Request;
use App\Models\AttributeValue;
use Validator;
class AttributeValueController extends Controller
{
    protected $model;
    protected $attrName;
    protected $attrIndex;
    protected $indexRoute;
    /**
     * __construct
     *
     * @param  mixed $model
     * @param  mixed $request
     * @param  mixed $redirector
     * @return void
     */
    public function __construct(AttributeValue $model, Request $request, Redirector $redirector){
        $this->model = $model;
        $options = $this->Query::getEnumValues('attribute_values','unique_name');
        $check = array_search($request->value, $options);
        if($check == false){
            //$redirector->route('404')->send();
            $this->attrIndex = $request->index;
            $this->attrName = $request->unique_name;
        }else{
            $this->attrName = $request->value;
            $this->attrIndex = $check;
        }
        if($this->attrIndex) {
            $this->indexRoute = redirect()->route('attribute_' . $this->attrIndex . '_index', $this->attrName);
        }
    }

    /**
     * index
     *
     * @return void
     */
    public function index(){
        $thisAttrName = $this->attrName;
        $thisAttrIndex = $this->attrIndex;
        if($thisAttrIndex){
            return view('admin.pages.attribute-value.index', compact('thisAttrName', 'thisAttrIndex'));
        } else {
            return redirect()->route('404');
        }

    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request){
        $request->validate([
            'value' => 'required',
            'slug' => 'unique:attribute_values,slug',
        ]);
        $attributes = [
            'unique_name' => $request->unique_name,
            'value' => $request->value,
            'slug' =>  !empty($request->slug) ? str_replace(' ', '-', $request->slug) : strtolower(str_replace(' ', '-', $request->value)),
            'status' => $request->status,
        ];
        $this->model::insert($attributes);
        return $this->indexRoute->with(['status'=> 1, 'message' => 'Successfully created']);
    }

    /**
     * edit
     *
     * @param  mixed $request
     * @return void
     */
    public function edit(Request $request){
        $id = $request->id;
        $attribute = $this->model::find($id);
        $thisAttrName = $this->attrName;
        $thisAttrIndex = $this->attrIndex;
        if($thisAttrIndex){
            return view('admin.pages.attribute-value.index', compact('thisAttrName', 'thisAttrIndex', 'attribute'));
        } else {
            return redirect()->route('404');
        }
    }


    /**
     * update
     *
     * @param  mixed $request
     * @return void
     */
    public function update(Request $request){
        //dd($request->all());

        $attributes = [
            'unique_name' => $request->unique_name,
            'value' => $request->value,
            'slug' =>  !empty($request->slug) ? str_replace(' ', '-', $request->slug) : strtolower(str_replace(' ', '-', $request->value)),
            'status' => $request->status,
        ];
         $this->model::where('id', $request->id)->update($attributes);
        return $this->indexRoute->with(['status'=> 1, 'message' => 'Successfully updated']);
    }

    /**
     * destroy
     *
     * @return void
     */
    public function destroy(Request $request){
        $id = $request->id;
        $data = $this->model::find($id);
        $data->delete();
        return $this->indexRoute->with(['status'=> 1, 'message' => 'Successfully deleted']);
    }
}
