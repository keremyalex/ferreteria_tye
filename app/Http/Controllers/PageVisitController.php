<?php

namespace App\Http\Controllers;

use App\Models\PageVisit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageVisitController extends Controller
{
    /**
     * Obtener el contador de visitas para una página específica
     */
    public function getVisitCount(Request $request)
    {
        $requestedUrl = $request->get('url', $request->url());
        
        // Extraer la ruta relativa de la URL solicitada
        $parsedUrl = parse_url($requestedUrl);
        $relativePath = $parsedUrl['path'] ?? '/';
        
        // Normalizar la ruta
        $relativePath = '/' . ltrim($relativePath, '/');
        if ($relativePath === '/') {
            $relativePath = '/'; // Página principal
        }
        
        \Log::info('PageVisitController::getVisitCount called', [
            'original_url' => $requestedUrl,
            'relative_path' => $relativePath,
            'request_url' => $request->url()
        ]);
        
        // Buscar por ruta relativa
        $visitCount = PageVisit::where('page_url', $relativePath)->value('visits_count') ?? 0;
        $pageVisit = PageVisit::where('page_url', $relativePath)->first();
        
        \Log::info('Visit count result', [
            'path_used' => $relativePath,
            'count' => $visitCount,
            'record_exists' => $pageVisit ? true : false
        ]);
        
        return response()->json([
            'visits' => $visitCount,
            'url' => $relativePath,
            'original_url' => $requestedUrl,
            'timestamp' => now()->toISOString(),
            'debug' => [
                'record_exists' => $pageVisit ? true : false,
                'total_pages_tracked' => PageVisit::count(),
                'relative_path' => $relativePath
            ]
        ]);
    }

    /**
     * Obtener el contador de visitas de la página actual
     */
    public function getCurrentPageVisits()
    {
        $currentUrl = url()->current();
        $visitCount = PageVisit::getVisitCount($currentUrl);
        
        return response()->json([
            'visits' => $visitCount,
            'url' => $currentUrl
        ]);
    }

    /**
     * Mostrar estadísticas de visitas (página de administración)
     */
    public function index()
    {
        $stats = PageVisit::getStats();
        $mostVisited = PageVisit::getMostVisited(10);
        $recentVisits = PageVisit::orderBy('last_visited_at', 'desc')->take(10)->get();
        
        return Inertia::render('Admin/PageVisits', [
            'stats' => $stats,
            'mostVisited' => $mostVisited,
            'recentVisits' => $recentVisits
        ]);
    }

    /**
     * API para obtener estadísticas en tiempo real
     */
    public function getStats()
    {
        return response()->json(PageVisit::getStats());
    }

    /**
     * API para obtener las páginas más visitadas
     */
    public function getMostVisited(Request $request)
    {
        $limit = $request->get('limit', 10);
        return response()->json(PageVisit::getMostVisited($limit));
    }

    /**
     * Incrementar contador manualmente (para testing)
     */
    public function increment(Request $request)
    {
        $request->validate([
            'page_url' => 'required|string|max:500',
            'page_name' => 'required|string|max:100'
        ]);

        $visitCount = PageVisit::incrementVisit(
            $request->page_url,
            $request->page_name
        );

        return response()->json([
            'visits' => $visitCount,
            'message' => 'Contador incrementado correctamente'
        ]);
    }

    /**
     * Limpiar registros antiguos (para mantenimiento)
     */
    public function cleanup(Request $request)
    {
        $daysOld = $request->get('days', 365); // Por defecto, eliminar registros de más de 1 año
        
        $deleted = PageVisit::where('last_visited_at', '<', now()->subDays($daysOld))
            ->where('visits_count', '<', 5) // Solo eliminar páginas con pocas visitas
            ->delete();

        return response()->json([
            'message' => "Se eliminaron {$deleted} registros antiguos",
            'deleted_count' => $deleted
        ]);
    }
}
