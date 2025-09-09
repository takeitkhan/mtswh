@extends('admin.layouts.master')

@section('title', 'Global Settings')

@section('content')
    <div class="content-wrapper">
      <?php
        $globalSettingRow = function() use ($Query){
            return $Query::accessModel('GlobalSettings')::orderBy('meta_order', 'ASC')->get()->groupBy('meta_group');
        };
        

        $globalSetting =  function ($arg) use ($Query)
        {
            $get = $Query::accessModel('GlobalSettings')::where('meta_name', $arg)->first();
            return $get->meta_value ?? NULL;
        }
        ?>

        <form action="{{route('admin_global_settings_update')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                @foreach( $globalSettingRow() as $index => $row)
                <div class="col-md-6 mb-2">

                    <div class="card border-1">
                        <div class="card-header card-info py-0">
                            <h3 class="card-title panel-title float-left mb-1" style="font-size: 19px;">
                               {{$index}} Settings
                            </h3>
                        </div><!-- end card-header-->


                        <div class="div card-body">
                            @foreach ($row as $item)

                                <div class="form-group">
                                    <label style="width: 20%" for="">{{$item->meta_title}}</label>
                                    <input name="meta_name[]" type="hidden" value="{{$item->meta_name}}">

                                    @if($item->meta_type == 'Text')
                                        <input name="{{$item->meta_name}}" type="text" class="form-control form-control-sm"
                                            value="{{$globalSetting($item->meta_name) }}" placeholder="{{$globalSetting($item->meta_placeholder) }}">
                                    @endif
                                    
                                    @if($item->meta_type == 'Textarea')
                                    <textarea name="{{$item->meta_name}}"
                                            class="form-control form-control-sm">{{$globalSetting($item->meta_name) }}</textarea>
                                    @endif

                                    @if($item->meta_type == 'Number')
                                        <input name="{{$item->meta_name}}" type="number" class="form-control form-control-sm"
                                            value="{{$globalSetting($item->meta_name) }}" placeholder="{{$globalSetting($item->meta_placeholder) }}">
                                    @endif

                                    @if($item->meta_type == 'Email')
                                    <input name="{{$item->meta_name}}" type="email" class="form-control form-control-sm"
                                        value="{{$globalSetting($item->meta_name) }}" placeholder="{{$globalSetting($item->meta_placeholder) }}">
                                    @endif
                                    @if($item->meta_type == 'Checkbox')
                                    <input name="{{$item->meta_name}}" type="checkbox" class="checkbox"
                                        {{$globalSetting($item->meta_name) == 1 ? 'checked' : NULL }} value="1">
                                    @endif

                                </div>

                            @endforeach

                            <div class="form-group mt-2">
                                <label for="" style="width: 20%">&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-sm py-0">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- End Frontend Setting -->
            </div>
        </form>

@endsection


@section('cusjs')
@endsection
