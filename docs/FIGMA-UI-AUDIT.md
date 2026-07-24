# Homepage UI Fidelity Audit — Figma vs Deployed CSS

Figma file `fxxMEEhuCXWpJci6VAkXki` ("WARDJET NEW"), page *WARDjet Desktop* (`0:1`),
screen **Homepage_1** (`3:789`). Specs pulled via Figma MCP `get_design_context`.

Legend: ✅ match · ⚠️ negligible · ❌ deviation to fix

---

## KPIs — Figma `AXYZStats` (3:1005) → `inc/assets/css/parts/new-kpis.css`

| Property | Figma | Deployed CSS | Status |
|---|---|---|---|
| Section padding | 80px 96px | 80px 96px | ✅ |
| BG gradient colors/stops | #093C71 62.95% → #072C52 100% | same | ✅ |
| BG gradient angle | 88.96° | 89.81° | ⚠️ <1°, negligible |
| Number | Montserrat 400 / 60 / lh60 / white | 400 / 60 / 60 / #FFFFFF | ✅ |
| Label font | Montserrat 400 / 18 / lh28 | 400 / 18 / 28 | ✅ |
| Card gap | 12px | 12px | ✅ |
| **Label color** | **rgba(255,255,255,0.9)** | **#FFFFFF** | ❌ deviation (I set it to 100% earlier from a misread "layer opacity 100%"; design fill is 90%) |

**Action:** consider reverting label color to `rgba(255,255,255,0.9)` to match Figma.

---

## Industries grid — Figma `Materials/Industries` (6:391) → `inc/assets/css/parts/ind-mat-grid.css`

| Property | Figma | Deployed CSS | Status |
|---|---|---|---|
| Section padding | top 25 / sides 96 / bottom 50 | 25px 96px 50px | ✅ |
| Header→grid gap | 64px | 64px | ✅ |
| Title | Montserrat 600 / 60 / lh60 / white / center | 600 / 60 / 60 / #FFFFFF | ✅ |
| Desc | Montserrat 400 / 18 / lh28 / white / center | 400 / 18 / 28 / #FFFFFF | ✅ |
| Grid | 3 cols, 1000px, gap 16, card 322.67×281.33 | 3 cols, max 1000, gap 16, h281 | ✅ |
| Card | pad16, radius10, shadow 10/15/-3 + 4/6/-4 @0.1 | identical | ✅ |
| Card align | items-end | flex-end | ✅ |
| Card title | Montserrat 600 / 32 / **lh28** / white | 600 / 32 / **lh38** / #FFFFFF | ❌ line-height 28 vs 38 |
| Card overlay gradient | (verify — card render is image+title) | rgba(9,60,113,.9→.4→0) 0/50/100% | ⚠️ verify against Figma layer |

**Action:** card-title `line-height: 38px` → `28px` to match Figma. Confirm the blue
legibility overlay exists in the design (image tint) vs. CSS-only.
