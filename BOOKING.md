# Booking Requests

## Set Up And Edit

After deploying the theme, use **WordPress Admin > Pages > Set up booking request
page**. This explicit action creates **Rezervācija** (`rezervacija`) or safely reuses
the existing page. Existing content is never overwritten. An existing custom page
template requires manual review; draft/private pages are not silently published.

Edit the eyebrow, heading, introduction and notice under **Pages > Rezervācija >
Edit**. Native Gutenberg content-only groups protect the presentation. The page
template supplies the accessible form. The editor-only **Pieejamība** group reserves
space in the content architecture for future availability work; no calendar or
calendar instructions are rendered publicly.

The header Rezervēt CTA uses the published native booking page. The homepage hero
and closing booking CTA resolve empty/contact-anchor destinations to the booking
page without rewriting stored content. Explicit owner-provided URLs are preserved.
No hostname, translated URL prefix or page ID is hardcoded. Translated pages can
share the booking template; the standard page query remains language-filterable.

## What Submission Means

This is a **request**, never an automatically confirmed booking or availability
check. Dates and arrangements require manual review and separate agreement. Same-day
events and combinations of hall/accommodation/sauna are accepted. Published capacity
information is contextual, not a universal rejection threshold.

## Email And Storage

Requests are email-only: no submissions table, public/private post records, uploads,
or stored request bodies are created. Values remain in the POST response only after
validation errors. Personal data is not placed in URLs or browser storage.

The notification recipient is **Settings > General > Administration Email Address**.
The visitor email is validated and can only supply the admin message's Reply-To;
user input never controls arbitrary headers or the From address. Plain-text messages
use `wp_mail()`, so an established SMTP/transactional delivery integration can handle
them later. The visitor acknowledgement contains only dates, guest count and event
type, and explicitly states that a separate agreement is required.

An admin-notification transport failure returns the generic error and sends no
acknowledgement. Once the admin transport accepts the message, the request is
accepted even if the acknowledgement transport fails, avoiding duplicate admin
notifications on retries. `wp_mail()` acceptance is NOT proof of inbox delivery.

**Before production use**, configure and test authenticated delivery through an
established solution such as WP Mail SMTP, Post SMTP, Brevo, Mailgun, Amazon SES,
or the host's authenticated SMTP. Do not put credentials in the theme, Gutenberg,
JavaScript or Git. Test admin delivery, visitor delivery, spam handling, SPF/DKIM/
DMARC and failure reporting through the chosen provider. No delivery plugin or
external service is installed/configured by this theme.

Request retention is managed in the administrator's email system. Delete requests
and related correspondence there according to the site's privacy/retention policy.
There is no WordPress booking-record deletion workflow because no such records are
stored.

## Security And Operations

- POST only, WordPress nonce, a signed per-form token bound to the page and a
  short-lived HttpOnly SameSite=Lax browser cookie, plus same-origin checks when the
  browser supplies Origin. Cookies use Secure on HTTPS.
- Server validation and sanitization, context-appropriate escaping, strict date and
  integer checks, allowed-option lists, required consent, and length limits.
- Hidden honeypot, a signed minimum three-second / maximum two-hour form age, and
  five attempts per rolling fifteen-minute window keyed by an HMAC of REMOTE_ADDR.
  Raw IP addresses are not stored. Client-controlled proxy headers are not trusted;
  configure the web server correctly behind a trusted proxy. Rate limiting is basic
  defense, not a replacement for monitored abuse prevention. Turnstile can be added
  server-side later if needed, without trusting client assertions.
- An atomic, non-autoloaded WordPress option claims each random token before mail is
  sent. It stores only pending/sent status; WordPress cron removes it after two hours.
  Rate-limit transients contain counters only. These are anti-abuse metadata, not
  stored booking submissions. Keep WP cron (or a real server cron invoking WP cron)
  healthy. No personal data is logged by the theme.
- A successful POST uses a 303 redirect and a signed HttpOnly receipt cookie. Refresh
  shows the receipt without resending mail. An arbitrary success query string alone
  cannot display an accepted-request state. Error POSTs retain escaped values.

Exclude the booking page and translated booking pages from full-page/CDN caching.
The theme sends no-cache headers and sets DONOTCACHEPAGE, but early cache layers
must also be configured explicitly. Otherwise stale nonces/tokens can prevent valid
submissions. HTTPS is required in production. Review the Privacy Policy under
**Settings > Privacy** and publish/select it so its link appears next to consent.
Consent is required, not prechecked, and does not include marketing permission.

Set **Settings > General > Timezone** to **Europe/Riga** after confirming the business
configuration. The theme uses `wp_timezone()` / `current_datetime()` and never fixed
UTC offsets. Do not change the timezone merely to work around date validation.

## Languages

The initial native page copy is Latvian. All form labels, validation/status text and
email templates use the `spridisi` text domain. The current translated page and
WordPress locale determine the submission page/language and visitor email language;
no language input from the visitor is trusted. Translate the native page with
Polylang/WPML and translate theme interface/email strings with standard catalogs or
that plugin's supported string workflow. Test routes and locales with the selected
plugin before multilingual launch. No separate language templates or routes exist.

## Future Availability

Recommended architecture: WordPress availability layer <-> secure server-side Google
Calendar API integration <-> a dedicated availability-only Google Calendar. Keep
OAuth/service-account credentials outside the theme and Git. Read free/busy only,
cache safely, tolerate outages, expose only available/busy/on-request states, use
Europe/Riga consistently, and recheck availability server-side before any future
confirmation. Prevent overlaps and retain manual approval. Never expose customer
event names/details or let the public form trust client calendar data.

No Google scripts, credentials, calendar URL, private events, fake availability,
payment flow or calendar synchronization are implemented.