<?php
/**
 * Created by Stefan Herndler.
 * User: Stefan
 * Date: 15.05.14
 * Time: 16:21
 * Version: 1.0
 * Since: 1.0
 */


/**
 * loads the langauge file including localization if exists
 * otherwise loads the langauge file without localization information
 * @since 1.0
 */
function footnotes_load_language()
{
    /* read current wordpress langauge */
    $l_str_locale = apply_filters('plugin_locale', get_locale(), FOOTNOTES_PLUGIN_NAME);
    /* get only language code (removed localization code) */
    $l_str_languageCode = footnotes_getLanguageCode();

    /* language file with localization exists */
    if ($l_bool_loaded = load_textdomain(FOOTNOTES_PLUGIN_NAME, FOOTNOTES_LANGUAGE_DIR . FOOTNOTES_PLUGIN_NAME . '-' . $l_str_locale . '.mo')) {

        /* language file without localization exists */
    } else if ($l_bool_loaded = load_textdomain(FOOTNOTES_PLUGIN_NAME, FOOTNOTES_LANGUAGE_DIR . FOOTNOTES_PLUGIN_NAME . '-' . $l_str_languageCode . '.mo')) {

        /* load default language file, nothing will happen: default language will be used (=english) */
    } else {
        load_textdomain(FOOTNOTES_PLUGIN_NAME, FOOTNOTES_LANGUAGE_DIR . FOOTNOTES_PLUGIN_NAME . '-en.mo');
    }
}

/**
 * reads the wordpress langauge and returns only the language code lowercase
 * removes the localization code
 * @since 1.0
 * @return string (only the "en" from "en_US")
 */
function footnotes_getLanguageCode()
{
    /* read current wordpress langauge */
    $l_str_locale = apply_filters('plugin_locale', get_locale(), FOOTNOTES_PLUGIN_NAME);
    /* check if wordpress language has a localization (e.g. "en_US" or "de_AT") */
    if (strpos($l_str_locale, "_") !== false) {
        /* remove localization code */
        $l_arr_languageCode = explode("_", $l_str_locale);
        $l_str_languageCode = $l_arr_languageCode[0];
        return $l_str_languageCode;
    }
    /* return language code lowercase */
    return strtolower($l_str_locale);
}