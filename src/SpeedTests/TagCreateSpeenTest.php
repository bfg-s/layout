<?php

namespace Bfg\Layout\SpeedTest;

use Bfg\Dev\Interfaces\SpeedTestInterface;

/**
 * Class ComponentNameGeneratorSpeenTest
 * @package Bfg\Layout\SpeedTest
 */
class TagCreateSpeenTest implements SpeedTestInterface
{
    /**
     * @inheritDoc
     */
    public function handle(): void
    {
        tag('a', ['href' => '#'])->render();
    }
}