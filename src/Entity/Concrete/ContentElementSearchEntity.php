<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SearchEntityBundle\Entity\Concrete;

use Contao\ContentModel;
use Contao\Model;
use HeimrichHannot\SearchEntityBundle\Entity\AbstractContaoSearchEntity;

/**
 * Class ContentElementSearchEntity.
 *
 * @property ContentModel $model
 */
class ContentElementSearchEntity extends AbstractContaoSearchEntity
{
    /**
     * @param ContentModel $model
     */
    public function findParents(Model $model): void
    {
        $this->addParent($this->model->ptable, $this->model->pid);
    }

    protected function loadModel(int $id): ?Model
    {
        return ContentModel::findByPk($id);
    }

    /**
     * @param ContentModel $model
     */
    protected function findName(Model $model): string
    {
        return $model->type;
    }
}
