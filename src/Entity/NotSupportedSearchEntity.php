<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SearchEntityBundle\Entity;

use Contao\Model;

class NotSupportedSearchEntity extends AbstractContaoSearchEntity
{
    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct(int $id, string $table)
    {
        $this->id = $id;
        $this->name = $table;
    }

    /**
     * {@inheritDoc}
     */
    public function getParents(): array
    {
        return [];
    }

    public function findParents(Model $model): void
    {
        // Empty
    }

    protected function loadModel(int $id): ?Model
    {
        return null;
    }

    protected function findName(Model $model): string
    {
        return '';
    }
}
