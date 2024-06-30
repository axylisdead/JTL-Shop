<?php declare(strict_types=1);

namespace JTL\Mail\SendMailObjects;

use JTL\DataObjects\AbstractDataObject;
use JTL\DataObjects\DataTableObjectInterface;
use JTL\Language\LanguageModel;

/**
 * Class MailDataTableObject
 * @package JTL\Mail\SendMailObjects
 */
class MailDataTableObject extends AbstractDataObject implements DataTableObjectInterface
{
    private string $primaryKey   = 'id';
    protected int $id            = 0;
    protected int $reSend        = 0;
    protected int $isSendingNow  = 0;
    protected int $sendCount     = 0;
    protected int $errorCount    = 0;
    protected string $lastError  = '';
    protected string $dateQueued = 'now()';
    protected string $dateSent   = '';
    protected string $fromMail;
    protected string $fromName = '';
    protected string $toMail;
    protected ?string $toName        = null;
    protected ?string $replyToMail   = null;
    protected ?string $replyToName   = null;
    protected string $copyRecipients = '';
    protected string $subject;
    protected string $bodyHTML;
    protected string $bodyText;
    protected int $hasAttachments    = 0;
    private array $attachments       = [];
    protected int $languageId        = 0;
    protected string $templateId     = '';
    private ?LanguageModel $language = null;
    protected int $customerGroupID   = 1;
    protected int $priority          = 100;

    private array $mapping = [];

    private array $columnMapping = [
        'primarykey'        => 'primarykey',
        'id'                => 'id',
        'reSend'            => 'reSend',
        'isCancelled'       => 'isCancelled',
        'isBlocked'         => 'isBlocked',
        'isSendingNow'      => 'isSendingNow',
        'sendCount'         => 'sendCount',
        'errorCount'        => 'errorCount',
        'lastError'         => 'lastError',
        'dateQueued'        => 'dateQueued',
        'dateSent'          => 'dateSent',
        'isHtml'            => 'isHtml',
        'fromMail'          => 'fromMail',
        'fromName'          => 'fromName',
        'toMail'            => 'toMail',
        'toName'            => 'toName',
        'replyToMail'       => 'replyToMail',
        'replyToName'       => 'replyToName',
        'copyRecipients'    => 'copyRecipients',
        'subject'           => 'subject',
        'bodyHTML'          => 'bodyHTML',
        'bodyText'          => 'bodyText',
        'hasAttachments'    => 'hasAttachments',
        'attachments'       => 'attachments',
        'pdfAttachments'    => 'attachments',
        'isEmbedImages'     => 'isEmbedImages',
        'customHeaders'     => 'customHeaders',
        'typeReference'     => 'typeReference',
        'deliveryOngoing'   => 'deliveryOngoing',
        'templateId'        => 'templateId',
        'languageId'        => 'languageId',
        'language'          => 'language',
        'customerGroupID'   => 'customerGroupID',
        'priority'          => 'priority',
    ];

    /**
     * @return string
     */
    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string|int $id
     * @return self
     */
    public function setId(string|int $id): self
    {
        $this->id = (int)$id;

        return $this;
    }

    /**
     * @return int
     */
    public function getReSend(): int
    {
        return $this->reSend;
    }

    /**
     * @param string|int $reSend
     * @return self
     */
    public function setReSend(string|int $reSend): self
    {
        $this->reSend = (int)$reSend;

        return $this;
    }

    /**
     * @return int
     */
    public function getIsSendingNow(): int
    {
        return $this->isSendingNow;
    }

    /**
     * @param string|int $isSendingNow
     * @return self
     */
    public function setIsSendingNow(string|int $isSendingNow): self
    {
        $this->isSendingNow = (int)$isSendingNow;

        return $this;
    }

    /**
     * @return int
     */
    public function getSendCount(): int
    {
        return $this->sendCount;
    }

