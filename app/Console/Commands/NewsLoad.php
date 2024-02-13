<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsLoad extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:news-load';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load news articles from worldnews.io api';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ini_set('max_execution_time', 0);
        $offset = 0;
        $requestLeft = 50;
        Log::info('Starting news load');
        while ($requestLeft > 20) {
            try {
                $response = Http::get('https://api.worldnewsapi.com/search-news', [
                    'source-countries' => 'co',
                    'language' => 'es',
                    'sort' => 'publish-time',
                    'sort-direction' => 'DESC',
                    'api-key' => config('app.world_news_api_key'),
                    'number' => 10,
                    'offset' => $offset,
                ]);

                if ($response->successful()) {
                    $headers = $response->headers();
                    $requestLeft = intval($headers['X-API-Quota-Left'][0]);
                    // print("Left: $requestLeft, Offset: $offset \n\n");
                    $result = $response->json();
                    $posts = $result['news'];
                    $createdCount = 0;
                    foreach ($posts as $post) {
                        if (Post::where('key', $post['id'])->exists()) continue;
                        if (strpos($post['image'], 'preview-image') !== false) continue;
                        $post = Post::create([
                            'key' => $post['id'],
                            'title' => $post['title'],
                            'url' => $post['url'],
                            'image_url' => $post['image'],
                            'text' => $post['text'],
                            'published_at' => $post['publish_date'],
                            'source' => 'worldnews',
                        ]);
                        $createdCount = $createdCount + 1;
                    }
                    Log::info("Created $createdCount posts");
                    $offset = $offset + 10;
                } else {
                    $statusCode = $response->status();
                    Log::error($response->body());
                }
            } catch (\Throwable $th) {
                throw $th;
            }
        }
        return 0;
    }
}
