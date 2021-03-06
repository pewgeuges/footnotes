<?php
/**
 * 
 * @filesource
 * @author Stefan Herndler
 * @since 1.5.0 14.09.14 17:47
 */

/**
 *
 * @author Stefan Herndler
 * @since 1.5.0
 */
class MCI_Footnotes_Language {

	/**
	 * Register WordPress Hook.
	 *
	 * @author Stefan Herndler
	 * @since 1.5.0
	 */
	public static function registerHooks() {
		add_action('plugins_loaded', array("MCI_Footnotes_Language", "loadTextDomain"));
	}

	/**
	 * Loads the text domain for current WordPress language if exists. Otherwise fallback "en_GB" will be loaded.
	 *
	 * @author Stefan Herndler
	 * @since 1.5.0
	 */
	public static function loadTextDomain() {
		// language file with localization exists
		if (self::load(apply_filters('plugin_locale', get_locale(), ''))) {
			// added 3rd (empty) parameter as a PHP-related bug fix thanks to MatKus (@matkus) in
			// <https://wordpress.org/support/topic/error-missing-parameter-if-using-php-7-1-or-later/>
			// <https://www.php.net/manual/en/migration71.incompatible.php>
			// "Fatal error: Uncaught ArgumentCountError: Too few arguments […]"
			// 2020-10-26T1609+0100
			return;
		}
		// fallback to english
		self::load("en_GB");
	}

	/**
	 * Loads a specific text domain.
	 *
	 * @author Stefan Herndler
	 * @since 1.5.1
	 * @param string $p_str_LanguageCode Language Code to load a specific text domain.
	 * @return bool
	 */
	private static function load($p_str_LanguageCode) {
		return load_textdomain(MCI_Footnotes_Config::C_STR_PLUGIN_NAME,
			dirname(__FILE__) . "/../languages/" . $p_str_LanguageCode . '.mo');
	}
}
