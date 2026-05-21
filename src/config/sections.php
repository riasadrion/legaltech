<?php

return [

    'hero' => [
        'label' => 'Hero',
        'max_per_page' => 1,
        'deletable' => true,
        'default_data' => [
            'en' => [
                'heading' => 'Welcome to our platform',
                'subheading' => 'Everything you need, all in one place.',
                'cta_text' => 'Get started',
                'cta_url' => '/register',
                'image' => null,
            ],
        ],
    ],

    'features' => [
        'label' => 'Features',
        'max_per_page' => null,
        'deletable' => true,
        'default_data' => [
            'en' => [
                'heading' => 'Why choose us',
                'features' => [
                    ['icon' => 'bolt', 'title' => 'Fast', 'body' => 'Lightning-fast performance.'],
                    ['icon' => 'shield-check', 'title' => 'Secure', 'body' => 'Enterprise-grade security.'],
                    ['icon' => 'chart-bar', 'title' => 'Scalable', 'body' => 'Grows with your business.'],
                ],
            ],
        ],
    ],

    'cta' => [
        'label' => 'Call to Action',
        'max_per_page' => null,
        'deletable' => true,
        'default_data' => [
            'en' => [
                'heading' => 'Ready to get started?',
                'body' => 'Join thousands of users today.',
                'primary_text' => 'Sign up free',
                'primary_url' => '/register',
                'secondary_text' => 'Learn more',
                'secondary_url' => '/about',
            ],
        ],
    ],

    'content' => [
        'label' => 'Rich Content',
        'max_per_page' => null,
        'deletable' => true,
        'default_data' => [
            'en' => [
                'heading' => '',
                'body' => '<p>Add your content here.</p>',
            ],
        ],
    ],

    'gallery' => [
        'label' => 'Gallery',
        'max_per_page' => null,
        'deletable' => true,
        'default_data' => [
            'en' => ['heading' => ''],
            'images' => [],
        ],
    ],

    'logos' => [
        'label' => 'Logo Strip',
        'max_per_page' => null,
        'deletable' => true,
        'default_data' => [
            'en' => ['heading' => 'Trusted by'],
            'logos' => [],
        ],
    ],

    'stats' => [
        'label' => 'Stats',
        'max_per_page' => null,
        'deletable' => true,
        'default_data' => [
            'en' => [
                'heading' => '',
                'stats' => [
                    ['value' => '10K+', 'label' => 'Users'],
                    ['value' => '99.9%', 'label' => 'Uptime'],
                    ['value' => '24/7', 'label' => 'Support'],
                ],
            ],
        ],
    ],

    'cta_strip' => [
        'label' => 'CTA Strip',
        'max_per_page' => null,
        'deletable' => true,
        'default_data' => [
            'en' => [
                'text' => 'Start your free trial today.',
                'cta_text' => 'Get started',
                'cta_url' => '/register',
            ],
        ],
    ],

    'latest_blog_posts' => [
        'label' => 'Latest Blog Posts',
        'max_per_page' => 1,
        'deletable' => true,
        'default_data' => [
            'en' => ['heading' => 'Latest from the blog'],
            'count' => 3,
        ],
    ],

    'custom_html' => [
        'label' => 'Custom HTML',
        'max_per_page' => null,
        'deletable' => true,
        'default_data' => [
            'en' => ['title' => 'Custom block', 'body' => ''],
        ],
    ],

];
