<?php

use App\Models\PageVisit;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

// Ruta temporal para testing del contador de visitas
Route::get('/test-visits', function () {
    
    $testUrl = 'http://127.0.0.1:8000/test-page';
    
    echo "<h2>Test de Contador de Visitas</h2>";
    
    echo "<h3>1. Test Manual de Incremento</h3>";
    
    // Incrementar visita manualmente
    try {
        $newCount = PageVisit::incrementVisit($testUrl, 'Página de Prueba Manual');
        echo "<p style='color: green;'><strong>✓ Incremento manual exitoso:</strong> {$newCount} visitas</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'><strong>✗ Error en incremento:</strong> " . $e->getMessage() . "</p>";
    }
    
    echo "<h3>2. Estado Actual de la Base de Datos</h3>";
    
    // Mostrar información actual
    $currentVisits = PageVisit::where('page_url', $testUrl)->first();
    if ($currentVisits) {
        echo "<p><strong>Registro encontrado:</strong></p>";
        echo "<ul>";
        echo "<li><strong>URL:</strong> {$currentVisits->page_url}</li>";
        echo "<li><strong>Nombre:</strong> {$currentVisits->page_name}</li>";
        echo "<li><strong>Visitas:</strong> {$currentVisits->visits_count}</li>";
        echo "<li><strong>Última visita:</strong> {$currentVisits->last_visited_at}</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>No hay registro para esta URL</p>";
    }
    
    echo "<h3>3. Test de API</h3>";
    
    // Probar el endpoint de la API
    $apiUrl = url('/api/page-visits/count?url=' . urlencode($testUrl));
    echo "<p><strong>URL de API:</strong> <a href='{$apiUrl}' target='_blank'>{$apiUrl}</a></p>";
    
    echo "<h3>4. Middleware Check</h3>";
    echo "<p><strong>Middleware registrado:</strong> " . (class_exists('App\\Http\\Middleware\\TrackPageVisits') ? '✓' : '✗') . "</p>";
    
    echo "<h3>5. Todas las páginas rastreadas</h3>";
    $allPages = PageVisit::orderBy('visits_count', 'desc')->get();
    
    if ($allPages->count() > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Nombre</th><th>URL</th><th>Visitas</th><th>Última Visita</th></tr>";
        foreach ($allPages as $page) {
            echo "<tr>";
            echo "<td>{$page->page_name}</td>";
            echo "<td style='max-width: 300px; overflow: hidden; text-overflow: ellipsis;'>{$page->page_url}</td>";
            echo "<td><strong>{$page->visits_count}</strong></td>";
            echo "<td>{$page->last_visited_at}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>⚠️ No hay páginas rastreadas aún - el middleware posiblemente no está funcionando</p>";
    }
    
    echo "<h3>6. Logs Recientes</h3>";
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $logs = file_get_contents($logFile);
        $pageVisitLogs = array_filter(explode("\n", $logs), function($line) {
            return strpos($line, 'PageVisit') !== false || strpos($line, 'TrackPageVisits') !== false;
        });
        
        if (!empty($pageVisitLogs)) {
            echo "<div style='background: #f5f5f5; padding: 10px; max-height: 200px; overflow-y: scroll;'>";
            echo "<pre>" . implode("\n", array_slice($pageVisitLogs, -10)) . "</pre>";
            echo "</div>";
        } else {
            echo "<p style='color: orange;'>No se encontraron logs de PageVisit en el archivo de log</p>";
        }
    } else {
        echo "<p style='color: red;'>Archivo de log no encontrado</p>";
    }
    
    echo "<hr>";
    echo "<p><a href='/test-visits'>🔄 Recargar para test nuevo incremento</a></p>";
    echo "<p><a href='/'>🏠 Ir al inicio (debería rastrear visita)</a></p>";
    echo "<p><a href='/dashboard'>📊 Dashboard (debería rastrear visita)</a></p>";
    
    return null;
});

// Ruta para limpiar todo
Route::get('/clear-all-visits', function () {
    try {
        $deleted = PageVisit::truncate();
        Log::info('All page visits cleared');
        return "Todas las visitas eliminadas. <a href='/test-visits'>Volver al test</a>";
    } catch (Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

// Ruta para test simple de incremento
Route::get('/test-increment', function () {
    try {
        $url = request()->url();
        $count = PageVisit::incrementVisit($url, 'Test Simple');
        return response()->json([
            'success' => true,
            'url' => $url,
            'count' => $count,
            'message' => 'Incremento exitoso'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});