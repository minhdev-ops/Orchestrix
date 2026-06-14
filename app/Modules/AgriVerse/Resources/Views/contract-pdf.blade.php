<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contract {{ $contract->contract_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; line-height: 1.6; }
        h1 { text-align: center; font-size: 20px; margin-bottom: 30px; }
        .meta { margin-bottom: 20px; }
        .meta strong { display: inline-block; width: 150px; }
        .content { margin-top: 20px; padding: 15px; border: 1px solid #ddd; }
        .signatures { margin-top: 40px; }
        .signatures div { display: inline-block; width: 45%; margin-right: 5%; vertical-align: top; }
        .signature-line { border-top: 1px solid #000; margin-top: 50px; padding-top: 5px; }
    </style>
</head>
<body>
    <h1>Electronic Contract</h1>

    <div class="meta">
        <p><strong>Contract No:</strong> {{ $contract->contract_number }}</p>
        <p><strong>Date:</strong> {{ $contract->created_at->format('d/m/Y') }}</p>
        <p><strong>Status:</strong> {{ $contract->status }}</p>
    </div>

    @if($contract->order)
    <div class="meta">
        <p><strong>Buyer:</strong> {{ $contract->order->buyer?->name ?? 'N/A' }}</p>
        <p><strong>Seller:</strong> {{ $contract->order->seller?->name ?? 'N/A' }}</p>
        <p><strong>Product:</strong> {{ $contract->order->product?->name ?? 'N/A' }}</p>
        <p><strong>Total:</strong> ${{ number_format($contract->order->total_price, 2) }}</p>
    </div>
    @endif

    <div class="content">
        {!! nl2br(e($contract->content)) !!}
    </div>

    @if($contract->signed_by_buyer || $contract->signed_by_seller)
    <div class="signatures">
        <div>
            <p><strong>Buyer</strong></p>
            <p>{{ $contract->order->buyer?->name ?? 'N/A' }}</p>
            @if($contract->signed_by_buyer)
                <div class="signature-line">Signed</div>
            @else
                <div class="signature-line">Pending</div>
            @endif
        </div>
        <div>
            <p><strong>Seller</strong></p>
            <p>{{ $contract->order->seller?->name ?? 'N/A' }}</p>
            @if($contract->signed_by_seller)
                <div class="signature-line">Signed</div>
            @else
                <div class="signature-line">Pending</div>
            @endif
        </div>
    </div>
    @endif

    @if($contract->content_hash)
    <p style="margin-top:30px; font-size:10px; color:#666;">
        Content Hash: {{ $contract->content_hash }}<br>
        Signed at: {{ $contract->signed_at?->format('d/m/Y H:i:s') }}
    </p>
    @endif
</body>
</html>
