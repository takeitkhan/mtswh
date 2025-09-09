@extends('admin.layouts.master')

@section('title')
 Product Category
@endsection

@section('content')
@php
    $warehouse_id = request()->get('warehouse_id');
    $warehouse_code = request()->get('warehouse_code');
@endphp
<div class="content-wrapper">
    <div class="row">
        <!-- Form -->
        <div class="col-md-3">
            <h6> 
                <div class="title-with-border"> 
                    @if(!empty($category))
                        <span class="text-primary">Edit Category Information</span>
                    @else
                        Product Category Information
                    @endif
                </div> 
            </h6>
            <form action="{{ !empty($category) ? route('product_category_update', $warehouse_code) : route('product_category_store', $warehouse_code) }}" method="post">
                @csrf
                @if (!empty($category))
                    <input type="hidden" name="id" value="{{ $category->id }}">
                @endif
                <div class="form-group"> 
                    <label for="name">Category Name: </label>
                    <input type="text" class="form-control" placeholder="Enter category name" name="name"
                        value="{{ !empty($category) ? $category->name : old('name') }}" required>
                </div>

                <div class="form-group"> 
                    <label for="description">Description: </label>
                    <textarea class="form-control" name="description">{{ !empty($category) ? $category->description : old('description') }}</textarea>
                </div>
                <div class="form-group">
                    <label for="description">Parent Category: </label>
                    <?php
                        global $avaiableCat;
                        $avaiableCat = (!empty($category)) ? $category->parent_id : '';
                        function selectCat($parent_id = null, $sub_mark = "") {
                            global $avaiableCat;
                            $getCat = \App\Models\ProductCategory::where('parent_id', $parent_id)->orderBy('created_at', 'desc')->get();
                            foreach($getCat as $row){ ?>
                                <option value="{{$row->id}}" {{$row->id == $avaiableCat ? 'selected' : ''}}>{{$sub_mark.$row->name}} </option>
                                <?php selectCat($row->id, $sub_mark .'— ');
                            }
                        }?>
                        <select class="form-control form-control-sm select-box" id="parent_id" name="parent_id">
                            <option value="">None</option>
                            <?php selectCat();?>
                        </select>
                </div>
                <div class="form-submit_btn">
                    <button type="submit" class="btn blue">Submit</button>
                </div>
            </form>
        </div><!-- ENd Form-->
        <div class="col-md-2"></div>
        <!-- Data -->
        <div class="col-md-5 table-wrapper desktop-view mobile-view">
            <h6> 
                <div class="title-with-border"> 
                    All Category
                </div> 
            </h6>
            <table class="">
                <thead>
                    <tr>
                        <th></th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        function showCat($parent_id = null, $sub_mark=""){ 
                            $showCat = \App\Models\ProductCategory::where('parent_id', $parent_id)->orderBy('created_at', 'desc')->paginate('20');
                            foreach($showCat as $data){ ?>
                                <tr class="">
                                    <td class="align-middle">
                                        {!! App\Helpers\ButtonSet::delete("product_category_destroy", [request()->get("warehouse_code"), $data->id]) !!}

                                        {!! App\Helpers\ButtonSet::edit("product_category_edit", [request()->get("warehouse_code"), $data->id]) !!}
                                    </td>
                                    <td class="align-middle">{{$data->id}}</td>
                                    <td class="align-middle">
                                        {{$sub_mark.$data->name}}
                                    </td>
                                    <td class="align-middle">
                                        {{$data->slug}}
                                    </td>
                                    <td class="align-middle">
                                        <a target="_blank" class="text-primary" href="">
                                            {{count(App\Models\Product::whereRaw("FIND_IN_SET($data->id , category_id)")->get())}}
                                        </a>
                                    </td>
                                </tr>
                              <?php showCat($data->id, $sub_mark .'— ');
                            } 
                        } ?>
                        {{showCat()}}
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection