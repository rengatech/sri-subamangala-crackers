<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sri Subamangala Crackers - Buy Crackers Online Sivakasi | Wholesale & Retail Fireworks</title>
    <meta name="description" content="Buy crackers online from Sivakasi at wholesale prices. Sri Subamangala Crackers offers 200+ fireworks products with up to 60% discount. Safe green crackers, gift boxes, sparklers, aerial shots & more. All India delivery.">
    <meta name="keywords" content="buy crackers online, sivakasi crackers, diwali crackers, wholesale crackers, fireworks online, green crackers, crackers price list, sivakasi fireworks, crackers gift box, crackers delivery india, madhu crackers">
    <meta name="author" content="Sri Subamangala Crackers">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="Sri Subamangala Crackers - Buy Crackers Online Sivakasi | Wholesale & Retail">
    <meta property="og:description" content="Buy crackers online from Sivakasi at wholesale prices. 200+ products, up to 60% discount, all India delivery. Sparklers, aerial shots, gift boxes & more.">
    <meta property="og:image" content="{{ asset('assets/img/MADHU.svg') }}">
    <meta property="og:site_name" content="Sri Subamangala Crackers">
    <meta property="og:locale" content="en_IN">

    <!-- Twitter / X -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="Sri Subamangala Crackers - Buy Crackers Online Sivakasi">
    <meta name="twitter:description" content="Buy crackers online from Sivakasi at wholesale prices. 200+ products, up to 60% discount, all India delivery.">
    <meta name="twitter:image" content="{{ asset('assets/img/MADHU.svg') }}">

    <!-- WhatsApp Preview -->
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Geo Tags -->
    <meta name="geo.region" content="IN-TN">
    <meta name="geo.placename" content="Sivakasi">
    <meta name="geo.position" content="9.371407;77.810753">
    <meta name="ICBM" content="9.371407, 77.810753">

    <!-- Favicon -->
    <link href="/assets/img/MADHU.svg" rel="icon">
    <link rel="apple-touch-icon" href="/assets/img/MADHU.svg">

    <!-- JSON-LD Structured Data -->
    @verbatim
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Store",
        "name": "Sri Subamangala Crackers",
        "description": "Buy crackers online from Sivakasi at wholesale and retail prices. Sparklers, aerial shots, gift boxes, green crackers and more with all India delivery.",
        "url": "http://sri-subamangala-crackers.test/",
        "logo": "http://sri-subamangala-crackers.test/assets/img/sri-mangala-crackers/logo.jpeg",
        "image": "http://sri-subamangala-crackers.test/assets/img/sri-mangala-crackers/logo.jpeg",
        "telephone": "+919600331523",
        "email": "mahendranramar80@gmail.com",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "D.No:3/203/D Palammal Colony, Thayilpatti",
            "addressLocality": "Sivakasi",
            "addressRegion": "Tamil Nadu",
            "postalCode": "626131",
            "addressCountry": "IN"
        },
        "geo": {
            "@type": "GeoCoordinates",
            "latitude": 9.371407,
            "longitude": 77.810753
        },
        "priceRange": "₹30 - ₹50,000",
        "openingHoursSpecification": {
            "@type": "OpeningHoursSpecification",
            "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"],
            "opens": "08:00",
            "closes": "21:00"
        },
        "sameAs": [
            "https://wa.link/w3i0ww"
        ]
    }
    </script>

    <!-- Sitelinks Search Box -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "Sri Subamangala Crackers",
        "url": "http://sri-subamangala-crackers.test/",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "http://sri-subamangala-crackers.test/?q={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>

    <!-- Breadcrumb / SiteNavigationElement for Sitelinks -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ItemList",
        "itemListElement": [
            {"@type": "SiteNavigationElement", "position": 1, "name": "Home", "url": "http://sri-subamangala-crackers.test/"},
            {"@type": "SiteNavigationElement", "position": 2, "name": "About Us", "url": "http://sri-subamangala-crackers.test/about"},
            {"@type": "SiteNavigationElement", "position": 3, "name": "FAQ", "url": "http://sri-subamangala-crackers.test/faq"},
            {"@type": "SiteNavigationElement", "position": 4, "name": "Blog", "url": "http://sri-subamangala-crackers.test/safetytips"},
            {"@type": "SiteNavigationElement", "position": 5, "name": "Contact", "url": "http://sri-subamangala-crackers.test/contact"},
            {"@type": "SiteNavigationElement", "position": 6, "name": "Privacy Policy", "url": "http://sri-subamangala-crackers.test/privacy-policy"},
            {"@type": "SiteNavigationElement", "position": 7, "name": "Price List", "url": "http://sri-subamangala-crackers.test/pricelist"}
        ]
    }
    </script>
    @endverbatim

    @routes
    @vite(['resources/js/app.js'])
    @inertiaHead
</head>
<body class="font-sans antialiased bg-brand-light text-brand-gray">
    @inertia
</body>
</html>
