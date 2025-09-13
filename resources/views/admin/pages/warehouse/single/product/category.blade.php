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
                <form
                    action="{{ !empty($category) ? route('product_category_update', $warehouse_code) : route('product_category_store', $warehouse_code) }}"
                    method="post">
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
                        <textarea class="form-control"
                            name="description">{{ !empty($category) ? $category->description : old('description') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="description">Parent Category: </label>
                        <select class="select-box form-control form-control-sm" id="category_id" name="category_id">
                            <option value="">None</option>
                            {!! \App\Helpers\CategoryHelper::renderOptions(!empty($product) ? $product->category_id : '') !!}
                        </select>

                    </div>
                    <div class="form-submit_btn">
                        <button type="submit" class="btn blue">Submit</button>
                    </div>
                </form>
            </div><!-- ENd Form-->
            <div class="col-md-2"></div>
            <!-- Data -->
            <div class="table-wrapper col-md-5 desktop-view mobile-view">
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
                         {!! \App\Helpers\CategoryTableHelper::renderRows() !!}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection