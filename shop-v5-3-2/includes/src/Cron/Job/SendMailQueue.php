<?php declare(strict_types=1);

namespace JTL\Cron\Job;

use JTL\Cron\Job;
use JTL\Cron\JobInterface;
use JTL\Cron\QueueEntry;
use JTL\Exceptions\CircularReferenceException;
use JTL\Exceptions\ServiceNotFoundException;
use JTL\Mail\Hydrator\DefaultsHydrator;
use JTL\Mail\Mailer;
use JTL\Mail\Renderer\SmartyRenderer;
use JTL\Mail\Validator\MailValidator;
use JTL\Shopsetting;
use JTL\Smarty\MailSmarty;
use SmartyException;

/**
 * Class SendMailQueue
 * @package JTL\Cron\Job
 */
final class SendMailQueue extends Job
{
    /**
     * @inheritdoc
     */
    public function hydrate($data): SendMailQueue
    {
        parent::hydrate($data);

        return $this;
    }

    /**
     * @inheritdoc
     * @param QueueEntry $queueEntry
     * @return JobInterface
     * @throws SmartyException
     * @throws CircularReferenceException
     * @throws ServiceNotFoundException
     */
    public function start(QueueEntry $queueEntry): JobInterface
    {
        $maxExecutionTime = \ini_get('max_execution_time');
        $maxJobLength     = $maxExecutionTime === false
            ? 300
            : ((int)\ceil((int)$maxExecutionTime / 2));

        parent::start($queueEntry);

        $settings  = Shopsetting::getInstance();
        $smarty    = new SmartyRenderer(new MailSmarty($this->db));
        $hydrator  = new DefaultsHydrator($smarty->getSmarty(), $this->db, $settings);
        $validator = new MailValidator($this->db, $settings->getAll());
        $mailer    = new Mailer(
            $hydrator,
            $smarty,
            $settings,
            $validator
        );
        $mailsSent = true;
        while ($mailsSent === true) {
            $mailsSent = $mailer->sendQueuedMails();
            if ($maxJobLength > 0 && \time() > $queueEntry->timestampCronHasStartedAt + $maxJobLength) {
                break;
            }
        }

        return $this;
    }
}
