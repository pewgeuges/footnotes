<?php
/**
 * Created by Stefan Herndler.
 * User: Stefan
 * Date: 15.05.14
 * Time: 16:21
 * Version: 1.0
 * Since: 1.0
 */

/*
 * collection of all footnotes found on the current page
 * @since 1.0
 */
$g_arr_Footnotes = array();

/*
 * collection of all footnotes settings
 * @since 1.0-beta
 */
$g_arr_FootnotesSettings = array();

/**
 * register all functions needed for the replacement in the wordpress core
 * @since 1.0-gamma
 */
function footnotes_RegisterReplacementFunctions() {
	/* access to the global settings collection */
	global $g_arr_FootnotesSettings;
	/* load footnote settings */
	$g_arr_FootnotesSettings = footnotes_filter_options( FOOTNOTE_SETTINGS_CONTAINER, Class_FootnotesSettings::$a_arr_Default_Settings, false );
	/* get setting for accepting footnotes in the excerpt and convert it to boolean */
	$l_bool_SearchExcerpt = footnotes_ConvertToBool($g_arr_FootnotesSettings[ FOOTNOTE_INPUTFIELD_SEARCH_IN_EXCERPT ]);

	/* calls the wordpress filter function to replace page content before displayed on public pages */
	add_filter( 'the_content', 'footnotes_startReplacing' );
	/* search in the excerpt only if activated */
	if ($l_bool_SearchExcerpt) {
		add_filter( 'the_excerpt', 'footnotes_DummyReplacing' );
	}

	/* calls the wordpress filter function to replace widget text before displayed on public pages */
	add_filter( 'widget_title', 'footnotes_DummyReplacing' );
	add_filter( 'widget_text', 'footnotes_DummyReplacing' );

	/* calls the wordpress action to display the footer */
	add_action( 'get_footer', 'footnotes_StopReplacing' );

	/* get setting for love and share this plugin and convert it to boolean */
	$l_bool_LoveMe = footnotes_ConvertToBool($g_arr_FootnotesSettings[ FOOTNOTE_INPUTFIELD_LOVE ]);
	/* check if the admin allows to add a link to the footer */
	if ($l_bool_LoveMe) {
		/* calls the wordpress action to hook to the footer */
		add_filter('wp_footer', 'footnotes_LoveAndShareMe', 0);
	}
}

/**
 * starts listening for footnotes to be replaced
 * output will be buffered and not displayed
 * @since 1.0
 * @param string $p_str_Content
 * @return string
 */
function footnotes_startReplacing( $p_str_Content )
{
	/* stop the output and move it to a buffer instead, defines a callback function */
	ob_start( "footnotes_replaceFootnotes" );
	/* return unchanged content */
	return $p_str_Content;
}

/**
 * dummy function to add the content to the buffer instead of output it
 * @since 1.0
 * @param string $p_str_Content
 * @return string
 */
function footnotes_DummyReplacing( $p_str_Content )
{
	/* return unchanged content */
	return $p_str_Content;
}

/**
 * stops buffering the output, automatically calls the ob_start() defined callback function
 * replaces all footnotes in the whole buffer and outputs it
 * @since 1.0
 */
function footnotes_StopReplacing()
{
	/* calls the callback function defined in ob_start(); */
	ob_end_flush();
}

/**
 * outputs a link to love and share this awesome plugin
 * @since 1.0-gamma
 */
function footnotes_LoveAndShareMe()
{
	echo '
		<div style="text-align:center; color:#acacac;">'.
		sprintf(__("Hey there, I'm using the awesome WordPress Plugin called %sfootnotes%s", FOOTNOTES_PLUGIN_NAME), '<a href="https://github.com/media-competence-institute/footnotes" target="_blank">', '</a>').
		'</div>'
	;
}

/**
 * replaces all footnotes in the given content
 * loading settings if not happened yet since 1.0-gamma
 * @since 1.0
 * @param string $p_str_Content
 * @param bool   $p_bool_OutputReferences [default: true]
 * @param bool $p_bool_ReplaceHtmlChars [ default: false]
 * @return string
 */
