<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $routes = Route::getRoutes();
        $urls = [];

        foreach ($routes->getByName() as $name => $route) {
            if ($route->isFallback || Str::is('api.*', $name) || Str::is('filament.*', $name) || Str::is('optimize.*', $name)) {
                continue;
            }

            $url = route($name);
            if (! $url) {
                continue;
            }

            $urls[] = [
                'loc' => $url,
                'lastmod' => now()->format('Y-m-d'),
                'changefreq' => 'daily',
                'priority' => '0.8',
            ];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'."\n";
        $xml .= '        xmlns:xhtml="http://www.w3.org/1999/xhtml">'."\n";

        foreach ($urls as $url) {
            $xml .= '    <url>'."\n";
            $xml .= '        <loc>'.$url['loc'].'</loc>'."\n";
            $xml .= '        <lastmod>'.$url['lastmod'].'</lastmod>'."\n";
            $xml .= '        <changefreq>'.$url['changefreq'].'</changefreq>'."\n";
            $xml .= '        <priority>'.$url['priority'].'</priority>'."\n";
            $xml .= '    </url>'."\n";
        }

        $xml .= '</urlset>'."\n";

        return response($xml)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}
