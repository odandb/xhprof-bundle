<?php

declare(strict_types=1);

namespace Odandb\XhprofBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class OdandbXhprofBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
