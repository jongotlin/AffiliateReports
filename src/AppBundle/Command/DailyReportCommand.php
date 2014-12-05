<?php

namespace AppBundle\Command;

use AppBundle\Service\EmailSender;
use AppBundle\Service\TransactionFetcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DailyReportCommand extends Command
{
    /**
     * @var \AppBundle\Service\TransactionFetcher
     */
    protected $transactionFetcher;

    /**
     * @var \AppBundle\Service\EmailSender
     */
    protected $emailSender;

    /**
     * @param TransactionFetcher $transactionFetcher
     * @param EmailSender $emailSender
     */
    public function __construct(TransactionFetcher $transactionFetcher, EmailSender $emailSender)
    {
        $this->transactionFetcher = $transactionFetcher;
        $this->emailSender = $emailSender;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('affiliatereports:daily')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $transactions = $this->transactionFetcher->getTransactions();

        $total = 0;
        $perChannel = [];
        foreach ($transactions as $transaction) {
            $total += $transaction->getCommission();
            if (!array_key_exists($transaction->getChannel()->getName(), $perChannel)) {
                $perChannel[$transaction->getChannel()->getName()] = 0;
            }
            $perChannel[$transaction->getChannel()->getName()] += $transaction->getCommission();
        }

        $this->emailSender->sendEmail(
            $transactions,
            $total,
            $perChannel
        );
    }
}
