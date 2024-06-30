<?php declare(strict_types=1);

namespace JTL\Checkout;

use Illuminate\Support\Collection;
use JTL\Customer\Customer;
use JTL\DB\DbInterface;
use JTL\DB\ReturnType;
use JTL\Helpers\Typifier;
use JTL\Language\LanguageHelper;
use JTL\Shop;
use stdClass;

/**
 * Class DeliveryAddressTemplate
 * @package JTL\Checkout
 */
class DeliveryAddressTemplate extends Adresse
{
    /**
     * @var int
     */
    public $kLieferadresse;

    /**
     * @var int
     */
    public $kKunde;

    /**
     * @var string
     */
    public $cAnredeLocalized;

    /**
     * @var string
     */
    public $angezeigtesLand;

    /**
     * @var int
     */
    public int $nIstStandardLieferadresse = 0;

    /**
     * @param DbInterface $db
     * @param int         $id
     */
    public function __construct(private readonly DbInterface $db, int $id = 0)
    {
        if ($id > 0) {
            $this->load($id);
        }
    }

    /**
     * @param int $id
     * @return $this|null
     */
    public function load(int $id): ?self
    {
        $data = $this->db->executeQueryPrepared(
            'SELECT tlieferadressevorlage.*
            FROM tlieferadressevorlage
            WHERE tlieferadressevorlage.kLieferadresse LIKE :kLieferadresse
            GROUP BY tlieferadressevorlage.kLieferadresse',
            ['kLieferadresse' => $id],
            ReturnType::SINGLE_OBJECT
        );
        if ($data === null || $data->kLieferadresse < 1) {
            return null;
        }
        $this->kKunde                    = (int)$data->kKunde;
        $this->cAnrede                   = $data->cAnrede;
        $this->cVorname                  = $data->cVorname;
        $this->cNachname                 = $data->cNachname;
        $this->cTitel                    = $data->cTitel;
        $this->cFirma                    = $data->cFirma;
        $this->cZusatz                   = $data->cZusatz;
        $this->cStrasse                  = $data->cStrasse;
        $this->cHausnummer               = $data->cHausnummer;
        $this->cAdressZusatz             = $data->cAdressZusatz;
        $this->cPLZ                      = $data->cPLZ;
        $this->cOrt                      = $data->cOrt;
        $this->cBundesland               = $data->cBundesland;
        $this->cLand                     = $data->cLand;
        $this->cTel                      = $data->cTel;
        $this->cMobil                    = $data->cMobil;
        $this->cFax                      = $data->cFax;
        $this->cMail                     = $data->cMail;
        $this->kLieferadresse            = $id;
        $this->nIstStandardLieferadresse = (int)$data->nIstStandardLieferadresse;
        $this->cAnredeLocalized          = Customer::mapSalutation($this->cAnrede, 0, $this->kKunde);
        // Workaround for WAWI-39370
        $this->cLand           = self::checkISOCountryCode($this->cLand);
        $this->angezeigtesLand = LanguageHelper::getCountryCodeByCountryName($this->cLand);
        $this->decrypt();

        \executeHook(\HOOK_LIEFERADRESSE_CLASS_LOADFROMDB, ['address' => $this]);

        return $this;
    }

