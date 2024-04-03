<?php

declare(strict_types=1);

namespace Odandb\XhprofBundle\Service;

use Xhgui\Profiler\Config;

class ConfigFactory
{
    private string $xhguiProfilerConfigFile;

    public function __construct(string $xhguiProfilerConfigFile)
    {
        $this->xhguiProfilerConfigFile = $xhguiProfilerConfigFile;
    }

    public function create(): ?Config
    {
        if (file_exists($this->xhguiProfilerConfigFile)) {
            $config = Config::create();
            $config->load($this->xhguiProfilerConfigFile);

            return $config;
        }

        return null;
    }
}
