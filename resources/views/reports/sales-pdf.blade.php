<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Ventas - {{ $period }}</title>
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
            width: 23%;
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
        <p>Reporte de Ventas</p>
        <p>{{ $period }}</p>
        <p>Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="metrics" style="display: flex; justify-content: space-between; margin-bottom: 30px;">
        <div class="metric-card">
            <h3>Total Vendido</h3>
            <div class="value">${{ number_format($totalSales, 2) }}</div>
        </div>
        <div class="metric-card">
            <h3>Número de Ventas</h3>
            <div class="value">{{ number_format($totalOrders) }}</div>
        </div>
        <div class="metric-card">
            <h3>Promedio por Venta</h3>
            <div class="value">${{ number_format($averageOrder, 2) }}</div>
        </div>
        <div class="metric-card">
            <h3>Productos Vendidos</h3>
            <div class="value">{{ number_format($totalProducts) }}</div>
        </div>
    </div>

    @if(count($topProducts) > 0)
    <h2>Productos Más Vendidos</h2>
    <table>
        <thead>
            <tr>
                <th>Posición</th>
                <th>Producto</th>
                <th class="text-right">Cantidad Vendida</th>
                <th class="text-right">Total en Ventas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topProducts as $index => $product)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $product->nombre }}</td>
                <td class="text-right">{{ number_format($product->total_sold) }}</td>
                <td class="text-right">${{ number_format($product->total_revenue, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(count($topClients) > 0)
    <h2 style="margin-top: 30px;">Mejores Clientes</h2>
    <table>
        <thead>
            <tr>
                <th>Posición</th>
                <th>Cliente</th>
                <th class="text-right">Total Compras</th>
                <th class="text-right">Número de Órdenes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topClients as $index => $client)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $client->cliente_nombre ?: 'Cliente Anónimo' }}</td>
                <td class="text-right">${{ number_format($client->total_spent, 2) }}</td>
                <td class="text-right">{{ number_format($client->order_count) }}</td>
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