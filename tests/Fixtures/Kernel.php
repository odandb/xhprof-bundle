<?php

namespace Odandb\XhprofBundle\Tests\Fixtures;

use Odandb\XhprofBundle\OdandbXhprofBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new OdandbXhprofBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config.yaml');
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }
}
