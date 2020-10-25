![footnotes](https://raw.githubusercontent.com/media-competence-institute/footnotes/master/assets/footnotes.png)

**footnotes** WordPress Plugin

# About this fork

The codebase of the footnotes WP plugin has been forked in this repository to fix major bugs that appeared when using it and, in a next step, when assessing its #1 rank among all WP plugins for footnotes.

## Major feature change

The extra **symbol for backlinks** taking up a column in the reference container has been removed, and its hyperlink has been moved to the reference number. The logic is usual and straightforward:
1. In the text, a click on the index number takes the reader to the footnote;
2. In the list, a click on the footnote number takes the reader back to the text.

### More about the background

First I lived with the extra backlink icons, using ▲ for the purpose. Then I made them half transparent and overlaid them over the footnote numbers. That looked cool and came close to what I was accustomed to, from Wikipedia and everywhere. Once a knowledgeable person to whom I shared a post wrote me that those icons are hiding the numbers and I should remove them. So I immediately removed the icons and moved the backlinks to the numbers, thanking him much.

When I got to write up a WordPress method and needed to assess whether there is some better footnotes plugin, I tried out literally every free footnotes plugin available on WordPress.org. While testing, I found some serious bugs in “footnotes” and quickly fixed them as part of the WordPress method I was putting together. At some point, when almost everything was ready, I realized just how unprofessional I acted in suggesting a bunch of hacks and workarounds instead of sitting down and putting everything on GitHub, then sending pull requests so as to properly fix the plugins for everyone. I understood that I couldn’t postpone that in favor of more urgent tasks. The urgency is here.

### Rationale

I consider that backlink icons are an outdated feature that makes any website look unprofessional. The backlinks belong in the footnote numbers, and even better, in their table cell for easier clicking.

Backlink icons only clutter the reference container and should absolutely be avoided. That’s the reason I completely eliminated any code referring to them, not even trying to add a setting making them optional. Backlink icons are simply no option for a professional footnotes plugin.

Most if not all other plugins append the backlinks to the footnote text. “footnotes” went one step further by aligning the backlinks next to the footnote numbers. When backlink icons are close to the numbers, it becomes clear to everyone that there is something wrong in that the idea wasn’t completed. Completing the move is really easy, fully backwards compatible and a good deal of UX enhancement.

## Other fixes
