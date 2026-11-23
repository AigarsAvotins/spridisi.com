# Vidējās Sekcijas Modernizācijas Rekomendācijas

## 📊 Pašreizējā Stāvokļa Analīze

### Vidējās sekcijas komponenti:
1. **Sekcija 01** - "Vieta, kur palikt ilgāk" (Introductory text)
2. **Sekcija 02** - "Svini Sprīdīšos" (Events/Venues - Large & Small halls)
3. **Sekcija 03** - "Paliec pa nakti" (Accommodation)

### Pašreizējās problēmas:
- ❌ Statisks, lineārs teksta izkārtojums bez vizuālās hierarhijas
- ❌ Lielās tekstbloku - grūti uztverami 
- ❌ Nav vizuālu sadalīto elementu starp sekcijām
- ❌ Neinformatīvi numuri (02/, 03/) - var būt visvieglāk ignorējami
- ❌ Mazāk iesaistoši interneta lietotājiem (īpaši mobilo ierīču)
- ❌ Statiska displeja - nemaz nav dinamikas/animāciju

---

## 🎯 Rekomendācijas Modernizācijai

### 1️⃣ **Interaktīvi Pakalpojumu Karti (Cards)**

**Problēma:** Pašreiz teksts ir monolīts, bez skaidras vizuālās sadalīšanas.

**Risinājums:**
```
┌─────────────────────────────────────────┐
│  🎉  SVINI SPRĪDĪŠOS                     │
│                                          │
│  ┌──────────────┐  ┌──────────────┐    │
│  │   Lielā      │  │   Mazā       │    │
│  │   Zāle       │  │   Zāle       │    │
│  │   220 m²     │  │   50 m²      │    │
│  │   līdz 80 v. │  │   10-15 v.   │    │
│  │   [Uzzināt]  │  │   [Uzzināt]  │    │
│  └──────────────┘  └──────────────┘    │
│                                          │
│  ┌─────────────────────────────────┐   │
│  │   🌳 Āra Terase un Teritorija   │   │
│  │   [Skatīt Detaļas]              │   │
│  └─────────────────────────────────┘   │
└─────────────────────────────────────────┘
```

**Implementācija:**
- Izveidot CSS Grid vai Flexbox kartīšu sistēmu
- Pievienot ikoniņas vai krāsas kodam
- Kartītes ar hover efektiem (pacelšanās, ēnojums, fona krāsu maiņa)
- Responsīvs dizains (1 kolonna mobilajās, 3+ darbvielas)

**CSS piemērs:**
```css
.venue-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2rem;
  margin: 2rem 0;
}

.venue-card {
  background: #f5f5f5;
  padding: 2rem;
  border-radius: 8px;
  transition: all 0.3s ease;
  cursor: pointer;
}

.venue-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.15);
  background: #fff;
}
```

---

### 2️⃣ **Scroll Animācijas (AOS - Animate On Scroll)**

**Problēma:** Statisks skaņu landscape bez jebkādas animācijas vai pārejas.

**Risinājums:** Pievienot subtīlas animācijas, kad lietotājs skrolē lapā:

**Efekti:**
- **Fade-in** - Teksts un kartītes tīri parādās
- **Slide-in** - Elementi slīd no sāniem
- **Zoom-in** - Mazas attēlus palielina
- **Parallax** - Fona attēli pārvietojas lēnāk par foreground

**Implementācija:**
```javascript
// Izvēlēties bibliotēku: AOS.js (populāra)
<link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
<script src="https://unpkg.com/aos@next/dist/aos.umd.js"></script>

// Inicializācija
<script>
  AOS.init({
    duration: 1000,
    offset: 100,
    easing: 'ease-in-out',
  });
</script>

// HTML
<div data-aos="fade-up" data-aos-delay="200">
  <h2>Svini Sprīdīšos</h2>
</div>

<div class="venue-card" data-aos="zoom-in" data-aos-delay="400">
  Lielā zāle
</div>
```

