<div>
    @php

//        $transferRoute = route('spi_transfer', [request()->get('warehouse_code')]);
//        $transferText = 'Transfer';
    //For Subordinate Manager
    global  $doneThisAction, $doneThisActionButton;
    $transferRoute = false;
    $transferText = false;
    if($generalUser && auth()->user()->hasRoutePermission('spi_sent_to_boss_action')){
        $transferRoute = route('spi_generate_transfer', [request()->get('warehouse_code')]);
        $transferText = 'Send to Warehouse Manager';
        if($checkSpiLastMainSts->code  == 'spi_sent_to_wh_manager'){
            $doneThisAction = true;
            $doneThisActionButton = true;
            $wheditable = false;
        } else {
            $doneThisAction = false;
            $doneThisActionButton = false;
            $wheditable = true;
        }
    }

    //Boss
    if($generalUser && auth()->user()->hasRoutePermission('spi_sent_to_wh_manager_action')){
        $doneThisAction = true;
        $doneThisActionButton = true;
        $wheditable = false;
    }

//    dump($doneThisAction);
    //For Warehouse Manager
    if($generalUser && auth()->user()->hasRoutePermission('spi_dispute_by_wh_manager_action')){
        $transferRoute = route('spi_transfer', [request()->get('warehouse_code')]);
        $transferText = 'Start to Transfer';
        $wheditable = false;
        $doneThisAction = false;

        $doneThisAction = false;
        $doneThisActionButton = false;

    }

    function doneThisAction(){
        global $doneThisAction;
        return $doneThisAction;
    }
    function doneThisActionButton(){
        global $doneThisActionButton;
        return $doneThisActionButton;
    }
    @endphp
    @if($doneThisAction == false)
        <form action="{{$transferRoute}}" method="post">
            @csrf
            <input type="hidden" name="spi_id" value="{{$spi->id}}">
            <input type="hidden" name="from_warehouse_id" value="{{request()->get('warehouse_id')}}">
            <div class="form-group">
                <label for="">Choose Warehouse</label>
                @php
                    $warehouses = $Model('Warehouse')::all();
                    $transferSpi = $Model('SpiTransfer')::where('spi_id', $spi->id)->first() ?? null;
                @endphp
                <select name="to_warehouse_id" id="" class="form-control"  style="pointer-events: {{isset($wheditable) && $wheditable == false ? 'none' : false}}" required>
                    <option value="" selected disabled>Select</option>
                    @foreach($warehouses as $data)
                        @if($data->code == request()->get('warehouse_code'))
                        @else
                            <option  value="{{$data->id}}"
                                {{!empty($transferSpi) && $transferSpi->to_warehouse_id == $data->id ? 'selected' : null}}
                            >{{$data->name}}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="form-submit_btn">
                <button type="submit" class="btn blue px-2 w-auto">{{$transferText}}</button>
            </div>
        </form>
    @endif
</div>
