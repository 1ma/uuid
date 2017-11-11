<?php

use UMA\Uuid\Uuid;
use UMA\Uuid\CombGenerator;
use UMA\Uuid\Version1Generator;
use UMA\Uuid\Version4Generator;
use UMA\Uuid\Version5Generator;

/**
 * @Revs(100000)
 * @Iterations(10)
 */
class GeneratorBench
{
    /**
     * @var CombGenerator
     */
    private $comb;

    /**
     * @var Version1Generator
     */
    private $v1;

    /**
     * @var Version4Generator
     */
    private $v4;

    /**
     * @var Version5Generator
     */
    private $v5;

    public function __construct()
    {
        $this->comb = new CombGenerator;
        $this->v1 = new Version1Generator('01:23:45:67:89:ab');
        $this->v4 = new Version4Generator;
        $this->v5 = new Version5Generator(Uuid::nil());
    }

    public function benchCombGenerator()
    {
        $this->comb->generate();
    }

    public function benchVersion1Generator()
    {
        $this->v1->generate();
    }

    public function benchVersion4Generator()
    {
        $this->v4->generate();
    }

    public function benchVersion5Generator()
    {
        $this->v5->generate('foo');
    }
}
