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
