<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Touch\Communication\Console;

use Spryker\Zed\Console\Business\Model\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \Spryker\Zed\Touch\Business\TouchFacade getFacade()
 */
class TouchCleanUpConsole extends Console
{

    const COMMAND_NAME = 'touch:cleanup';
    const COMMAND_DESCRIPTION = 'Cleans up the Touch tables by removing outdated / unneeded data';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::COMMAND_DESCRIPTION);

        parent::configure();
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $deleteCount = $this->getFacade()->removeTouchEntriesMarkedAsDeleted();

        $output->writeln('');
        $output->writeln('<fg=yellow>----------------------------------------</fg=yellow>');
        $output->writeln('Cleaning up the touch table(s)');
        $output->writeln('<fg=yellow>----------------------------------------</fg=yellow>');
        $output->writeln('');
        $output->writeln("<fg=white>Removed $deleteCount rows</fg=white>");
        $output->writeln('');
        $output->writeln('<fg=yellow>----------------------------------------</fg=yellow>');
        $output->writeln('Finished. All Done.');
        $output->writeln('<fg=yellow>----------------------------------------</fg=yellow>');
        $output->writeln('');
    }

}