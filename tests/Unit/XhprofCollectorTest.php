<?php

namespace Odandb\XhprofBundle\Tests\Unit;

use Odandb\XhprofBundle\DataCollector\XhprofCollector;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Xhgui\Profiler\Config;

class XhprofCollectorTest extends TestCase
{
    public function testCollectWithConfig(): void
    {
        $config = Config::create();
        $config->load(__DIR__ . '/../Fixtures/php_xhgui_config.php');


        $c = $this->createCollector($config);

        $request = new Request();
        $request->attributes->set('_xhprof_data', ['profile' => [
            'main()==>strlen' => [
                'ct' => 1,
                'wt' => 279,
            ],
        ]]);

        $c->collect($request, new Response());

        self::assertFalse($c->getIsEnabled());
        self::assertIsArray($c->getExtensionLoaded());
        self::assertSame('file', $c->getSaverMethod());
        self::assertIsArray($c->getUploadMethod());
        self::assertIsArray($c->getProfileData());
    }

    public function testCollectWithoutConfig(): void
    {
        $this->expectException(\RuntimeException::class);

        $c = $this->createCollector();
        $c->collect(new Request(), new Response());
    }

    private function createCollector(?Config $config = null): XhprofCollector
    {
        return new XhprofCollector($config);
    }
}
