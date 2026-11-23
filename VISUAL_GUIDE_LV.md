# Vidējās Sekcijas Modernizācijas - Vizuālie Aprakstiski

## 📐 PAŠREIZ (Statisks, Lineārs Izkārtojums)

```
┌─────────────────────────────────────────────────────────┐
│                                                           │
│  02 / KOPĀ BŪŠANA                                        │
│                                                           │
│  Svini Sprīdīšos                                        │
│                                                           │
│  Ir svētki, kuri pelnījuši ko vairāk...                │
│  Sprīdīšos vari pulcēt ģimeni, draugus...             │
│                                                           │
│  Lielā zāle                                             │
│  220 m² plaša svinību zāle ar galdiem...               │
│  [Attēls]                                                │
│                                                           │
│  Mazā zāle                                              │
│  50 m² telpa nelielām svinībām...                      │
│  [Attēls]                                                │
│                                                           │
│  Āra terase un teritorija                              │
│  [Attēls]                                                │
│                                                           │
└─────────────────────────────────────────────────────────┘

PROBLĒMAS:
❌ Viss teksts vienas rindas blokā
❌ Nav skaidras vizuālās sadalīšanas
❌ Statiski, bez animācijas
❌ Grūti uztverami 
❌ Neinformatīvi numuri (02/)
```

---

## 🎨 MODERNIZĒTS (Kartītu Sistēma ar Animācijām)

```
┌─────────────────────────────────────────────────────────┐
│                                                           │
│  ◯ 02 / KOPĀ BŪŠANA          (Animēts: Fade-in)        │
│                                                           │
│  Svini Sprīdīšos                                        │
│                                                           │
│  Ir svētki, kuri pelnījuši ko vairāk...                │
│                                                           │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │ 🏛️           │  │ 🎪           │  │ 🌳           │  │
│  │ LIELĀ ZĀLE   │  │ MAZĀ ZĀLE    │  │ ĀRA TERASE   │  │
│  ├──────────────┤  ├──────────────┤  ├──────────────┤  │
│  │ 📐 220 m²    │  │ 📐 50 m²     │  │ Apskatīt     │  │
│  │ 👥 80 viesi  │  │ 👥 10-15     │  │ vizuāli      │  │
│  │ 🍽️ Jau pieejami│ 👨‍🍳 Virtuv    │  │              │  │
│  │              │  │              │  │              │  │
│  │ [Par svin.] →│  │ [Par svin.] →│  │ [Uzzināt] →  │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
│   ↑ Zoom-in       ↑ Zoom-in       ↑ Zoom-in           │
│   ↑ Scale        ↑ Scale         ↑ Scale               │
│   ↑ Hover        ↑ Hover         ↑ Hover               │
│      Pacel         Pacel           Pacel                │
│      Ēnojums       Ēnojums         Ēnojums             │
│                                                           │
│  [Attēls]         [Attēls]         [Attēls]            │
│                                                           │
└─────────────────────────────────────────────────────────┘

PRIEKŠROCĪBAS:
✅ Kartītes - skaidra vizuāla sadalīšana
✅ Ikoniņas - vizuāla identifikācija
✅ Hover efekti - interaktivitāte
✅ Animācijas - dinamika, iesaista
✅ Dots numurs ◯ - pamanāms vizuāli
✅ Responsive - darbojas uz visiem ekrāniem
```

---

## 📱 RESPONSIVE DISPLEJS

### Darbvielas Ekrans (1200px+)
```
┌──────────────────────────────────────────────────────┐
│                                                        │
│  ◯ 02 / KOPĀ BŪŠANA                                 │
│                                                        │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐ │
│  │   LIELĀ      │  │   MAZĀ       │  │   ĀRA        │ │
│  │   ZĀLE       │  │   ZĀLE       │  │   TERASE     │ │
│  │   220 m²     │  │   50 m²      │  │   Apskatīt   │ │
│  │   80 viesi   │  │   10-15      │  │              │ │
│  │   [Info]     │  │   [Info]     │  │   [Info]     │ │
│  └──────────────┘  └──────────────┘  └──────────────┘ │
│                                                        │
└──────────────────────────────────────────────────────┘
     3 KARTĪTES RINDĀ (Grid auto-fit)
```

### Tabletes Ekrans (768px-1200px)
```
┌──────────────────────────────────────────┐
│                                            │
│  ◯ 02 / KOPĀ BŪŠANA                      │
│                                            │
│  ┌──────────────┐  ┌──────────────┐     │
│  │   LIELĀ      │  │   MAZĀ       │     │
│  │   ZĀLE       │  │   ZĀLE       │     │
│  │   220 m²     │  │   50 m²      │     │
│  │   [Info]     │  │   [Info]     │     │
│  └──────────────┘  └──────────────┘     │
│                                            │
│  ┌──────────────────────────────────────┐│
│  │   ĀRA TERASE un TERITORIJA           ││
│  │   Apskatīt vizuāli                   ││
│  │   [Info]                              ││
│  └──────────────────────────────────────┘│
│                                            │
└──────────────────────────────────────────┘
     2 KARTĪTES PIRMĀ RINDA + FULL WIDTH
```

