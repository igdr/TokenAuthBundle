<?php

namespace Igdr\Bundle\TokenAuthBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Clear old tokens.
 */
class ClearCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('token:clear')
            ->setDescription('Clear expired authorization tokens');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('igdr_token_auth_service_token')->cleanupToken();

        $output->writeln('<info>Success</info>');
    }
}
