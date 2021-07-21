<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SearchEntityBundle\Command;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Database;
use Contao\LayoutModel;
use Contao\Model\Collection;
use Contao\PageModel;
use HeimrichHannot\Blocks\BlockModel;
use HeimrichHannot\Blocks\BlockModuleModel;
use HeimrichHannot\SearchEntityBundle\Entity\Concrete\ArticleSearchEntity;
use HeimrichHannot\SearchEntityBundle\Entity\Concrete\ContentElementSearchEntity;
use HeimrichHannot\SearchEntityBundle\Entity\Concrete\FrontendModuleSearchEntity;
use HeimrichHannot\SearchEntityBundle\Entity\Concrete\PageSearchEntity;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SearchEntityCommand extends Command
{
    protected static $defaultName = 'huh:search-entity';

    /**
     * @var array
     */
    protected $tree = [];
    /**
     * @var SymfonyStyle
     */
    private $io;
    /**
     * @var ContaoFramework
     */
    private $framework;

    public function __construct(ContaoFramework $framework)
    {
        parent::__construct();
        $this->framework = $framework;
    }

    protected function configure()
    {
        $this
            ->addOption('contentelement', 'c', InputOption::VALUE_OPTIONAL, 'The id of an content element', false)
            ->addOption('frontendmodule', 'm', InputOption::VALUE_OPTIONAL, 'The id of an frontend module', false)
            ->addOption('article', 'a', InputOption::VALUE_OPTIONAL, 'The id of an article', false)
            ->addOption('page', 'p', InputOption::VALUE_OPTIONAL, 'The id of an page', false)
//            ->addOption('layout', 'l', InputOption::VALUE_OPTIONAL, 'The id of an layout', false)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->framework->initialize();
        $io = new SymfonyStyle($input, $output);
        $this->io = $io;

        $io->title('Show element embedding information');

        if ($input->hasOption('contentelement') && false !== $input->getOption('contentelement')) {
            $searchEntity = new ContentElementSearchEntity((int) $input->getOption('contentelement'));
        } elseif ($input->hasOption('frontendmodule') && false !== $input->getOption('frontendmodule')) {
            $searchEntity = new FrontendModuleSearchEntity((int) $input->getOption('frontendmodule'));
        } elseif ($input->hasOption('article') && false !== $input->getOption('article')) {
            $searchEntity = new ArticleSearchEntity((int) $input->getOption('article'));
        } elseif ($input->hasOption('page') && false !== $input->getOption('page')) {
            $searchEntity = new PageSearchEntity((int) $input->getOption('page'));
        }

        $io->write($searchEntity->render());

        $io->success('Finished getting element information.');

        return 0;

        if ($input->hasOption('layout') && false !== $input->getOption('layout')) {
            $this->pagesByLayout($input->getOption('layout'));
        }
    }

    protected function createText(string $text, int $depth)
    {
        $this->tree[] = [$depth, $text];
    }

    protected function createWarning(string $text, int $depth)
    {
        $this->tree[] = [$depth, '<fg=red>'.$text.'</>'];
    }

    /**
     * @param int|string $id Layout id
     */
    protected function pagesByLayout($id, int $depth = 0)
    {
        $layout = LayoutModel::findById($id);

        if (!$layout) {
            $this->io->warning('Found no layout for given id!');

            return;
        }
        $pages = PageModel::findByLayout($layout->id);
        $childDepth = $depth++;

        if ($pages) {
            foreach ($pages as $page) {
                $this->findChildPages($page->id, $childDepth);
            }
        }
    }

    protected function findChildPages($id, int $depth = 0)
    {
        $page = PageModel::findByIdOrAlias($id);

        if (!$page) {
            $this->createWarning('Found no page for given id or alias: '.$id, $depth);

            return;
        }
        $this->createPageText($page, $depth);
        $childPages = PageModel::findByPid($page->id);

        if ($childPages) {
            foreach ($childPages as $childPage) {
                $this->findChildPages($childPage->id, ++$depth);
            }
        }
    }

    /**
     * @param int|string $id   Module id
     * @param array      $data
     *
     * @return array
     */
    protected function layoutsByModule($id, int $depth = 0): void
    {
        if (!is_numeric($id)) {
            return;
        }
        $result = Database::getInstance()->prepare("SELECT id FROM tl_layout WHERE modules LIKE '%:\"".(string) ((int) $id)."\"%'")->execute();

        foreach ($result->fetchEach('id') as $id) {
            $layout = LayoutModel::findById($id);

            if (!$layout) {
                continue;
            }
            $this->createText('Layout: '.$layout->name.' (ID: '.$layout->id.')', $depth);
        }
    }

    protected function blockElementsByModule($id, int $depth = 0): void
    {
        /** @var BlockModuleModel[]|Collection|null $blockModules */
        $blockModules = BlockModuleModel::findByModule($id);

        if (!$blockModules) {
            return;
        }

        foreach ($blockModules as $blockModule) {
            $this->createText('Block module: '.$blockModule->id, $depth);
            $block = BlockModel::findByPk($blockModule->id);

            if (!$block) {
                $this->createWarning('Found no parent block for block module '.$blockModule->id, $depth);

                continue;
            }
            ++$depth;
            $this->createText('Block: '.$block->title.' (ID: '.$block->id.')', $depth);
            $this->frontendmodule($block->module, ++$depth);
        }
    }

    protected function createPageText(PageModel $page, int $depth)
    {
        $this->createText('<options=bold>Page: '.$page->title.'</> (ID: '.$page->id.', Type: '.$page->type.', DNS: '.$page->getFrontendUrl().' )', $depth);
    }
}
