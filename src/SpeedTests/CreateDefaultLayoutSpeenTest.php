<?php

namespace Bfg\Layout\SpeedTest;

use Bfg\Dev\Interfaces\SpeedTestInterface;
use Bfg\Layout\Core\MainLayout;

/**
 * Class ComponentNameGeneratorSpeenTest
 * @package Bfg\Layout\SpeedTest
 */
class CreateDefaultLayoutSpeenTest implements SpeedTestInterface
{
    /**
     * @inheritDoc
     */
    public function handle(): void
    {
        (new class extends MainLayout{})->render();
    }
}