<html>
<head>
<style>
    body { font-family: sans-serif; font-size: 12px; }
    .header-table td { padding: 3px; vertical-align: top; }
    .material-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .material-table th, .material-table td { border: 1px solid #000; padding: 5px; text-align: center; }
    .material-table th { background-color: #f0f0f0; }
    .note-box { width: 100%; border: 1px solid #000; padding: 5px; margin-top: 10px; }
    .footer-box { width: 45%; border: 1px solid #000; padding: 5px; display: inline-block; vertical-align: top; margin-top: 20px; }
    .logo-top { position: absolute; top: 10px; right: 10px; width: 100px; }
    .logo-bottom { position: absolute; bottom: 0px; left: 10px; width: 150px; margin-bottom: 15px; }
    .footer_icons { width: 10px; height: 10px; }
</style>
</head>
<body>
    <table>
        <tr>
            <td width="22%">PPI No:</td>
            <td width="40%">{{ $ppi->id ?? NULL }}</td>
            <td width="22%">Requested By (PM):</td>
            <td>{{ $ppi->requested_by ?? NULL }}</td>
        </tr>
        <tr>
            <td>Date:</td>
            <td>{{ $ppi->created_at->format('d/m/Y') }}</td>
            <td>Warehouse In-Charge:</td>
            <td>{{ $performedBy?->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Project Name:</td>
            <td>{{ $ppi->project ?? '' }}</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Source:</td>
            <td colspan="3">
                @php
                    $formattedSources = $sources->map(function($source) {
                        return "{$source->source_type}: {$source->who_source}";
                    });
                    
                    $result = $formattedSources->implode(' > ');
                    
                    echo $result;
                @endphp
            </td>
        </tr>
    </table>
            

    <div class="note-box">
        <strong>Note: </strong>
        {{ "  " . $ppi->note ?? '' }}
    </div>

    <h4 style="margin-top:20px;">MATERIAL DETAILS</h4>
    <table class="material-table">
        <tr>
            <th>SL</th>
            <th>Name</th>
            <th>Description</th>
            <th>Condition<br>(New/Used/Faulty)</th>
            <th>Unit</th>
            <th>Qty</th>
            <th>Remarks</th>
        </tr>
        @foreach($products as $index => $product)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $product->productInfo->name ?? '' }}</td>
            <td>{{ $product->productInfo->description ?? '' }}</td>
            <td>{{ $product->product_state ?? '' . " > " . $product->health_status ?? '' }}</td>
            <td>
                @php
                    $unitName = 'N/A';
                    if($product->productInfo && $product->productInfo->unit) {
                        $unitName = $product->productInfo->unit->value ?? 'N/A';
                    }
                    echo $unitName;
                @endphp
            </td>
            <td>{{ $product->qty ?? '' }}</td>
            <td>{{ $product->remarks ?? '' }}</td>
        </tr>
        @endforeach
        
        {{-- Fill remaining rows to reach $maxRows --}}
        @for($i = count($products) + 1; $i <= $maxRows; $i++)
        <tr>
            <td>{{ $i }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        @endfor
    </table>

    <table style="width:100%; margin-top:30px; border-collapse: collapse;">
        <tr>
            <td style="width:70%; vertical-align: top; border-top: 1px solid #000; padding-top:10px;">
                <strong>Delivered By</strong><br/>
                {{ $performedBy->name ?? '' }}<br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <!-- {{ $ppi->requester_contact ?? '' }}<br/>
                {{ $ppi->requester_company ?? '' }}<br/>
                {{ $ppi->requester_id ?? '' }}<br/>
                {{ $ppi->requester_designation ?? '' }} -->
            </td>
            <td style="width:30%; vertical-align: top; border-top: 1px solid #000; padding-top:10px;">
                <strong>Received By</strong><br/>
                {{ $ppi->received_by }}<br/>
                <!-- {{ $ppi->receiver_contact ?? '' }}<br/>
                {{ $ppi->receiver_company ?? '' }}<br/>
                {{ $ppi->receiver_id ?? '' }}<br/>
                {{ $ppi->receiver_designation ?? '' }} -->
            </td>
        </tr>
    </table>

</body>
</html>
