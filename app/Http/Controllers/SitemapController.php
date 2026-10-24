<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Program;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = [
            ['loc' => route('home'), 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['loc' => route('about'), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => route('program'), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => route('registration'), 'changefreq' => 'monthly', 'priority' => '0.9'],
            ['loc' => route('schedules'), 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['loc' => route('calendar'), 'changefreq' => 'weekly', 'priority' => '0.6'],
            ['loc' => route('blog'), 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['loc' => route('gallery'), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => route('testimonials'), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => route('faqs'), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => route('contact-us'), 'changefreq' => 'monthly', 'priority' => '0.7'],
        ];

        Program::query()
            ->active()
            ->ordered()
            ->get(['slug'])
            ->each(function (Program $program) use (&$urls): void {
                $urls[] = [
                    'loc' => route('program.show', $program->slug),
                    'changefreq' => 'monthly',
                    'priority' => '0.8',
                ];
            });

        Post::query()
            ->published()
            ->ordered()
            ->get(['slug'])
            ->each(function (Post $post) use (&$urls): void {
                $urls[] = [
                    'loc' => route('blog.show', $post->slug),
                    'changefreq' => 'monthly',
                    'priority' => '0.6',
                ];
            });

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        $xml .= ' xmlns:xhtml="http://www.w3.org/1999/xhtml">'."\n";

        foreach ($urls as $url) {
            $xml .= '    <url>'."\n";
            $xml .= '        <loc>'.e($url['loc']).'</loc>'."\n";
            $xml .= '        <lastmod>'.now()->format('Y-m-d').'</lastmod>'."\n";
            $xml .= '        <changefreq>'.$url['changefreq'].'</changefreq>'."\n";
            $xml .= '        <priority>'.$url['priority'].'</priority>'."\n";
            $xml .= '    </url>'."\n";
        }

        $xml .= '</urlset>'."\n";

        return response($xml)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}
