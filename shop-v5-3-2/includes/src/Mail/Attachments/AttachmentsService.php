<?php declare(strict_types=1);

namespace JTL\Mail\Attachments;

use JTL\Abstracts\AbstractService;
use JTL\Mail\Mail\Attachment;
use JTL\Mail\SendMailObjects\MailDataAttachmentObject;

/**
 * Class AttachmentsService
 * @package JTL\Mail\Attachments
 */
class AttachmentsService extends AbstractService
{
    /**
     * @param AttachmentsRepository|null $repository
     */
    public function __construct(
        protected ?AttachmentsRepository $repository = null
    ) {
        $this->repository = $this->repository ?? new AttachmentsRepository();
    }
    
    /**
     * @return AttachmentsRepository
     */
    public function getRepository(): AttachmentsRepository
    {
        return $this->repository;
    }

    /**
     * @param Attachment $attachment
     * @param int        $mailID
     * @return int
     */
    public function insertAttachment(Attachment $attachment, int $mailID): int
    {
        $attachmentTableObject = (new MailDataAttachmentObject())->hydrateWithObject($attachment->toObject());
        $attachmentTableObject->setMailID($mailID);

        return $this->getRepository()->insertMailAttachment($attachmentTableObject);
    }

    /**
     * @param array $IDs
     * @return array
     */
    public function getListByMailIDs(array $IDs): array
    {
        $list           = $this->getRepository()->getListByMailIDs($IDs);
        $associatedList = [];
        foreach ($list as $item) {
            $associatedList[$item->mailID][] = (new Attachment())->hydrateWithObject($item);
        }
        return $associatedList;
    }
}
