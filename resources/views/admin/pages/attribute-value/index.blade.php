@extends('admin.layouts.master')

@section('title')
    Manage {{$thisAttrName}}
@endsection

@section('content')

<div class="content-wrapper">
    <div class="row">
        <!-- Form -->
        <div class="col-md-4">
            <h6> 
                <div class="title-with-border"> 
                    @if(!empty($attribute))
                        <span class="text-primary">Edit {{$attribute->unique_name}} Information</span>
                    @else
                        Information
                    @endif
                </div> 
            </h6>
            <form action="{{ !empty($attribute) ? route('attribute_update') : route('attribute_store') }}" method="post">
                @csrf
                @if (!empty($attribute))
                    <input type="hidden" name="id" value="{{ $attribute->id }}">
                @endif
                <div class="form-group"> 
                    <label for="name">Name: </label>
                    <input type="text" class="form-control" placeholder="Enter {{$thisAttrIndex}} name" name="value"
                        value="{{ !empty($attribute) ? $attribute->value : old('value') }}" required>
                </div>
                <div class="form-group"> 
                    <label for="slug">Slug: </label>
                    <input type="text" class="form-control" placeholder="Enter {{$thisAttrIndex}} slug" name="slug"
                        value="{{ !empty($attribute) ? $attribute->slug : old('slug') }}">
                </div>

                <div class="form-group select arrow_class"> 
                    <label for="slug">Status: </label>
                    @php 
                        $statuses = $Query::getEnumValues('attribute_values', 'status'); 
                    @endphp
                    <select name="status" id="" class="form-select">
                        @foreach($statuses as $key => $status)
                            <option value="{{$status}}" 
                            {{  !empty($attribute) && $status == $attribute->status ? 'selected' : null }}
                            >{{$status}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group"> 
                    <label for="name">Attribute: </label>
                    <input type="text" class="form-control readonly" name="unique_name"
                        value="{{$thisAttrName}}" required>
                    <input type="hidden" name="index" value="{{ $thisAttrIndex }}">
                </div>

                <div class="form-submit_btn">
                    <button type="submit" class="btn blue">Submit</button>
                </div>
            </form>
        </div><!-- ENd Form-->
        <div class="col-md-1"></div>
        <!-- Data -->
        <div class="col-md-2 table-wrapper desktop-view mobile-view">
            <h6> 
                <div class="title-with-border"> 
                    All {{$thisAttrName}}
                </div> 
            </h6>
            <table class="">
                <thead>
                    <tr>                        
                        <th> </th>                        
                        <th class="text-center">Name</th>                      
                        <th class="text-center">Slug</th>                      
                        <th class="text-center">Status</th>                      
                    </tr>
                </thead>
                <tbody>
                    @php  $getAttribute = $Query::accessModel('AttributeValue')::where('unique_name', $thisAttrName)->get(); @endphp
                    @foreach ($getAttribute as $data)
                        <tr>
                           <td>
                            {!! $ButtonSet::delete('attribute_'.$thisAttrIndex.'_destroy', [$thisAttrName, $data->id]) !!}
                            {!! $ButtonSet::edit('attribute_'.$thisAttrIndex.'_edit', [$thisAttrName, $data->id]) !!}
                           </td>
                           <td class="text-center">{{$data->value}}</td>
                           <td class="text-center">{{$data->slug}}</td>
                           <td class="text-center">{{$data->status}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection