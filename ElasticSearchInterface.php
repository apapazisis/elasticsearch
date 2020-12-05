<?php declare(strict_types=1);

namespace App\Contracts;

interface ElasticSearchInterface
{
    public function getIndexName(): string;

    public function addDocument(): array;

    public function updateDocument(): array;
}
