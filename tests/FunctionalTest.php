<?php

declare(strict_types=1);


namespace Odandb\XhprofBundle\Tests;


use Odandb\XhprofBundle\EventSubscriber\KernelEventSubscriber;
use Odandb\XhprofBundle\OdandbXhprofBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * This is a functional test to check if the service wiring is correct.
 * It is based on https://symfonycasts.com/screencast/symfony-bundle.
 */
class FunctionalTest extends TestCase
{
    public function testServiceWiring()
    {
        $kernel = new OdandbXhprofTestingKernel('test', true);
        $kernel->boot();
        $container = $kernel->getContainer();
        $service = $container->get(KernelEventSubscriber::class);
        self::assertInstanceOf(KernelEventSubscriber::class, $service);
    }
}

class OdandbXhprofTestingKernel extends Kernel
{

    public function registerBundles(): iterable
    {
        return [
            new OdandbXhprofBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
    }
}
