<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SearchEntityBundle\Entity;

interface ContaoSearchEntityInterface
{
    public function getName(): string;

    public function getId(): int;

    public function getType(): string;

    /**
     * @return ContaoSearchEntityInterface[]
     */
    public function getParents(): array;

    public function getChild(): self;

    public static function supported(): bool;

//    public function render(int $depth = 0, bool $pLastChild = false);
}
