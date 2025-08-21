<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'seo:generate-sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $sitemap = SitemapGenerator::create(url('/'))->getSitemap();

        // products
        DB::table('products')->select('slug')
            ->orderBy('id')->chunk(100, function ($items) use ($sitemap) {
                foreach ($items as $item) {
                    $sitemap->add(route('show.offers', $item->slug));
                }
            });

        // inventories
        DB::table('inventories')->select('slug')
            ->orderBy('id')->chunk(100, function ($items) use ($sitemap) {
                foreach ($items as $item) {
                    $sitemap->add(route('show.product', $item->slug));
                }
            });

        // shops
        DB::table('shops')->select('slug')
            ->orderBy('id')->chunk(100, function ($items) use ($sitemap) {
                foreach ($items as $item) {
                    $sitemap->add(route('show.store', $item->slug));
                }
            });

        // brands
        DB::table('manufacturers')->select('slug')
            ->orderBy('id')->chunk(100, function ($items) use ($sitemap) {
                foreach ($items as $item) {
                    $sitemap->add(route('show.brand', $item->slug));
                }
            });

        // categories
        DB::table('categories')->select('slug')
            ->orderBy('id')->chunk(100, function ($cats) use ($sitemap) {
                foreach ($cats as $cat) {
                    $sitemap->add(route('category.browse', $cat->slug));
                }
            });

        $sitemap->writeToFile(public_path('sitemap.xml'));
    }
}
