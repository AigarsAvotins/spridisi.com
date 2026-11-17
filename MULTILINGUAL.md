# Multilingual Readiness

Planned languages: Latvian (default), English, Lithuanian, Estonian, German.
No translations, language routes, hreflang, canonical overrides, or plugin-specific integration are implemented.

## Interface Versus Content

Theme interface labels, fallback labels, navigation fallbacks, accessibility text,
Customizer labels, and placeholders use the `spridisi` text domain. PHP translation
calls use literal source strings so normal WordPress translation extraction works.
Theme palette and font names in theme.json use WordPress's native theme.json
internationalization. The existing text domain is loaded on `after_setup_theme`.

Editorial content belongs in ordinary WordPress pages, not in gettext catalogs.
The recommended source is the static homepage's protected native Gutenberg blocks.
The earlier child-page title/excerpt/body/featured-image sources remain as fallbacks.
There are no language columns, per-language templates, custom translation tables,
custom locale detection, or language-specific URLs.

## Homepage Content

The recommended editor is now **Pages > Sākumlapa > Edit**. Use the one-time
**Set up editable homepage** notice in WordPress Admin to create it. The action is
permission-checked, nonce-protected, and idempotent. It fills an empty assigned
homepage or publishes a new homepage without overwriting existing page content.
It sets the page as the static front page. No pages are created on normal visits.

The homepage contains named, content-only locked groups of native Gutenberg blocks:
Hero; Introduction; Celebrations and halls; Accommodation; Sauna; Experiences and
SUP; Gallery; Booking and contact details. Click the text or photograph to edit it,
or select the relevant group in List View. Button blocks edit both labels and links.
Save the page to update the site. No technical HTML, custom fields, or page builder
is needed. The theme renders these values through its existing templates and CSS;
it does not render the editor's layout as the homepage design.

Editable content includes hero eyebrow, heading, supporting text and both buttons;
section headings, labels and descriptions; large/small hall headings and information;
accommodation and sauna descriptions and buttons; each experience including SUP;
three gallery images and introductory text; closing booking heading, label and
destination; location identity and contact information. Empty descriptions do not
appear on the public homepage. Hall headings only appear after their descriptions
are populated. Unknown business facts and contact details are not invented.

The three real photographs remain the same. Their native Image blocks initially
reference the existing theme assets; they are not duplicated in the Media Library
or per language. Use the Image block's Replace control to choose a Media Library
image later. The same attachment may be shared across translated pages; alt text
can be edited on each page's Image block. Missing images retain existing theme
fallbacks/placeholders. The hero remains decorative and high-priority/eager.

Contact details belong in the Contact description using normal paragraphs and
links. They also appear in the footer. The closing Button's link is the booking
destination (URL, mailto, or tel). It stays disabled until a real destination is
provided. Existing Customizer contact values remain backward-compatible fallbacks.

Content-only locks protect section order and layout. The homepage's code editor
and lock controls are disabled. The owner edits content; structural layout changes
remain a theme development task. History remains separate, using its current
timeline renderer and child-page approach described below.

Translate the actual homepage page with Polylang/WPML later. Each translated page
stores its own native text/button/image blocks with the same stable structural
markers. There is no language lookup table or language-specific PHP template.
The current queried front-page ID is used, allowing the plugin to supply the
translated page. Preserve structural block classes when duplicating/translating.
Normal WordPress revisions apply to these block-content edits.

### Legacy Child-Page Content

The following older child-page architecture remains as a fallback for existing
sites until the protected homepage content is set up. Native homepage blocks take
precedence. Existing child pages are not deleted or silently rewritten. If a new
homepage is created, review any legacy history/experience/gallery child pages and
their parent relationships before switching a production site. The initial setup
copies existing top-level section titles, summaries, descriptions, and selected
section photographs where available; legacy child collections require review.

Use a static homepage in Settings > Reading. Create published, non-password-protected
child pages directly beneath it. The standard Page Attributes > Order field assigns
a section; slugs and titles can be translated freely. Use one page per order value.

