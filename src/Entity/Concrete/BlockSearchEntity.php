<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SearchEntityBundle\Entity\Concrete;

use Contao\Model;
use Contao\ModuleModel;
use HeimrichHannot\Blocks\BlockModel;
use HeimrichHannot\Blocks\ModuleBlock;
use HeimrichHannot\SearchEntityBundle\Entity\AbstractContaoSearchEntity;

class BlockSearchEntity extends AbstractContaoSearchEntity
{
    /**
     * @param BlockModel $model
     */
    public function findParents(Model $model): void
    {
        $modules = ModuleModel::findBy(['tl_module.type=?', 'tl_module.block=?'], [ModuleBlock::TYPE, $model->id]);

        if ($modules) {
            foreach ($modules as $module) {
                $this->addParent('tl_module', $module->id);
            }
        }
        $this->addParent('tl_theme', $module->pid);
    }

    protected function loadModel(int $id): ?Model
    {
        return BlockModel::findByPk($id);
    }

    /**
     * @param BlockModel $model
     */
    protected function findName(Model $model): string
    {
        return $model->title;
    }
}
