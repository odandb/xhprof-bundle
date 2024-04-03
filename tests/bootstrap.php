<?php

declare(strict_types=1);

use Symfony\Component\ErrorHandler\ErrorHandler;

require __DIR__ . '/../vendor/autoload.php';

set_exception_handler([new ErrorHandler(), 'handleException']);

// Clean up from previous runs
@exec('rm -rf ' . escapeshellarg(__DIR__ . '/Fixtures/var'));
@exec('mkdir -p ' . escapeshellarg(__DIR__ . '/Fixtures/var/xhgui'));
