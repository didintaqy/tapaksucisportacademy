<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class RobotsController extends Controller
{
    public function __invoke(): Response
    {
        $content = Cache::remember('robots.txt', now()->addMinutes(60), function () {
            return <<<'TXT'
User-agent: *
Allow: /

Sitemap: https://tapaksucisportacademy.com/sitemap.xml
TXT;
        });

        return response($content)
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }
}
