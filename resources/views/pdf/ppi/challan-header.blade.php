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

<div style="page-break-after: avoid;">
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="margin: 5px 0;">PPI CHALLAN</h2>
        <p style="margin: 2px 0; font-size: 11px;">Physical Product Information</p>
    </div>

    <table class="header-table">
        <tr>
            <td width="22%">PPI No:</td>
            <td width="40%"><strong>{{ $ppi->id ?? NULL }}</strong></td>
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
                    echo $formattedSources->implode(' > ');
                @endphp
            </td>
        </tr>
    </table>
    
    @if($ppi->note)
    <div class="note-box">
        <strong>Note: </strong> {{ $ppi->note }}
    </div>
    @endif
</div>
