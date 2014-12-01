<?php

namespace AppBundle\Service;

use Symfony\Component\Templating\EngineInterface;

class EmailSender
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var \Symfony\Component\Templating\EngineInterface
     */
    protected $templating;

    /**
     * @var string
     */
    protected $from;

    /**
     * @var array
     */
    protected $recipients;

    /**
     * @param \Swift_Mailer $mailer
     * @param EngineInterface $templating
     * @param string $from
     * @param array $recipients
     */
    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating, $from, array $recipients)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->from = $from;
        $this->recipients = $recipients;
    }

    /**
     * @param \AffiliateInterface\TransactionInterface[] $transactions
     */
    public function sendEmail(array $transactions)
    {
        $total = 0;
        foreach ($transactions as $transaction) {
            $total += $transaction->getCommission();
        }

        $message = \Swift_Message::newInstance()
            ->setSubject('Gårdagens försäljningar')
            ->setFrom($this->from)
            ->setTo($this->recipients)
            ->setBody(
                $this->templating->render(
                    '::email.html.twig',
                    [
                        'transactions' => $transactions,
                        'total' => $total,
                    ]
                ),
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }
}