---

### 3️⃣ **Numurēšanas Vizualizācija**

**Problēma:** "02 /" un "03 /" ir grūti pamanāmi un neinformatīvi.

**Risinājums:** Pārveidot par progresa indikatoru vai visuālu marķieri:

```
Variants A - Progresa bārs:
████░░░░░░  Sekcija 2 no 5

Variants B - Visuālo punktu indikators:
● ● ● ○ ○   (Aktīvais ●, neatklāts ○)

Variants C - Timeline vizuāls:
  Svēt   
   │
  🎉 Svini Sprīdīšos (Sekcija 02)
   │
  🛏️ Paliec pa nakti (Sekcija 03)
   │
  🔥 Melnā pirts (Sekcija 04)
```

**Implementācija ar CSS:**
```css
.section-number {
  display: inline-block;
  font-size: 1.2rem;
  font-weight: bold;
  color: #d4a574;
  position: relative;
  padding-left: 2rem;
}

.section-number::before {
  content: '';
  position: absolute;
  left: 0;
  width: 1.5rem;
  height: 1.5rem;
  border-radius: 50%;
  background: linear-gradient(135deg, #d4a574, #c99968);
  display: flex;
  align-items: center;
  justify-content: center;
}
```

---

### 4️⃣ **Interaktīvie Gadgets un Gadgeti**

#### A. **Flip Cards** (Pagriezt kartītes)
Kartīte parāda aprakstu vienas puses, tad tiek pagriezta, lai rādītu citas informācijas:
```
Priekšpuse: "Lielā zāle - 220 m²"
Pārliecies pele: Pagriezas
Aizmugure: "Vietas: 80 | Nomstiprinātas zāles | Pilna aprīkojuma"
```

#### B. **Hover Info Tooltips**
Pārceļot peli virs ikonas/cipara, parādās papildu informācija:
```
🛏️ Hovered → Rāda "28 vietas | Ērts kāzām un jubilejai"
```

#### C. **Tabs/Accordion**
Izvēlnē izvēlieties, kādu hall skatīt - dinamiska satura maiņa:
```
[Lielā zāle] [Mazā zāle] [Āra terase]
         ↓
   Rāda attiecīgo satura/attēlu
```

#### D. **Image Carousel/Slider** (Bildju ritošā lenta)
Vairākas bildes per ziņu ar bultiņām vai automātiskais slaidošana:
```
◄  [Bildes no Lielās zāles]  ►
   Auto: Nākamā bildes var 5 sec.
   Punkti-indikatori zemāk
```

---

### 5️⃣ **Teksta Hierarhijas Uzlabošana**

**Pašreiz:** Lielas tekstbloku, grūti skenēt.

**Risinājums:**
- Sarakstos un punktos sadalīt galvenās priekšrocības
- Izmantot **bold** svarīgiem skaitļiem un frāzēm
- Pievienot ikonas katram pakalpojumam

```markdown
### Lielā zāle
- 🏛️ **220 m²** plaša telpa
- 👥 Vietas līdz **80 viesiem**
- 🍽️ Jau pieejami **galdi un krēsli**
- 📸 **Universāla** telpa: kāzas, jubilejas, semināri
```

---

### 6️⃣ **Krāsu Pasvītrojums un Vizuālā Diferenciācija**

**Problēma:** Viss ir vienā Cream/White toni.

