![footnotes](https://raw.githubusercontent.com/media-competence-institute/footnotes/master/assets/footnotes.png)

**footnotes** WordPress Plugin

# About this fork

The codebase of the footnotes WP plugin has been forked in this repository to fix major bugs that appeared:
1. when using the plugin;
2. when assessing its #1 rank among all WP plugins for footnotes;
3. when writing up a method to get a functional WordPress site instantly, without waiting for official development, updates and releases;
4. when answering the topmost open threads on WordPress.org Support;
5. when getting, in that process, to 2 threads about upgrading an included obsolete jQuery library.

#5 got me to speed up and submit a new version right away.

## Most visible enhancement

The extra **symbol for backlinks** taking up a column in the reference container has been removed, and its hyperlink has been moved to the reference number. The logic is usual and straightforward:
1. In the text, a click on the index number takes the reader to the footnote;
2. In the list, a click on the footnote number takes the reader back to the text.

As an apparent disruption, this fix requires incrementing the major version number according to semantic versioning.

### More about the background

First I lived with the extra backlink icons, using ▲ for the purpose. Then I made them half transparent and overlaid them over the footnote numbers. That looked cool and came close to what I was accustomed to, from Wikipedia and everywhere. Once a knowledgeable person to whom I shared a post wrote me that those icons are hiding the numbers and I should remove them. So I instantly removed the icons and moved the backlinks to the numbers, thanking him much.

When I got to write up a WordPress method and needed to assess whether there is some better footnotes plugin, I tried out literally every free footnotes plugin available on WordPress.org. While testing, I found some serious bugs in “footnotes” and quickly fixed them as part of the WordPress method I was putting together. At some point, when almost everything was ready, I realized just how unprofessional I acted in suggesting a bunch of hacks and workarounds instead of sitting down and putting everything on GitHub, then sending pull requests so as to properly fix the plugins for everyone. I understood that I couldn’t postpone that in favor of more urgent tasks. The urgency is here.

### Rationale

I consider that backlink icons are an outdated feature that makes any website look unprofessional. The backlinks belong in the footnote numbers, and even better, in their table cell for easier clicking.

Backlink icons only clutter the reference container and should absolutely be avoided. That’s the reason I completely eliminated any code referring to them, not even trying to add a setting making them optional. Backlink icons are simply no option for a professional footnotes plugin.

Most if not all other plugins append the backlinks to the footnote text. “footnotes” went one step further by aligning the backlinks next to the footnote numbers. When backlink icons are close to the numbers, it becomes clear to everyone that there is something wrong in that the idea wasn’t carried out. Completing the move is really easy, fully backwards compatible and a good deal of UX enhancement.

## Other fixes

### Upgrade jQuery library

See https://wordpress.org/support/topic/tooltip-hover-not-showing/#post-13456762

As @vonpiernik advised, the jQueryUI library has been linked, jquery.tools.min.js has been fixed, and the tooltip infobox tested in WordPress v5.5.1, where the mouse-over boxes have started showing up.

### Make footnote links script independent

The footnotes plugin did not include common hyperlinks between footnote anchors and footnotes. These have been added according to https://wordpress.org/support/topic/mouse-over-box-continue-bug/#post-13579708 to ensure interactivity when JavaScript is disabled in the user agent.

[in progress]
