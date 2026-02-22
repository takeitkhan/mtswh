<style>
    .material-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .material-table th, .material-table td { border: 1px solid #000; padding: 5px; text-align: center; font-size: 11px; }
    .material-table th { background-color: #f0f0f0; font-weight: bold; }
    .material-table td { vertical-align: top; }
</style>

<h4 style="margin-top:20px; margin-bottom: 10px;">PRODUCT DETAILS</h4>
<table class="material-table">
    <tr>
        <th style="width: 5%;">SL</th>
        <th style="width: 15%;">Product Name</th>
        <th style="width: 20%;">Description</th>
        <th style="width: 15%;">Condition</th>
        <th style="width: 10%;">Unit</th>
        <th style="width: 10%;">Quantity</th>
        <th style="width: 15%;">Remarks</th>
    </tr>
    @forelse($spi->spiProducts ?? [] as $index => $product)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $product->productInfo->name ?? 'N/A' }}</td>
        <td style="text-align: left;">{{ $product->productInfo->description ?? 'N/A' }}</td>
        <td>
            @php
                $condition = [];
                if($product->product_state) $condition[] = $product->product_state;
                if($product->health_status) $condition[] = $product->health_status;
                echo implode(' > ', $condition) ?: 'N/A';
            @endphp
        </td>
        <td>
            @php
                $unitName = 'N/A';
                if($product->productInfo && $product->productInfo->unit) {
                    $unitName = $product->productInfo->unit->value ?? 'N/A';
                }
                echo $unitName;
            @endphp
        </td>
        <td>{{ $product->qty ?? 'N/A' }}</td>
        <td style="text-align: left;">{{ $product->remarks ?? '' }}</td>
    </tr>
    @empty
    <tr>
        <td colspan="7" style="text-align: center; padding: 20px; font-style: italic;">No products found</td>
    </tr>
    @endforelse
</table>

<table style="width:100%; margin-top:30px; border-collapse: collapse;">
    <tr>
        <td style="width:70%; vertical-align: top; border-top: 1px solid #000; padding-top:10px;">
            <strong>Prepared By</strong><br/>
            {{ auth()->user()->name ?? '' }}<br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
        </td>
        <td style="width:30%; vertical-align: top; border-top: 1px solid #000; padding-top:10px;">
            <strong>Authorized By</strong><br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
        </td>
    </tr>
</table>
