<?php

namespace ElasticSearch\Scout\Console;

use Illuminate\Console\Command;
use Elastic\Elasticsearch\Client;
use Exception;

class IndexElasticSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scout:index
            {--d|delete : Delete an existing index}
            {--k|key= : The name of primary key}
            {name : The name of the index}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or delete an index';

    /**
     * Execute the console command.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     *
     * @return void
     */
    public function handle(Client $client)
    {
        try {
            if ($this->option('delete')) {
                $client->indices()->delete($this->argument('name'));
                $this->info('Index "'.$this->argument('name').'" deleted.');

                return;
            }

            $creation_options = [];
            if ($this->option('key')) {
                $creation_options = ['primaryKey' => $this->option('key')];
            }
            $client->indices()->create(
                $this->argument('name'),
                $creation_options
            );
            $this->info('Index "'.$this->argument('name').'" created.');
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
