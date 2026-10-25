<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function __invoke(): Response
    {
        $content = <<<'TXT'
User-agent: *
Allow: /

Sitemap: https://tapaksucisportacademy.com/sitemap.xml
TXT;

        return response($content)
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }
}
