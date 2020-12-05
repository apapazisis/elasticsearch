<?php declare(strict_types=1);

namespace App\Observers;

use App\Contracts\ElasticSearchInterface;
use Elasticsearch\Client;

class ElasticSearchObserver
{
    private Client $elasticSearch;

    public function __construct(Client $elasticSearch)
    {
        $this->elasticSearch = $elasticSearch;
    }

    public function created(ElasticSearchInterface $model)
    {
        $this->createIndex($model);

        $this->elasticSearch->index([
            'index' => $model->getIndexName(),
            'id'    => $model->getKey(),
            'body'  => $model->addDocument(),
        ]);
    }

    public function updated(ElasticSearchInterface $model)
    {
        $this->elasticSearch->update([
            'index' => $model->getIndexName(),
            'id'    => $model->getKey(),
            'body'  => [
                'doc' => $model->updateDocument()
            ]
        ]);
    }

    public function deleted(ElasticSearchInterface $model)
    {
        $this->elasticSearch->delete([
            'index' => $model->getIndexName(),
            'id'    => $model->getKey(),
        ]);
    }

    private function createIndex(ElasticSearchInterface $model)
    {
        $indexExists = $this->elasticSearch->indices()->exists(['index' => $model->getIndexName()]);

        if (!$indexExists) {
            $index['index'] = $model->getIndexName();

            $settings = $model->getIndexSettings();

            if (!is_null($settings)) {
                $index['body']['settings'] = $settings;
            }

            if (property_exists($model, 'shards')) {
                $index['body']['settings']['number_of_shards'] = $model->shards;
            }

            if (property_exists($model, 'replicas')) {
                $index['body']['settings']['number_of_replicas'] = $model->replicas;
            }

            $mappingProperties = $model->getMappingProperties();

            if (!is_null($mappingProperties)) {
                $index['body']['mappings'] = [
                    '_source' => ['enabled' => true],
                    'properties' => $mappingProperties,
                ];
            }

            $this->elasticSearch->indices()->create($index);
        }
    }
}