    /**
     * @return int
     */
    public function persist(): int
    {
        $this->encrypt();
        $ins                            = new stdClass();
        $ins->kKunde                    = $this->kKunde;
        $ins->cAnrede                   = $this->cAnrede;
        $ins->cVorname                  = $this->cVorname;
        $ins->cNachname                 = $this->cNachname;
        $ins->cTitel                    = $this->cTitel;
        $ins->cFirma                    = $this->cFirma;
        $ins->cZusatz                   = $this->cZusatz;
        $ins->cStrasse                  = $this->cStrasse;
        $ins->cHausnummer               = $this->cHausnummer;
        $ins->cAdressZusatz             = $this->cAdressZusatz;
        $ins->cPLZ                      = $this->cPLZ;
        $ins->cOrt                      = $this->cOrt;
        $ins->cBundesland               = $this->cBundesland;
        $ins->cLand                     = self::checkISOCountryCode($this->cLand);
        $ins->cTel                      = $this->cTel;
        $ins->cMobil                    = $this->cMobil;
        $ins->cFax                      = $this->cFax;
        $ins->cMail                     = $this->cMail;
        $ins->nIstStandardLieferadresse = $this->nIstStandardLieferadresse;

        $this->kLieferadresse = $this->db->insert('tlieferadressevorlage', $ins);
        $this->decrypt();
        $this->cAnredeLocalized = $this->mappeAnrede($this->cAnrede);

        return $this->kLieferadresse;
    }

    /**
     * @return int
     */
    public function update(): int
    {
        $this->encrypt();
        $upd                            = new stdClass();
        $upd->kLieferadresse            = $this->kLieferadresse;
        $upd->kKunde                    = $this->kKunde;
        $upd->cAnrede                   = $this->cAnrede;
        $upd->cVorname                  = $this->cVorname;
        $upd->cNachname                 = $this->cNachname;
        $upd->cTitel                    = $this->cTitel;
        $upd->cFirma                    = $this->cFirma;
        $upd->cZusatz                   = $this->cZusatz;
        $upd->cStrasse                  = $this->cStrasse;
        $upd->cHausnummer               = $this->cHausnummer;
        $upd->cAdressZusatz             = $this->cAdressZusatz;
        $upd->cPLZ                      = $this->cPLZ;
        $upd->cOrt                      = $this->cOrt;
        $upd->cBundesland               = $this->cBundesland;
        $upd->cLand                     = self::checkISOCountryCode($this->cLand);
        $upd->cTel                      = $this->cTel;
        $upd->cMobil                    = $this->cMobil;
        $upd->cFax                      = $this->cFax;
        $upd->cMail                     = $this->cMail;
        $upd->nIstStandardLieferadresse = $this->nIstStandardLieferadresse;

        $res = $this->db->update('tlieferadressevorlage', 'kLieferadresse', $upd->kLieferadresse, $upd);
        $this->decrypt();
        $this->cAnredeLocalized = $this->mappeAnrede($this->cAnrede);

        return $res;
    }

    /**
     * @return int
     */
    public function delete(): int
    {
        $this->encrypt();

        return $this->db->delete(
            'tlieferadressevorlage',
            ['kLieferadresse', 'kKunde'],
            [$this->kLieferadresse, $this->kKunde]
        );
    }

    /**
     * get shipping address
     *
     * @return array
     */
    public function gibLieferadresseAssoc(): array
    {
        return $this->kLieferadresse > 0
            ? $this->toArray()
            : [];
    }

    /**
     * @param object $data
     * @return DeliveryAddressTemplate
     */
    public static function createFromObject($data): DeliveryAddressTemplate
    {
        $address                            = new self(Shop::Container()->getDB());
        $address->kLieferadresse            = Typifier::intify($data->kLieferadresse ?? 0);
        $address->cVorname                  = Typifier::stringify($data->cVorname ?? null, null);
        $address->cNachname                 = Typifier::stringify($data->cNachname ?? null, null);
        $address->cFirma                    = Typifier::stringify($data->cFirma ?? null, null);
        $address->cZusatz                   = Typifier::stringify($data->cZusatz ?? null, null);
        $address->kKunde                    = Typifier::intify($data->kKunde ?? null);
        $address->cAnrede                   = Typifier::stringify($data->cAnrede ?? null, null);
        $address->cTitel                    = Typifier::stringify($data->cTitel ?? null, null);
        $address->cStrasse                  = Typifier::stringify($data->cStrasse ?? null, null);
        $address->cHausnummer               = Typifier::stringify($data->cHausnummer ?? null, null);
        $address->cAdressZusatz             = Typifier::stringify($data->cAdressZusatz ?? null, null);
        $address->cPLZ                      = Typifier::stringify($data->cPLZ ?? null, null);
        $address->cOrt                      = Typifier::stringify($data->cOrt ?? null, null);
        $address->cLand                     = Typifier::stringify($data->cLand ?? null, null);
        $address->cBundesland               = Typifier::stringify($data->cBundesland ?? null, null);
        $address->cTel                      = Typifier::stringify($data->cTel ?? null, null);
        $address->cMobil                    = Typifier::stringify($data->cMobil ?? null, null);
        $address->cFax                      = Typifier::stringify($data->cFax ?? null, null);
        $address->cMail                     = Typifier::stringify($data->cMail ?? null, null);
        $address->nIstStandardLieferadresse = Typifier::intify($data->nIstStandardLieferadresse ?? null, 0);

        return $address;
    }

