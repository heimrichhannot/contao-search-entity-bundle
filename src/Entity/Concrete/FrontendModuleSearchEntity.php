<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SearchEntityBundle\Entity\Concrete;

use Contao\ContentModel;
use Contao\Database;
use Contao\LayoutModel;
use Contao\Model;
use Contao\ModuleModel;
use HeimrichHannot\Blocks\BlockModuleModel;
use HeimrichHannot\SearchEntityBundle\Entity\AbstractContaoSearchEntity;

class FrontendModuleSearchEntity extends AbstractContaoSearchEntity
{
    /**
     * @param ModuleModel $model
     */
    public function findParents(Model $model): void
    {
        // Find in content elements
        $contentelements = ContentModel::findBy(['tl_content.type=?', 'tl_content.module=?'], ['module', $model->id]);

        if ($contentelements) {
            foreach ($contentelements as $element) {
                $this->addParent('tl_content', $element->id);
            }
        }

        // find in layouts
        $result = Database::getInstance()->prepare("SELECT id FROM tl_layout WHERE modules LIKE '%:\"".(string) ((int) $model->id)."\"%'")->execute();

        foreach ($result->fetchEach('id') as $id) {
            $layout = LayoutModel::findById($id);

            if (!$layout) {
                continue;
            }
            $this->addParent(LayoutModel::getTable(), $layout->id);
        }

        // find in blocks
        if (class_exists("HeimrichHannot\Blocks\BlockModuleModel")) {
            $blockModules = BlockModuleModel::findByModule($id);

            if ($blockModules) {
                foreach ($blockModules as $blockModule) {
                    $this->addParent(BlockModuleModel::getTable(), $blockModule->id);
                }
            }
        }

        // find as inserttag in html frontend modules
        $result = Database::getInstance()
            ->prepare("SELECT id FROM tl_module
                            WHERE type='html'
                            AND (
                                html LIKE '%{{insert_module::".$model->id."}}%'
                                OR html LIKE '%{{insert_module::".$model->id."::%')")
            ->execute();

        foreach ($result->fetchEach('id') as $id) {
            $this->addParent(ModuleModel::getTable(), $id);
        }
    }

    protected function loadModel(int $id): ?Model
    {
        return ModuleModel::findByPk($id);
    }

    /**
     * @param ModuleModel $model
     */
    protected function findName(Model $model): string
    {
        return $model->name;
    }
}
