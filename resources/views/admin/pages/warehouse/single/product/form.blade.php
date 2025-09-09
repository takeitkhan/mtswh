@extends('admin.layouts.master')

@section('title', !empty($product) ?  'Edit ' : 'Add new '. 'product')

@section('content')
@php
    $warehouse_id = request()->get('warehouse_id');
    $warehouse_code = request()->get('warehouse_code');
@endphp
<div class="content-wrapper">
    <form class="product-input-form"
        action="{{ !empty($product) ? route('product_update', $warehouse_code) : route('product_store', $warehouse_code) }}"
        method="post">
    @csrf
    @if (!empty($product))
        <input type="hidden" name="id" value="{{ $product->id }}">
    @endif
        <div class="row">
            <div class="col-lg-6">

                {{-- Product Name --}}
                <div class="form-group">
                    <label for="name">Product Name: </label>
                    <input type="text" class="form-control" placeholder="Enter product name" name="name"
                        value="{{ !empty($product) ? $product->name : old('name') }}" required>
                </div>

                <div class="form-group">
                    <label for="description">Product description: </label>
                    <textarea name="description" id="" class="form-control" cols="30" rows="10">{{ !empty($product) ? $product->description : old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="description">Product Category: </label>
                    <?php
                        global $avaiableCat;
                        $avaiableCat = (!empty($product)) ? $product->category_id : '';
                        function selectCat($parent_id = null, $sub_mark = "") {
                            global $avaiableCat;
                            $getCat = \App\Models\ProductCategory::where('parent_id', $parent_id)->orderBy('created_at', 'desc')->get();
                            foreach($getCat as $row){ ?>
                                <option value="{{$row->id}}" {{$row->id == $avaiableCat ? 'selected' : ''}}>{{$sub_mark.$row->name}} </option>
                                <?php selectCat($row->id, $sub_mark .'— ');
                            }
                        }?>
                        <select class="form-control form-control-sm select-box" id="category_id" name="category_id">
                            <option value="">None</option>
                            <?php selectCat();?>
                        </select>
                </div>
                <div class="form-group">
                    <label for="alert_stock_qty">Product Code: </label>
                    <input type="text" class="form-control" name="code"
                        value="{{ !empty($product) ? $product->code : old('code') }}" required>
                </div>

                <div class="form-group">
                    <label for="alert_stock_qty">Stock Qty Alert: </label>
                    <input type="number" class="form-control" name="stock_qty_alert"
                        value="{{ !empty($product) ? $product->stock_qty_alert : old('stock_qty_alert') }}" required>
                </div>
                <?php /*
                <div class="form-group">
                    <label for="use_for">Use for</label>
                    <select name="use_for" id="" class="form-select">
                        <option value="single">Single</option>
                        <option value="multiple">Multiple</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="price_range">Usual product price range: </label>
                    <input type="text" class="form-control" name="price_range"
                        value="{{ !empty($product) ? $product->price_range : old('price_range') }}">
                </div>
                */ ?>

            </div>
            <div class="col-lg-1"></div>

            <div class="col-lg-4">
                <!-- Product Type -->
                <div class="form-group">
                    <label for="product_type">Product Type</label>
                    @php
                       if(!empty($product)){
                        $product_type = explode(',', $product->product_type);
                    } else {
                        $product_type = ['Supply', 'Service'];
                    }
                    @endphp
                    <div class="form-check">
                        @foreach($product_type as $value)
                            <div class="form-group d-inline-flex me-2
                                {{!empty($product) ?  $value == in_array($value, $product_type) ? '' : 'd-none' : ''}} ">
                                <input id="pt{{$value}}" type="checkbox" class="product_type"
                                    name="product_type[]" value="{{$value}}"
                                    {{!empty($product) && $value == in_array($value, $product_type) ? 'checked' : ''}}
                                    {{!empty($product) ? 'disabled' : null}}
                                    class="checkItem">

                                <label class="w-100" for="pt{{$value}}">{{$value}}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- Unit -->
                <div class="form-group">
                    @php $units = $Query::accessModel('AttributeValue')::getValues('Unit'); @endphp
                    <label for="unit_id">Product Unit:</label>
                    <select name="unit_id" class="form-select">
                        <option value="">Select a Unit</option>
                        @foreach ($units as $unit)
                            <option
                                value="{{$unit->id}}"
                                {{!empty($product) && $product->unit_id == $unit->id ? 'selected' : ''}}
                                >{{$unit->value}}</option>
                        @endforeach
                    </select>
                </div><!--/Unit -->
                <!-- Brand -->
                <div class="form-group">
                    @php $brands = $Query::accessModel('AttributeValue')::getValues('Brand'); @endphp
                    <label for="unit_id">Product Brand:</label>
                    <select name="brand_id" id="" class="form-select">
                        <option value="">Select Brand</option>
                        @foreach ($brands as $brand)
                            <option
                                value="{{$brand->id}}"
                                {{!empty($product) && $product->brand_id == $brand->id ? 'selected' : ''}}
                                >{{$brand->value}}</option>
                        @endforeach
                    </select>
                </div><!--/Brand -->

                <!-- Barcode System -->
                <div class="form-group">
                    <label for="barcode_format">Barcode Format</label>
                    @php $getBarcodeFormat = $Query::getEnumValues('products', 'barcode_format') @endphp
                    <select name="barcode_format" id="barcode_format" class="form-select" required {{!empty($product) ? 'disabled' : null}}>
                        <option value="" disabled="" selected="">Select</option>
                        @foreach($getBarcodeFormat as $value)
                            <option value="{{$value}}"
                                {{ isset($product) && $product->barcode_format == $value? 'selected' : ''  }}
                                >{{$value}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- Barcode Prefix -->
                <?php /*
                <div class="form-group" id='barcode_prefix'>
                    <label for="price_range">Barcode Prefix: </label>
                    <small>MTS_</small>
                    <input type="text" class="form-control" name="barcode_prefix" minlength="5" maxlength="5"
                        value="{{ !empty($product) ? substr($product->barcode_prefix, 4) : old('barcode_prefix') }}">
                </div>
                */ ?>

                <!-- Warehouse -->
                <div class="form-group">
                    <label for="select">For Warehouse</label>
                    <div class="form-check">
                        @foreach(auth()->user()->getUserWarehouse() as $wh)
                            <div class="form-group d-inline-flex me-2">
                                <input type="checkbox"
                                    id="warehouse_id{{$wh->code}}"
                                    class="checkItem"
                                    name="warehouse_id[]"
                                    value="{{$wh->id}}"
                                @if(!empty($product))
                                    {{strstr($product->warehouse_id, (string)$wh->id) == true ? 'checked' : ''}}
                                @else
                                    {{$wh->code == $warehouse_code ? 'checked' : ''}}
                                @endif>
                                <label class="w-100" for="warehouse_id{{$wh->code}}">{{$wh->name}}</label>
                            </div>
                        @endforeach
                    </div>
                </div><!--/Warehouse -->
            </div>
        </div>

        <div class="form-submit_btn">
            <button type="submit" class="btn blue">Submit</button>
        </div>
    </form>
</div>

@endsection

@section('cusjs')
    @if(!empty($product) && $product->barcode_format == 'Tag')
        <script>
            $('div#barcode_prefix input').attr('required', true);
        </script>
    @else
        <script>
            $('#barcode_prefix').css('display', 'none');
        </script>
    @endif
<script>
    /*
    $('select#barcode_format').change(function(){
        let value = $(this).find(':selected').val();
        if(value == 'Tag'){
            $('div#barcode_prefix').css('display', 'flex');
            $('div#barcode_prefix input').attr('required', true);
        }else{
            $('div#barcode_prefix').css('display', 'none');
            $('div#barcode_prefix input').attr('required', false);
        }
    })
    */


    $("form.product-input-form").submit(function(){
        if ($('input.product_type:checkbox').filter(':checked').length < 1){
            alert("Please Check at least one Product Type");
            return false;
        }
    });

</script>

@endsection
