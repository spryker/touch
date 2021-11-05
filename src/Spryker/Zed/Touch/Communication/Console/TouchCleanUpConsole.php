<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Touch\Communication\Console;

use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @deprecated Use TouchCleanDataConsole instead.
 *
 * @method \Spryker\Zed\Touch\Business\TouchFacadeInterface getFacade()
 * @method \Spryker\Zed\Touch\Persistence\TouchQueryContainerInterface getQueryContainer()
 */
class TouchCleanUpConsole extends Console
{
    /**
     * @var string
     */
    public const COMMAND_NAME = 'touch:cleanup';

    /**
     * @var string
     */
    public const COMMAND_DESCRIPTION = 'Cleans up the Touch tables by removing obsolete touch data';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(static::COMMAND_NAME);
        $this->setDescription(static::COMMAND_DESCRIPTION);

        parent::configure();
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $deleteCount = $this->getFacade()->removeTouchEntriesMarkedAsDeleted();

        $output->writeln('<fg=yellow>----------------------------------------</fg=yellow>');
        $output->writeln('<fg=yellow>Cleaning up the touch table(s)<fg=yellow>');
        $output->writeln('');
        $output->writeln("<fg=white>Removed $deleteCount Touch table entries (along with related touch data)</fg=white>");
        $output->writeln('');
        $output->writeln('<fg=green>Finished. All Done.</fg=green>');
        $output->writeln('');

        return static::CODE_SUCCESS;
    }
}
