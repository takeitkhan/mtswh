<style>
    body { font-family: sans-serif; font-size: 12px; }
    .header-table td { padding: 3px; vertical-align: top; }
</style>

<div style="page-break-after: avoid;">
    <div style="text-align: center; margin-bottom: 15px;">
        @if(isset($logoPath) && file_exists($logoPath))
            <img src="{{ $logoPath }}" alt="Logo" style="height: 50px; margin-bottom: 10px;">
        @endif
        <h2 style="margin: 5px 0; font-size: 16px;">DELIVERY CHALLAN</h2>
        <p style="margin: 2px 0; font-size: 10px; font-weight: bold;">Sales Product Information (SPI)</p>
    </div>

    <table class="header-table" style="width: 100%;">
        <tr>
            <td width="22%"><strong>SPI No:</strong></td>
            <td width="40%">{{ $spi->id ?? 'N/A' }}</td>
            <td width="22%"><strong>Requested By:</strong></td>
            <td>{{ $spi->requested_by ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Date:</strong></td>
            <td>{{ $spi->created_at ? $spi->created_at->format('d/m/Y') : 'N/A' }}</td>
            <td><strong>Warehouse:</strong></td>
            <td>{{ $warehouse->code ?? 'N/A' }} - {{ $warehouse->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Project:</strong></td>
            <td>{{ $spi->project ?? 'N/A' }}</td>
            <td><strong>Trans Type:</strong></td>
            <td>{{ $spi->tran_type ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>SPI Type:</strong></td>
            <td>{{ $spi->ppi_spi_type ?? 'N/A' }}</td>
            <td><strong>To Whom:</strong></td>
            <td>{{ $spi->to_whom ?? 'N/A' }}</td>
        </tr>
        @if($spi->note)
        <tr>
            <td colspan="4">
                <div style="border: 1px solid #000; padding: 5px; margin-top: 5px;">
                    <strong>Note:</strong> {{ $spi->note }}
                </div>
            </td>
        </tr>
        @endif
    </table>
</div>
