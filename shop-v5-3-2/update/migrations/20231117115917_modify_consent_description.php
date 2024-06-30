<?php declare(strict_types=1);
/**
 * Modify consent description
 *
 * @author Tim Niko Tegtmeyer
 * @created Fri, 17 Nov 2023 11:59:17 +0100
 */

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration_20231117115917
 */
class Migration_20231117115917 extends Migration implements IMigration
{
    protected $author = 'Tim Niko Tegtmeyer';
    protected $description = 'Modify consent description';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->setLocalization(
            'ger',
            'consent',
            'consentDescription',
            'Durch Klicken auf „Alle akzeptieren“ gestatten Sie den Einsatz folgender Dienste auf unserer'
                . ' Website: %s. Sie können die Einstellung jederzeit ändern (Fingerabdruck-Icon links unten). Weitere'
                . ' Details finden Sie unter <i>Konfigurieren</i> und in unserer <i>Datenschutzerklärung</i>.'
        );
        $this->setLocalization(
            'eng',
            'consent',
            'consentDescription',
            'By selecting "Accept all", you give us permission to use the following services on our website: %s.'
            . ' You can change the settings at any time (fingerprint icon in the bottom left corner). For further'
            . ' details, please see <i>Individual configuration</i> and our <i>Privacy notice</i>.'
        );
        $this->setLocalization(
            'ger',
            'consent',
            'cookieSettingsDescription',
            'Einstellungen, die Sie hier vornehmen, werden auf Ihrem Endgerät im „Local Storage“ gespeichert und'
                . ' sind beim nächsten Besuch unseres Onlineshops wieder aktiv. Sie können diese Einstellungen'
                . ' jederzeit ändern (Fingerabdruck-Icon links unten).<br><br>Informationen zur Cookie-Funktionsdauer'
                . ' sowie Details zu technisch notwendigen Cookies erhalten Sie in unserer <i>Datenschutzerklärung</i>.'
        );
        $this->setLocalization(
            'eng',
            'consent',
            'cookieSettingsDescription',
            'The settings you specify here are stored in the "local storage" of your device. The settings will be'
                . ' remembered for the next time you visit our online shop. You can change these settings at any time'
                . ' (fingerprint icon in the bottom left corner).<br><br>For more information on cookie lifetime and'
                . ' required essential cookies, please see the <i>Privacy notice</i>.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->setLocalization(
            'ger',
            'consent',
            'consentDescription',
            'Durch Klicken auf „Alle akzeptieren“ gestatten Sie den Einsatz folgender Dienste auf unserer'
                . ' Website: %s. Sie können die Einstellung jederzeit ändern (Fingerabdruck-Icon links unten). Weitere'
                . ' Details finden Sie unter <i>Konfigurieren</i> und in unserer'
                . ' <a href="%s" target="_blank">Datenschutzerklärung</a>.'
        );
        $this->setLocalization(
            'eng',
            'consent',
            'consentDescription',
            'By selecting "Accept all", you give us permission to use the following services on our website: %s.'
                . ' You can change the settings at any time (fingerprint icon in the bottom left corner). For further'
                . ' details, please see Individual configuration and our'
                . ' <a href="%s" target="_blank">Privacy notice</a>.'
        );
        $this->setLocalization(
            'ger',
            'consent',
            'cookieSettingsDescription',
            'Einstellungen, die Sie hier vornehmen, werden auf Ihrem Endgerät im „Local Storage“ gespeichert und'
            . ' sind beim nächsten Besuch unseres Onlineshops wieder aktiv. Sie können diese Einstellungen'
            . ' jederzeit ändern (Fingerabdruck-Icon links unten).<br><br>Informationen zur Cookie-Funktionsdauer'
            . ' sowie Details zu technisch notwendigen Cookies erhalten Sie in unserer'
            . ' <a href="%s" target="_blank">Datenschutzerklärung</a>.'
        );
        $this->setLocalization(
            'eng',
            'consent',
            'cookieSettingsDescription',
            'The settings you specify here are stored in the "local storage" of your device. The settings will be'
            . ' remembered for the next time you visit our online shop. You can change these settings at any time'
            . ' (fingerprint icon in the bottom left corner).<br><br>For more information on cookie lifetime and'
            . ' required essential cookies, please see the <a href="%s" target="_blank">Privacy notice</a>.'
        );
    }
}