| Order | Section | Title | Excerpt | Body / Featured Image |
| --- | --- | --- | --- | --- |
| 10 | Hero | Main heading | Supporting copy | Optional body / hero photograph |
| 20 | Introduction | Heading | Short section label | Introduction text |
| 30 | Venue | Heading | Short section label | Venue description / photograph |
| 40 | Accommodation | Heading | Supporting text | Description / photograph |
| 50 | Sauna | Heading | Short section label | Description / photograph |
| 60 | Experiences | Heading | Section supporting text | Introduction / child experiences |
| 70 | History | Heading | Subheading | Introduction / child timeline entries |
| 80 | Gallery | Heading | Short section label | Introduction / child photographs |
| 90 | Contact | Closing heading | Short location identity | Contact information and links |

The introduction still accepts the static homepage body when no introduction child
page exists. Empty sections retain the current Latvian fallback copy. Do not populate
editorial descriptions through theme translation files. Publish real page content
before launching other languages.

Each timeline entry is a child page of the history section page. Its title is the
entry title, its excerpt is supporting text (for example a verified period label),
and its body is the description. Standard page Order controls chronology. There is
one timeline renderer for all languages. Sample markers disappear once real entries
exist. The history CTA links to the section page; the old Customizer history-page
setting is only a fallback.

Each experience is a child page of the experiences section: title, excerpt, body,
and featured image are editable and translatable. Its link uses `get_permalink()`.
The gallery uses featured images from the first three ordered gallery child pages.
Image alternative text belongs to the WordPress Media Library and must also be
translated through the selected plugin's media workflow.

## Remaining Fallbacks And Global Settings

Latvian default headings, hero copy, introduction copy, location labels, section
labels, CTAs, and sample timeline text remain in templates as gettext-wrapped
fallbacks for sites that have not completed setup. They do not override the stored
homepage blocks. The starter-copy function is only a one-time initializer, not the
editorial source after setup. True theme interface strings (navigation fallbacks,
skip/menu labels, status messages, image placeholders, branding, and editor/setup
labels) remain in PHP with the spridisi text domain. History remains separate.

Existing Customizer images, booking email/URL, phone, postal address, social URLs,
and the legacy history page ID remain global for backward compatibility. Native
section featured images take precedence over global section photographs. Contact
page body content takes precedence over the footer's global contact details.
Move any language-dependent contact/address text into that contact page body.
The contact excerpt supplies the short location identity. Shared email and phone
values need no translation; language-specific booking/social destinations and any
remaining global page-ID fallback will need the chosen plugin's supported settings
workflow when integration is explicitly requested. Do not create per-language
theme options or hardcode URL prefixes as a workaround.

## Language Menu And Layout

The registered `language` menu location is an integration point inside the header's
responsive navigation region. It renders nothing until a real menu is assigned.
There are no fake LV/EN/LT/ET/DE links. A future plugin-supported menu switcher can
be assigned there and will be available on desktop and inside the mobile disclosure.
Do not treat manually entered static menu links as a functioning language switcher.

Navigation labels and buttons have no fixed widths. Navigation can wrap; headings
are complete translation strings and wrap without hardcoded line breaks. A native
ResizeObserver adjusts hero/header clearance when longer labels enlarge the header.
Text-heavy cards, timeline items, and content sections grow with their content.

## Before Multilingual Launch

1. Set the WordPress site language to Latvian and use the editable static homepage.
2. Populate and review the homepage's native blocks, verified history, real photographs,
   image alternative text, and contact information. Do not describe the venue as
   being on the Daugava riverbank; the river is nearby.
3. Choose and configure one multilingual plugin. Translate the homepage page and its
   protected block content. Retain the structural block classes and group order.
   Translate separate history/legacy child pages if they are used, preserving their
   Order values. Let the plugin manage page relationships and queries.
4. Configure translated primary/footer menus and the plugin's actual language
   switcher in the `language` location. Translate theme interface strings using the
   plugin's supported workflow or ordinary WordPress translation catalogs.
5. Configure any language-dependent global settings through that plugin's supported
   integration, including legacy page references if still used. Prefer native pages
   and native featured images over global fallbacks for editorial content.
6. Let the multilingual and SEO plugins manage translated permalinks, metadata,
   canonical URLs, hreflang, and sitemaps. Test their interaction on staging.

The core theme can be tested without either plugin. Actual translated ID resolution,
cross-language navigation, media translation, and SEO output require integration
testing with the selected plugin; they are not certified by a plugin-free test.