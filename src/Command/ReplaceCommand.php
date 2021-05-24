<?php

declare(strict_types=1);

namespace Unixslayer\ComposerReplace\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReplaceCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this->setName('replace')
            ->setDescription('Shortcut for replacing old packages with new ones')
            ->setDefinition([
                new InputArgument('replace', InputArgument::REQUIRED, 'Package name that will be replaced'),
                new InputArgument('with', InputArgument::REQUIRED, 'Package name that will replace the old one'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $removeCommand = $this->getApplication()->find('remove');

        $input->setArgument('packages', $input->getArgument('replace'));
        $ret = $removeCommand->execute($input, $output) ?? 0;

        if ($ret !== 0) {
            return $ret;
        }

        $requireCommand = $this->getApplication()->find('require');
        $input->setArgument('packages', $input->getArgument('with'));

        return $requireCommand->execute($input, $output) ?? 0;
    }
}
