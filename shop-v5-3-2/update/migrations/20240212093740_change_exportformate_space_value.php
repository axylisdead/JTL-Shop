<?php declare(strict_types=1);
/**
 * Change exportformate space value
 *
 * @author dr
 * @created Mon, 12 Feb 2024 09:37:40 +0100
 */

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration_20240212093740
 */
class Migration_20240212093740 extends Migration implements IMigration
{
    protected $author = 'dr';
    protected $description = 'Change exportformate space value';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->setConfig(
            configName:           'exportformate_quot',
            configValue:          'N',
            configSection:        CONF_EXPORTFORMATE,
            externalName:         'Zeichenmaskierung "',
            inputType:            'selectbox',
            sort:                 140,
            additionalProperties: (object)[
                'cBeschreibung' => 'Sie können bei Bedarf diese Zeichen maskieren lassen.',
                'inputOptions'  => [
                    'N'  => 'Nicht maskieren', // former N
                    's'  => 'Mit Leerzeichen maskieren', // former ' '
                    'qq' => 'Mit "" maskieren', // former qq
                    'bq' => 'Mit \" maskieren', // former q
                    'sq' => "Mit ' maskieren", // former '
                ],
            ],
            overwrite:            true,
        );
        $this->setConfig(
            configName:           'exportformate_equot',
            configValue:          'N',
            configSection:        CONF_EXPORTFORMATE,
            externalName:         "Zeichenmaskierung '",
            inputType:            'selectbox',
            sort:                 150,
            additionalProperties: (object)[
                'cBeschreibung' => 'Sie können bei Bedarf diese Zeichen maskieren lassen.',
                'inputOptions'  => [
                    'N'  => 'Nicht maskieren', // former N
                    's'  => 'Mit Leerzeichen maskieren', // former ' '
                    'bs' => "Mit \\' maskieren", // former \'
                    'q'  => 'Mit \" maskieren', // former bq
                    'ss' => "Mit '' maskieren", // former ''
                ],
            ],
            overwrite:            true,
        );
        $this->setConfig(
            configName:           'exportformate_semikolon',
            configValue:          'N',
            configSection:        CONF_EXPORTFORMATE,
            externalName:         "Zeichenmaskierung ;",
            inputType:            'selectbox',
            sort:                 160,
            additionalProperties: (object)[
                'cBeschreibung' => 'Sie können bei Bedarf diese Zeichen maskieren lassen.',
                'inputOptions'  => [
                    'N' => 'Nicht maskieren', // former N
                    's' => 'Mit Leerzeichen maskieren', // former ' '
                    'c' => 'Mit Komma maskieren', // former ,
                ],
            ],
            overwrite:            true,
        );
        $this->execute("
            UPDATE texportformateinstellungen SET cWert = 's'
            WHERE cWert = ' ' AND cName IN ('exportformate_quot', 'exportformate_equot', 'exportformate_semikolon')
        ");
        $this->execute("
            UPDATE texportformateinstellungen SET cWert = 'bq'
            WHERE cWert = 'q' AND cName = 'exportformate_quot'
        ");
        $this->execute('
            UPDATE texportformateinstellungen SET cWert = "sq"
            WHERE cWert = "\'" AND cName = "exportformate_quot"
        ');
        $this->execute('
            UPDATE texportformateinstellungen SET cWert = "bs"
            WHERE cWert = "\\\\\'" AND cName = "exportformate_equot"
        ');
        $this->execute("
            UPDATE texportformateinstellungen SET cWert = 'q'
            WHERE cWert = 'bq' AND cName = 'exportformate_equot'
        ");
        $this->execute('
            UPDATE texportformateinstellungen SET cWert = "ss"
            WHERE cWert = "\'\'" AND cName = "exportformate_equot"
        ');
        $this->execute('
            UPDATE texportformateinstellungen SET cWert = "c"
            WHERE cWert = "," AND cName = "exportformate_semikolon"
        ');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->setConfig(
            configName:           'exportformate_quot',
            configValue:          'N',
            configSection:        CONF_EXPORTFORMATE,
            externalName:         'Zeichenmaskierung "',
            inputType:            'selectbox',
            sort:                 140,
            additionalProperties: (object)[
                'cBeschreibung' => 'Sie können bei Bedarf diese Zeichen maskieren lassen.',
                'inputOptions'  => [
                    'N'  => 'Nicht maskieren',
                    ' '  => 'Mit Leerzeichen maskieren',
                    'qq' => 'Mit "" maskieren',
                    'q'  => 'Mit \" maskieren',
                    "'"  => "Mit ' maskieren",
                ],
            ],
            overwrite:            true,
        );
        $this->setConfig(
            configName:           'exportformate_equot',
            configValue:          'N',
            configSection:        CONF_EXPORTFORMATE,
            externalName:         "Zeichenmaskierung '",
            inputType:            'selectbox',
            sort:                 150,
            additionalProperties: (object)[
                'cBeschreibung' => 'Sie können bei Bedarf diese Zeichen maskieren lassen.',
                'inputOptions'  => [
                    'N'   => 'Nicht maskieren',
                    ' '   => 'Mit Leerzeichen maskieren',
                    "\\'" => "Mit \\' maskieren",
                    'bq'  => 'Mit \" maskieren',
                    "''"  => "Mit '' maskieren",
                ],
            ],
            overwrite:            true,
        );
        $this->setConfig(
            configName:           'exportformate_semikolon',
            configValue:          'N',
            configSection:        CONF_EXPORTFORMATE,
            externalName:         "Zeichenmaskierung ;",
            inputType:            'selectbox',
            sort:                 160,
            additionalProperties: (object)[
                'cBeschreibung' => 'Sie können bei Bedarf diese Zeichen maskieren lassen.',
                'inputOptions'  => [
                    'N' => 'Nicht maskieren',
                    ' ' => 'Mit Leerzeichen maskieren',
                    ',' => 'Mit Komma maskieren',
                ],
            ],
            overwrite:            true,
        );
        $this->execute("
            UPDATE texportformateinstellungen SET cWert = ' '
            WHERE cWert = 's' AND cName IN ('exportformate_quot', 'exportformate_equot', 'exportformate_semikolon')
        ");
        $this->execute("
            UPDATE texportformateinstellungen SET cWert = 'q'
            WHERE cWert = 'bq' AND cName = 'exportformate_quot'
        ");
        $this->execute('
            UPDATE texportformateinstellungen SET cWert = "\'"
            WHERE cWert = "sq" AND cName = "exportformate_quot"
        ');
        $this->execute('
            UPDATE texportformateinstellungen SET cWert = "\\\\\'"
            WHERE cWert = "bs" AND cName = "exportformate_equot"
        ');
        $this->execute("
            UPDATE texportformateinstellungen SET cWert = 'bq'
            WHERE cWert = 'q' AND cName = 'exportformate_equot'
        ");
        $this->execute('
            UPDATE texportformateinstellungen SET cWert = "\'\'"
            WHERE cWert = "ss" AND cName = "exportformate_equot"
        ');
        $this->execute('
            UPDATE texportformateinstellungen SET cWert = ","
            WHERE cWert = "c" AND cName = "exportformate_semikolon"
        ');
    }
}
