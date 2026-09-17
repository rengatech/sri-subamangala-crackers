# User-Facing Design Overhaul — Design Document

**Goal:** Complete redesign of all user-facing pages (Home, About, Contact, Privacy Policy) with a modern, custom look while preserving the existing color palette. Ecommerce-first approach — minimize friction from browse to purchase.

**Pages removed:** Collection, Portfolio (merged into Home product experience)

---

## Tech Stack

- **CSS:** Tailwind CSS v3 (replaces Bootstrap 5 entirely)
- **Icons:** Heroicons (replaces Bootstrap Icons)
- **Removed:** jQuery, Magnific Popup, Swiper.js, AOS, Animate.css, main.js
- **Animations:** Vue 3 `<Transition>`, CSS transforms, native IntersectionObserver
- **Image zoom:** CSS-only hover zoom
- **Fonts:** Open Sans (body) + Raleway (headings) — kept

### Color Palette (preserved)

| Token | Hex | Usage |
|-------|-----|-------|
| Primary Red | `#e03a3c` | Buttons, links, active states, accents |
| Dark Maroon | `#991b1b` | Discount badges, emphasis |
| Text Dark | `#111111` | Headings, nav |
| Text Gray | `#444444` | Body text |
| Background | `#f8f9fa` | Page background |
| White | `#ffffff` | Cards, navbar |
| Success Green | (Tailwind green-600) | Final price display |

### Layout Components (unified)

- Single `Navbar.vue` (replaces Navbar.vue + Navbar1.vue)
- Single `Footer.vue`
- `HomeLayout.vue` — shopping page
- `OtherLayout.vue` — secondary pages

---

## Home Page

### Structure (top to bottom)

1. **Slim announcement bar** — Thin colored strip above header with marquee content. Dismissible with ×.

2. **Sticky header** — Logo left, nav center (Home, About, Contact), right side: "Download Price List" outlined button + cart icon with badge. White bg, subtle shadow on scroll.

3. **Sticky category tabs** — Horizontal scrollable pills below header. Active tab = primary red. Sticks on scroll below header.

4. **Product grid** — 3 col desktop, 2 col tablet, 1 col mobile. Each card:
   - Product image (CSS hover zoom)
   - Product name (English + Tamil below in lighter gray)
   - Original price ~~strikethrough~~ → Final price in green
   - Discount badge (red pill, e.g., "-30%")
   - Add to cart / quantity stepper (+/-)

5. **Slide-out cart drawer** — Opens from right on cart icon click. Shows items, quantities, subtotal. "Proceed to Checkout" button.

6. **Footer** — 3-column: company info, quick links, contact + Google Maps.

### Removed from current design
- Hero section / dark maroon banner
- Product disclaimer block
- Inline customer form on right side
- Accordion-style category grouping
- Marquee (replaced with announcement bar)

---

## Checkout Page (NEW — `/checkout`)

### Layout

- **Left (60%):** Order summary — item list with name, qty, unit price, line total. Editable quantities. Remove per item. Subtotal, discount, net total.
- **Right (40%):** Customer details form — Name, Mobile, WhatsApp (with "same as mobile" checkbox), Address (street, city, pincode), Payment method, "Place Order" button.
- **Mobile:** Stacks vertically — summary on top, form below.
- **Post-order:** Redirect to Thank You page with order ID.

---

## Navigation

### Desktop
- Fixed/sticky white navbar
- Logo + company name left
- Center: Home, About, Contact Us
- Right: "Download Price List" (outlined red button) + Cart icon with badge
- Shadow appears on scroll

### Mobile
- Sticky compact header — logo left, hamburger right
- Hamburger opens full-screen overlay menu
- **Floating cart button** — fixed bottom-right, red circle, cart icon + badge count, opens cart drawer

### Unified
- Single Navbar.vue for all pages
- Single Footer.vue for all pages
- Active link highlighted in red

---

## About Page

- Breadcrumb
- Company story: text + image side by side (no carousel)
- 4 feature cards grid (Genuine Price, Best Quality, Safe To Use, Trusted) with Heroicons
- FAQ expandable cards (Vue `<Transition>`)

## Contact Page

- Two-column: contact info cards left, form right
- Google Maps embed below, full width
- Phone numbers shown once, cleanly

## Privacy Policy Page

- Single-column readable layout
- Proper heading hierarchy
- Same content, better typography

### All secondary pages share
- Consistent breadcrumb header
- Same max-width container (`max-w-6xl`)
- Consistent card styling (rounded-xl, shadow-sm)

---

## Visual Style

### Typography
- Headings: Raleway semi-bold, tight letter-spacing
- Body: Open Sans 16px, 1.6 line-height
- Product names: 14px medium, Tamil in lighter gray
- Prices: Tabular numbers

### Spacing
- 8px grid system
- Cards: 16px padding, 8px gap
- Sections: 64px vertical spacing

### Cards & Surfaces
- `rounded-xl` (12px corners)
- `shadow-sm`, elevated shadow on hover
- White cards on `#f8f9fa` background

### Interactions
- Buttons: 150ms color transition on hover
- Product cards: subtle lift + shadow on hover
- Cart drawer: slide from right with backdrop
- Category tabs: smooth highlight transition
- Quantity stepper: compact +/- with number between

### Animations (no libraries)
- Inertia progress bar for page transitions
- Vue `<Transition>` for drawer, menu, accordions
- CSS `transform` for hover lift
- No scroll-triggered animations (fast, clean)
