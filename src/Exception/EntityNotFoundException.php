<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SearchEntityBundle\Exception;

use HeimrichHannot\SearchEntityBundle\Entity\ContaoSearchEntityInterface;
use Throwable;

class EntityNotFoundException extends \Exception
{
    public function __construct(ContaoSearchEntityInterface $entity, $code = 0, Throwable $previous = null)
    {
        $message = 'Entity '.$entity->getId().'of type '.$entity->getType().' could not be found.';

        parent::__construct($message, $code, $previous);
    }
}
