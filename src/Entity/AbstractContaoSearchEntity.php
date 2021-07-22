<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SearchEntityBundle\Entity;

use Contao\Model;
use Exception;
use HeimrichHannot\SearchEntityBundle\Exception\EntityNotFoundException;

abstract class AbstractContaoSearchEntity implements ContaoSearchEntityInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $id;

    /** @var array|ContaoSearchEntityInterface[] */
    protected $parents;

    /** @var callable */
    protected $output;

    /** @var ContaoSearchEntityInterface */
    protected $child;

    /** @var Model|null */
    protected $model;

    /**
     * AbstractContaoSearchEntity constructor.
     */
    public function __construct(int $id)
    {
        $this->id = $id;
        $this->model = $this->loadModel($id);

        if (!$this->model) {
            throw new EntityNotFoundException(static::getType(), $id);
        }
        $this->name = $this->findName($this->model);
    }

    public function getParents(): array
    {
        if (!$this->parents) {
            $this->findParents($this->model);

            if (!$this->parents) {
                $this->parents = [];
            }
        }

        return $this->parents;
    }

    abstract public function findParents(Model $model): void;

    public function getName(): string
    {
        return ucfirst($this->getType()).': '.$this->name.' [ID: '.$this->id.']';
    }

    public function getId(): int
    {
        return $this->getId();
    }

    public function getType(): string
    {
        $className = static::class;

        if ('SearchEntity' != substr($className, -12)) {
            throw new Exception('This class does not follow the naming convention; you must overwrite the getType() method.');
        }
        $classBaseName = substr(strrchr($className, '\\'), 1, -12);

        return strtolower($classBaseName);
    }

    public function setOutput(callable $output): void
    {
        $this->output = $output;
    }

    public function getChild(): ContaoSearchEntityInterface
    {
        return $this->child;
    }

    public function setChild(ContaoSearchEntityInterface $child): void
    {
        $this->child = $child;
    }

    public function render(int $depth = 0, bool $pLastChild = false): string
    {
        $lastChild = false;

        if ($depth > 0) {
            $siblings = $this->getChild()->getParents();

            if (\count($siblings) < 2) {
                $lastChild = true;
            } else {
                reset($siblings);

                while ($value = current($siblings)) {
                    if ($value === $this) {
                        if (false === next($siblings)) {
                            $lastChild = true;

                            break;
                        }
                    }
                    next($siblings);
                }
            }
        }

        $name = $this->getName();

        if ($depth > 0) {
            if ($lastChild) {
                $name = '└─ '.$name;
            } else {
                $name = '├─ '.$name;
            }
        }

        $parents = $this->getParents();

        if (empty($parents)) {
            return $name;
        }

        $parentResult = '';

        foreach ($parents as $parent) {
            $parentResult .= $parent->render(($depth + 1), $lastChild)."\n";
        }

        $parentResult = trim($parentResult);

        if ($depth > 0 && !empty($parentResult)) {
            $lines = explode("\n", $parentResult);

            foreach ($lines as $key => $line) {
                $lines[$key] = ($lastChild ? '   ' : '│  ').$line;
            }
            $parentResult = implode("\n", $lines);
        }

        return $name."\n".$parentResult;
    }

    abstract protected function loadModel(int $id): ?Model;

    abstract protected function findName(Model $model): string;

    protected function addParent(string $table, int $id)
    {
        $parent = SearchEntityFactory::createSearchEntity($table, $id);
        $parent->setChild($this);
        $this->parents[] = $parent;
    }
}
