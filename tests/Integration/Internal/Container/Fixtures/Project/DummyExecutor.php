<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Container\Fixtures\Project;

class DummyExecutor
{
    public function execute()
    {
        return 'Service overwriting for Project!';
    }
}
