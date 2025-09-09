<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Validator;

class ContactController extends Controller
{
    protected $model;     
    /**
     * __construct
     *
     * @param  mixed $model
     * @return void
     */
    public function __construct(Contact $model){
        $this->model = $model;
    }
    /**
     * index
     *
     * @return void
     */
    public function index(){
        return view ('admin.pages.contact.index');
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
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'license_no' => $request->license_no,
                'contact_person' => $request->contact_person,
                'contact_person_no' => $request->contact_person_no,
                'vat_type' => $request->vat_type,
                'vat_percent' => $request->vat_percent,
                'tax_type' => $request->tax_type,
                'tax_percent' => $request->tax_percent,
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
        $contact = $this->model::find($id);
        return view('admin.pages.contact.index', ['contact' => $contact]);
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
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'license_no' => $request->license_no,
                'contact_person' => $request->contact_person,
                'contact_person_no' => $request->contact_person_no,
                'vat_type' => $request->vat_type,
                'vat_percent' => $request->vat_percent,
                'tax_type' => $request->tax_type,
                'tax_percent' => $request->tax_percent,
                'note' => $request->note,
            ];
            try {
                $data = $this->model::where('id', $request->id)->update($attributes);
                return redirect()->back()->with(['status' => 1, 'message' => 'Successfully created']);
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
        $data = $this->model::find($id);
        $data->delete();
        return redirect()->back()->with(['status' => 1, 'message' => 'Successfully deleted']);
    }
    
    /**
     * apiSource
     *
     * @return void
     */
    public function apiSources(){
        $data = $this->model::get();
        return $data;
    }
}
