<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Models\Routelist;

class RoutelistController extends Controller
{

    private $routelist;

    public function __construct(RouteList $routelist)
    {
        $this->routelist = $routelist;
    }

    public function index()
    {
        $routelists = $this->routelist::orderBy('route_group', 'ASC')->get();
        return view('admin.pages.routelists.index', ['routelists' => $routelists]);
    }

    public function create()
    {
        return view('admin.pages.routelists.form');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'route_name' => 'required',
                'route_title' => 'required',
                'route_description' => 'required'
            ]
        );

        // process the login
        if ($validator->fails()) {
            return redirect('routelists.create')
                ->withErrors($validator)
                ->withInput();
        } else {
            // store
            $attributes = [
                'route_name' => $request->route_name,
                'route_title' => $request->route_title,
                'route_hash' => bcrypt($request->route_name),
                'route_group' => $request->route_group,
                'parent_menu_id' => $request->parent_route_id,
                'show_menu' => $request->show_menu,
                'dashboard_position' => implode(',',$request->dashboard_position ?? [Null]),
                'route_icon' => $request->route_icon,
                'route_order' => $request->route_order,
                'route_description' => $request->route_description,
            ];
            $routelist = $this->routelist::create($attributes);
            try {
                return redirect()->route('routelist_index')->with(['status' => 1, 'message' => 'Successfully created']);
            } catch (\Exception $e) {
                return redirect()->route('routelist_create')->with(['status' => 0, 'message' => 'Error']);
            }
        }
    }


    public function edit($id)
    {   
        $routelist = $this->routelist::find($id);
        return view('admin.pages.routelists.form', ['routelist' => $routelist]);
    }


    public function update(Request $request)
    {
        // store
        $attributes = [
            'route_name' => $request->route_name,
            'route_title' => $request->route_title,
            'route_hash' => bcrypt($request->route_name),
            'route_group' => $request->route_group,
            'parent_menu_id' => $request->parent_route_id,
            'show_menu' => $request->show_menu,
            'dashboard_position' => implode(',',$request->dashboard_position ?? [Null]),
            'route_icon' => $request->route_icon,
            'route_order' => $request->route_order,
            'route_description' => $request->route_description,
        ];

        try {
            $routelist = $this->routelist::where('id', $request->id)->update($attributes);
            return redirect()->route('routelist_index')->with(['status' => 1, 'message' => 'Successfully updated']);
        } catch (\Exception $e) {
            return redirect()->route('routelist_edit', $request->id)->with(['status' => 0, 'message' => 'Error']);
        }
    }



    public function destroy($id)
    {
        $routelist = $this->routelist::find($id);
        $routelist->delete(); 
        return redirect()->route('routelist_index', ['status' => 1, 'message' => 'Successfully deleted']);
    }

    public function apiGet(Request $request){

        $model = $this->routelist;
        $query = $this->routelist::query();

        $fields  = [
            'button' =>  '\App\Helpers\ButtonSet::delete("routelist_destroy", $data->id).\App\Helpers\ButtonSet::edit("routelist_edit", $data->id)',
            'route_title' => '$data->route_title',
            'route_name' =>  '$data->route_name',
            'route_group' =>  'App\Helpers\Query::accessModel("Routegroup")::name($data->route_group)',
            'route_description' =>  '$data->route_description',
            'route_order' =>  '$data->route_order',
            'show_menu' =>  '$data->show_menu',
            'dashboard_position' =>  '$data->dashboard_position',
        ];

        return $this->Datatable::generate($request, $query, $fields,   ['daterange' => 'updated_at']);
    }
}
