<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SearchEntityBundle\Entity\Concrete;

use Contao\Model;
use HeimrichHannot\Blocks\BlockModuleModel;
use HeimrichHannot\SearchEntityBundle\Entity\AbstractContaoSearchEntity;

class BlockModuleSearchEntity extends AbstractContaoSearchEntity
{
    /**
     * @param BlockModuleModel $model
     */
    public function findParents(Model $model): void
    {
        $this->addParent('tl_block', $model->pid);
    }

    protected function loadModel(int $id): ?Model
    {
        return BlockModuleModel::findByPk($id);
    }

    /**
     * @param BlockModuleModel $model
     */
    protected function findName(Model $model): string
    {
        return $model->title;
    }
}
