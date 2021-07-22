<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SearchEntityBundle\Entity;

use HeimrichHannot\SearchEntityBundle\Entity\Concrete\ArticleSearchEntity;
use HeimrichHannot\SearchEntityBundle\Entity\Concrete\BlockModuleSearchEntity;
use HeimrichHannot\SearchEntityBundle\Entity\Concrete\BlockSearchEntity;
use HeimrichHannot\SearchEntityBundle\Entity\Concrete\ContentElementSearchEntity;
use HeimrichHannot\SearchEntityBundle\Entity\Concrete\FrontendModuleSearchEntity;
use HeimrichHannot\SearchEntityBundle\Entity\Concrete\LayoutSearchEntity;
use HeimrichHannot\SearchEntityBundle\Entity\Concrete\PageSearchEntity;
use HeimrichHannot\SearchEntityBundle\Entity\Concrete\ThemeSearchEntity;

class SearchEntityFactory
{
    public static function mapping(): array
    {
        return [
            'tl_article' => ArticleSearchEntity::class,
            'tl_block' => BlockSearchEntity::class,
            'tl_block_module' => BlockModuleSearchEntity::class,
            'tl_content' => ContentElementSearchEntity::class,
            'tl_layout' => LayoutSearchEntity::class,
            'tl_module' => FrontendModuleSearchEntity::class,
            'tl_page' => PageSearchEntity::class,
            'tl_theme' => ThemeSearchEntity::class,
        ];
    }

    public static function createSearchEntity(string $table, int $id): ContaoSearchEntityInterface
    {
        $mapping = static::mapping();

        if (isset($mapping[$table])) {
            if (!$mapping[$table]::supported()) {
                $searchEntity = new MissingDependencySearchEntity($id, $table);
            } else {
                $searchEntity = new $mapping[$table]($id);
            }
        } else {
            $searchEntity = new NotSupportedSearchEntity($id, $table);
        }

        return $searchEntity;
    }
}