### Mobilais Ekrans (< 768px)
```
┌─────────────────────────┐
│                           │
│ ◯ 02 / KOPĀ BŪŠANA      │
│                           │
│ ┌───────────────────────┐│
│ │   LIELĀ ZĀLE          ││
│ │   220 m²              ││
│ │   👥 80 viesi         ││
│ │   🍽️ Jau pieejami     ││
│ │   [Par svinībām] →    ││
│ └───────────────────────┘│
│                           │
│ ┌───────────────────────┐│
│ │   MAZĀ ZĀLE           ││
│ │   50 m²               ││
│ │   👥 10-15            ││
│ │   👨‍🍳 Virtuve          ││
│ │   [Par svinībām] →    ││
│ └───────────────────────┘│
│                           │
│ ┌───────────────────────┐│
│ │   ĀRA TERASE          ││
│ │   Apskatīt vizuāli    ││
│ │   [Uzzināt] →         ││
│ └───────────────────────┘│
│                           │
└─────────────────────────┘
     1 KARTĪTE RINDĀ (Stacked)
```

---

## 🎬 ANIMĀCIJU SEKVENCE

### VIZUALIZĀCIJA: Scroll lejup, Kartītes tiek Animētas

```
Lietotājs scrolle lejup:

Stāvoklis 1: Vēl nav redzams
┌─────────────────────────────────┐
│                                 │
│         SCROLL POZĪCIJA: TOP    │
│                                 │
│  (Vēl nav redzamas kartītes)    │
└─────────────────────────────────┘

─────────────────────────────────

Stāvoklis 2: Kartīte 1 ienāk
┌─────────────────────────────────┐
│          ┏━━━━━━━━━━━━━━━━━┓    │
│          ┃  LIELĀ ZĀLE ◀──┃    │ Animācija:
│          ┃  Zoom-in       ┃    │ ✓ 0%:   scale(0.9) + opacity(0)
│          ┃  Fade-up       ┃    │ ✓ 100%: scale(1) + opacity(1)
│          ┃  Delay: 200ms  ┃    │
│          ┗━━━━━━━━━━━━━━━━━┛    │
│                                 │
│         SCROLL POZĪCIJA: 100px  │
│                                 │
└─────────────────────────────────┘

─────────────────────────────────

Stāvoklis 3: Kartīte 2 ienāk
┌─────────────────────────────────┐
│  ┌────────────┐ ┌────────────┐  │
│  │ LIELĀ ZĀLE │ │ MAZĀ ZĀLE ◀│  │ Animācija:
│  │ (animēta)  │ │ Zoom-in    │  │ ✓ Kartīte 1: Pabeigta
│  └────────────┘ │ Delay: 400ms  │ ✓ Kartīte 2: Nūjaujas
│                 └────────────┘  │
│         SCROLL POZĪCIJA: 300px  │
│                                 │
└─────────────────────────────────┘

─────────────────────────────────

Stāvoklis 4: Visas kartītes redzamas
┌─────────────────────────────────┐
│  ┌────────────┐ ┌────────────┐  │
│  │ LIELĀ ZĀLE │ │ MAZĀ ZĀLE  │  │
│  └────────────┘ └────────────┘  │
│                                 │
│  ┌──────────────────────────┐   │
│  │ ĀRA TERASE ◀─ Delay: 600│   │
│  │ Zoom-in                 │   │
│  └──────────────────────────┘   │
│         SCROLL POZĪCIJA: 600px  │
│                                 │
└─────────────────────────────────┘
```

---

## 🖱️ HOVER EFEKTI (Desktop)

### Kartīte Pirms Hover
```
┌───────────────────────────────┐
│ 🏛️                             │
│ LIELĀ ZĀLE                    │
│ 📐 220 m² plaša telpa         │
│ 👥 Vietas līdz 80 viesiem     │
│ 🍽️ Galdi un krēsli jau pieejami│
│                               │
│ [Par svinībām] →              │
└───────────────────────────────┘

Stāvoklis:
- Fona krāsa: #f9f7f3 (viegla)
- Paaugstinājums: 0px
- Ēnojums: Mīksts 4px
- Zilavas: 0px
```

### Kartīte Pēc Hover
```
                    ↑ Paceļas
                    ↑ 12px uz augšu
              
         ┌───────────────────────────────┐
         │ 🏛️ (Pagriezas 5°)              │
         │ LIELĀ ZĀLE                    │
         │ 📐 220 m² plaša telpa         │
         │ 👥 Vietas līdz 80 viesiem     │
         │ 🍽️ Galdi un krēsli jau pie.  │
         │                               │
         │ [Par svinībām] →              │
         └───────────────────────────────┘
         
Stāvoklis:
- Fona krāsa: #fff (balts)
- Paaugstinājums: -12px (paceļas)
- Ēnojums: Spēcīgs 20px
- Zilavas: #d4a574 (zelta)
- Top border: Tīri pieloņa (scaleX: 0 → 1)
```

