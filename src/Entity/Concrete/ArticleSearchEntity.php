<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SearchEntityBundle\Entity\Concrete;

use Contao\ArticleModel;
use Contao\Model;
use HeimrichHannot\SearchEntityBundle\Entity\AbstractContaoSearchEntity;

class ArticleSearchEntity extends AbstractContaoSearchEntity
{
    /**
     * @param ArticleModel $model
     */
    public function findParents(Model $model): void
    {
        $this->addParent('tl_page', $model->pid);
    }

    protected function loadModel(int $id): ?Model
    {
        return ArticleModel::findByPk($id);
    }

    /**
     * @param ArticleModel $model
     */
    protected function findName(Model $model): string
    {
        return $model->title;
    }
}
