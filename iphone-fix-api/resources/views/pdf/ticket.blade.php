<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    body { 
        font-family: 'Arial', sans-serif;
        font-size: 10px;
        line-height: 1.5;
        color: #000;
        background: #fff;
    }
    
    .ticket { 
        width: 58mm;
        max-width: 100%;
        margin: 0 auto;
        padding: 10px;
    }
    
    /* Logo */
    .logo-container {
        text-align: center;
        margin-bottom: 12px;
    }
    
    .logo-container img {
        max-width: 100px;
        height: auto;
    }
    
    /* Header elegante */
    .header {
        text-align: center;
        margin-bottom: 15px;
    }
    
    .company-name {
        font-size: 16px;
        font-weight: 900;
        letter-spacing: 1px;
        margin-bottom: 6px;
        text-transform: uppercase;
    }
    
    .company-details {
        font-size: 9px;
        line-height: 1.6;
        color: #333;
    }
    
    /* Separadores elegantes */
    .divider {
        height: 1px;
        background: repeating-linear-gradient(
            90deg,
            #000 0px,
            #000 4px,
            transparent 4px,
            transparent 8px
        );
        margin: 10px 0;
    }
    
    .divider-solid {
        border-top: 2px solid #000;
        margin: 12px 0;
    }
    
    .divider-thin {
        border-top: 1px solid #ddd;
        margin: 8px 0;
    }
    
    /* Sección de factura */
    .invoice-header {
        background: #f5f5f5;
        padding: 8px;
        margin: 10px -10px;
        text-align: center;
    }
    
    .invoice-number {
        font-size: 13px;
        font-weight: bold;
        letter-spacing: 0.5px;
    }
    
    .invoice-date {
        font-size: 9px;
        color: #666;
        margin-top: 3px;
    }
    
    /* Información cliente */
    .section-title {
        font-size: 9px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #666;
        margin: 12px 0 6px 0;
        padding-bottom: 3px;
        border-bottom: 1px solid #ddd;
    }
    
    .info-grid {
        margin: 8px 0;
    }
    
    .info-line {
        display: flex;
        justify-content: space-between;
        padding: 3px 0;
        font-size: 10px;
    }
    
    .info-line .label {
        color: #666;
        font-size: 9px;
    }
    
    .info-line .value {
        font-weight: 600;
        text-align: right;
    }
    
    /* Badge moderno */
    .status-badge {
        display: inline-block;
        padding: 3px 10px;
        background: #000;
        color: #fff;
        font-size: 8px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-radius: 3px;
        margin-top: 5px;
    }
    
    /* Productos con diseño premium */
    .products-section {
        margin: 15px 0;
    }
    
    .product-item {
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .product-item:last-child {
        border-bottom: none;
    }
    
    .product-name {
        font-size: 11px;
        font-weight: 600;
        margin-bottom: 4px;
        line-height: 1.3;
    }
    
    .product-details {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 9px;
        color: #666;
    }
    
    .product-qty {
        background: #f5f5f5;
        padding: 2px 6px;
        border-radius: 3px;
        font-weight: 600;
    }
    
    .product-price {
        font-size: 11px;
        font-weight: bold;
        color: #000;
    }
    
    /* Totales con estilo */
    .totals-section {
        margin: 15px 0;
        padding-top: 10px;
        border-top: 2px solid #000;
    }
    
    .total-line {
        display: flex;
        justify-content: space-between;
        padding: 4px 0;
        font-size: 10px;
    }
    
    .total-line.discount {
        color: #e74c3c;
    }
    
    .total-line.tax {
        color: #666;
    }
    
    .total-line.final {
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid #ddd;
        font-size: 14px;
        font-weight: bold;
    }
    
    .total-line .amount {
        font-weight: 600;
    }
    
    .total-line.final .amount {
        font-size: 16px;
    }
    
    /* Payment info */
    .payment-info {
        background: #f9f9f9;
        padding: 6px 8px;
        margin: 10px -10px;
        font-size: 9px;
        text-align: center;
        color: #666;
    }
    
    /* Footer elegante */
    .footer {
        margin-top: 15px;
        text-align: center;
    }
    
    .thank-you {
        font-size: 12px;
        font-weight: bold;
        margin-bottom: 5px;
        letter-spacing: 0.5px;
    }
    
    .footer-details {
        font-size: 9px;
        line-height: 1.4;
        color: #666;
    }
    
    .print-time {
        margin-top: 6px;
        padding-top: 6px;
        border-top: 1px solid #eee;
        font-size: 8px;
        color: #999;
    }
    
    /* QR Code */
    .qr-section {
        text-align: center;
        margin: 15px 0;
        padding: 10px 0;
    }
    
    .qr-section img {
        max-width: 80px;
        height: auto;
    }
    
    /* Notes section */
    .notes-section {
        background: #fffbf0;
        padding: 8px;
        margin: 10px -10px;
        border-left: 3px solid #ffc107;
        font-size: 9px;
        font-style: italic;
        color: #666;
    }
    
    @media print {
        body { 
            width: 58mm;
            margin: 0;
            padding: 0;
        }
        .ticket {
            width: 100%;
            padding: 5px;
        }
    }
</style>
</head>
<body>
<div class="ticket">
    
    {{-- Logo --}}
    @if(isset($empresa->logo) && $empresa->logo)
    <div class="logo-container">
        <img src="{{ $empresa->logo }}" alt="Logo">
    </div>
    @endif
    
    {{-- Header Empresa --}}
    <div class="header">
        <div class="company-name">
            {{ $empresa->nombre ?? 'MI EMPRESA' }}
        </div>
        <div class="company-details">
            @if(isset($empresa->nit))
            NIT: {{ $empresa->nit }}<br>
            @endif
            @if(isset($empresa->direccion))
            {{ $empresa->direccion }}<br>
            @endif
            @if(isset($empresa->telefono))
            ☎ {{ $empresa->telefono }}
            @endif
            @if(isset($empresa->email))
            <br>✉ {{ $empresa->email }}
            @endif
        </div>
    </div>
    
    <div class="divider-solid"></div>
    
    {{-- Invoice Header --}}
    <div class="invoice-header">
        <div class="invoice-number">
            FACTURA {{ $factura->codigo }}
        </div>
        <div class="invoice-date">
            @if(is_string($factura->fecha_emision))
                {{ $factura->fecha_emision }}
            @else
                {{ $factura->fecha_emision->format('d/m/Y H:i') }}
            @endif
        </div>
    </div>
    
    {{-- Cliente Info --}}
    <div class="section-title">Cliente</div>
    <div class="info-grid">
        <div class="info-line">
            <span class="label">Nombre</span>
            <span class="value">{{ Str::limit($factura->cliente->nombre ?? 'CONSUMIDOR FINAL', 22) }}</span>
        </div>
        @if(isset($factura->cliente->documento) && $factura->cliente->documento)
        <div class="info-line">
            <span class="label">Documento</span>
            <span class="value">{{ $factura->cliente->documento }}</span>
        </div>
        @endif
        <div class="info-line">
            <span class="label">Atendió</span>
            <span class="value">{{ $factura->usuario->name ?? 'N/A' }}</span>
        </div>
    </div>
    
    @if(isset($factura->estado->nombre))
    <div style="text-align: center;">
        <span class="status-badge">{{ $factura->estado->nombre }}</span>
    </div>
    @endif
    
    {{-- Productos --}}
    <div class="section-title">Productos</div>
    <div class="products-section">
        @foreach($factura->detalles as $d)
        <div class="product-item">
            <div class="product-name">{{ $d->descripcion }}</div>
            <div class="product-details">
                <span>
                    <span class="product-qty">{{ number_format($d->cantidad, 0) }}x</span>
                    ${{ number_format($d->valor_unitario, 0, ',', '.') }}
                </span>
                <span class="product-price">${{ number_format($d->total, 0, ',', '.') }}</span>
            </div>
        </div>
        @endforeach
    </div>
    
    {{-- Totales --}}
    <div class="totals-section">
        <div class="total-line">
            <span>Subtotal</span>
            <span class="amount">${{ number_format($factura->subtotal, 0, ',', '.') }}</span>
        </div>
        
        @if(isset($factura->descuentos) && $factura->descuentos > 0)
        <div class="total-line discount">
            <span>Descuento</span>
            <span class="amount">-${{ number_format($factura->descuentos, 0, ',', '.') }}</span>
        </div>
        @endif
        
        @if(isset($factura->impuestos) && $factura->impuestos > 0)
        <div class="total-line tax">
            <span>IVA</span>
            <span class="amount">${{ number_format($factura->impuestos, 0, ',', '.') }}</span>
        </div>
        @endif
        
        <div class="total-line final">
            <span>TOTAL</span>
            <span class="amount">${{ number_format($factura->total, 0, ',', '.') }}</span>
        </div>
    </div>
    
    {{-- Payment Method --}}
    @if(isset($factura->metodo_pago))
    <div class="payment-info">
        💳 Pago: <strong>{{ $factura->metodo_pago }}</strong>
    </div>
    @endif
    
    {{-- Notes --}}
    @if(isset($factura->notas) && $factura->notas)
    <div class="notes-section">
        📝 {{ $factura->notas }}
    </div>
    @endif
    
    <div class="divider"></div>
    
    {{-- QR Code --}}
    @if(isset($factura->qr_code))
    <div class="qr-section">
        <img src="{{ $factura->qr_code }}" alt="QR">
    </div>
    @endif
    
    {{-- Footer --}}
    <div class="footer">
        <div class="thank-you">
            ¡GRACIAS POR SU COMPRA!
        </div>
        <div class="footer-details">
            {{ $empresa->nombre ?? 'MI EMPRESA' }}<br>
            @if(isset($empresa->slogan))
            <em>{{ $empresa->slogan }}</em><br>
            @endif
            Vuelva pronto
        </div>
        <div class="print-time">
            Impreso: {{ now()->format('d/m/Y H:i:s') }}
        </div>
    </div>
    
</div>
</body>
</html>