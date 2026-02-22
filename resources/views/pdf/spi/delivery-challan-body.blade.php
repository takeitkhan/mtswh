<h4 style="margin-top:20px;">PRODUCT DETAILS</h4>
<table class="material-table">
    <tr>
        <th>SL</th>
        <th>Product Name</th>
        <th>Description</th>
        <th>Condition</th>
        <th>Unit</th>
        <th>Quantity</th>
        <th>Remarks</th>
    </tr>
    @forelse($spi->spiProducts ?? [] as $index => $product)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $product->productInfo->name ?? '' }}</td>
        <td>{{ $product->productInfo->description ?? '' }}</td>
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
        <td>{{ $product->qty ?? '' }}</td>
        <td>{{ $product->remarks ?? '' }}</td>
    </tr>
    @empty
    <tr>
        <td colspan="7" style="text-align: center; padding: 20px;">No products found</td>
    </tr>
    @endforelse
</table>
