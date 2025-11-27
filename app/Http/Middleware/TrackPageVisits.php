<?php

namespace App\Http\Middleware;

use App\Models\PageVisit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrackPageVisits
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $response = $next($request);
        
        // Solo rastrear peticiones GET y HEAD (navegación del usuario)
        if (in_array($request->method(), ['GET', 'HEAD'])) {
            $currentPath = $request->path();
            
            Log::info('TrackPageVisits middleware processing', [
                'path' => $currentPath,
                'url' => $request->url()
            ]);
            
            // Filtrar rutas que no necesitan ser rastreadas (simplificado)
            $excludedPaths = [
                'api/',
                'storage/',
                'build/',
                'hot'
            ];

            $shouldTrack = true;
            foreach ($excludedPaths as $excluded) {
                if (str_starts_with($currentPath, $excluded)) {
                    $shouldTrack = false;
                    break;
                }
            }
            
            // Excluir archivos por extensión
            if (preg_match('/\.(js|css|map|ico|png|jpg|jpeg|gif|svg|woff|woff2|ttf|eot)$/i', $currentPath)) {
                $shouldTrack = false;
            }

            if ($shouldTrack) {
                try {
                    // Usar la ruta relativa en lugar de la URL completa
                    $relativePath = '/' . ltrim($request->path(), '/');
                    if ($relativePath === '/') {
                        $relativePath = '/'; // Asegurar que la página principal sea solo '/'
                    }
                    
                    $pageName = $this->getPageName($request);
                    
                    Log::info('Tracking page visit', [
                        'relative_path' => $relativePath,
                        'name' => $pageName,
                        'full_url' => $request->url()
                    ]);
                    
                    // Rastrear la visita inmediatamente usando ruta relativa
                    PageVisit::incrementVisit($relativePath, $pageName);
                    
                } catch (\Exception $e) {
                    Log::warning('Error tracking page visit: ' . $e->getMessage(), [
                        'exception' => $e->getTraceAsString()
                    ]);
                }
            } else {
                Log::info('Page visit not tracked (excluded)', ['path' => $currentPath]);
            }
        }

        return $response;
    }

    /**
     * Obtener nombre amigable de la página basado en la ruta
     */
    private function getPageName(Request $request): string
    {
        $routeName = $request->route()?->getName();
        $path = $request->path();

        // Mapeo de rutas a nombres amigables
        $pageNames = [
            '/' => 'Inicio',
            'dashboard' => 'Panel de Control',
            'productos' => 'Productos',
            'productos/create' => 'Nuevo Producto',
            'compras' => 'Compras',
            'compras/create' => 'Nueva Compra',
            'ventas' => 'Ventas',
            'ventas/create' => 'Nueva Venta',
            'inventario' => 'Inventario',
            'proveedores' => 'Proveedores',
            'proveedores/create' => 'Nuevo Proveedor',
            'clientes' => 'Clientes',
            'clientes/create' => 'Nuevo Cliente',
            'movimientos' => 'Movimientos de Stock',
            'reportes' => 'Reportes',
            'configuracion' => 'Configuración',
            'perfil' => 'Mi Perfil',
            'login' => 'Iniciar Sesión',
            'register' => 'Registrarse',
            'password/reset' => 'Restablecer Contraseña'
        ];

        // Buscar coincidencia exacta
        if (isset($pageNames[$path])) {
            return $pageNames[$path];
        }

        // Buscar por nombre de ruta si está disponible
        if ($routeName && isset($pageNames[$routeName])) {
            return $pageNames[$routeName];
        }

        // Generar nombre basado en la URL
        if ($path === '/') {
            return 'Inicio';
        }

        // Para rutas con parámetros como productos/{id}
        $segments = explode('/', trim($path, '/'));
        $basePath = $segments[0] ?? '';
        
        if (isset($pageNames[$basePath])) {
            if (count($segments) > 1) {
                return $pageNames[$basePath] . ' - Detalle';
            }
            return $pageNames[$basePath];
        }

        // Fallback: capitalizar y limpiar la URL
        return ucfirst(str_replace(['_', '-'], ' ', $basePath)) ?: 'Página';
    }
}
