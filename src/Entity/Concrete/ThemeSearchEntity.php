<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SearchEntityBundle\Entity\Concrete;

use Contao\Model;
use Contao\ThemeModel;
use HeimrichHannot\SearchEntityBundle\Entity\AbstractContaoSearchEntity;

class ThemeSearchEntity extends AbstractContaoSearchEntity
{
    public function findParents(Model $model): void
    {
    }

    protected function loadModel(int $id): ?Model
    {
        return ThemeModel::findByPk($id);
    }

    /**
     * @param ThemeModel $model
     */
    protected function findName(Model $model): string
    {
        return $model->name;
    }
}
