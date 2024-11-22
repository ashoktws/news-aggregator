<?php

namespace App\Console\Commands;
use GuzzleHttp\Client;
use App\Models\Article;
use Illuminate\Support\Facades\Log;

use Illuminate\Console\Command;

class FetchArticles extends Command
{
    protected $signature = 'fetch:articles';
    protected $description = 'Fetch articles from external news APIs';

    public function handle() {
        Log::info('Cron Job Started');
        $client = new Client();
        $statusCode = 0;
        $sources = ['https://newsapi.org/v2/top-headlines?country=us&apiKey=8f03b8754e5149d194ce51285a4f9036','https://newsapi.org/v2/everything?q=Apple&apiKey=8f03b8754e5149d194ce51285a4f9036','https://newsapi.org/v2/everything?q=sports&apiKey=8f03b8754e5149d194ce51285a4f9036'];

        
        foreach ($sources as $source) {
            //$response = $client->get($source);
            $context = stream_context_create([
                "http" => [
                    "method" => "GET",
                    "header" => "Content-Type: application/json\r\n"
                ]
            ]);
            
            // Get the response headers
            $response = @file_get_contents($source, false, $context);
            if ($response === FALSE) {
                
            } else {
                $articles = json_decode($response);
                //$articles = $articles['articles'];
                //$articles = json_decode($response->getBody(), true);
                $articles = $articles->articles;
                foreach ($articles as $articleData) {
                    if($articleData->description != 'null' &&  $articleData->description != null){
                        Article::updateOrCreate(
                            ['title' => $articleData->title],
                            [
                                'description' => $articleData->description,
                                'url' => $articleData->url,
                                'source' => $articleData->source->name,
                                'created_at' => $articleData->publishedAt,
                            ]
                        );
                        $statusCode = 1;
                    }
                }
                
            }

            
        }
        if($statusCode == 1){
            Log::info('Articles fetched and stored successfully.');
        } else {
            Log::error('Failed to fetch articles. Status code: ' . $response);
        }
        Log::info('Cron Job Ended');
    }
}
