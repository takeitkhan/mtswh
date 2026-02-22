<div class="timeline shadow-sm px-2 block mb-4 position-sticky top-0" style="max-height: 85vh; overflow-y: scroll; padding-bottom: 150px;">
    @php
        $spiStatuses = $Model('PpiSpiStatus')::where('ppi_spi_id', $spi_id)
                                        ->orderBy('id', 'desc')->get();
        $spiComplete = $Model('PpiSpiStatus')::getSpiLastMainStatus($spi_id)->code ?? null;
    @endphp
    
    @if($spiComplete == 'spi_all_steps_complete')
        <div class="alert alert-success mb-3" style="background-color: #d4edda; border-color: #c3e6cb;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong><i class="fa fa-check-circle me-2"></i>SPI Closed</strong>
                    <p class="mb-1 small">This SPI is closed. You can view the delivery challan anytime.</p>
                </div>
                <a href="{{ route('spi_delivery_challan_preview', [\Request::get('warehouse_code'), $spi_id, 'spi_all_steps_complete']) }}" 
                   target="_blank" 
                   class="btn btn-sm btn-success">
                    <i class="fa fa-file-pdf me-1"></i>View PDF
                </a>
            </div>
        </div>
    @endif
    
    @foreach ($spiStatuses as $key => $data)
    <div class="tl-item active">
        <div class="tl-dot border-{{$data->status_type}}">
            <i class="fas fa-arrow-up text-{{$data->status_type}}"></i>
        </div>
        <div class="tl-content"  style="{{isset($spi_product_id) && $spi_product_id == $data->ppi_spi_product_id ? 'background: #f5f5dc' : null}}">
            <div class="title" title="Status ID: {{$data->id}}">{!! $data->message !!}</div>
            <div class="tl-date text-{{$data->status_type}}">{!!  $data->note !!}</div>
            <div class="lead">Action performed by {{$Model('User')::getColumn($data->action_performed_by, 'name')}}</div>
            <div class="tl-date">Performed at {{$data->created_at->format('d M Y h:s a')}}</div>
            @php
                $checkHistory = $Model('PpiSpiHistory')::where('action_format', 'Spi')
                                    ->where('ppi_spi_id', $spi_id)
                                    ->where('status_id', $data->id)
                                    ->first();
            @endphp
            @if(!empty($checkHistory))
                <div class="lead">
                    <a href="{{route('spi_history', [request()->get('warehouse_code'), $checkHistory->id])}}?ppi_spi_product_id={{$data->ppi_spi_product_id}}" class="text-primary" target="_blank">
                        <i class="fa fa-link"></i> Previous History
                    </a>
                </div>
            @endif
        </div>
    </div>
    @endforeach
</div>