**Risinājums:** Pievienot diskrētas krāsas kodu sistēmu:
- Sekcija 02 (Events) → Zelta/brūna tēma (#d4a574, #8b6f47)
- Sekcija 03 (Accommodation) → Zaļa tēma (#5a8a6a, #4a7a5a)
- Sekcija 04 (Sauna) → Sarkana/tumši sarkanā tēma (#a85a4a)

```css
.section-venue {
  --accent-color: #d4a574;
  --accent-dark: #8b6f47;
  border-left: 4px solid var(--accent-color);
  padding-left: 1.5rem;
}

.section-accommodation {
  --accent-color: #5a8a6a;
  --accent-dark: #4a7a5a;
}
```

---

### 7️⃣ **Responsive Design Uzlabojumi**

**Mobilajam ekranam:**
- Kartītes 1 rindā (nevis 3)
- Mazāki fonti, bet pārlasi pieejami
- Touch-friendly pogas (vismaz 48px augstums)
- Kartītes piecu sadalītu (swipe-able) uz mobilajām

**Tabletes ekranam:**
- Kartītes 2 rindā
- Dāļu dati savāti kompaktākā formā

**Darbvielas ekranam:**
- Kartītes 3+ rindā
- Pilna informācija redzama

---

## 🎨 Ieteiktie Skripti un Bibliotēkas

| Funkcionalitāte | Bibliotēka | Izmērs | Piezīmes |
|-----------------|-----------|--------|---------|
| Scroll animācijas | AOS.js | ~4KB | Viegli lietojama, populāra |
| Kartītu slīdējumi | Swiper | ~40KB | Perfekti responsīvai kartītēm |
| Paralax efekti | Rellax.js | ~3KB | Viegla parallax implementācija |
| Modal/popups | SimpleModal | ~8KB | Papildu informācija kartītēs |
| Icon library | Font Awesome | ~50KB | Daudz ikoniņu (kāzas, pirts, āra, u.c.) |

---

## 📱 Konkrēto Izmaiņu Prioritāte

### 🔴 **1. Prioritāte (Svarīgākā)**
1. Konvertēt zāļu aprakstus uz kartīšu sistēmu
2. Pievienot AOS scroll animācijas
3. Uzlabot virsrakstu/krāsu hierarhiju

### 🟠 **2. Prioritāte (Augstāka)**
1. Pievienot ikoniņas uz dažādi pakalpojumiem
2. Paralax efekti lielām bildēm
3. Hover efekti uz kartītēm

### 🟡 **3. Prioritāte (Papildus uzlabojumi)**
1. Flip kartītes papildu informācijai
2. Image carousel bildes
3. Timeline vizuāls sekciju sadalei

---

## 💻 Implementācijas Pavēļi

### Pieeja 1: Gulpo WordPress Plugini (Ātrākā)
```
1. Instalēt: "Elementor" VAI "Divi"
2. Ieslēgt animācijas konfigurāciju
3. Pārveidot kartītes visual builder
4. Pievienot AOS uz header.php
```

### Pieeja 2: Manuāl CSS/JS (Precīzākā)
```
1. Rediģēt: template-parts/venue.php un accommodation.php
2. Pievienot CSS klases: .venue-card, .venue-cards
3. Nolikt AOS.js bibliotēku assets/js/
4. Inicializēt AOS objektu functions.php
```

---

## 🎬 Prototipa Piemērs

### Pašreizējais kods (venue.php):
```html
<div class="section-content venue-hall">
  <h3>Lielā zāle</h3>
  <p>220 m² plaša svinību zāle ar galdiem un krēsliem līdz 80 viesiem.</p>
  <figure class="venue-hall__image"><!-- Attēls --></figure>
</div>
```

### Modernizētais kods:
```html
<div class="venue-cards">
  <div class="venue-card" data-aos="zoom-in" data-aos-delay="200">
    <div class="venue-card__icon">🏛️</div>
    <h3 class="venue-card__title">Lielā Zāle</h3>
    <div class="venue-card__details">
      <div class="detail">
        <span class="detail-icon">📐</span>
        <span class="detail-text"><strong>220 m²</strong> telpa</span>
      </div>
      <div class="detail">
        <span class="detail-icon">👥</span>
        <span class="detail-text">Vietas līdz <strong>80 viesiem</strong></span>
      </div>
      <div class="detail">
        <span class="detail-icon">🍽️</span>
        <span class="detail-text">Galdi un krēsli <strong>jau pieejami</strong></span>
      </div>
    </div>
    <a href="#kontakti" class="venue-card__cta">Par svinībām →</a>
  </div>

  <div class="venue-card" data-aos="zoom-in" data-aos-delay="400">
    <div class="venue-card__icon">🎪</div>
    <h3 class="venue-card__title">Mazā Zāle</h3>
    <div class="venue-card__details">
      <div class="detail">
        <span class="detail-icon">📐</span>
        <span class="detail-text"><strong>50 m²</strong> telpa</span>
      </div>
      <div class="detail">
        <span class="detail-icon">👥</span>
        <span class="detail-text"><strong>10-15 cilvēki</strong></span>
      </div>
      <div class="detail">
        <span class="detail-icon">👨‍🍳</span>
        <span class="detail-text"><strong>Pilnībā aprīkota virtuve</strong></span>
      </div>
    </div>
    <a href="#kontakti" class="venue-card__cta">Par svinībām →</a>
  </div>

  <div class="venue-card venue-card--full" data-aos="zoom-in" data-aos-delay="600">
    <div class="venue-card__icon">🌳</div>
    <h3 class="venue-card__title">Āra Terase un Teritorija</h3>
    <p>Plašā apkārtne ideāla vasaras svinībām un relaxu.</p>
    <a href="#kontakti" class="venue-card__cta">Uzzināt detaļas →</a>
  </div>
</div>
```

### Tīpiskais CSS:
```css
.venue-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
  margin: 3rem 0;
}

.venue-card {
  background: #f9f7f3;
  border-radius: 12px;
  padding: 2rem;
  transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  border: 2px solid transparent;
  position: relative;
  overflow: hidden;
}

.venue-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 4px;
  background: linear-gradient(90deg, #d4a574, #8b6f47);
}

.venue-card:hover {
  transform: translateY(-12px);
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
  border-color: #d4a574;
}

.venue-card__icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.venue-card__title {
  font-size: 1.5rem;
  margin: 1rem 0;
  color: #2a2a2a;
}

.detail {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin: 0.75rem 0;
  font-size: 0.95rem;
  color: #555;
}

.detail-icon {
  font-size: 1.2rem;
}

.venue-card__cta {
  display: inline-block;
  margin-top: 1.5rem;
  color: #d4a574;
  font-weight: 600;
  text-decoration: none;
  transition: gap 0.3s ease;
}

.venue-card__cta:hover {
  gap: 0.5rem;
}

.venue-card--full {
  grid-column: 1 / -1;
  background: linear-gradient(135deg, #f9f7f3 0%, #ebe8e1 100%);
}

@media (max-width: 768px) {
  .venue-cards {
    grid-template-columns: 1fr;
  }
  
  .venue-card--full {
    grid-column: 1;
  }
}
```

---

## 🌟 Sagaidāmie Rezultāti

| Metrika | Pašreiz | Pēc Modernizācijas | Uzlabojums |
|---------|---------|-------------------|-----------|
| Vizuālā pievilcība | ⭐⭐ | ⭐⭐⭐⭐⭐ | +150% |
| Lietotāja engagement | ⭐⭐ | ⭐⭐⭐⭐ | +100% |
| Konversijas iespēja | Vidēja | Augsta | +40-60% |
| Responsīvums | Pamatīgs | Perfekts | ✓ |
| Animāciju atpazīstamība | 0% | 100% | Jauns |

---

## 📞 Nākamie Soļi

1. ✅ Apstiprināt prototipus
2. ✅ Izvēlēties implementācijas pieeja (plugini vai CSS/JS)
3. ✅ Izveidot kartīšu sistēma
4. ✅ Testēt mobilajā, planšetē, darbvielas ekrānos
5. ✅ Pievienot GA4 tracking engagement metrikām
6. ✅ Ieviešana un tiešraide

---

**Autors:** Dizaina analīze  
**Datums:** 2024  
**Versija:** 1.0
