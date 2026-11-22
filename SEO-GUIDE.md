# SEO Optimization Guide — Sprīdīši

## ✅ Completed Improvements

### 1. **robots.txt** (Crawling & Indexing)
- ✓ Created `robots.txt` with proper directives
- ✓ Allows search engines to crawl public pages
- ✓ Excludes WordPress admin, plugins, and unnecessary pages
- ✓ Points to sitemap.xml for discovery

**Location:** `/robots.txt`

### 2. **sitemap.xml** (Search Engine Discovery)
- ✓ XML sitemap created with key pages:
  - Homepage (priority 1.0)
  - Reservation page `/rezervacija/` (priority 0.9)
- ✓ Includes lastmod and changefreq for update frequency hints

**Location:** `/sitemap.xml`

### 3. **.htaccess** (Server Optimization)
- ✓ WordPress URL rewriting enabled (pretty permalinks)
- ✓ GZIP compression for HTML, CSS, JavaScript
- ✓ Browser caching for static assets (images, fonts, CSS, JS)
- ✓ Security headers:
  - `X-UA-Compatible` for IE compatibility
  - `X-Content-Type-Options: nosniff` (prevent MIME type sniffing)
  - `X-Frame-Options: SAMEORIGIN` (prevent clickjacking)
  - `Referrer-Policy: strict-origin-when-cross-origin`
- ✓ Directory browsing disabled
- ✓ ETag optimization for caching efficiency

**Location:** `/.htaccess`

### 4. **Meta Tags & Open Graph** (Social Sharing & SEO)
- ✓ Dynamic meta descriptions per page
- ✓ Open Graph tags (og:title, og:description, og:image, og:url)
- ✓ Twitter Card tags for social sharing
- ✓ Canonical URLs to prevent duplicate content
- ✓ Language alternate links (hreflang)

**Location:** `functions.php` → `spridisi_add_meta_tags()`

### 5. **Structured Data (Schema.org)**
- ✓ LocalBusiness schema added with:
  - Business name, description, address
  - Contact details (phone, email)
  - Social media links
  - Aggregate rating (Google reviews)
- ✓ Improves rich snippets in search results

**Location:** `functions.php` → `spridisi_add_structured_data()`

---

## 🔍 What Happens Now?

### Search Engine Crawling Process:
1. Google bot visits `robots.txt` → sees sitemap reference
2. Google crawls `sitemap.xml` → finds all pages to index
3. Google fetches each page → reads meta tags, structured data
4. Google indexes page content with enhanced context from schema
5. Social media bots (Facebook, Twitter) read Open Graph tags

---

## ⚠️ Known Issues & Solutions

### Issue 1: **404 Error on /rezervacija/**
**Problem:** Browser shows "Page not found" but lapa pastāv kā WordPress lapa.

**Root Cause:**
- Reservation page is created dynamically via `booking.php`
- Page must be manually published in WordPress admin OR
- Page template `page-templates/booking.php` must be assigned

**Solution:**
1. Log in to WordPress admin: `https://spridisi.com/wp-admin/`
2. Go to **Pages**
3. Find **"Rezervācija"** page
4. Click **Edit** and **Publish** (if not published)
5. Ensure page has template `Booking Request` assigned
6. Click **Update**

**Alternative:** 
- If page doesn't exist, go to WordPress and create:
  - Title: "Rezervācija"
  - Slug: `rezervacija`
  - Template: "Booking Request"
  - Publish

### Issue 2: **Subdomain Conflicts**
**Observation:** Browser shows issues for both:
- `https://spridisi.com/rezervacija/` (main)
- `https://new.spridisi.com/rezervacija/` (staging/testing)

**Solution:**
- If `new.spridisi.com` is staging, that's normal
- Ensure production (`spridisi.com`) has page published
- Update `robots.txt` to exclude staging:
  ```
  User-agent: googlebot
  Disallow: /staging/
  Disallow: /test/
  ```

---

## 📈 Recommended Next Steps

### 1. **Verify Page Publication**
```bash
# SSH into server and check page status
wp post list --post_type=page --format=table
```

### 2. **Submit to Google Search Console**
1. Go to [Google Search Console](https://search.google.com/search-console)
2. Add property: `https://spridisi.com`
3. Upload `robots.txt` and `sitemap.xml`
4. Request indexing for key pages
5. Monitor indexing status

### 3. **Monitor Performance**
- Use **Google PageSpeed Insights** to check performance
- Use **Lighthouse** to verify SEO score
- Check **Core Web Vitals** in Search Console

### 4. **Add More Pages to Sitemap** (Optional)
If you add gallery, blog posts, or other pages:
```xml
<url>
    <loc>https://spridisi.com/page-slug/</loc>
    <lastmod>2026-11-22</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
</url>
```

### 5. **Install SEO Plugin** (Optional but Recommended)
- **Yoast SEO** or **Rank Math** can:
  - Auto-generate and update XML sitemaps
  - Optimize per-page meta descriptions
  - Analyze content readability
  - Monitor indexing issues

---

## 🔧 Testing SEO Configuration

### Test 1: Check robots.txt
```bash
curl https://spridisi.com/robots.txt
# Should return 200 OK with crawl directives
```

### Test 2: Check sitemap.xml
```bash
curl https://spridisi.com/sitemap.xml
# Should return valid XML with URLs
```

### Test 3: Check meta tags
```bash
# View page source for meta tags:
# - <meta name="description" content="...">
# - <meta property="og:title" content="...">
# - <script type="application/ld+json">...</script>
```

### Test 4: Google Cache
1. Search Google: `site:spridisi.com`
2. Check if pages appear in results
3. Click cached version to verify indexing

---

## 📋 SEO Checklist

- [x] robots.txt created and configured
- [x] sitemap.xml created with key pages
- [x] .htaccess optimization enabled
- [x] Meta descriptions added
- [x] Open Graph tags implemented
- [x] Structured data (Schema.org) added
- [x] Security headers configured
- [ ] Verify /rezervacija/ page is published
- [ ] Submit sitemap to Google Search Console
- [ ] Monitor indexing in Search Console
- [ ] Test Core Web Vitals
- [ ] Add more pages to sitemap (as needed)
- [ ] Consider adding SEO plugin (optional)

---

## 🆘 Support & References

- **WordPress SEO:** https://wordpress.org/support/article/seo/
- **Google Search Console Help:** https://support.google.com/webmasters/
- **Schema.org Documentation:** https://schema.org/LocalBusiness
- **Open Graph Protocol:** https://ogp.me/

---

**Last Updated:** 2026-11-22  
**SEO Status:** ✅ Initial configuration complete (pending page publication verification)
