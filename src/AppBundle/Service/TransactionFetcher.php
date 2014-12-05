<?php

namespace AppBundle\Service;

use AdrecordApiWrapper\Adrecord;
use TradedoublerReportWrapper\Tradedoubler;

class TransactionFetcher
{
    /**
     * @var \AdrecordApiWrapper\Adrecord
     */
    protected $adrecord;

    /**
     * @var \TradedoublerReportWrapper\Tradedoubler
     */
    protected $tradedoubler;

    /**
     * @param Adrecord $adrecord
     * @param Tradedoubler $tradedoubler
     */
    public function __construct(Adrecord $adrecord, Tradedoubler $tradedoubler)
    {
        $this->adrecord = $adrecord;
        $this->tradedoubler = $tradedoubler;
    }

    /**
     * @return \AffiliateInterface\TransactionInterface[]
     */
    public function getTransactions()
    {
        $transactions = array_merge(
            $this->getAdrecordAtransactions(),
            $this->getTradedoublerTransactions()
        );

        usort($transactions, function($a, $b) {
            return $a->getCreatedAt() > $b->getCreatedAt();
        });

        return $transactions;
    }

    /**
     * @return \AdrecordApiWrapper\Transaction[]
     */
    protected function getAdrecordAtransactions()
    {
        $adrecordTransactions = $this->adrecord->getTransactions(
            null,
            null,
            new \DateTime('-1 day'),
            new \DateTime('-1 day')
        );

        return $this->fillWithAdrecordChannelData($adrecordTransactions);
    }

    /**
     * @return \TradedoublerReportWrapper\Transaction[]
     */
    protected function getTradedoublerTransactions()
    {
        return $this->tradedoubler->getTransactions(
            new \DateTime('-1 day'),
            new \DateTime('-1 day')
        );
    }

    /**
     * @param \AdrecordApiWrapper\Transaction[] $transactions
     *
     * @return \AdrecordApiWrapper\Transaction[]
     */
    protected function fillWithAdrecordChannelData($transactions)
    {
        $channels = [];
        foreach ($this->adrecord->getChannels() as $channel) {
            $channels[$channel->getOriginalId()] = $channel;
        }

        foreach ($transactions as $transaction) {
            $transaction->getChannel()->setName($channels[$transaction->getChannel()->getOriginalId()]->getName());
        }

        return $transactions;
    }
}
