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
 * Class Class_FootnotesSettings
 * @since 1.0
 */
class Class_FootnotesSettings
{
	/*
	 * attribute for default settings value
	 * @since 1.0
	 */
	public static $a_arr_Default_Settings = array(
		FOOTNOTE_INPUTFIELD_COMBINE_IDENTICAL => 'yes',
		FOOTNOTE_INPUTFIELD_REFERENCES_LABEL  => 'References'
	);
	/*
	 * resulting pagehook for adding a new sub menu page to the settings
	 * @since 1.0
	 */
	var $a_str_Pagehook;
	/*
	 * collection of settings values for this plugin
	 * @since 1.0
	 */
	var $a_arr_Options;
	/*
	 * collection of tabs for the settings page of this plugin
	 * @since 1.0
	 */
	private $a_arr_SettingsTabs = array();

	/**
	 * @constructor
	 * @since 1.0
	 */
	function __construct()
	{
		/* loads and filters the settings for this plugin */
		$this->a_arr_Options = footnote_filter_options( FOOTNOTE_SETTINGS_CONTAINER );
		/* validates the settings of the plugin and replaces them with the default settings if invalid */
		add_option( FOOTNOTE_SETTINGS_CONTAINER, self::$a_arr_Default_Settings );

		/* execute class includes on action-even: init, admin_init and admin_menu */
		add_action( 'init', array( $this, 'LoadScriptsAndStylesheets' ) );
		add_action( 'admin_init', array( $this, 'RegisterSettings' ) );

		add_action( 'admin_init', array( $this, 'RegisterTab_General' ) );
		add_action( 'admin_init', array( $this, 'RegisterTab_HowTo' ) );

		add_action( 'admin_menu', array( $this, 'AddSettingsMenuPanel' ) );
	}

	/**
	 * initialize settings page, loads scripts and stylesheets needed for the layout
	 * called in class constructor @ init
	 * @since 1.0
	 */
	function LoadScriptsAndStylesheets()
	{
		/* add the jQuery plugin (already registered by WP) */
		wp_enqueue_script( 'jquery' );
		/* register public stylesheet */
		wp_register_style( 'footnote_public_style', plugins_url( '../css/footnote.css', __FILE__ ) );
		/* add public stylesheet */
		wp_enqueue_style( 'footnote_public_style' );
		/* register settings stylesheet */
		wp_register_style( 'footnote_settings_style', plugins_url( '../css/settings.css', __FILE__ ) );
		/* add settings stylesheet */
		wp_enqueue_style( 'footnote_settings_style' );
	}

	/**
	 * register the settings field in the database for the "save" function
	 * called in class constructor @ admin_init
	 * @since 1.0
	 */
	function RegisterSettings()
	{
		register_setting( FOOTNOTE_SETTINGS_LABEL_GENERAL, FOOTNOTE_SETTINGS_CONTAINER );
	}

	/**
	 * sets the plugin's title for the admins settings menu
	 * called in class constructor @ admin_menu
	 * @since 1.0
	 */
	function AddSettingsMenuPanel()
	{
		/* current user needs the permission to update plugins for further access */
		if ( !current_user_can( 'update_plugins' ) ) {
			return;
		}
		/* submenu page title */
		$l_str_PageTitle = 'footnotes';
		/* submenu title */
		$l_str_MenuTitle = 'footnotes';
		/* Add a new submenu to the standard Settings panel */
		$this->a_str_Pagehook = add_options_page( $l_str_PageTitle, $l_str_MenuTitle, 'administrator', FOOTNOTES_SETTINGS_PAGE_ID, array( $this, 'OutputSettingsPage' ) );
	}

	/**
	 * Plugin Options page rendering goes here, checks
	 * for active tab and replaces key with the related
	 * settings key. Uses the plugin_options_tabs method
	 * to render the tabs.
	 * @since 1.0
	 */
	function OutputSettingsPage()
	{
		/* gets active tag, or if nothing set the "general" tab will be set to active */
		$l_str_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : FOOTNOTE_SETTINGS_LABEL_GENERAL;
		/* outputs all tabs */
		echo '<div class="wrap">';
		$this->OutputSettingsPageTabs();
		/* outputs a form with the content of the current active tab */
		echo '<form method="post" action="options.php">';
		wp_nonce_field( 'update-options' );
		settings_fields( $l_str_tab );
		/* outputs the settings field of the current active tab */
		do_settings_sections( $l_str_tab );
		/* adds a submit button to the current page */
		submit_button();
		echo '</form>';
		echo '</div>';
	}

