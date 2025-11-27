<?php

use App\Models\PageVisit;
use Illuminate\Support\Facades\Route;

// Ruta para migrar URLs existentes a rutas relativas
Route::get('/migrate-page-visits', function () {
    echo "<h2>Migración de URLs a Rutas Relativas</h2>";
    
    $pageVisits = PageVisit::all();
    $migrated = 0;
    $errors = 0;
    
    echo "<h3>Procesando " . $pageVisits->count() . " registros...</h3>";
    
    foreach ($pageVisits as $pageVisit) {
        try {
            $originalUrl = $pageVisit->page_url;
            
            // Si ya es una ruta relativa, saltarla
            if (strpos($originalUrl, 'http') !== 0) {
                echo "<p style='color: blue;'>✓ Ya es relativa: {$originalUrl}</p>";
                continue;
            }
            
            // Extraer la ruta de la URL completa
            $parsedUrl = parse_url($originalUrl);
            $relativePath = $parsedUrl['path'] ?? '/';
            
            // Normalizar
            $relativePath = '/' . ltrim($relativePath, '/');
            if ($relativePath === '//' || $relativePath === '') {
                $relativePath = '/';
            }
            
            // Verificar si ya existe una entrada con esa ruta relativa
            $existing = PageVisit::where('page_url', $relativePath)->first();
            
            if ($existing && $existing->id !== $pageVisit->id) {
                // Si existe otra entrada, combinar las visitas
                $existing->visits_count += $pageVisit->visits_count;
                if ($pageVisit->last_visited_at > $existing->last_visited_at) {
                    $existing->last_visited_at = $pageVisit->last_visited_at;
                }
                $existing->save();
                $pageVisit->delete();
                
                echo "<p style='color: green;'>✓ Combinado: {$originalUrl} → {$relativePath} (visitas combinadas: {$existing->visits_count})</p>";
            } else {
                // Actualizar la URL a ruta relativa
                $pageVisit->page_url = $relativePath;
                $pageVisit->save();
                
                echo "<p style='color: green;'>✓ Migrado: {$originalUrl} → {$relativePath}</p>";
            }
            
            $migrated++;
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>✗ Error en {$pageVisit->page_url}: " . $e->getMessage() . "</p>";
            $errors++;
        }
    }
    
    echo "<hr>";
    echo "<p><strong>Resumen:</strong></p>";
    echo "<ul>";
    echo "<li><strong>Registros migrados:</strong> {$migrated}</li>";
    echo "<li><strong>Errores:</strong> {$errors}</li>";
    echo "</ul>";
    
    echo "<h3>Registros finales:</h3>";
    $finalRecords = PageVisit::orderBy('visits_count', 'desc')->get();
    
    if ($finalRecords->count() > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Ruta</th><th>Nombre</th><th>Visitas</th><th>Última Visita</th></tr>";
        foreach ($finalRecords as $record) {
            echo "<tr>";
            echo "<td><strong>{$record->page_url}</strong></td>";
            echo "<td>{$record->page_name}</td>";
            echo "<td>{$record->visits_count}</td>";
            echo "<td>{$record->last_visited_at}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<hr>";
    echo "<p><a href='/'>🏠 Probar página principal</a></p>";
    echo "<p><a href='/test-visits'>🔧 Volver al test</a></p>";
    
    return null;
});