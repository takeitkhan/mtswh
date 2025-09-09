<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Project;
use Validator;
class ProjectController extends Controller
{

    protected $model;
    /**
     * __construct
     *
     * @param  mixed $model
     * @return void
     */
    public function __construct(Project $model){
        $this->model = $model;
    }
    /**
     * index
     *
     * @return void
     */
    public function index(){
        return view ('admin.pages.warehouse.project.index');
    }

    /**
     * create
     *
     * @return void
     */
    public function create(){

    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else {
            $attributes = [
                'name' => $request->name,
                'code' => strtolower(str_replace(' ', '_', $request->name)),
                'type' => $request->type,
                'customer' => $request->customer,
                'vendor' => $request->vendor,
                'note' => $request->note,
            ];
            try {
                $data = $this->model::create($attributes);
                return redirect()->back()->with(['status' => 1, 'message' => 'Successfully created']);
            } catch (\Exception $e) {
                return redirect()->back();
            }
        }
    }

    /**
     * edit
     *
     * @param  mixed $id
     * @return void
     */
    public function edit($id){
        $project = $this->model::find($id);
        return view('admin.pages.warehouse.project.index', ['project' => $project]);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @return void
     */
    public function update(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else {
            $attributes = [
                'name' => $request->name,
                'code' => strtolower(str_replace(' ', '_', $request->name)),
                'type' => $request->type,
                'customer' => $request->customer,
                'vendor' => $request->vendor,
                'note' => $request->note,
            ];
            try {
                $data = $this->model::where('id', $request->id)->update($attributes);
                return redirect()->back()->with(['status' => 1, 'message' => 'Successfully updated']);
            } catch (\Exception $e) {
                return redirect()->back();
            }
        }
    }

    /**
     * destroy
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy($id){
        $data = Project::find($id);
        $data->delete();
        return redirect()->back()->with(['status' => 0, 'message' => 'Successfully deleted']);
    }

    public function importMTSProject(){
        $get = \App\Helpers\ApiCollection::getMtsProject();
        $data = [];
        foreach($get as $item){
           $check = Project::where('code', $item->code)->where('type', 'Service')->first();
            if($check){

            }else{
               $data = [
                   'name' => $item->name,
                   'code' =>  $item->code,
                   'type' => 'Service',
                   'customer' => $item->customer ?? null,
                   'vendor' => $item->vendor ?? null,
                   'note' => null,
                   'created_at' => Carbon::now(),
                   'updated_at' => Carbon::now(),
               ];
                Project::create($data);
            }

        }

        return redirect()->back()->with(['status' => 1, 'message' => 'Successfully imported']);

    }
}
