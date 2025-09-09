@extends('admin.layouts.master')

@section('title', 'Notifications')

@section('content')
    <div class="content-wrapper">
        @php
            $checkNoty = $Model('PpiSpiStatus')::notifications();
        @endphp
      
      
      	
        @if(!empty($checkNoty))
            <div class="row">
                <div class="col-md-8 px-3">
                    @php
                      $allNoty = $Model('PpiSpiStatus')::notifications(['paginate' => 61]);
                      /**
                  		$allNoty = $allNoty->where(function($query) {
                          $query->whereNull('is_read');
                      });**/
                    @endphp
                    <h6>
                        <div class="title-with-border py-2">
                            Notifications
                            <div class="d-inline-block float-end">
                                <form id="notyAllClear" action="{{route('warehouse_notification_clear_all')}}" method="post">
                                    @csrf

                                    <input type="hidden" name="clearAll"
                                           value="{{implode('|', $allNoty->pluck('id')->toArray())}}">
                                    <button onclick="DeleteconfirmAlertCustom('notyAllClear')"  type="button" class="btn btn-outline-secondary btn-sm py-0">Clear all</button>
                                </form>
                            </div>
                        </div>
                    </h6>


                    <div class="row mx-0">
						
                          @foreach($allNoty as $data)

                          @if($data->is_read != 2)

                          @else
                            <div class="col-md-4 mb-2 ps-0"
                                 title="is read {{ $data->is_read }}">
                              <div class="noty_theme_light p-2" style="{{$data->is_read == 1 ? null : 'border: 1px solid #0d6efd;'}}">
                                <p class="font-13">
                                  <a href="{{route('warehouse_notification', $data->id)}}">{{$data->message}} </a>
                                </p>
                                <p class="font-11 text-secondary">
                                  {{ $data->created_at->format('Y-m-d h:i a') }} . {{$Model('Warehouse')::name($data->warehouse_id)}} . {{$data->status_for}} ID: {{$data->ppi_spi_id}}
                                </p>

                              </div>
                            </div>
                      
                          @endif
                          @endforeach

                          <div class="row paginition ps-0">
                            {!! $allNoty->links() !!}
                          </div>                        
                        
                    </div>
            </div>
        </div>
        @endif
    </div>
@endsection