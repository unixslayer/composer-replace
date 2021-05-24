<?php

declare(strict_types=1);

namespace Unixslayer\ComposerReplace;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\PluginInterface;
use Unixslayer\ComposerReplace\Command\ReplaceCommand;

class ComposerReplace implements PluginInterface, CommandProvider
{

    /**
     * @inheritDoc
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        // TODO: Implement activate() method.
    }

    /**
     * @inheritDoc
     */
    public function deactivate(Composer $composer, IOInterface $io)
    {
        // TODO: Implement deactivate() method.
    }

    /**
     * @inheritDoc
     */
    public function uninstall(Composer $composer, IOInterface $io)
    {
        // TODO: Implement uninstall() method.
    }

    public function getCommands(): array
    {
        return [
            new ReplaceCommand(),
        ];
    }
}
