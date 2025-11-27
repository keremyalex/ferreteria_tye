<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PageVisit extends Model
{
    protected $fillable = [
        'page_url',
        'page_name', 
        'visits_count',
        'last_visited_at'
    ];

    protected $casts = [
        'last_visited_at' => 'datetime',
        'visits_count' => 'integer'
    ];

    /**
     * Incrementar el contador de visitas para una página
     */
    public static function incrementVisit(string $pageUrl, string $pageName): int
    {
        \Log::info('PageVisit::incrementVisit called', [
            'url' => $pageUrl,
            'name' => $pageName
        ]);
        
        // Incrementar o crear el registro directamente
        $pageVisit = self::firstOrCreate(
            ['page_url' => $pageUrl],
            [
                'page_name' => $pageName,
                'visits_count' => 0,
                'last_visited_at' => now()
            ]
        );

        $pageVisit->increment('visits_count');
        $pageVisit->update(['last_visited_at' => now()]);
        
        \Log::info('PageVisit incremented', [
            'url' => $pageUrl,
            'new_count' => $pageVisit->visits_count
        ]);
        
        return $pageVisit->visits_count;
    }

    /**
     * Obtener contador de visitas para una URL específica
     */
    public static function getVisitCount(string $pageUrl): int
    {
        return self::where('page_url', $pageUrl)->value('visits_count') ?? 0;
    }

    /**
     * Obtener estadísticas generales
     */
    public static function getStats(): array
    {
        return [
            'total_pages' => self::count(),
            'total_visits' => self::sum('visits_count'),
            'most_visited' => self::orderBy('visits_count', 'desc')->first(),
            'recent_visits' => self::orderBy('last_visited_at', 'desc')->take(5)->get()
        ];
    }

    /**
     * Obtener las páginas más visitadas
     */
    public static function getMostVisited(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return self::orderBy('visits_count', 'desc')
            ->limit($limit)
            ->get();
    }
}
