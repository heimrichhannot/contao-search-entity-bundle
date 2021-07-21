<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SearchEntityBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class HeimrichHannotSearchEntityBundle extends Bundle
{
    public function getPath()
    {
        return \dirname(__DIR__);
    }
}
