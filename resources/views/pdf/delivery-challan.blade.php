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
    .title { text-align: center; font-weight: bold; font-size: 14px; margin-bottom: 15px; }
</style>
</head>
<body>
    <div class="title">DELIVERY CHALLAN - SPI (Sales Product Information)</div>
    
    <table>
        <tr>
            <td width="22%">SPI No:</td>
            <td width="40%">{{ $spi->id ?? NULL }}</td>
            <td width="22%">Requested By (PM):</td>
            <td>{{ $spi->requested_by ?? NULL }}</td>
        </tr>
        <tr>
            <td>Date:</td>
            <td>{{ $spi->created_at->format('d/m/Y') }}</td>
            <td>Warehouse Code:</td>
            <td>{{ $warehouse->code ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Project Name:</td>
            <td>{{ $spi->project ?? '' }}</td>
            <td>Transaction Type:</td>
            <td>{{ $spi->tran_type ?? '' }}</td>
        </tr>
        <tr>
            <td>SPI Type:</td>
            <td>{{ $spi->ppi_spi_type ?? '' }}</td>
            <td>To Whom:</td>
            <td>{{ $spi->to_whom ?? '' }}</td>
        </tr>
    </table>
            
    @if($spi->note)
    <div class="note-box">
        <strong>Note: </strong>{{ $spi->note }}
    </div>
    @endif

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

    <div style="margin-top: 30px;">
        <div style="float: left; width: 45%; text-align: center;">
            <p style="margin-top: 40px;">_____________________</p>
            <p>Prepared By</p>
        </div>
        <div style="float: right; width: 45%; text-align: center;">
            <p style="margin-top: 40px;">_____________________</p>
            <p>Authorized By</p>
        </div>
    </div>

</body>
</html>
