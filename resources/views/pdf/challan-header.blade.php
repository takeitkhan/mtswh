<div style="width: 100%; text-align: center; margin-bottom: 5px; padding-bottom: 10px;">
    <h2 style="text-align:center; text-decoration: underline;">
        RECEIPT CHALLAN
    </h2>
    <p style="margin: 0; font-size: 11px; color: #666;">
        PPI No: {{ $ppi->id ?? 'N/A' }} | Date: {{ $ppi->created_at->format('d/m/Y H:i') ?? 'N/A' }}
    </p>
</div>