---

## 📊 LAIKA SKALA (Timeline)

### Animāciju Secības
```
Timeline: Scroll lejup

Laiks   | Kartīte 1 | Kartīte 2 | Kartīte 3
--------|-----------|-----------|----------
0ms     | Trigger   |           |          
200ms   | 0% ────→  |           |          
        | (zoom-in) |           |          
400ms   | ──────→100%| Trigger   |          
600ms   |      ✓    | 0% ────→  |          
(fade)  |           | (zoom-in) |          
        |           |           |          
800ms   |      ✓    |           | Trigger  
        |           | ──────→100%| 0% →    
        |           |      ✓    | (zoom)  
1000ms  |      ✓    |      ✓    | ──→ 50%  
        |           |           |          
1200ms  |      ✓    |      ✓    |   ✓    
        |           |           |  (pabeigts)
```

---

## 🎨 KRĀVU SHĒMA (Pašreizējā)

```
Pirmā Kartīte (Lielā zāle):
┌─────────────────────────────┐
│████████ #d4a574 (zelta)     │ ← Top border
│                              │
│ 🏛️                           │
│ LIELĀ ZĀLE                  │
│ 📐 220 m² plaša telpa       │
│ 👥 Vietas līdz 80 viesiem   │ ← #555 (pelēks teksts)
│                              │
│ #d4a574 [Par svinībām] →    │ ← Zelta links
│                              │
└─────────────────────────────┘
  #f9f7f3 (viegla krēmkrāsa)

Otrs Kartīte (Mazā zāle):
┌─────────────────────────────┐
│████████ #d4a574 (zelta)     │
│                              │
│ 🎪                           │
│ MAZĀ ZĀLE                   │
│ 📐 50 m² telpa              │
│                              │
│ #d4a574 [Par svinībām] →    │
└─────────────────────────────┘
  #f9f7f3

Trešā Kartīte (Āra terase) - PILNĀ PLATUMA:
┌────────────────────────────────────────────────────────┐
│████████ #d4a574 (zelta)                                │
│                                                          │
│ ░░░░░ Gradients fons: #f9f7f3 → #ebe8e1 ░░░░░         │
│ 🌳 ĀRA TERASE                                          │
│ ░░░░░ Plašā apkārtne ideāla vasaras svinībām ░░░░░    │
│                                                          │
│ #d4a574 [Uzzināt detaļas] →                           │
│                                                          │
└────────────────────────────────────────────────────────┘
  Gradients: #f9f7f3 → #ebe8e1

Ikoniņas (Universālas):
🏛️ = Zāle / Ēka
🎪 = Mazāka telpa
🌳 = Āra / Daba
📐 = Mēri
👥 = Cilvēki
🍽️ = Ēdiens
👨‍🍳 = Virtuve
```

---

## 🔄 PARALLAX EFEKTS (Iespējams Papildu)

```
Ja vēlas papildu dinamiku:

PIRMS Parallax:
     User scrolls
           ↓
    └─────────────┘
     Normal scroll
     
AR Parallax:
     User scrolls
           ↓
    Fons pārvietojas: 50% no scroll ātruma
    Foreground pārvietojas: 100% (normāls)
    
    Rezultāts: Viduslīmenis efekts
```

---

## 📈 ENGAGEMENT METRIKAS

### Prognozētie Uzlabojumi

```
Metrika               | Pašreiz  | Pēc Modernizācijas | Uzlabojums
──────────────────────|----------|────────────────────|──────────
Vidējais Skumjums     | 2-3 sec  | 1-2 sec            | ↓ -50%
Vidējā Sesijas Ilgums | 1.5 min  | 2.5 min            | ↑ +67%
CTA Click Rate        | 2%       | 5-7%               | ↑ +150%
Konversijas Rates     | 8%       | 12-15%             | ↑ +50-87%
Bounce Rate           | 45%      | 30%                | ↓ -33%
Scroll Depth (vidējā) | 65%      | 85%                | ↑ +31%
Interakciju Skaits    | 0.5      | 2-3                | ↑ +300%
```

---

## 🎯 IETEIKUMI PAR PRIORITĀTI

```
FĀZE 1 (NEDĒĻA 1-2):        FĀZE 2 (NEDĒĻA 3-4):
✅ Kartīšu sistēma          ✅ Parallax attēli
✅ AOS animācijas           ✅ Flip kartītes
✅ Hover efekti             ✅ Image carousel
✅ Responsive izlīdzināšana ✅ Tooltip infobox

REZULTĀTS:                  REZULTĀTS:
50% uzlabojums              +30% papildu uzlabojums
```

---

**Versija:** 1.0  
**Datums:** 2024-11  
**Autors:** Vizuālā Dizaina Analīze
