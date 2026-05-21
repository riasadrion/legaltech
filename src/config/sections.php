<?php

return [

    'hero' => [
        'label' => 'Hero',
        'max_per_page' => 1,
        'deletable' => false,
        'fields' => [
            ['pill_label', 'text', 'Pill — badge label'],
            ['pill_text',  'text', 'Pill — text'],
            ['pill_url',   'url',  'Pill — URL'],
            ['heading_line_1',   'text', 'Heading line 1'],
            ['heading_italic_1', 'text', 'Heading line 1 — italic accent'],
            ['heading_line_2',   'text', 'Heading line 2'],
            ['heading_italic_2', 'text', 'Heading line 2 — italic accent'],
            ['subheading', 'textarea', 'Subheading'],
            ['subheading_emphasis', 'text', 'Subheading — emphasis (white)'],
            ['primary_text', 'text', 'Primary button — text'],
            ['primary_url',  'url',  'Primary button — URL'],
            ['secondary_text', 'text', 'Secondary button — text'],
            ['secondary_url',  'url',  'Secondary button — URL'],
        ],
    ],

    'logos' => [
        'label' => 'Trusted-by / Logo Strip',
        'max_per_page' => 1,
        'deletable' => false,
        'fields' => [
            ['heading', 'text', 'Eyebrow / heading'],
            ['logos', 'Logos', [
                ['url', 'image', 'Image'],
                ['alt', 'text',  'Alt text'],
            ]],
        ],
    ],

    'blog' => [
        'label' => 'Latest Blog Posts',
        'max_per_page' => 1,
        'deletable' => true,
        'fields' => [
            ['eyebrow', 'text', 'Eyebrow label'],
            ['heading', 'text', 'Heading (HTML allowed)'],
            ['link_text', 'text', 'View all link — text'],
            ['link_url', 'url', 'View all link — URL'],
        ],
    ],

    'cta' => [
        'label' => 'Final CTA',
        'max_per_page' => 1,
        'deletable' => true,
        'fields' => [
            ['pill_text', 'text', 'Pill / badge text'],
            ['heading_line_1', 'text', 'Heading line 1'],
            ['heading_line_2', 'text', 'Heading line 2 (italic accent)'],
            ['subheading', 'textarea', 'Subheading'],
            ['primary_text', 'text', 'Primary button — text'],
            ['primary_url', 'url', 'Primary button — URL'],
            ['primary_icon', 'text', 'Primary button — icon class (e.g. ri-arrow-right-up-line)'],
            ['secondary_text', 'text', 'Secondary button — text'],
            ['secondary_url', 'url', 'Secondary button — URL'],
            ['secondary_icon', 'text', 'Secondary button — icon class (e.g. ri-calendar-line)'],
        ],
    ],

    'faq' => [
        'label' => 'FAQ',
        'max_per_page' => 1,
        'deletable' => true,
        'fields' => [
            ['eyebrow', 'text', 'Eyebrow label'],
            ['heading', 'text', 'Heading (HTML allowed)'],
            ['items', 'Items', [
                ['question', 'text', 'Question'],
                ['answer', 'textarea', 'Answer'],
            ]],
        ],
    ],

    'testimonials' => [
        'label' => 'Customer Stories / Testimonials',
        'max_per_page' => 1,
        'deletable' => true,
        'fields' => [
            ['eyebrow', 'text', 'Eyebrow label'],
            ['heading', 'text', 'Heading (HTML allowed)'],
            ['rating_text', 'text', 'Rating label (e.g. 4.9 · 1,247 reviews on G2)'],
            ['testimonials', 'Testimonials', [
                ['quote', 'textarea', 'Quote'],
                ['name', 'text', 'Name'],
                ['role', 'text', 'Role & company'],
            ]],
        ],
    ],

    'why_us' => [
        'label' => 'Why Us',
        'max_per_page' => 1,
        'deletable' => true,
        'fields' => [
            ['eyebrow', 'text', 'Eyebrow label'],
            ['heading', 'text', 'Heading (HTML allowed)'],
            ['body', 'textarea', 'Body paragraph'],
            ['stats', 'Stats', [
                ['value', 'text', 'Stat value (e.g. 94%)'],
                ['label', 'text', 'Stat label'],
            ]],
            ['cards', 'Cards', [
                ['icon', 'text', 'Remix icon class (e.g. ri-shield-keyhole-fill)'],
                ['title', 'text', 'Title'],
                ['body', 'textarea', 'Body'],
                ['dark', 'text', 'Dark card? (yes/no)'],
            ]],
        ],
    ],

    'pricing' => [
        'label' => 'Pricing',
        'max_per_page' => 1,
        'deletable' => true,
        'fields' => [
            ['eyebrow', 'text', 'Eyebrow label'],
            ['heading', 'text', 'Heading'],
            ['subheading', 'textarea', 'Subheading'],
            ['tab_per_contract', 'text', 'Tab label — Pay per contract'],
            ['tab_monthly', 'text', 'Tab label — Monthly'],
            ['tab_yearly', 'text', 'Tab label — Yearly'],
        ],
    ],

    'workflow' => [
        'label' => 'Workflow / How It Works',
        'max_per_page' => 1,
        'deletable' => true,
        'fields' => [
            ['eyebrow', 'text', 'Eyebrow label'],
            ['heading', 'text', 'Heading (HTML allowed)'],
            ['cta_text', 'text', 'CTA button — text'],
            ['cta_url', 'url', 'CTA button — URL'],
            ['steps', 'Steps', [
                ['tag', 'text', 'Step tag (e.g. brief)'],
                ['title', 'text', 'Title'],
                ['body', 'textarea', 'Body'],
            ]],
        ],
    ],

    'custom_html' => [
        'label' => 'Custom HTML',
        'max_per_page' => null,
        'deletable' => true,
        'fields' => [
            ['body', 'richtext', 'Content'],
        ],
    ],

];
