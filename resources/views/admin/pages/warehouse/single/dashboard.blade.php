@extends('admin.layouts.master')

@section('title')
Dashboard of {{request()->get('warehouse_name') ?? null}}

@endsection

@section('content')
    <div class="content-wrapper">
        @php
            //$checkNoty = $Model('PpiSpiStatus')::notifications();
            $allNoty = $Model('PpiSpiStatus')::notifications(['query' => true, 'warehouse_id' => request()->get('warehouse_id')])
                ->whereNull('is_read')
                ->paginate(30);
        @endphp
        @if($allNoty->isNotEmpty())
            <div class="row">
            <div class="col-md-6 px-3">
                <h6>
                    <div class="title-with-border py-2">
                        Notifications
                        <div class="d-inline-block float-end">
                            <form id="notyAllClear" action="{{route('warehouse_notification_clear_all')}}" method="post">
                                @csrf
                                <input type="hidden" name="clearAll" value="{{implode('|', $Model('PpiSpiStatus')::notifications(['warehouse_id' => request()->get('warehouse_id')])->pluck('id')->toArray())}}">
                                <button onclick="DeleteconfirmAlertCustom('notyAllClear')"  type="button" class="btn btn-outline-secondary btn-sm py-0">Clear all</button>
                            </form>
                        </div>
                    </div>
                </h6>

                <div class="row mx-0">
                    @php
                        $allNoty = $Model('PpiSpiStatus')::notifications(['query' => true, 'warehouse_id' => request()->get('warehouse_id')]);
                          $allNoty = $allNoty->where(function($query){
//                                    $query->where('is_read' ,1)->orWhereNull('is_read');
                                    $query->whereNull('is_read');
                                })->paginate(30);
                    @endphp
                    @if(!empty($allNoty))
                        @foreach($allNoty as $data)
                            @if($data->is_read == 2)

                            @else
                            <div class="col-md-4 mb-2 ps-0">
                                <div class="noty_theme_light p-2" style="{{$data->is_read == 1 ? null : 'border: 1px solid #0d6efd;'}}">
                                    <p class="font-13">
                                        <a href="{{route('warehouse_notification', $data->id)}}">{{$data->message}} </a>
                                    </p>
                                    <p class="font-11 text-secondary">
                                        {{$data->created_at->format('Y-m-d h:i a')}} . {{$Model('Warehouse')::name($data->warehouse_id)}} . {{$data->status_for}} ID: {{$data->ppi_spi_id}}
                                    </p>
                                </div>
                            </div>
                            @endif
                        @endforeach
                        <div class="row paginition ps-0">
                            {!! $allNoty->links() !!}
                        </div>

                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection
