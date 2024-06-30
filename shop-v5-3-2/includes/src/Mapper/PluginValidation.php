<?php declare(strict_types=1);

namespace JTL\Mapper;

use JTL\Plugin\InstallCode;

/**
 * Class PluginValidation
 * @package JTL\Mapper
 */
class PluginValidation
{
    /**
     * @param int         $code
     * @param string|null $pluginID
     * @return string
     */
    public function map(int $code, string $pluginID = null): string
    {
        if ($code === 0) {
            return '';
        }
        $return = match ($code) {
            InstallCode::WRONG_PARAM => \__('WRONG_PARAM'),
            InstallCode::DIR_DOES_NOT_EXIST => \__('DIR_DOES_NOT_EXIST'),
            InstallCode::INFO_XML_MISSING => \__('INFO_XML_MISSING'),
            InstallCode::NO_PLUGIN_FOUND => \__('NO_PLUGIN_FOUND'),
            InstallCode::INVALID_NAME => \__('INVALID_NAME'),
            InstallCode::INVALID_PLUGIN_ID => \__('INVALID_PLUGIN_ID'),
            InstallCode::INSTALL_NODE_MISSING => \__('INSTALL_NODE_MISSING'),
            InstallCode::INVALID_XML_VERSION_NUMBER => \__('INVALID_XML_VERSION_NUMBER'),
            InstallCode::INVALID_VERSION_NUMBER => \__('INVALID_VERSION_NUMBER'),
            InstallCode::INVALID_DATE => \__('INVALID_DATE'),
            InstallCode::MISSING_SQL_FILE => \__('MISSING_SQL_FILE'),
            InstallCode::MISSING_HOOKS => \__('MISSING_HOOKS'),
            InstallCode::INVALID_HOOK => \__('INVALID_HOOK'),
            InstallCode::INVALID_CUSTOM_LINK_NAME => \__('INVALID_CUSTOM_LINK_NAME'),
            InstallCode::INVALID_CUSTOM_LINK_FILE_NAME => \__('INVALID_CUSTOM_LINK_FILE_NAME'),
            InstallCode::MISSING_CUSTOM_LINK_FILE => \__('MISSING_CUSTOM_LINK_FILE'),
            InstallCode::INVALID_CONFIG_LINK_NAME => \__('INVALID_CONFIG_LINK_NAME'),
            InstallCode::MISSING_CONFIG => \__('MISSING_CONFIG'),
            InstallCode::INVALID_CONFIG_TYPE => \__('INVALID_CONFIG_TYPE'),
            InstallCode::INVALID_CONFIG_INITIAL_VALUE => \__('INVALID_CONFIG_INITIAL_VALUE'),
            InstallCode::INVALID_CONFIG_SORT_VALUE => \__('INVALID_CONFIG_SORT_VALUE'),
            InstallCode::INVALID_CONFIG_NAME => \__('INVALID_CONFIG_NAME'),
            InstallCode::MISSING_CONFIG_SELECTBOX_OPTIONS,
            InstallCode::MISSING_PAYMENT_METHOD_SELECTBOX_OPTIONS => \__('MISSING_CONFIG_SELECTBOX_OPTIONS'),
            InstallCode::INVALID_CONFIG_OPTION,
            InstallCode::INVALID_PAYMENT_METHOD_OPTION => \__('INVALID_CONFIG_OPTION'),
            InstallCode::MISSING_LANG_VARS => \__('MISSING_LANG_VARS'),
            InstallCode::INVALID_LANG_VAR_NAME => \__('INVALID_LANG_VAR_NAME'),
            InstallCode::MISSING_LOCALIZED_LANG_VAR => \__('MISSING_LOCALIZED_LANG_VAR'),
            InstallCode::INVALID_LANG_VAR_ISO => \__('INVALID_LANG_VAR_ISO'),
            InstallCode::INVALID_LOCALIZED_LANG_VAR_NAME => \__('INVALID_LOCALIZED_LANG_VAR_NAME'),
            InstallCode::MISSING_HOOK_FILE => \__('MISSING_HOOK_FILE'),
            InstallCode::MISSING_VERSION_DIR => \__('MISSING_VERSION_DIR'),
            InstallCode::INVALID_CONF => \__('INVALID_CONF'),
            InstallCode::INVALID_CONF_VALUE_NAME => \__('INVALID_CONF_VALUE_NAME'),
            InstallCode::INVALID_XML_VERSION => \__('INVALID_XML_VERSION'),
            InstallCode::INVALID_SHOP_VERSION => \__('INVALID_SHOP_VERSION'),
            InstallCode::SHOP_VERSION_COMPATIBILITY => \__('SHOP_VERSION_COMPATIBILITY'),
            InstallCode::MISSING_FRONTEND_LINKS => \__('MISSING_FRONTEND_LINKS'),
            InstallCode::INVALID_FRONTEND_LINK_FILENAME => \__('INVALID_FRONTEND_LINK_FILENAME'),
            InstallCode::INVALID_FRONTEND_LINK_NAME => \__('INVALID_FRONTEND_LINK_NAME'),
            InstallCode::INVALID_FRONEND_LINK_VISIBILITY => \__('INVALID_FRONEND_LINK_VISIBILITY'),
            InstallCode::INVALID_FRONEND_LINK_PRINT => \__('INVALID_FRONEND_LINK_PRINT'),
            InstallCode::INVALID_FRONEND_LINK_ISO => \__('INVALID_FRONEND_LINK_ISO'),
            InstallCode::INVALID_FRONEND_LINK_SEO => \__('INVALID_FRONEND_LINK_SEO'),
            InstallCode::INVALID_FRONEND_LINK_NAME => \__('INVALID_FRONEND_LINK_NAME'),
            InstallCode::INVALID_FRONEND_LINK_TITLE => \__('INVALID_FRONEND_LINK_TITLE'),
            InstallCode::INVALID_FRONEND_LINK_META_TITLE => \__('INVALID_FRONEND_LINK_META_TITLE'),
            InstallCode::INVALID_FRONEND_LINK_META_KEYWORDS => \__('INVALID_FRONEND_LINK_META_KEYWORDS'),
            InstallCode::INVALID_FRONEND_LINK_META_DESCRIPTION => \__('INVALID_FRONEND_LINK_META_DESCRIPTION'),
            InstallCode::INVALID_PAYMENT_METHOD_NAME => \__('INVALID_PAYMENT_METHOD_NAME'),
            InstallCode::INVALID_PAYMENT_METHOD_MAIL => \__('INVALID_PAYMENT_METHOD_MAIL'),
            InstallCode::INVALID_PAYMENT_METHOD_TSCODE => \__('INVALID_PAYMENT_METHOD_TSCODE'),
            InstallCode::INVALID_PAYMENT_METHOD_PRE_ORDER => \__('INVALID_PAYMENT_METHOD_PRE_ORDER'),
            InstallCode::INVALID_PAYMENT_METHOD_CLASS_FILE => \__('INVALID_PAYMENT_METHOD_CLASS_FILE'),
            InstallCode::MISSING_PAYMENT_METHOD_FILE => \__('MISSING_PAYMENT_METHOD_FILE'),
            InstallCode::INVALID_PAYMENT_METHOD_TEMPLATE => \__('INVALID_PAYMENT_METHOD_TEMPLATE'),
            InstallCode::MISSING_PAYMENT_METHOD_TEMPLATE => \__('MISSING_PAYMENT_METHOD_TEMPLATE'),
            InstallCode::MISSING_PAYMENT_METHOD_LANGUAGES => \__('MISSING_PAYMENT_METHOD_LANGUAGES'),
            InstallCode::INVALID_PAYMENT_METHOD_LANGUAGE_ISO => \__('INVALID_PAYMENT_METHOD_LANGUAGE_ISO'),
            InstallCode::INVALID_PAYMENT_METHOD_NAME_LOCALIZED => \__('INVALID_PAYMENT_METHOD_NAME_LOCALIZED'),
            InstallCode::INVALID_PAYMENT_METHOD_CHARGE_NAME => \__('INVALID_PAYMENT_METHOD_CHARGE_NAME'),
            InstallCode::INVALID_PAYMENT_METHOD_INFO_TEXT => \__('INVALID_PAYMENT_METHOD_INFO_TEXT'),
            InstallCode::INVALID_PAYMENT_METHOD_CONFIG_TYPE => \__('INVALID_PAYMENT_METHOD_CONFIG_TYPE'),
            InstallCode::INVALID_PAYMENT_METHOD_CONFIG_INITITAL_VALUE
            => \__('INVALID_PAYMENT_METHOD_CONFIG_INITITAL_VALUE'),
            InstallCode::INVALID_PAYMENT_METHOD_CONFIG_SORT => \__('INVALID_PAYMENT_METHOD_CONFIG_SORT'),
            InstallCode::INVALID_PAYMENT_METHOD_CONFIG_CONF => \__('INVALID_PAYMENT_METHOD_CONFIG_CONF'),
            InstallCode::INVALID_PAYMENT_METHOD_CONFIG_NAME => \__('INVALID_PAYMENT_METHOD_CONFIG_NAME'),
            InstallCode::INVALID_PAYMENT_METHOD_VALUE_NAME => \__('INVALID_PAYMENT_METHOD_VALUE_NAME'),
            InstallCode::INVALID_PAYMENT_METHOD_SORT => \__('INVALID_PAYMENT_METHOD_SORT'),
            InstallCode::INVALID_PAYMENT_METHOD_SOAP => \__('INVALID_PAYMENT_METHOD_SOAP'),
            InstallCode::INVALID_PAYMENT_METHOD_CURL => \__('INVALID_PAYMENT_METHOD_CURL'),
            InstallCode::INVALID_PAYMENT_METHOD_SOCKETS => \__('INVALID_PAYMENT_METHOD_SOCKETS'),
            InstallCode::INVALID_PAYMENT_METHOD_CLASS_NAME => \__('INVALID_PAYMENT_METHOD_CLASS_NAME'),
            InstallCode::INVALID_FULLSCREEN_TEMPLATE => \__('INVALID_FULLSCREEN_TEMPLATE'),
            InstallCode::MISSING_FRONTEND_LINK_TEMPLATE => \__('MISSING_FRONTEND_LINK_TEMPLATE'),
            InstallCode::TOO_MANY_FULLSCREEN_TEMPLATE_NAMES => \__('TOO_MANY_FULLSCREEN_TEMPLATE_NAMES'),
            InstallCode::INVALID_FULLSCREEN_TEMPLATE_NAME => \__('INVALID_FULLSCREEN_TEMPLATE_NAME'),
            InstallCode::MISSING_FULLSCREEN_TEMPLATE_FILE => \__('MISSING_FULLSCREEN_TEMPLATE_FILE'),
            InstallCode::INVALID_FRONTEND_LINK_TEMPLATE_FULLSCREEN_TEMPLATE
            => \__('INVALID_FRONTEND_LINK_TEMPLATE_FULLSCREEN_TEMPLATE'),
            InstallCode::MISSING_BOX => \__('MISSING_BOX'),
            InstallCode::INVALID_BOX_NAME => \__('INVALID_BOX_NAME'),
            InstallCode::INVALID_BOX_TEMPLATE => \__('INVALID_BOX_TEMPLATE'),
            InstallCode::MISSING_BOX_TEMPLATE_FILE => \__('MISSING_BOX_TEMPLATE_FILE'),
            InstallCode::MISSING_LICENCE_FILE => \__('MISSING_LICENCE_FILE'),
            InstallCode::INVALID_LICENCE_FILE_NAME => \__('INVALID_LICENCE_FILE_NAME'),
            InstallCode::MISSING_LICENCE => \__('MISSING_LICENCE'),
            InstallCode::MISSING_LICENCE_CHECKLICENCE_METHOD => \__('MISSING_LICENCE_CHECKLICENCE_METHOD'),
            InstallCode::DUPLICATE_PLUGIN_ID => \__('DUPLICATE_PLUGIN_ID'),
            InstallCode::MISSING_EMAIL_TEMPLATES => \__('MISSING_EMAIL_TEMPLATES'),
            InstallCode::INVALID_TEMPLATE_NAME => \__('INVALID_TEMPLATE_NAME'),
            InstallCode::INVALID_TEMPLATE_TYPE => \__('INVALID_TEMPLATE_TYPE'),
            InstallCode::INVALID_TEMPLATE_MODULE_ID => \__('INVALID_TEMPLATE_MODULE_ID'),
            InstallCode::INVALID_TEMPLATE_ACTIVE => \__('INVALID_TEMPLATE_ACTIVE'),
            InstallCode::INVALID_TEMPLATE_AKZ => \__('INVALID_TEMPLATE_AKZ'),
            InstallCode::INVALID_TEMPLATE_AGB => \__('INVALID_TEMPLATE_AGB'),
            InstallCode::INVALID_TEMPLATE_WRB => \__('INVALID_TEMPLATE_WRB'),
            InstallCode::INVALID_EMAIL_TEMPLATE_ISO => \__('INVALID_EMAIL_TEMPLATE_ISO'),
            InstallCode::INVALID_EMAIL_TEMPLATE_SUBJECT => \__('INVALID_EMAIL_TEMPLATE_SUBJECT'),
            InstallCode::MISSING_EMAIL_TEMPLATE_LANGUAGE => \__('MISSING_EMAIL_TEMPLATE_LANGUAGE'),
            InstallCode::INVALID_CHECKBOX_FUNCTION_NAME => \__('INVALID_CHECKBOX_FUNCTION_NAME'),
            InstallCode::INVALID_CHECKBOX_FUNCTION_ID => \__('INVALID_CHECKBOX_FUNCTION_ID'),
            InstallCode::INVALID_FRONTEND_LINK_NO_FOLLOW => \__('INVALID_FRONTEND_LINK_NO_FOLLOW'),
            InstallCode::MISSING_WIDGETS => \__('MISSING_WIDGETS'),
            InstallCode::INVALID_WIDGET_TITLE => \__('INVALID_WIDGET_TITLE'),
            InstallCode::INVALID_WIDGET_CLASS => \__('INVALID_WIDGET_CLASS'),
            InstallCode::MISSING_WIDGET_CLASS_FILE => \__('MISSING_WIDGET_CLASS_FILE'),
            InstallCode::INVALID_WIDGET_CONTAINER => \__('INVALID_WIDGET_CONTAINER'),
            InstallCode::INVALID_WIDGET_POS => \__('INVALID_WIDGET_POS'),
            InstallCode::INVALID_WIDGET_EXPANDED => \__('INVALID_WIDGET_EXPANDED'),
            InstallCode::INVALID_WIDGET_ACTIVE => \__('INVALID_WIDGET_ACTIVE'),
            InstallCode::INVALID_PAYMENT_METHOD_ADDITIONAL_STEP_TEMPLATE_FILE
            => \__('INVALID_PAYMENT_METHOD_ADDITIONAL_STEP_TEMPLATE_FILE'),
            InstallCode::MISSING_PAYMENT_METHOD_ADDITIONAL_STEP_FILE
            => \__('MISSING_PAYMENT_METHOD_ADDITIONAL_STEP_FILE'),
            InstallCode::MISSING_FORMATS => \__('MISSING_FORMATS'),
            InstallCode::INVALID_FORMAT_NAME => \__('INVALID_FORMAT_NAME'),
            InstallCode::INVALID_FORMAT_FILE_NAME => \__('INVALID_FORMAT_FILE_NAME'),
            InstallCode::MISSING_FORMAT_CONTENT => \__('MISSING_FORMAT_CONTENT'),
            InstallCode::INVALID_FORMAT_ENCODING => \__('INVALID_FORMAT_ENCODING'),
            InstallCode::INVALID_FORMAT_SHIPPING_COSTS_DELIVERY_COUNTRY
            => \__('INVALID_FORMAT_SHIPPING_COSTS_DELIVERY_COUNTRY'),
            InstallCode::INVALID_FORMAT_CONTENT_FILE => \__('INVALID_FORMAT_CONTENT_FILE'),
            InstallCode::MISSING_EXTENDED_TEMPLATE => \__('MISSING_EXTENDED_TEMPLATE'),
            InstallCode::INVALID_EXTENDED_TEMPLATE_FILE_NAME => \__('INVALID_EXTENDED_TEMPLATE_FILE_NAME'),
            InstallCode::MISSING_EXTENDED_TEMPLATE_FILE => \__('MISSING_EXTENDED_TEMPLATE_FILE'),
            InstallCode::MISSING_UNINSTALL_FILE => \__('MISSING_UNINSTALL_FILE'),
            InstallCode::IONCUBE_REQUIRED => \__('IONCUBE_REQUIRED'),
            InstallCode::INVALID_OPTIONS_SOURE_FILE => \__('INVALID_OPTIONS_SOURE_FILE'),
            InstallCode::MISSING_OPTIONS_SOURE_FILE => \__('MISSING_OPTIONS_SOURE_FILE'),
            InstallCode::MISSING_BOOTSTRAP_CLASS => \__('MISSING_BOOTSTRAP_CLASS'),
            InstallCode::INVALID_BOOTSTRAP_IMPLEMENTATION => \__('INVALID_BOOTSTRAP_IMPLEMENTATION'),
            InstallCode::INVALID_AUTHOR => \__('INVALID_AUTHOR'),
            InstallCode::MISSING_PORTLETS => \__('MISSING_PORTLETS'),
            InstallCode::INVALID_PORTLET_TITLE => \__('INVALID_PORTLET_TITLE'),
            InstallCode::INVALID_PORTLET_CLASS => \__('INVALID_PORTLET_CLASS'),
            InstallCode::INVALID_PORTLET_CLASS_FILE => \__('INVALID_PORTLET_CLASS_FILE'),
            InstallCode::INVALID_PORTLET_GROUP => \__('INVALID_PORTLET_GROUP'),
            InstallCode::INVALID_PORTLET_ACTIVE => \__('INVALID_PORTLET_ACTIVE'),
            InstallCode::MISSING_BLUEPRINTS => \__('MISSING_BLUEPRINTS'),
            InstallCode::INVALID_BLUEPRINT_NAME => \__('INVALID_BLUEPRINT_NAME'),
            InstallCode::INVALID_BLUEPRINT_FILE => \__('INVALID_BLUEPRINT_FILE'),
            InstallCode::PORTLET_CLASS_NAME_EXISTS => \__('PORTLET_CLASS_NAME_EXISTS'),
            InstallCode::EXT_MUST_NOT_HAVE_UNINSTALLER => \__('EXT_MUST_NOT_HAVE_UNINSTALLER'),
            InstallCode::WRONG_EXT_DIR => \__('WRONG_EXT_DIR'),
            InstallCode::MISSING_PLUGIN_NODE => \__('MISSING_PLUGIN_NODE'),
            InstallCode::OK_LEGACY => \__('OK_LEGACY'),
            InstallCode::SQL_MISSING_DATA => \__('SQL_MISSING_DATA'),
            InstallCode::SQL_ERROR => \__('SQL_ERROR'),
            InstallCode::SQL_WRONG_TABLE_NAME_DELETE => \__('SQL_WRONG_TABLE_NAME_DELETE'),
            InstallCode::SQL_WRONG_TABLE_NAME_CREATE => \__('SQL_WRONG_TABLE_NAME_CREATE'),
            InstallCode::SQL_INVALID_FILE_CONTENT => \__('SQL_INVALID_FILE_CONTENT'),
            InstallCode::SQL_CANNOT_SAVE_HOOK => \__('SQL_CANNOT_SAVE_HOOK'),
            InstallCode::SQL_CANNOT_SAVE_UNINSTALL => \__('SQL_CANNOT_SAVE_UNINSTALL'),
            InstallCode::SQL_CANNOT_SAVE_ADMIN_MENU_ITEM => \__('SQL_CANNOT_SAVE_ADMIN_MENU_ITEM'),
            InstallCode::SQL_CANNOT_SAVE_SETTINGS_ITEM => \__('SQL_CANNOT_SAVE_SETTINGS_ITEM'),
            InstallCode::SQL_CANNOT_SAVE_SETTING => \__('SQL_CANNOT_SAVE_SETTING'),
            InstallCode::SQL_CANNOT_FIND_LINK_GROUP => \__('SQL_CANNOT_FIND_LINK_GROUP'),
            InstallCode::SQL_CANNOT_SAVE_LINK => \__('SQL_CANNOT_SAVE_LINK'),
            InstallCode::SQL_CANNOT_SAVE_PAYMENT_METHOD => \__('SQL_CANNOT_SAVE_PAYMENT_METHOD'),
            InstallCode::SQL_CANNOT_SAVE_PAYMENT_METHOD_LOCALIZATION => \__('SQL_CANNOT_SAVE_PAYMENT_METHOD_LOCALIZATION'),
            InstallCode::SQL_CANNOT_SAVE_PAYMENT_METHOD_LANGUAGE => \__('SQL_CANNOT_SAVE_PAYMENT_METHOD_LANGUAGE'),
            InstallCode::SQL_CANNOT_SAVE_PAYMENT_METHOD_SETTING => \__('SQL_CANNOT_SAVE_PAYMENT_METHOD_SETTING'),
            InstallCode::SQL_CANNOT_SAVE_BOX_TEMPLATE => \__('SQL_CANNOT_SAVE_BOX_TEMPLATE'),
            InstallCode::SQL_CANNOT_SAVE_TEMPLATE => \__('SQL_CANNOT_SAVE_TEMPLATE'),
            InstallCode::SQL_CANNOT_SAVE_EMAIL_TEMPLATE => \__('SQL_CANNOT_SAVE_EMAIL_TEMPLATE'),
            InstallCode::SQL_CANNOT_SAVE_LANG_VAR => \__('SQL_CANNOT_SAVE_LANG_VAR'),
            InstallCode::SQL_CANNOT_SAVE_LANG_VAR_LOCALIZATION => \__('SQL_CANNOT_SAVE_LANG_VAR_LOCALIZATION'),
            InstallCode::SQL_CANNOT_SAVE_WIDGET => \__('SQL_CANNOT_SAVE_WIDGET'),
            InstallCode::SQL_CANNOT_SAVE_PORTLET => \__('SQL_CANNOT_SAVE_PORTLET'),
            InstallCode::SQL_CANNOT_SAVE_BLUEPRINT => \__('SQL_CANNOT_SAVE_BLUEPRINT'),
            InstallCode::SQL_CANNOT_SAVE_EXPORT => \__('SQL_CANNOT_SAVE_EXPORT'),
            InstallCode::SQL_CANNOT_SAVE_INPUT_TYPE => \__('SQL_CANNOT_SAVE_INPUT_TYPE'),
            InstallCode::INVALID_STORE_ID => \__('INVALID_STORE_ID'),
            InstallCode::SQL_CANNOT_SAVE_VENDOR => \__('SQL_CANNOT_SAVE_VENDOR'),
            InstallCode::MISSING_CONSENT_VENDOR => \__('MISSING_CONSENT_VENDOR'),
            InstallCode::INVALID_CONSENT_VENDOR_NAME => \__('INVALID_CONSENT_VENDOR_NAME'),
            InstallCode::INVALID_CONSENT_VENDOR_PURPOSE => \__('INVALID_CONSENT_VENDOR_PURPOSE'),
            InstallCode::INVALID_CONSENT_VENDOR_LOCALIZATION => \__('INVALID_CONSENT_VENDOR_LOCALIZATION'),
            InstallCode::INVALID_CONSENT_VENDOR_LOCALIZATION_ISO => \__('INVALID_CONSENT_VENDOR_LOCALIZATION_ISO'),
            InstallCode::INVALID_CONSENT_VENDOR_DESCRIPTION => \__('INVALID_CONSENT_VENDOR_DESCRIPTION'),
            InstallCode::INVALID_CONSENT_VENDOR_PRIV_POL => \__('INVALID_CONSENT_VENDOR_PRIV_POL'),
            InstallCode::INVALID_CONSENT_VENDOR_ID => \__('INVALID_CONSENT_VENDOR_ID'),
            InstallCode::INVALID_CONSENT_VENDOR_COMPANY => \__('INVALID_CONSENT_VENDOR_COMPANY'),
            InstallCode::INVALID_LINK_IDENTIFIER => \__('INVALID_LINK_IDENTIFIER'),
            InstallCode::INVALID_MIGRATION => \__('INVALID_MIGRATION'),
            default => \__('unknownError'),
        };

        return \str_replace('%cPluginID%', $pluginID ?? '', $return);
    }
}
