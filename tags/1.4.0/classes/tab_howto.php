<?php
/**
 * Created by Stefan Herndler.
 * User: Stefan
 * Date: 30.07.14 10:53
 * Version: 1.0
 * Since: 1.3
 */


// define class only once
if (!class_exists("MCI_Footnotes_Tab_HowTo")) :

/**
 * Class MCI_Footnotes_Tab_HowTo
 * @since 1.3
 */
class MCI_Footnotes_Tab_HowTo extends MCI_Footnotes_Admin {

	/**
	 * register the meta boxes for the tab
	 * @constructor
	 * @since 1.3
	 * @param array $p_arr_Tabs
	 */
	public function __construct(&$p_arr_Tabs) {
		// add tab to the tab array
		$p_arr_Tabs[FOOTNOTES_SETTINGS_TAB_HOWTO] = __("How to", FOOTNOTES_PLUGIN_NAME);
		// register settings tab
		add_settings_section(
			"MCI_Footnotes_Settings_Section_HowTo",
			"&nbsp;",
			array($this, 'Description'),
			FOOTNOTES_SETTINGS_TAB_HOWTO
		);
		// help
		add_meta_box(
			'MCI_Footnotes_Tab_HowTo_Help',
			__("Brief introduction in how to use the plugin", FOOTNOTES_PLUGIN_NAME),
			array($this, 'Help'),
			FOOTNOTES_SETTINGS_TAB_HOWTO,
			'main'
		);
		// donate
		add_meta_box(
			'MCI_Footnotes_Tab_HowTo_Donate',
			__("Help us to improve our Plugin.", FOOTNOTES_PLUGIN_NAME),
			array($this, 'Donate'),
			FOOTNOTES_SETTINGS_TAB_HOWTO,
			'main'
		);
	}

	/**
	 * output a description for the tab
	 * @since 1.3
	 */
	public function Description() {
		// unused
	}

	/**
	 * outputs the help box
	 * @since 1.3
	 */
	public function Help() {
		global $g_obj_MCI_Footnotes;
		// load footnotes starting and end tag
		$l_arr_Footnote_StartingTag = $this->LoadSetting(FOOTNOTES_INPUT_PLACEHOLDER_START);
		$l_arr_Footnote_EndingTag = $this->LoadSetting(FOOTNOTES_INPUT_PLACEHOLDER_END);

		if ($l_arr_Footnote_StartingTag["value"] == "userdefined" || $l_arr_Footnote_EndingTag["value"] == "userdefined") {
			// load user defined starting and end tag
			$l_arr_Footnote_StartingTag = $this->LoadSetting(FOOTNOTES_INPUT_PLACEHOLDER_START_USERDEFINED);
			$l_arr_Footnote_EndingTag = $this->LoadSetting(FOOTNOTES_INPUT_PLACEHOLDER_END_USERDEFINED);
		}
		$l_str_Example = "Hello" . $l_arr_Footnote_StartingTag["value"] . __("example string", FOOTNOTES_PLUGIN_NAME) . $l_arr_Footnote_EndingTag["value"] . " World!";
		?>
		<div style="text-align:center;">
			<div class="footnote_placeholder_box_container">
				<p>
					<?php echo __("Start your footnote with the following shortcode:", FOOTNOTES_PLUGIN_NAME); ?>
					<span class="footnote_highlight_placeholder">
						<?php echo $l_arr_Footnote_StartingTag["value"]; ?>
					</span>
				</p>
				<p>
					<?php echo __("...and end your footnote with this shortcode:", FOOTNOTES_PLUGIN_NAME); ?>
					<span class="footnote_highlight_placeholder">
						<?php echo $l_arr_Footnote_EndingTag["value"]; ?>
					</span>
				</p>
				<div class="footnote_placeholder_box_example">
					<p>
                        <span class="footnote_highlight_placeholder"><?php echo $l_str_Example; ?></span>
						<?php echo __("will be displayed as:", FOOTNOTES_PLUGIN_NAME); ?>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<?php echo $g_obj_MCI_Footnotes->a_obj_Task->exec($l_str_Example, true); ?>
					</p>
				</div>
				<p>
					<?php echo sprintf(__("For further information please check out our %ssupport forum%s on WordPress.org.", FOOTNOTES_PLUGIN_NAME), '<a href="http://wordpress.org/support/plugin/footnotes" target="_blank" class="footnote_plugin">', '</a>'); ?>
				</p>
			</div>
		</div>
	<?php
	}

	/**
	 * outputs the diagnostics
	 * @since 1.4.0
	 */
	public function Donate() {
		$l_str_Url = "https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6Z6CZDW8PPBBJ";
		echo '<input type="button" class="button button-primary" value="'.__('Donate now',FOOTNOTES_PLUGIN_NAME).'" onclick="window.open(\''.$l_str_Url.'\');" />';
	}
} // class MCI_Footnotes_Tab_HowTo

endif;