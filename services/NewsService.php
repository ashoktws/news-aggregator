namespace App\Services;

use GuzzleHttp\Client;

class NewsService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function fetchArticles()
    {
        $response = $this->client->get('https://newsapi.org/v2/top-headlines?country=us&apiKey=8f03b8754e5149d194ce51285a4f9036');
        return json_decode($response->getBody()->getContents(), true);
    }
}