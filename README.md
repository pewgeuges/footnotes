![footnotes](https://raw.githubusercontent.com/media-competence-institute/footnotes/master/assets/footnotes.png)

**footnotes** WordPress Plugin

# About this fork

The codebase of the `footnotes` WP plugin has been forked in this repository to fix major bugs that appeared:
1. when using the plugin;
2. when assessing its #1 rank among all WP plugins for footnotes;
3. when writing up a method to get a functional WordPress site without delay;
4. when answering the topmost open threads on WordPress.org Support;
5. when getting, in that process, to 2 threads about upgrading an included obsolete jQuery library.

#5 got me to speed up and submit a new version right away.

## Most visible enhancement

The extra **symbol for backlinks** taking up a column next to the footnote IDs in the reference container has been removed, and its hyperlink has been moved to the reference number. The logic is usual and straightforward:
1. In the text, a click on the index number takes the reader to the footnote;
2. In the list, a click on the footnote number takes the reader back to the text.

This removal is designed to make `footnotes` state-of-the-art and align it with the best and widespread layout practice on the marketplace.

Given this change may be considered a disruption, it requires incrementing the major version number according to semantic versioning. Hence this release is v2.0.0.

### More about the background

First I lived with the extra backlink icons, using ▲ for the purpose. Then I made them half transparent and overlaid them over the footnote numbers. That looked cool and came close to what I was accustomed to, from Wikipedia and everywhere. Once a knowledgeable person to whom I shared a post wrote me that those icons are hiding the numbers and I should remove them. So I instantly removed the icons and moved the backlinks to the numbers, thanking him much.

When I got to write up a WordPress method and needed to assess whether there is some better footnotes plugin, I tried out literally every free footnotes plugin available on WordPress.org. While testing, I found some serious bugs in “`footnotes`” and quickly fixed them as part of the WordPress method I was putting together. At some point, when almost everything was ready, I realized just how unprofessional I acted in suggesting a bunch of hacks and workarounds instead of sitting down and putting everything on GitHub, then sending pull requests so as to properly fix the plugins for everyone. I understood that I couldn’t postpone that in favor of more urgent tasks. The urgency is here.

### Rationale

I consider that backlink icons are an outdated feature that makes any website look unprofessional. The backlinks belong in the footnote numbers, and even better, in their table cell for easier clicking.

Backlink icons only clutter the reference container and should absolutely be avoided. That’s the reason I completely eliminated any code referring to them, not even trying to add a setting making them optional. Backlink icons are simply no option for a professional footnotes plugin.

Most if not all other plugins append the backlinks to the footnote text. `footnotes` went one step further by aligning the backlinks next to the footnote numbers. When backlink icons are close to the numbers, it becomes clear to everyone that there is something wrong in that the idea wasn’t carried out. Completing the move is really easy, fully backwards compatible and a good deal of UX enhancement.

## Infrastructure updates

### Upgrade jQuery library

As @vonpiernik advised in https://wordpress.org/support/topic/tooltip-hover-not-showing/#post-13456762, the jQueryUI library has been linked, jquery.tools.min.js has been fixed, and the tooltip infobox tested in WordPress v5.5.1, where these mouse-over boxes have started showing up again.

### Account for disruptive PHP change

MatKus advised in https://wordpress.org/support/topic/error-missing-parameter-if-using-php-7-1-or-later/ that most recent PHP versions throw a fatal error when the apply_filter() function is not given a third, undocumented empty argument.

## Other major fixes

### Make footnote links script independent

`footnotes` did not include common hyperlinks between footnote anchors and footnotes. These have been added according to https://wordpress.org/support/topic/mouse-over-box-continue-bug/#post-13579708 to ensure interactivity when JavaScript is disabled in the user agent.

### Get the tooltip “Continue reading” link to work

The hyperlink on %scontinue%s had an empty address causing the page to scroll to top, and the jQuery function was lacking the footnote ID prefix so it couldn’t override the default. The prefix has been added as intended, and the correct fragment identifier has been replicated to populate the href. Additionally the button is given a CSS class.

### Disable the random footnote ID prefix

Based on a random number generated at page load, a prefix was prepended to the footnote fragment identifiers, so that these were unstable and users were prevented from sharing them, or worse they were sharing ephemeral and proactively broken links.

But in another install, this does not happen for an unknown reason, despite the code is still there.

That feature has been removed from the code. Fragment identifiers of footnotes and footnote anchors are guaranteed to be stable across page loads.

### Debug printed posts and pages

When printing, the tooltip infobox content was not hidden:

https://wordpress.org/support/topic/printing-pdf-with-footnotes-generated-by-footnotes/

That has been fixed with a CSS media query.

### Fix display of combined identical notes

When the “Combine identical notes” setting is enabled—and it is so by default—the footnote identifiers are lining up in the notes list because the white-space property was set to nowrap. That property has been reset to default.

### Scroll time and scroll offset

The jQuery controlled scrolling was set to take one second. This time has been reduced to 80 milliseconds, because that suffices to hint the direction, that is almost known in advance.

The scroll target displayed in the center of the screen, at half-height. This value has also been set to a more expected default: 5 % from top.

Settings are projected to give the user full control over scrolling behavior.

### Tooltip infobox not covering footnote anchors

The box only covered the text, not the inline footnote anchors. That is fixed by adding a z-index both to the anchors (1) and to the hoverbox (99).

## Other fixes

### Reference container inner borders

The table and the first column were defined as borderless, but not so the rest of the table.

These borders resulting from defaults raised criticism:
https://wordpress.org/support/topic/thin-box-around-notes-in-reference-container/
https://wordpress.org/support/topic/box-around-c-references-container/

These borders are uncommon. They are now disabled for the rest of the table, too.

### Footnote anchors vertical alignment

The footnote anchors are positioned relatively by a value in em measured from top. For v1.6.5, the footnote anchors were *raised* from 0.7em (too low) to 0.4em, but the changelog states: “The CSS had been modified in order to show the tooltip numbers a little less higher than text”. If in v1.6.4 they were considered too high, the number should be 0.8em, but that is even lower than the ascender height in WordPress’ Twentytwenty theme. 0.6em is more correct, it makes the anchors’ baseline sit at half x-height.

### Tooltip infobox font-size

The plugin’s public style sheet forgot to style the tooltip text, leaving it to defaults that made it way too small, compromising accessibility if not downright legibility.

This font size is now equal to the text font size (property value "inherit"), improving both user experience and footnote importance, as negative font size discrimination tends to inflect the mind that would take the small-printed less seriously.

### Tooltip infobox display timing

Delays and fade-in/out times are adjusted for a smoother user experience and are projected to be part of the planned new settings.

### Cursor shape on the references container label

The cursor was a pointer by default when hovering the references container. But when clicking the label, the container only expands, not collapses, so the pointer is correct only on the appended button.

### Style in the references container expand/collapse button

The plus sign in the button was underlined by default (not when hovered), making it appear like a plus-minus sign. Now it is underlined only when hovered, and then its color turns green.