    /**
     * @param string|int $sendCount
     * @return self
     */
    public function setSendCount(string|int $sendCount): self
    {
        $this->sendCount = (int)$sendCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getErrorCount(): int
    {
        return $this->errorCount;
    }

    /**
     * @param string|int $errorCount
     * @return self
     */
    public function setErrorCount(string|int $errorCount): self
    {
        $this->errorCount = (int)$errorCount;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastError(): string
    {
        return $this->lastError;
    }

    /**
     * @param string $lastError
     * @return self
     */
    public function setLastError(string $lastError): self
    {
        $this->lastError = $lastError;

        return $this;
    }

    /**
     * @return string
     */
    public function getDateQueued(): string
    {
        return $this->dateQueued;
    }

    /**
     * @param string $dateQueued
     * @return self
     */
    public function setDateQueued(string $dateQueued): self
    {
        $this->dateQueued = $dateQueued;

        return $this;
    }

    /**
     * @return string
     */
    public function getDateSent(): string
    {
        return $this->dateSent;
    }

    /**
     * @param string $dateSent
     * @return self
     */
    public function setDateSent(string $dateSent): self
    {
        $this->dateSent = $dateSent;

        return $this;
    }

    /**
     * @return string
     */
    public function getFromMail(): string
    {
        return $this->fromMail;
    }

    /**
     * @param string $fromMail
     * @return self
     */
    public function setFromMail(string $fromMail): self
    {
        $this->fromMail = $fromMail;

        return $this;
    }

    /**
     * @return string
     */
    public function getFromName(): string
    {
        return $this->fromName;
    }

    /**
     * @param string $fromName
     * @return self
     */
    public function setFromName(string $fromName): self
    {
        $this->fromName = $fromName;

        return $this;
    }

    /**
     * @return string
     */
    public function getToMail(): string
    {
        return $this->toMail;
    }

    /**
     * @param string $toEmail
     * @return self
     */
    public function setToMail(string $toEmail): self
    {
        $this->toMail = $toEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getToName(): string
    {
        return $this->toName;
    }

    /**
     * @param string|null $toName
     * @return self
     */
    public function setToName(?string $toName): self
    {
        $this->toName = $toName;

        return $this;
    }

    /**
     * @return string
     */
    public function getReplyToMail(): string
    {
        return $this->replyToMail;
    }

    /**
     * @param string|null $replyToMail
     * @return self
     */
    public function setReplyToMail(?string $replyToMail): self
    {
        $this->replyToMail = $replyToMail;

        return $this;
    }

    /**
     * @return string
     */
    public function getReplyToName(): string
    {
        return $this->replyToName;
    }

    /**
     * @param string $replyToName
     * @return self
     */
    public function setReplyToName(?string $replyToName): self
    {
        $this->replyToName = $replyToName;

        return $this;
    }

    /**
     * @return array
     */
    public function getCopyRecipients(): array
    {
        return \explode(';', $this->copyRecipients);
    }

    /**
     * @param array $copyRecipients
     * @return self
     */
    public function setCopyRecipients(array $copyRecipients): self
    {
        $this->copyRecipients = \implode(';', $copyRecipients);

        return $this;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     * @return self
     */
    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return string
     */
    public function getBodyHTML(): string
    {
        return $this->bodyHTML;
    }

    /**
     * @param string $bodyHTML
     * @return self
     */
    public function setBodyHTML(string $bodyHTML): self
    {
        $this->bodyHTML = $bodyHTML;

        return $this;
    }

    /**
     * @return string
     */
    public function getBodyText(): string
    {
        return $this->bodyText;
    }

    /**
     * @param string $bodyText
     * @return self
     */
    public function setBodyText(string $bodyText): self
    {
        $this->bodyText = $bodyText;

        return $this;
    }

    /**
     * @return int
     */
    public function getHasAttachments(): int
    {
        return $this->hasAttachments;
    }

    /**
     * @param string|int $hasAttachments
     * @return self
     */
    public function setHasAttachments(string|int $hasAttachments): self
    {
        $this->hasAttachments = (int)$hasAttachments;

        return $this;
    }

    /**
     * @return array
     */

    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * @param array|null $attachments
     * @return self
     */
    public function setAttachments(?array $attachments): self
    {
        if (\is_array($attachments)) {
            foreach ($attachments as $attachment) {
                $this->attachments[] = $attachment;
            }
        }
        if (!empty($attachments[0])) {
            $this->hasAttachments = 1;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplateId(): string
    {
        return $this->templateId;
    }

    /**
     * @param string|int|null $template
     * @return self
     */
    public function setTemplateId(null|string|int $template): self
    {
        if ($template !== null) {
            $this->templateId = (string)$template;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getLanguageId(): int
    {
        return $this->languageId;
    }


    /**
     * @param string|int $language
     * @return self
     */
    public function setLanguageId(string|int $language): self
    {
        $this->languageId = (int)$language;

        return $this;
    }

    /**
     * @return LanguageModel|null
     */
    public function getLanguage(): ?LanguageModel
    {
        return $this->language;
    }

    /**
     * @param LanguageModel|null $languageModel
     * @return self
     */
    public function setLanguage(?LanguageModel $languageModel): self
    {
        $this->language = $languageModel;

        return $this;
    }

    /**
     * @return int
     */
    public function getCustomerGroupID(): int
    {
        return $this->customerGroupID;
    }

    /**
     * @param string|int $customerGroupID
     * @return self
     */
    public function setCustomerGroupID(string|int $customerGroupID): self
    {
        $this->customerGroupID = (int)$customerGroupID;

        return $this;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     * @return void
     */
    public function setPriority(string|int $priority): void
    {
        $this->priority = (int)$priority;
    }

    /**
     * @return array
     */
    public function getMapping(): array
    {
        return \array_merge($this->mapping, $this->columnMapping);
    }

    /**
     * @return array
     */
    public function getReverseMapping(): array
    {
        return \array_flip($this->mapping);
    }

    /**
     * @return array
     */
    public function getColumnMapping(): array
    {
        return \array_flip($this->columnMapping);
    }
}
