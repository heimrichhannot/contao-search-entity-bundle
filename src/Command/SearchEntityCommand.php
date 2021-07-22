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
use HeimrichHannot\SearchEntityBundle\Entity\SearchEntityFactory;
use HeimrichHannot\SearchEntityBundle\Exception\EntityNotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SearchEntityCommand extends Command
{
    protected static $defaultName = 'huh:search-entity';

    protected static $tableAliases = [
        'contentelement' => 'tl_content',
        'c' => 'tl_content',
        'frontendmodule' => 'tl_module',
        'module' => 'tl_module',
        'm' => 'tl_module',
        'article' => 'tl_article',
        'a' => 'tl_article',
        'page' => 'tl_page',
        'p' => 'tl_page',
    ];

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
            ->addArgument('type', InputArgument::REQUIRED, 'The type you search for. Could be a name or a table.')
            ->addArgument('id', InputArgument::REQUIRED, 'The id or the element you search for.')
            ->setHelp(
                "This command located an contao entity an return it's location.\n\n"
                .'You can search for entities within the following tables: '
                .implode(', ', array_keys(SearchEntityFactory::mapping()))."\n\n"
                .'You can use following aliases for tables: '
                .'<info>'.str_replace('=', '</info> (', http_build_query(static::$tableAliases, null, '), <info>')).')</info>'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->framework->initialize();
        $io = new SymfonyStyle($input, $output);
        $this->io = $io;

        $io->title('Show element embedding information');

        $id = $input->getArgument('id');

        if (!is_numeric($id)) {
            throw new \Exception('Argument id must be an integer.');
        }

        $type = $input->getArgument('type');

        if (isset(static::$tableAliases[$type])) {
            $type = static::$tableAliases[$type];
        }

        if ('tl_' !== substr($type, 0, 3)) {
            throw new \Exception('Type must be a valid type or a valid contao table.');
        }

        try {
            $searchEntity = SearchEntityFactory::createSearchEntity($type, (int) $id);
        } catch (EntityNotFoundException $entityNotFoundException) {
            $io->error($entityNotFoundException->getMessage());

            if ($io->isVerbose()) {
                $io->text($entityNotFoundException->getTraceAsString());
            }

            return 1;
        }
        $io->write($searchEntity->render());

        $io->success('Finished getting element information.');

        return 0;
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
