<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SearchEntityBundle\Entity\Concrete;

use Contao\Model;
use Contao\PageModel;
use HeimrichHannot\SearchEntityBundle\Entity\AbstractContaoSearchEntity;

/**
 * Class PageSearchEntity.
 *
 * @property PageModel $model
 */
class PageSearchEntity extends AbstractContaoSearchEntity
{
    public function getParents(): array
    {
        if ('root' === $this->model->type) {
            return [];
        }

        return parent::getParents();
    }

    public function findParents(Model $model): void
    {
        $this->addParent($model::getTable(), $model->pid);
    }

    protected function loadModel(int $id): ?Model
    {
        return PageModel::findByPk($id);
    }

    /**
     * @param PageModel $model
     */
    protected function findName(Model $model): string
    {
        return $model->title;
    }
}
