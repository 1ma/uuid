<?php

declare(strict_types=1);

use UMA\Uuid\CombGenerator;
use UMA\Uuid\Tests\Performance\Benchmarker;
use UMA\Uuid\Uuid;
use UMA\Uuid\Version1Generator;
use UMA\Uuid\Version4Generator;
use UMA\Uuid\Version5Generator;

require_once __DIR__ . '/../vendor/autoload.php';

echo sprintf("Running on: %s\n\n", PHP_VERSION);

if (extension_loaded('xdebug')) {
    echo "[!] The xdebug extension is enabled. This has a major impact on runtime performance.\n\n";
}

$runTime = (int)($argv[1] ?? 5);

$ns = Uuid::fromString(Version5Generator::NS_DNS);

(new Benchmarker(new CombGenerator))->runFor($runTime);
(new Benchmarker(new Version1Generator('01:23:45:67:89:ab')))->runFor($runTime);
(new Benchmarker(new Version4Generator))->runFor($runTime);
(new Benchmarker(new Version5Generator($ns)))->runFor($runTime);
