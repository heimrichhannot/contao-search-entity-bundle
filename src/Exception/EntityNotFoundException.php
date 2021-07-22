<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SearchEntityBundle\Exception;

use Throwable;

class EntityNotFoundException extends \Exception
{
    public function __construct(string $type, int $id, $code = 0, Throwable $previous = null)
    {
        $message = 'Entity '.$id.' of type '.$type.' could not be found.';

        parent::__construct($message, $code, $previous);
    }
}
