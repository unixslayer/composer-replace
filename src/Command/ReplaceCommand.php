<?php

declare(strict_types=1);

namespace Unixslayer\ComposerReplace\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ReplaceCommand extends BaseCommand
{
    private $removeCommandOptionsProxy
        = [
            'dev',
            'dry-run',
            'no-update',
            'no-install',
            'no-scripts',
            'update-no-dev',
            'update-with-dependencies',
            'update-with-all-dependencies',
            'no-update-with-dependencies',
            'unused',
            'ignore-platform-req',
            'ignore-platform-reqs',
            'optimize-autoloader',
            'classmap-authoritative',
        ];

    private $requireCommandOptionsProxy
        = [
            'dev',
            'dry-run',
            'prefer-source',
            'prefer-dist',
            'fixed',
            'no-update',
            'no-install',
            'no-scripts',
            'update-no-dev',
            'update-with-dependencies',
            'update-with-all-dependencies',
            'ignore-platform-req',
            'ignore-platform-reqs',
            'prefer-stable',
            'prefer-lowest',
            'optimize-autoloader',
            'classmap-authoritative',
        ];

    protected function configure(): void
    {
        $this->setName('replace')
            ->setDescription('Shortcut for replacing old packages with new ones')
            ->setDefinition([
                new InputArgument('replace', InputArgument::REQUIRED, 'Package name that will be replaced'),
                new InputArgument('with', InputArgument::REQUIRED, 'Package name that will replace the old one'),
                new InputOption('dev', null, InputOption::VALUE_NONE, 'Add requirement to require-dev.'),
                new InputOption('dry-run', null, InputOption::VALUE_NONE, 'Outputs the operations but will not execute anything (implicitly enables --verbose).'),
                new InputOption('prefer-source', null, InputOption::VALUE_NONE, 'Forces installation from package sources when possible, including VCS information.'),
                new InputOption('prefer-dist', null, InputOption::VALUE_NONE, 'Forces installation from package dist (default behavior).'),
                new InputOption('prefer-install', null, InputOption::VALUE_REQUIRED, 'Forces installation from package dist|source|auto (auto chooses source for dev versions, dist for the rest).'),
                new InputOption('fixed', null, InputOption::VALUE_NONE, 'Write fixed version to the composer.json.'),
                new InputOption('no-update', null, InputOption::VALUE_NONE, 'Disables the automatic update of the dependencies (implies --no-install).'),
                new InputOption('no-install', null, InputOption::VALUE_NONE, 'Skip the install step after updating the composer.lock file.'),
                new InputOption('no-scripts', null, InputOption::VALUE_NONE, 'Skips the execution of all scripts defined in composer.json file.'),
                new InputOption('update-no-dev', null, InputOption::VALUE_NONE, 'Run the dependency update with the --no-dev option.'),
                new InputOption('update-with-dependencies', 'w', InputOption::VALUE_NONE, 'Allows inherited dependencies to be updated, except those that are root requirements.'),
                new InputOption('update-with-all-dependencies', 'W', InputOption::VALUE_NONE, 'Allows all inherited dependencies to be updated, including those that are root requirements.'),
                new InputOption('with-dependencies', null, InputOption::VALUE_NONE, 'Alias for --update-with-dependencies'),
                new InputOption('with-all-dependencies', null, InputOption::VALUE_NONE, 'Alias for --update-with-all-dependencies'),
                new InputOption('no-update-with-dependencies', null, InputOption::VALUE_NONE, 'Does not allow inherited dependencies to be updated with explicit dependencies.'),
                new InputOption('unused', null, InputOption::VALUE_NONE, 'Remove all packages which are locked but not required by any other package.'),
                new InputOption('ignore-platform-req', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Ignore a specific platform requirement (php & ext- packages).'),
                new InputOption('ignore-platform-reqs', null, InputOption::VALUE_NONE, 'Ignore all platform requirements (php & ext- packages).'),
                new InputOption('prefer-stable', null, InputOption::VALUE_NONE, 'Prefer stable versions of dependencies.'),
                new InputOption('prefer-lowest', null, InputOption::VALUE_NONE, 'Prefer lowest versions of dependencies.'),
                new InputOption('optimize-autoloader', 'o', InputOption::VALUE_NONE, 'Optimize autoloader during autoloader dump'),
                new InputOption('classmap-authoritative', 'a', InputOption::VALUE_NONE, 'Autoload classes from the classmap only. Implicitly enables `--optimize-autoloader`.'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputProxy = $this->prepareInputProxy($input->getArgument('replace'), $this->removeCommandOptionsProxy, $input);
        $removeCommand = $this->getApplication()->find('remove');
        $ret = $removeCommand->run($inputProxy, $output) ?? 0;

        if ($ret !== 0) {
            return $ret;
        }

        $requireCommand = $this->getApplication()->find('require');
        $inputProxy = $this->prepareInputProxy($input->getArgument('with'), $this->requireCommandOptionsProxy, $input);

        return $requireCommand->run($inputProxy, $output) ?? 0;
    }

    private function prepareInputProxy(string $package, array $optionsProxy, InputInterface $input): InputInterface
    {
        $parameters = ['packages' => [$package]];
        foreach ($optionsProxy as $key) {
            if ($input->hasOption($key)) {
                $parameters['--'.$key] = $input->getOption($key);
            }
        }

        return new ArrayInput($parameters);
    }
}
