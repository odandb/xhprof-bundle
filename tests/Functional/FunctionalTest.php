<?php

declare(strict_types=1);


namespace Odandb\XhprofBundle\Tests\Functional;


use Odandb\XhprofBundle\EventSubscriber\KernelEventSubscriber;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This is a functional test to check if the service wiring is correct.
 * It is based on https://symfonycasts.com/screencast/symfony-bundle.
 */
class FunctionalTest extends WebTestCase
{
    protected ?KernelBrowser $client;

    protected function setUp() : void
    {
        $this->client = self::createClient();
    }

    public function testServiceWiring()
    {
        $service = self::getContainer()->get(KernelEventSubscriber::class);
        self::assertInstanceOf(KernelEventSubscriber::class, $service);
    }

    public function testProfile()
    {
        $this->client->request('GET', '/profile');

        self::assertResponseIsSuccessful();
    }
}
