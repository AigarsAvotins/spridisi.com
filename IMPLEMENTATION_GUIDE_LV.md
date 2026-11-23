# Vidējās Sekcijas Modernizācijas Implementācijas Instrukcija

## 🚀 Ātrs Sākums (5 minūtes)

### 1. Vispirms: Pievienot CSS failu
Jau izveidots fails: `assets/css/sections-modern.css`

Rediģējiet `header.php` un pievienojiet:
```php
<?php
// Pirms closing </head> taga
wp_enqueue_style( 'sections-modern', get_template_directory_uri() . '/assets/css/sections-modern.css' );
?>
```

### 2. Pievienot AOS.js bibliotēku
Rediģējiet `footer.php` un pievienojiet pirms closing `</body>` taga:
```php
<?php
// AOS JavaScript bibliotēka
wp_enqueue_script( 'aos', 'https://unpkg.com/aos@next/dist/aos.umd.js', array(), '2.3.4', true );

// AOS inicializācija
wp_add_inline_script( 'aos', "
	document.addEventListener('DOMContentLoaded', function() {
		AOS.init({
			duration: 1000,      // Animācijas ilgums ms
			offset: 100,         // Triggeris pirms elementa redzams 100px
			easing: 'ease-in-out-back', // Animation easing
			once: false,         // Animācija atkārtojas katru reizi
			mirror: true,        // Animācija iziet arī skrolējot augšup
		});
	});
");
?>
```

### 3. Mainīt Venue Sekciju
Rediģējiet `front-page.php`:

**Pašreiz:**
```php
get_template_part( 'template-parts/venue' );
```

**Jaunais:**
```php
get_template_part( 'template-parts/venue', 'modern' );
```

### 4. Mainīt Accommodation Sekciju
**Pašreiz:**
```php
get_template_part( 'template-parts/accommodation' );
```

**Jaunais:**
```php
get_template_part( 'template-parts/accommodation', 'modern' );
```

---

## 📋 Detalizētā Implementācija

### Pozīcija A: Kompilēta CSS Integr.

Ja nevar/negribu jaunu CSS failu:

1. Atvērt `style.css`
2. Paste šo kodu beigās:

```css
/* ============ VENUE CARDS ============ */
.venue-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2.5rem; margin: 2rem 0; }
.venue-card { background: #f9f7f3; border-radius: 12px; padding: 2.5rem 2rem; transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94); border: 2px solid transparent; position: relative; overflow: hidden; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); }
.venue-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 5px; background: linear-gradient(90deg, #d4a574, #8b6f47); transform: scaleX(0); transform-origin: left; transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94); }
.venue-card:hover::before { transform: scaleX(1); }
.venue-card__icon { font-size: 3.5rem; margin-bottom: 1rem; display: block; transition: transform 0.3s ease; }
.venue-card:hover .venue-card__icon { transform: scale(1.1) rotate(5deg); }
.venue-card__title { font-size: 1.75rem; margin: 1rem 0; color: #2a2a2a; font-weight: 600; }
.venue-card__details { margin: 1.5rem 0; }
.detail { display: flex; align-items: center; gap: 0.75rem; margin: 1rem 0; font-size: 0.95rem; color: #555; transition: transform 0.3s ease; }
.detail:hover { transform: translateX(5px); }
.detail-icon { font-size: 1.3rem; min-width: 1.5rem; }
.detail-text strong { color: #d4a574; font-weight: 600; }
.venue-card__cta { display: inline-flex; align-items: center; gap: 0.5rem; margin-top: 1.5rem; color: #d4a574; font-weight: 600; text-decoration: none; transition: all 0.3s ease; }
.venue-card__cta::after { content: '→'; transition: transform 0.3s ease; }
.venue-card__cta:hover::after { transform: translateX(4px); }
.venue-card:hover { transform: translateY(-12px); box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15); border-color: #d4a574; background: #fff; }
.venue-card--full { grid-column: 1 / -1; background: linear-gradient(135deg, #f9f7f3 0%, #ebe8e1 100%); }

/* ============ SECTION BADGE ============ */
.section-number-badge { display: inline-block; font-size: 1rem; font-weight: bold; color: #d4a574; position: relative; padding-left: 2.5rem; margin-bottom: 0.5rem; letter-spacing: 1px; }
.section-number-badge::before { content: ''; position: absolute; left: 0; width: 2rem; height: 2rem; border-radius: 50%; background: linear-gradient(135deg, #d4a574, #c99968); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(212, 165, 116, 0.3); transition: all 0.3s ease; }
.section-number-badge:hover::before { transform: scale(1.15); box-shadow: 0 6px 15px rgba(212, 165, 116, 0.5); }

/* ============ ACCOMMODATION ============ */
.accommodation-benefits { margin: 2rem 0; display: flex; flex-direction: column; gap: 1.2rem; }
.benefit { display: flex; gap: 1rem; align-items: flex-start; padding: 0.75rem; border-radius: 8px; transition: all 0.3s ease; }
.benefit:hover { background: rgba(212, 165, 116, 0.1); padding-left: 1.25rem; }
.benefit-icon { font-size: 1.5rem; min-width: 2rem; }
.benefit-text { font-size: 0.95rem; color: #555; line-height: 1.5; }

/* ============ AOS ANIMATIONS ============ */
[data-aos] { transition-property: opacity, transform; }
.aos-animate { opacity: 1 !important; transform: translate(0) scale(1) !important; }

/* ============ RESPONSIVE ============ */
@media (max-width: 768px) {
  .venue-cards { grid-template-columns: 1fr; gap: 1.5rem; }
  .venue-card__title { font-size: 1.3rem; }
  .detail { font-size: 0.9rem; }
  .accommodation-content { grid-template-columns: 1fr; gap: 2rem; }
}
```

