<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>{{ $factura->codigo }}</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 20px; }
    .header { text-align: center; border-bottom: 1px solid #ccc; padding-bottom: 10px; }
    .logo { width: 100px; margin-bottom: 5px; }
    .info { margin-top: 10px; }
    .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    .table th, .table td { border: 1px solid #ccc; padding: 6px; text-align: left; }
    .totales { margin-top: 15px; text-align: right; }
  </style>
</head>
<body>
  <div class="header">
    @if($empresa && $empresa->logo_path)
      <img src="{{ public_path('storage/'.$empresa->logo_path) }}" class="logo" alt="Logo">
    @endif
    <h2>{{ $empresa->nombre ?? 'EMPRESA' }}</h2>
    <p>{{ $empresa->direccion ?? '' }} - {{ $empresa->telefono ?? '' }}</p>
  </div>

  <div class="info">
    <p><strong>Factura:</strong> {{ $factura->codigo }}</p>
    <p><strong>Fecha:</strong> {{ $factura->fecha_emision->format('d/m/Y') }}</p>
    <p><strong>Cliente:</strong> {{ $factura->cliente->nombre ?? 'N/A' }}</p>
    <p><strong>Usuario:</strong> {{ $factura->usuario->name ?? 'N/A' }}</p>
    <p><strong>Estado:</strong> {{ $factura->estado->nombre }}</p>
  </div>

  <table class="table">
    <thead>
      <tr>
        <th>Descripción</th>
        <th>Cant</th>
        <th>Valor Unitario</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($factura->detalles as $d)
      <tr>
        <td>{{ $d->descripcion }}</td>
        <td>{{ $d->cantidad }}</td>
        <td>${{ number_format($d->valor_unitario, 0, ',', '.') }}</td>
        <td>${{ number_format($d->total, 0, ',', '.') }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div class="totales">
    <p><strong>Subtotal:</strong> ${{ number_format($factura->subtotal, 0, ',', '.') }}</p>
    <p><strong>Total:</strong> ${{ number_format($factura->total, 0, ',', '.') }}</p>
  </div>

  <div style="margin-top: 20px;">
    <p><em>Documento generado automáticamente - {{ now()->format('d/m/Y H:i') }}</em></p>
  </div>
</body>
</html>
