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

    'custom_html' => [
        'label' => 'Custom HTML',
        'max_per_page' => null,
        'deletable' => true,
        'fields' => [
            ['body', 'richtext', 'Content'],
        ],
    ],

];
