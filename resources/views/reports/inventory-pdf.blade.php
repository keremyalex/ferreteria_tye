<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Inventario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .metrics {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .metric-card {
            text-align: center;
            padding: 15px;
            background: #f5f5f5;
            border-radius: 5px;
            width: 30%;
        }
        .metric-card h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #666;
        }
        .metric-card .value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .alert-high { background-color: #fee; }
        .alert-medium { background-color: #ffeaa7; }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Ferretería Tye</h1>
        <p>Reporte de Inventario</p>
        <p>Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="metrics" style="display: flex; justify-content: space-between; margin-bottom: 30px;">
        <div class="metric-card">
            <h3>Total Productos</h3>
            <div class="value">{{ number_format($totalProducts) }}</div>
        </div>
        <div class="metric-card">
            <h3>Valor Total Inventario</h3>
            <div class="value">${{ number_format($totalValue, 2) }}</div>
        </div>
        <div class="metric-card">
            <h3>Productos en Stock Crítico</h3>
            <div class="value">{{ number_format($criticalStock) }}</div>
        </div>
    </div>

    @if(count($lowStock) > 0)
    <h2>Productos con Stock Crítico</h2>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th class="text-right">Stock Actual</th>
                <th class="text-right">Stock Mínimo</th>
                <th class="text-right">Diferencia</th>
                <th class="text-right">Precio</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lowStock as $item)
            <tr class="{{ $item->cantidad_actual <= ($item->cantidad_minima * 0.5) ? 'alert-high' : 'alert-medium' }}">
                <td>{{ $item->producto_nombre }}</td>
                <td class="text-right">{{ number_format($item->cantidad_actual) }}</td>
                <td class="text-right">{{ number_format($item->cantidad_minima) }}</td>
                <td class="text-right">{{ number_format($item->cantidad_actual - $item->cantidad_minima) }}</td>
                <td class="text-right">${{ number_format($item->precio_venta, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(count($currentStock) > 0)
    <h2 style="margin-top: 30px;">Estado General del Inventario</h2>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th class="text-right">Stock Actual</th>
                <th class="text-right">Stock Mínimo</th>
                <th class="text-right">Stock Máximo</th>
                <th class="text-right">Precio</th>
                <th class="text-right">Valor Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($currentStock as $item)
            <tr>
                <td>{{ $item->producto_nombre }}</td>
                <td class="text-right">{{ number_format($item->cantidad_actual) }}</td>
                <td class="text-right">{{ number_format($item->cantidad_minima) }}</td>
                <td class="text-right">{{ number_format($item->cantidad_maxima) }}</td>
                <td class="text-right">${{ number_format($item->precio_venta, 2) }}</td>
                <td class="text-right">${{ number_format($item->cantidad_actual * $item->precio_venta, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        <p>Este reporte fue generado automáticamente por el sistema de gestión de Ferretería Tye</p>
    </div>
</body>
</html>