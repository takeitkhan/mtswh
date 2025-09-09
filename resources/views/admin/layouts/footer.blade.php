    <div class="">
        <footer class="footer-wrapper clear-space ">
            <div class="breadcrumb-content">
                <div class="breadcrumb">
                    @yield('breadcrumb-bottom')
                </div>
            </div>

            <div class="footer-custom-icons text-center ">
                <ul>
                    @foreach(auth()->user()->getUserWarehouse() as $wh)
                    <li>
                        @php
                            $css = $wh->code == request()->get('warehouse_code') ? 'text-primary' : '';
                        @endphp
                        <a href="{{route('warehouse_single_index', $wh->code)}}">
                            <i class="fas fa-warehouse {{$css}}"></i>

                            <?php $notyCount =  $Model('PpiSpiStatus')::notifications(['count' => true, 'is_read' => true, 'warehouse_id' => $wh->id]); ?>
                            @if($notyCount > 0)
                            <span id="notification_count" style="display: unset;line-height: normal;">
                                {!!  $notyCount !!}
                            </span>

                            @endif

                        </a>
                        <span class="{{$css}}">{{$wh->name}}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </footer>
    </div>
