<?php declare(strict_types=1);

namespace App\Traits;

use Elasticsearch\Client;
use Exception;
use Illuminate\Support\Arr;

trait ElasticSearch
{
    public array $params = [];

    public static function bootElasticSearch(): void
    {
        static::observe(ElasticSearchObserver::class);
    }
    
    /**
     * @return static
     */
    public static function instance(): self
    {
        return (new static);
    }

    /**
     * @return \Elasticsearch\Client
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getElasticSearchInstance(): Client
    {
        return app()->make(Client::class);
    }

    /**
     * @return string
     */
    public function getIndexName(): string
    {
        if (property_exists($this, 'elasticIndex')) {
            return strtolower($this->elasticIndex);
        }

        return strtolower($this->getTable());
    }

    /**
     * @return array
     */
    public function addDocument(): array
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function updateDocument(): array
    {
        return $this->getDirty();
    }

    /**
     * @return array|null
     */
    public function getMappingProperties(): ?array
    {
        return $this->mappingProperties;
    }

    /**
     * @return array|null
     */
    public function getIndexSettings(): ?array
    {
        return $this->indexSettings;
    }

    /**
     * @param  array  $query
     *
     * @return static
     * @throws \Exception
     */
    public static function searchByQuery(array $query): self
    {
        $instance = static::instance();
        $instance->params['index'] = $instance->getIndexName();

        if (!empty($query)) {
            $instance->params['body']['query'] = $query;

            return $instance;
        }

        throw new Exception('No query is specified', 500);
    }

    /**
     * @param  string  $term
     *
     * @return static
     * @throws \Exception
     */
    public static function search($term = ''): self
    {
        $instance = static::instance();
        $instance->params['index'] = $instance->getIndexName();

        if (!empty($term)) {
            $instance->params['body']['query']['match']['_all'] = $term;

            return $instance;
        }

        throw new Exception('No term is specified', 500);
    }

    /**
     * @param  string  $field
     * @param  string  $dir
     *
     * @return $this
     */
    public function orderBy(string $field, string $dir = 'asc'): self
    {
        $this->params['body']['sort'] = [
            $field => ['order' => $dir]
        ];

        return $this;
    }

    /**
     * @param  int  $limit
     *
     * @return $this
     */
    public function limit(int $limit): self
    {
        $this->params['body']['size'] = $limit;

        return $this;
    }

    /**
     * @param  int  $offset
     *
     * @return $this
     */
    public function offset(int $offset): self
    {
        $this->params['body']['from'] = $offset;

        return $this;
    }

    /**
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function get(): array
    {
        return ($this->getElasticSearchInstance())->search($this->params);
    }

    /**
     * @param  string  $field
     *
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function pluck(string $field): array
    {
        $elasticSearch = $this->getElasticSearchInstance();

        $result = $elasticSearch->search($this->params);

        return $this->pluckField($result, $field);
    }

    /**
     * @param  array  $result
     * @param  string  $field
     *
     * @return array
     */
    protected function pluckField(array $result, string $field): array
    {
        if ($field === 'id') {
            return Arr::pluck($result['hits']['hits'], "_$field");
        }

        return Arr::pluck($result['hits']['hits'], "_source.$field");
    }
}
