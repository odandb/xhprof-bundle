<?php

declare(strict_types=1);


namespace Odandb\XhprofBundle\DataCollector;


use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Xhgui\Profiler\Config;

class XhprofCollector extends AbstractDataCollector
{
    private ?Config $config;

    public function __construct(?Config $config = null)
    {
        $this->config = $config;
    }

    public function getName(): string
    {
        return 'odb.xhprof';
    }

    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
        if (null === $this->config) {
            throw new \RuntimeException('Config file is not defined: run "cp vendor/odandb/xhprof-bundle/php_xhgui_config.php.dist php_xhgui_config.php" to create a config file.');
        }

        $configArr = $this->config->toArray();
        $data = $request->attributes->all('_xhprof_data')['profile'] ?? [];

        uasort($data, function(array $a, array $b) {
            return $b['wt'] - $a['wt'];
        });

        $this->data = [
            'isEnabled' => $configArr['profiler.enable'](),
            'saver' => $configArr['save.handler'],
            'stats' => $data,
            'upload' => $configArr['save.handler.upload'] ?? [],
            'extensionLoaded' => [
                'xhprof' => extension_loaded('xhprof'),
                'tideways_xhprof' => extension_loaded('tideways_xhprof'),
                'tideways' => extension_loaded('tideways'),
                'uprofiler' => extension_loaded('uprofiler'),
            ],
        ];
    }

    public function getIsEnabled(): bool
    {
        return $this->data['isEnabled'];
    }

    public function getExtensionLoaded(): array
    {
        return $this->data['extensionLoaded'];
    }

    public function getSaverMethod(): string
    {
        return $this->data['saver'];
    }

    public function getUploadMethod(): array
    {
        return $this->data['upload'];
    }

    public function getProfileData(): array
    {
        $stats = $this->data['stats'];

        foreach ($stats as $execution => &$data) {
            $tmp = explode('==>', $execution);

            $data['parent'] = $tmp[0];
            $data['child']  = $tmp[1] ?? '';

            $data['parent_trimmed'] = $this->trimString($data['parent']);
            $data['child_trimmed']  = $this->trimString($data['child']);
        }

        if (isset($_GET['sortBy'])) {
            uasort($stats, function(array $a, array $b) {
                if ($_GET['sortOrder'] === 'asc') {
                    return $a[$_GET['sortBy']] - $b[$_GET['sortBy']];
                } else {
                    return $b[$_GET['sortBy']] - $a[$_GET['sortBy']];
                }
            });
        }

        return $stats;
    }

    public function trimString(string $value): string
    {
        if (strlen($value) <= 50) {
            return $value;
        }

        return '...' . substr($value, strlen($value) - 50, strlen($value));
    }
}
