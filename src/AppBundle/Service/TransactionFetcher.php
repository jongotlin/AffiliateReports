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
        $adrecordTransactions = $this->adrecord->getTransactions(
            null,
            null,
            new \DateTime('-1 day'),
            new \DateTime('-1 day')
        );

        $tradedoublerTransactions = $this->tradedoubler->getTransactions(
            new \DateTime('-1 day'),
            new \DateTime('-1 day')
        );

        $transactions = array_merge($adrecordTransactions, $tradedoublerTransactions);

        usort($transactions, function($a, $b) {
            return $a->getCreatedAt() > $b->getCreatedAt();
        });

        return $transactions;
    }
}