    /**
     * @return Lieferadresse
     */
    public function getDeliveryAddress(): Lieferadresse
    {
        $address                = new Lieferadresse();
        $address->cVorname      = Typifier::stringify($this->cVorname ?? null, null);
        $address->cNachname     = Typifier::stringify($this->cNachname ?? null, null);
        $address->cFirma        = Typifier::stringify($this->cFirma ?? null, null);
        $address->cZusatz       = Typifier::stringify($this->cZusatz ?? null, null);
        $address->kKunde        = Typifier::intify($this->kKunde ?? null);
        $address->cAnrede       = Typifier::stringify($this->cAnrede ?? null, null);
        $address->cTitel        = Typifier::stringify($this->cTitel ?? null, null);
        $address->cStrasse      = Typifier::stringify($this->cStrasse ?? null, null);
        $address->cHausnummer   = Typifier::stringify($this->cHausnummer ?? null, null);
        $address->cAdressZusatz = Typifier::stringify($this->cAdressZusatz ?? null, null);
        $address->cPLZ          = Typifier::stringify($this->cPLZ ?? null, null);
        $address->cOrt          = Typifier::stringify($this->cOrt ?? null, null);
        $address->cLand         = Typifier::stringify($this->cLand ?? null, null);
        $address->cBundesland   = Typifier::stringify($this->cBundesland ?? null, null);
        $address->cTel          = Typifier::stringify($this->cTel ?? null, null);
        $address->cMobil        = Typifier::stringify($this->cMobil ?? null, null);
        $address->cFax          = Typifier::stringify($this->cFax ?? null, null);
        $address->cMail         = Typifier::stringify($this->cMail ?? null, null);

        return $address;
    }

    /**
     * @param int $id
     * @param int $customerID
     * @return bool
     * @since 5.3.0
     */
    public function setAsDefault(int $id, int $customerID):bool
    {
        $this->db->executeQueryPrepared(
            'UPDATE tlieferadressevorlage
            SET nIstStandardLieferadresse = 0
            WHERE kKunde = :customerID
              AND nIstStandardLieferadresse = 1',
            ['customerID' => $customerID]
        );
        $upd                            = new stdClass();
        $upd->nIstStandardLieferadresse = 1;
        return (bool)$this->db->updateRow(
            'tlieferadressevorlage',
            ['kLieferadresse', 'kKunde'],
            [$id, $customerID],
            $upd
        );
    }

    /**
     * @param int $customerID
     * @return Collection
     * @since 5.3.0
     */
    public static function getAll(int $customerID = 0): Collection
    {
        return Shop::Container()->getDB()->getCollection(
            'SELECT tlieferadressevorlage.*
                FROM tlieferadressevorlage
                WHERE tlieferadressevorlage.kKunde LIKE :customerID
                ORDER BY tlieferadressevorlage.nIstStandardLieferadresse DESC,
                tlieferadressevorlage.kLieferadresse DESC',
            ['customerID' => $customerID]
        )->map(static function ($address): self {
            $result = self::createFromObject($address);
            $result->decrypt();

            return $result;
        });
    }
}
