<style>
    body { font-family: sans-serif; font-size: 12px; }
    .header-table td { padding: 3px; vertical-align: top; }
    .material-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .material-table th, .material-table td { border: 1px solid #000; padding: 5px; text-align: center; }
    .material-table th { background-color: #f0f0f0; }
    .note-box { width: 100%; border: 1px solid #000; padding: 5px; margin-top: 10px; }
</style>

<div style="page-break-after: avoid;">
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="margin: 5px 0;">DELIVERY CHALLAN</h2>
        <p style="margin: 2px 0; font-size: 11px;">Sales Product Information (SPI)</p>
    </div>

    <table class="header-table">
        <tr>
            <td width="22%">SPI No:</td>
            <td width="40%"><strong>{{ $spi->id ?? NULL }}</strong></td>
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
        <strong>Note: </strong> {{ $spi->note }}
    </div>
    @endif
</div>
