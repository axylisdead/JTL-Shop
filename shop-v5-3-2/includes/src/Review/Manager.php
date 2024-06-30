<?php declare(strict_types=1);

namespace JTL\Review;

use JTL\Cache\JTLCacheInterface;
use JTL\Customer\Customer;
use JTL\DB\DbInterface;
use JTL\Mail\Mail\Mail;
use JTL\Mail\Mailer;
use JTL\Model\DataModelInterface;
use JTL\Services\JTL\AlertServiceInterface;
use JTL\Shop;
use JTL\Smarty\JTLSmarty;
use stdClass;

/**
 * Class Manager
 * @package JTL\Review
 */
class Manager
{
    /**
     * @param DbInterface           $db
     * @param AlertServiceInterface $alertService
     * @param JTLCacheInterface     $cache
     * @param JTLSmarty             $smarty
     * @param array                 $config
     */
    public function __construct(
        protected DbInterface           $db,
        protected AlertServiceInterface $alertService,
        protected JTLCacheInterface     $cache,
        protected JTLSmarty             $smarty,
        protected array                 $config
    ) {
    }

    /**
     * @param int    $productID
     * @param string $activate
     * @return bool
     */
    public function updateAverage(int $productID, string $activate): bool
    {
        $sql = $activate === 'Y' ? ' AND nAktiv = 1' : '';
        $cnt = $this->db->getSingleInt(
            'SELECT COUNT(*) AS cnt
                FROM tbewertung
                WHERE kArtikel = :pid' . $sql,
            'cnt',
            ['pid' => $productID]
        );
        if ($cnt === 1) {
            $sql = '';
        } elseif ($cnt === 0) {
            $this->db->delete('tartikelext', 'kArtikel', $productID);

            return false;
        }
        $avg = $this->db->getSingleObject(
            'SELECT (SUM(nSterne) / COUNT(*)) AS fDurchschnitt
                FROM tbewertung
                WHERE kArtikel = :pid' . $sql,
            ['pid' => $productID]
        );
        if ($avg !== null && $avg->fDurchschnitt > 0) {
            $this->db->delete('tartikelext', 'kArtikel', $productID);
            $ext = (object)['kArtikel' => $productID, 'fDurchschnittsBewertung' => (float)$avg->fDurchschnitt];
            $this->db->insert('tartikelext', $ext);
        }

        return true;
    }

    /**
     * @param ReviewModel $review
     * @return float
     */
    public function addReward(ReviewModel $review): float
    {
        $reward = 0.0;
        if ($this->config['bewertung']['bewertung_guthaben_nutzen'] !== 'Y') {
            return $reward;
        }
        $maxBalance    = (float)$this->config['bewertung']['bewertung_max_guthaben'];
        $level2balance = (float)$this->config['bewertung']['bewertung_stufe2_guthaben'];
        $level1balance = (float)$this->config['bewertung']['bewertung_stufe1_guthaben'];
        $reviewBonus   = $this->db->getSingleObject(
            'SELECT SUM(fGuthabenBonus) AS fGuthabenProMonat
                FROM tbewertungguthabenbonus
                WHERE kKunde = :cid
                    AND kBewertung != :rid
                    AND YEAR(dDatum) = :dyear
                    AND MONTH(dDatum) = :dmonth',
            [
                'cid'    => $review->getCustomerID(),
                'rid'    => $review->getId(),
                'dyear'  => \date('Y'),
                'dmonth' => \date('m')
            ]
        );
        if ($reviewBonus !== null && (float)$reviewBonus->fGuthabenProMonat > $maxBalance) {
            return $reward;
        }
        if ((int)$this->config['bewertung']['bewertung_stufe2_anzahlzeichen'] <= \mb_strlen($review->getContent())) {
            $reward = ((float)$reviewBonus->fGuthabenProMonat + $level2balance) > $maxBalance
                ? $maxBalance - (float)$reviewBonus->fGuthabenProMonat
                : $level2balance;
        } else {
            $reward = ((float)$reviewBonus->fGuthabenProMonat + $level1balance) > $maxBalance
                ? $maxBalance - (float)$reviewBonus->fGuthabenProMonat
                : $level1balance;
        }
        $this->increaseCustomerBalance($review->getCustomerID(), $reward);

        $reviewBonus = ReviewBonusModel::loadByAttributes(
            ['customerID' => $review->getCustomerID(), 'reviewID' => $review->getId()],
            $this->db,
            DataModelInterface::ON_NOTEXISTS_NEW
        );
        $reviewBonus->setBonus($reward);
        $reviewBonus->setReviewID($review->getId());
        $reviewBonus->setCustomerID($review->getCustomerID());
        $reviewBonus->setDate('NOW()');
        $reviewBonus->save();
        $this->sendRewardMail($reviewBonus);

        return $reward;
    }

    /**
     * @param int   $customerID
     * @param float $reward
     * @return int
     */
    public function increaseCustomerBalance(int $customerID, float $reward): int
    {
        return $this->db->getAffectedRows(
            'UPDATE tkunde
                SET fGuthaben = fGuthaben + :rew
                WHERE kKunde = :cid',
            ['cid' => $customerID, 'rew' => $reward]
        );
    }

    /**
     * @param ReviewBonusModel $reviewBonus
     * @return bool
     */
    public function sendRewardMail(ReviewBonusModel $reviewBonus): bool
    {
        $obj                          = new stdClass();
        $obj->tkunde                  = new Customer($reviewBonus->customerID);
        $obj->oBewertungGuthabenBonus = $reviewBonus->rawObject();
        $mailer                       = Shop::Container()->get(Mailer::class);
        $mail                         = new Mail();

        return $mailer->send($mail->createFromTemplateID(\MAILTEMPLATE_BEWERTUNG_GUTHABEN, $obj));
    }
}