---

## 🎨 Personalizācija

### Mainīt Krāvas

Ja vēlies citu krāvu shēmu, aizstāj:
- `#d4a574` → Jaunā zelta krāsa
- `#8b6f47` → Jaunā brūnā krāsa
- `#f9f7f3` → Jaunā fona krāsa
- `#2a2a2a` → Jaunā teksta krāsa

### Mainīt Animāciju Ātrumu

AOS inicializācijā (footer.php):
```javascript
duration: 1000,  // 1000ms = 1 sekunde. Mainīt uz: 500 (ātrāk), 1500 (lēnāk)
offset: 100,     // Mainīt uz: 50 (ātrāka triggeris), 200 (lēnāka)
```

### Mainīt Efektus

Rediģējiet datu atribūtus HTML:
```php
<!-- Pašreiz: -->
data-aos="zoom-in" 
data-aos-delay="200"

<!-- Iespējamas vērtības: -->
data-aos="fade-up"           // Tīri parādās no apakšas
data-aos="fade-down"         // Tīri parādās no augšas
data-aos="fade-left"         // Slīd no kreisās puses
data-aos="fade-right"        // Slīd no labās puses
data-aos="zoom-in"           // Palielinas
data-aos="zoom-out"          // Samazinās
data-aos="flip-left"         // Pagriezt no kreisās
data-aos="flip-right"        // Pagriezt no labās
```

---

## ✅ Testēšana

### 1. Desktop pārlūkprogrammatūra (Chrome, Firefox)
- Navigējiet uz lapas vidējās daļas
- Rullējiet uz leju - jāredz kartītes animētas
- Hover uz kartītēm - jāredz efekti (pacelšanās, ēnojums)

### 2. Mobilais (iPhone, Android)
- Atvērt lapā mobilajā
- Rullējiet - animācijas jāstrādā
- Kartītes jāsastumj 1 rindā

### 3. Tabletes (iPad, Android tablets)
- Kartītes jāsastumj 2-3 rindās
- Teksts jāmanto lasāms

### 4. Ātruma Testēšana
Izmantot Google PageSpeed Insights: https://pagespeed.web.dev
- Pārliecinieties, ka lapu ielādes laiks nav ievērojami pieaudzis

---

## 🔧 Troubleshooting

### Problēma: Kartītes nav animējošas
**Risinājums:**
1. Pārliecinieties, ka AOS.js ielādējas (pārlūka DevTools → Network tab)
2. Pārliecinieties, ka ir `data-aos` atribūti HTML
3. Console.log lai pārbaudītu: `console.log(AOS)` - ja undefined, AOS nav ielādēts

### Problēma: CSS nav tiekts lejupielādēts
**Risinājums:**
1. Pārliecinieties, ka `sections-modern.css` eksistē `assets/css/` mapē
2. Pārliecinieties, ka tiek ielādēts ar `wp_enqueue_style()`
3. Iespējams nepieciešams skaidīt cache: `Ctrl+Shift+R` (Chrome)

### Problēma: Kartītes izskatās "saspiests"
**Risinājums:**
1. Rediģējiet CSS: `.venue-card { padding: 2.5rem 2rem; }` - palieliniet skaitļus
2. Rediģējiet CSS: `.venue-cards { gap: 2.5rem; }` - palieliniet atstarpi

### Problēma: Teksts nav teikums "PALIEC PA NAKTI" uz mobilā
**Risinājums:**
Pievienojiet mobile breakpoint CSS:
```css
@media (max-width: 480px) {
  .accommodation-copy h2 { font-size: 1.5rem; line-height: 1.1; }
}
```

---

## 🚀 Nākamie Soļi (Pēc Implementācijas)

1. ✅ Testēt uz visām ierīcēm
2. ✅ Mērīt Core Web Vitals (lapu ielādes laik, interaktivitāte)
3. ✅ Mērīt konversiju izmaiņas (reservation rate)
4. ✅ Paņemt feedback no lietotājiem
5. ✅ Optimizēt basēts uz mērījumiem

---

## 📚 Papildu Resursi

- **AOS.js dokumentācija:** https://michalsnik.github.io/aos/
- **CSS Grid:** https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_Grid_Layout
- **CSS Transitions:** https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_Transitions
- **WordPress Enqueue:** https://developer.wordpress.org/plugins/basics/enqueuing-scripts-and-styles/

---

## 🎓 Izglītības Resursi par Animācijām

Ja vēlies vairāk uzzināt un pārveidot:
- Cubic-bezier vizuāls rīks: https://cubic-bezier.com/
- Animation timing: https://developer.mozilla.org/en-US/docs/Web/CSS/animation-timing-function

---

**Versija:** 1.0  
**Pēdējais Update:** 2024-11  
**Autors:** Dizaina analīze un modernizācija