function footnotes_replaceFootnotes( $p_str_Content, $p_bool_OutputReferences = true, $p_bool_ReplaceHtmlChars = false )
{
	/* get access to the global array */
	global $g_arr_Footnotes;
	/* access to the global settings collection */
	global $g_arr_FootnotesSettings;
	/* load footnote settings */
	$g_arr_FootnotesSettings = footnotes_filter_options( FOOTNOTE_SETTINGS_CONTAINER, Class_FootnotesSettings::$a_arr_Default_Settings, $p_bool_ReplaceHtmlChars );
	/* replace all footnotes in the content */
	$p_str_Content = footnotes_getFromString( $p_str_Content );

	/* add the reference list if set */
	if ( $p_bool_OutputReferences ) {
		$p_str_Content = $p_str_Content . footnotes_OutputReferenceContainer();
		/* free all found footnotes if reference container will be displayed */
		$g_arr_Footnotes = array();
	}
	/* return the replaced content */
	return $p_str_Content;
}

/**
 * replace all footnotes in the given string and adds them to an array
 * using a personal starting and ending tag for the footnotes since 1.0-gamma
 * @since 1.0
 * @param string $p_str_Content
 * @return string
 */
function footnotes_getFromString( $p_str_Content )
{
	/* get access to the global array to store footnotes */
	global $g_arr_Footnotes;
	/* access to the global settings collection */
	global $g_arr_FootnotesSettings;
	/* contains the index for the next footnote on this page */
	$l_int_FootnoteIndex = 1;
	/* contains the starting position for the lookup of a footnote */
	$l_int_PosStart = 0;
	/* contains the footnote template */
	$l_str_FootnoteTemplate = file_get_contents( FOOTNOTES_TEMPLATES_DIR . "footnote.html" );
	/* get footnote starting tag */
	$l_str_StartingTag = $g_arr_FootnotesSettings[FOOTNOTE_INPUTFIELD_PLACEHOLDER_START];
	/*get footnote ending tag */
	$l_str_EndingTag = $g_arr_FootnotesSettings[FOOTNOTE_INPUTFIELD_PLACEHOLDER_END];
	/*get footnote counter style */
	$l_str_CounterStyle = $g_arr_FootnotesSettings[FOOTNOTE_INPUTFIELD_COUNTER_STYLE];

	/* check for a footnote placeholder in the current page */
	do {
		/* get first occurence of a footnote starting tag */
		$l_int_PosStart = strpos( $p_str_Content, $l_str_StartingTag, $l_int_PosStart );
		/* tag found */
		if ( $l_int_PosStart !== false ) {
			/* get first occurence of a footnote ending tag after the starting tag */
			$l_int_PosEnd = strpos( $p_str_Content, $l_str_EndingTag, $l_int_PosStart );
			/* tag found */
			if ( $l_int_PosEnd !== false ) {
				/* get length of footnote text */
				$l_int_Length = $l_int_PosEnd - $l_int_PosStart;
				/* get text inside footnote */
				$l_str_FootnoteText = substr( $p_str_Content, $l_int_PosStart + strlen( $l_str_StartingTag ), $l_int_Length - strlen( $l_str_StartingTag ) );
				/* set replacer for the footnote */
				$l_str_ReplaceText = str_replace( "[[FOOTNOTE INDEX]]", footnote_convert_index($l_int_FootnoteIndex,$l_str_CounterStyle), $l_str_FootnoteTemplate );
				$l_str_ReplaceText = str_replace( "[[FOOTNOTE TEXT]]", $l_str_FootnoteText, $l_str_ReplaceText );
				/* replace footnote in content */
				$p_str_Content = substr_replace( $p_str_Content, $l_str_ReplaceText, $l_int_PosStart, $l_int_Length + strlen( $l_str_EndingTag ) );
				/* set footnote to the output box at the end */
				$g_arr_Footnotes[ ] = $l_str_FootnoteText;
				/* increase footnote index */
				$l_int_FootnoteIndex++;
				/* add offset to the new starting position */
				$l_int_PosStart += ( $l_int_PosEnd - $l_int_PosStart );
				/* no ending tag found */
			} else {
				$l_int_PosStart++;
			}
			/* no starting tag found */
		} else {
			break;
		}
	} while ( true );

	/* return content */
	return $p_str_Content;
}

/**
 * looks through all footnotes that has been replaced in the current content and
 * adds a reference to the footnote at the end of the content
 * function to collapse the reference container since 1.0-beta
 * @since 1.0
 * @return string
 */
