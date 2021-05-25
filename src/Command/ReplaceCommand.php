<?php

declare(strict_types=1);

namespace Unixslayer\ComposerReplace\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
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
        $inputProxy = $this->prepareInputProxy($input->getArgument('replace'), $input);
        $removeCommand = $this->getApplication()->find('remove');
        $ret = $removeCommand->execute($inputProxy, $output) ?? 0;

        if ($ret !== 0) {
            return $ret;
        }

        $inputProxy = $this->prepareInputProxy($input->getArgument('with'), $input);
        $requireCommand = $this->getApplication()->find('require');

        return $requireCommand->execute($inputProxy, $output) ?? 0;
    }

    private function prepareInputProxy(string $package, InputInterface $input): InputInterface
    {
        $parameters = ['packages' => $package];
        foreach (array_keys($input->getOptions()) as $key) {
            $parameters['--'.$key] = $input->getOption($key);
        }

        return new ArrayInput($parameters);
    }
}
