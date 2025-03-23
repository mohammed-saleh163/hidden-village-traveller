<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetPaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:paths {source} {destination}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all the possible travel paths';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $response = Http::post('http://127.0.0.1:8000/api/paths', [
            'source' => $this->argument('source'),
            'destination' => $this->argument('destination')
        ]);
        // dd($response->json());

        $this->table(['Route', 'Cost'], $response->json());
        $this->info("Number of Paths: " . count($response->json()));
    }
}
