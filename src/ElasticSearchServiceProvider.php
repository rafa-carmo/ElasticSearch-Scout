<?php

namespace ElasticSearch\Scout;

use Illuminate\Support\ServiceProvider;
use Laravel\Scout\EngineManager;
use Elastic\Elasticsearch\ClientBuilder;
use Elasticsearch\Scout\Engines\ElasticSearchEngine;

class ElasticSearchServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'elasticsearch');
        $this->app->singleton(ClientBuilder::class, function () {
            return ClientBuilder::create()
            ->setHosts([env('ELASTICSEARCH_HOST')])
            ->setApiKey(env('ELASTICSEARCH_API_KEY'))
            ->build();

        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([IndexElasticSearch::class]);
        }

        resolve(EngineManager::class)->extend('elasticsearch', function () {
            return new ElasticSearchEngine(
                resolve(Client::class),
                config('scout.soft_delete', false)
            );
        });
    }
}