function footnotes_OutputReferenceContainer()
{
	/* get access to the global array to read footnotes */
	global $g_arr_Footnotes;
	/* access to the global settings collection */
	global $g_arr_FootnotesSettings;

	/* no footnotes has been replaced on this page */
	if ( empty( $g_arr_Footnotes ) ) {
		/* return empty string */
		return "";
	}

	/* get setting for combine identical footnotes and convert it to boolean */
	$l_bool_CombineIdentical = footnotes_ConvertToBool($g_arr_FootnotesSettings[ FOOTNOTE_INPUTFIELD_COMBINE_IDENTICAL ]);
	/* get setting for preferences label */
	$l_str_ReferencesLabel = $g_arr_FootnotesSettings[ FOOTNOTE_INPUTFIELD_REFERENCES_LABEL ];
	/* get setting for collapse reference footnotes and convert it to boolean */
	$l_bool_CollapseReference = footnotes_ConvertToBool($g_arr_FootnotesSettings[ FOOTNOTE_INPUTFIELD_COLLAPSE_REFERENCES ]);
	/*get footnote counter style */
	$l_str_CounterStyle = $g_arr_FootnotesSettings[FOOTNOTE_INPUTFIELD_COUNTER_STYLE];

	/* output string, prepare it with the reference label as headline */
	$l_str_Output = '<div class="footnote_container_prepare"><p><span onclick="footnote_expand_reference_container(\"\");">' . $l_str_ReferencesLabel . '</span></p></div>';
	/* add a box around the footnotes */
	$l_str_Output .= '<div id="'.FOOTNOTE_REFERENCES_CONTAINER_ID.'"';
	/* add class to hide the references by default, if the user wants it */
	if ($l_bool_CollapseReference) {
		$l_str_Output .= ' class="footnote_hide_box"';
	}
	$l_str_Output .= '>';

	/* contains the footnote template */
	$l_str_FootnoteTemplate = file_get_contents( FOOTNOTES_TEMPLATES_DIR . "container.html" );

	/* loop through all footnotes found in the page */
	for ( $l_str_Index = 0; $l_str_Index < count( $g_arr_Footnotes ); $l_str_Index++ ) {
		/* get footnote text */
		$l_str_FootnoteText = $g_arr_Footnotes[ $l_str_Index ];
		/* if fottnote is empty, get to the next one */
		if ( empty( $l_str_FootnoteText ) ) {
			continue;
		}
		/* get footnote index */
		$l_str_FirstFootnoteIndex = ( $l_str_Index + 1 );
		$l_str_FootnoteIndex = footnote_convert_index(( $l_str_Index + 1 ),$l_str_CounterStyle);

		/* check if it isn't the last footnote in the array */
		if ( $l_str_FirstFootnoteIndex < count( $g_arr_Footnotes ) && $l_bool_CombineIdentical ) {
			/* get all footnotes that I haven't passed yet */
			for ( $l_str_CheckIndex = $l_str_FirstFootnoteIndex; $l_str_CheckIndex < count( $g_arr_Footnotes ); $l_str_CheckIndex++ ) {
				/* check if a further footnote is the same as the actual one */
				if ( $l_str_FootnoteText == $g_arr_Footnotes[ $l_str_CheckIndex ] ) {
					/* set the further footnote as empty so it won't be displayed later */
					$g_arr_Footnotes[ $l_str_CheckIndex ] = "";
					/* add the footnote index to the actual index */
					$l_str_FootnoteIndex .= ", " . footnote_convert_index(( $l_str_CheckIndex + 1 ),$l_str_CounterStyle);
				}
			}
		}

		/* add the footnote to the output box */
		$l_str_ReplaceText = str_replace( "[[FOOTNOTE INDEX SHORT]]", $l_str_FirstFootnoteIndex, $l_str_FootnoteTemplate );
		$l_str_ReplaceText = str_replace( "[[FOOTNOTE INDEX]]", $l_str_FootnoteIndex, $l_str_ReplaceText );
		$l_str_ReplaceText = str_replace( "[[FOOTNOTE TEXT]]", $l_str_FootnoteText, $l_str_ReplaceText );
		/* add the footnote container to the output */
		$l_str_Output = $l_str_Output . $l_str_ReplaceText;
	}
	/* add closing tag for the div of the references container */
	$l_str_Output = $l_str_Output . '</div>';
	/* add a javascript to expand the reference container when clicking on a footnote or the reference label */
	$l_str_Output .= '
		<script type="text/javascript">
			function footnote_expand_reference_container(p_str_ID) {
				jQuery("#'.FOOTNOTE_REFERENCES_CONTAINER_ID.'").show();
				if (p_str_ID.length > 0) {
					jQuery(p_str_ID).focus();
				}
			}
		</script>
	';

	/* return the output string */
	return $l_str_Output;
}