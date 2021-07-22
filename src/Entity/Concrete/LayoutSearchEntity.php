<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SearchEntityBundle\Entity\Concrete;

use Contao\LayoutModel;
use Contao\Model;
use Contao\PageModel;
use Contao\ThemeModel;
use HeimrichHannot\SearchEntityBundle\Entity\AbstractContaoSearchEntity;

class LayoutSearchEntity extends AbstractContaoSearchEntity
{
    /**
     * @param LayoutModel $model
     */
    public function findParents(Model $model): void
    {
        $this->addParent(ThemeModel::getTable(), $model->pid);

        $pages = PageModel::findByLayout($model->id);

        if ($pages) {
            foreach ($pages as $page) {
                $this->addParent($page::getTable(), $page->id);
            }
        }
    }

    protected function loadModel(int $id): ?Model
    {
        return LayoutModel::findByPk($id);
    }

    /**
     * @param LayoutModel $model
     */
    protected function findName(Model $model): string
    {
        return $model->name;
    }
}