	/**
	 * Renders our tabs in the plugin options page,
	 * walks through the object's tabs array and prints
	 * them one by one. Provides the heading for the
	 * plugin_options_page method.
	 * @since 1.0
	 */
	function OutputSettingsPageTabs()
	{
		/* gets active tag, or if nothing set the "general" tab will be set to active */
		$l_str_CurrentTab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : FOOTNOTE_SETTINGS_LABEL_GENERAL;
		screen_icon();
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->a_arr_SettingsTabs as $l_str_TabKey => $l_str_TabCaption ) {
			$active = $l_str_CurrentTab == $l_str_TabKey ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . $active . '" href="?page=' . FOOTNOTES_SETTINGS_PAGE_ID . '&tab=' . $l_str_TabKey . '">' . $l_str_TabCaption . '</a>';
		}
		echo '</h2>';
	}

	/**
	 * loads specific setting and returns an array with the keys [id, name, value]
	 * @since 1.0
	 * @param $p_str_FieldID
	 * @return array
	 */
	protected function LoadSetting( $p_str_FieldID )
	{
		$p_arr_Return = array();
		$p_arr_Return[ "id" ] = $this->getFieldID( $p_str_FieldID );
		$p_arr_Return[ "name" ] = $this->getFieldName( $p_str_FieldID );
		$p_arr_Return[ "value" ] = esc_attr( $this->getFieldValue( $p_str_FieldID ) );
		return $p_arr_Return;
	}

	/**
	 * access settings field by name
	 * @since 1.0
	 * @param string $p_str_FieldName
	 * @return string
	 */
	protected function getFieldName( $p_str_FieldName )
	{
		return sprintf( '%s[%s]', FOOTNOTE_SETTINGS_CONTAINER, $p_str_FieldName );
	}

	/**
	 * access settings field by id
	 * @since 1.0
	 * @param string $p_str_FieldID
	 * @return string
	 */
	protected function getFieldID( $p_str_FieldID )
	{
		return sprintf( '%s[%s]', FOOTNOTE_SETTINGS_CONTAINER, $p_str_FieldID );

	}

	/**
	 * get settings field value
	 * @since 1.0
	 * @param string $p_str_Key
	 * @return string
	 */
	protected function getFieldValue( $p_str_Key )
	{
		return $this->a_arr_Options[ $p_str_Key ];
	}

	/**
	 * initialize general settings tab
	 * called in class constructor @ admin_init
	 * @since 1.0
	 */
	function RegisterTab_General()
	{
		$l_str_SectionName = "Footnote_Secion_Settings_General";
		/* add tab to the tab array */
		$this->a_arr_SettingsTabs[ FOOTNOTE_SETTINGS_LABEL_GENERAL ] = __( "General", FOOTNOTES_PLUGIN_NAME );
		/* register settings tab */
		add_settings_section( $l_str_SectionName, __( "Settings", FOOTNOTES_PLUGIN_NAME ), array( $this, 'RegisterTab_General_Description' ), FOOTNOTE_SETTINGS_LABEL_GENERAL );
		add_settings_field( 'Register_References_Label', __( "References label:", FOOTNOTES_PLUGIN_NAME ), array( $this, 'Register_References_Label' ), FOOTNOTE_SETTINGS_LABEL_GENERAL, $l_str_SectionName );
		add_settings_field( 'Register_Combine_Identical', __( "Combine identical footnotes:", FOOTNOTES_PLUGIN_NAME ), array( $this, 'Register_Combine_Identical' ), FOOTNOTE_SETTINGS_LABEL_GENERAL, $l_str_SectionName );
	}

	/**
	 * adds a desciption to the general settings tab
	 * called in RegisterTab_General
	 * @since 1.0
	 */
	function RegisterTab_General_Description()
	{
		// unused description
	}

	/**
	 * outputs the settings field for the "references label"
	 * @since 1.0
	 */
	function Register_References_Label()
	{
		/* collect data for "combine identical" */
		$l_arr_Data = $this->LoadSetting( FOOTNOTE_INPUTFIELD_REFERENCES_LABEL );
		?>
		<input class="footnote_plugin_50"
			   type="text"
			   name="<?php echo $l_arr_Data[ "name" ]; ?>"
			   id="<?php echo $l_arr_Data[ "id" ]; ?>"
			   value="<?php echo $l_arr_Data[ "value" ]; ?>"/>
	<?php
	}

	/**
	 * outputs the settings field for the "combine identical footnotes"
	 * @since 1.0
	 */
	function Register_Combine_Identical()
	{
		/* collect data for "combine identical" */
		$l_arr_Data = $this->LoadSetting( FOOTNOTE_INPUTFIELD_COMBINE_IDENTICAL );
		?>
		<select class="footnote_plugin_25"
				id="<?php echo $l_arr_Data[ "id" ]; ?>"
				name="<?php echo $l_arr_Data[ "name" ]; ?>">

			<option value="yes"><?php echo __( "Yes", FOOTNOTES_PLUGIN_NAME ); ?></option>
			<option value="no"><?php echo __( "No", FOOTNOTES_PLUGIN_NAME ); ?></option>

		</select>

		<script type="text/javascript">
			jQuery(document).ready(function () {
				jQuery("#<?php echo $l_arr_Data["id"]; ?> option[value='<?php echo $l_arr_Data["value"]; ?>']").prop("selected", true);
			});
		</script>
	<?php
	}

	/**
	 * initialize howto settings tab
	 * called in class constructor @ admin_init
	 * @since 1.0
	 */
	function RegisterTab_HowTo()
	{
		$l_str_SectionName = "Footnote_Secion_Settings_Howto";
		/* add tab to the tab array */
		$this->a_arr_SettingsTabs[ FOOTNOTE_SETTINGS_LABEL_HOWTO ] = __( "HowTo", FOOTNOTES_PLUGIN_NAME );
		/* register settings tab */
		add_settings_section( $l_str_SectionName, __( "HowTo", FOOTNOTES_PLUGIN_NAME ), array( $this, 'RegisterTab_HowTo_Description' ), FOOTNOTE_SETTINGS_LABEL_HOWTO );
		add_settings_field( 'Register_Howto_Box', "", array( $this, 'Register_Howto_Box' ), FOOTNOTE_SETTINGS_LABEL_HOWTO, $l_str_SectionName );
	}

	/**
	 * adds a descrption to the HowTo settings tab
	 * called int RegisterTab_HowTo
	 * @since 1.0
	 */
	function RegisterTab_HowTo_Description()
	{
		echo __( "This is a brief introduction in how to use the plugin.", FOOTNOTES_PLUGIN_NAME );
	}

	/**
	 * outputs the content of the HowTo settings tab
	 * @since 1.0
	 */
	function Register_Howto_Box()
	{
		?>
		<div style="text-align:center;">
			<div class="footnote_placeholder_box_container">
				<p>
					<?php echo __( "Start your footnote with the following shortcode:", FOOTNOTES_PLUGIN_NAME ); ?>
					<span class="footnote_highlight_placeholder"><?php echo FOOTNOTE_PLACEHOLDER_START; ?></span>
				</p>

				<p>
					<?php echo __( "...and end your footnote with this shortcode:", FOOTNOTES_PLUGIN_NAME ); ?>
					<span class="footnote_highlight_placeholder"><?php echo FOOTNOTE_PLACEHOLDER_END; ?></span>
				</p>

				<div class="footnote_placeholder_box_example">
					<p>
						<span class="footnote_highlight_placeholder"><?php echo FOOTNOTE_PLACEHOLDER_START . __( "example string", FOOTNOTES_PLUGIN_NAME ) . FOOTNOTE_PLACEHOLDER_END; ?></span>
						<?php echo __( "will be displayed as:", FOOTNOTES_PLUGIN_NAME ); ?>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<?php echo footnotes_replaceFootnotes( FOOTNOTE_PLACEHOLDER_START . __( "example string", FOOTNOTES_PLUGIN_NAME ) . FOOTNOTE_PLACEHOLDER_END, true ); ?>
					</p>
				</div>

				<p>
					<?php echo sprintf( __( "If you have any questions, please don't hesitate to %smail us%s.", FOOTNOTES_PLUGIN_NAME ), '<a href="mailto:admin@herndler.org" class="footnote_plugin">', '</a>' ); ?>
				</p>
			</div>
		</div>
	<?php
	}
} /* Class Class_FootnotesSettings */