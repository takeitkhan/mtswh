@if(isset($role) && $role->type == 'Custom')
<?php
    $ppiStatuses = \App\Helpers\Warehouse\PpiSpiHelper::ppiStatusHandler();
    $spiStatuses = \App\Helpers\Warehouse\PpiSpiHelper::spiStatusHandler();
    $translate = function($to_text) use ($Model, $role){
        return $Model('Translate')::getColumn('to_text', [
            'translate_for' => 'Role',
            'for_id' => $role->id,
            'base_text' => $to_text,
        ]);
    };
?>

<h6>
    <div class="title-with-border">Status Handler</div>
</h6>

<form action="{{route('translate_store_or_update')}}" method="post">
        @csrf
    <div class="row">
        <div class="col-md-5">
            <p><strong>PPI Status</strong></p>
            @foreach($ppiStatuses as $status)
                @if($status['status_format'] == 'Main')
                    <div class="form-group d-block">
                        <label for="" class="d-block w-auto"> {{$status['message']}}</label>
                        <input type="hidden" name="translate[{{$status['key']}}][base_text]"  value="{{$status['key']}}" />
                        <input type="hidden" name="translate[{{$status['key']}}][for_id]" value="{{$role->id}}" />
                        <input type="hidden" name="translate[{{$status['key']}}][translate_for]" value="Role" />
                        <input type="text" name="translate[{{$status['key']}}][to_text]" class="form-control form-control-sm h-22" value="{{$translate($status['key'])}}">
                    </div>
                @endif
            @endforeach

        </div>
        <div class="col-md-1"></div>
        <div class="col-md-5">
            <p><strong>SPI Status</strong></p>
            @foreach($spiStatuses as $status)
                @if($status['status_format'] == 'Main')
                    <div class="form-group d-block">
                        <label for="" class="d-block w-auto"> {{$status['message']}}</label>
                        <input type="hidden" name="translate[{{$status['key']}}][base_text]"  value="{{$status['key']}}" />
                        <input type="hidden" name="translate[{{$status['key']}}][for_id]" value="{{$role->id}}" />
                        <input type="hidden" name="translate[{{$status['key']}}][translate_for]" value="Role" />
                        <input type="text" name="translate[{{$status['key']}}][to_text]" class="form-control form-control-sm h-22" value="{{$translate($status['key'])}}">
                    </div>
                @endif
            @endforeach
        </div>

        <div class="col-md-12">
            <div class="form-submit_btn">
                <button type="submit" class="btn blue">Submit</button>
            </div>
        </div>
    </div>
</form>
@endif
