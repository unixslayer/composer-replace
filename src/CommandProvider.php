<?php

declare(strict_types=1);

namespace Unixslayer\ComposerReplace;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Unixslayer\ComposerReplace\Command\ReplaceCommand;

class CommandProvider implements CommandProviderCapability
{
    public function getCommands(): array
    {
        return [
            new ReplaceCommand(),
        ];
    }
}
