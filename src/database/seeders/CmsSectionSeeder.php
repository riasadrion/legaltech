<?php

namespace Database\Seeders;

use App\Domain\Cms\Models\Page;
use App\Domain\Cms\Models\PageSection;
use Illuminate\Database\Seeder;

class CmsSectionSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create a demo page for sections
        $page = Page::firstOrCreate(
            ['slug' => 'home'],
            [
                'title' => ['en' => 'Home', 'fr' => 'Accueil'],
                'status' => 'published',
                'is_system' => false,
            ]
        );

        $sections = [
            [
                'type' => 'hero',
                'data' => [
                    'en' => [
                        'heading' => 'Welcome to Our Platform',
                        'subheading' => 'The easiest way to manage your payments and subscriptions',
                        'cta_text' => 'Get Started',
                        'cta_url' => '/register',
                        'background_image' => '/images/hero-bg.jpg',
                    ],
                    'fr' => [
                        'heading' => 'Bienvenue sur notre plateforme',
                        'subheading' => 'Le moyen le plus simple de gérer vos paiements et abonnements',
                        'cta_text' => 'Commencer',
                        'cta_url' => '/register',
                        'background_image' => '/images/hero-bg.jpg',
                    ],
                ],
                'enabled' => true,
                'sort' => 1,
            ],
            [
                'type' => 'features',
                'data' => [
                    'en' => [
                        'heading' => 'Powerful Features',
                        'description' => 'Everything you need to manage payments',
                        'features' => [
                            ['icon' => 'check', 'title' => 'Multiple Payment Methods', 'description' => 'Support for 12+ payment gateways'],
                            ['icon' => 'lock', 'title' => 'Secure Transactions', 'description' => 'Industry-leading security standards'],
                            ['icon' => 'trending-up', 'title' => 'Analytics & Reporting', 'description' => 'Real-time insights and detailed reports'],
                            ['icon' => 'zap', 'title' => 'Fast Payouts', 'description' => 'Withdraw your earnings quickly'],
                        ],
                    ],
                    'fr' => [
                        'heading' => 'Fonctionnalités Puissantes',
                        'description' => 'Tout ce dont vous avez besoin pour gérer les paiements',
                        'features' => [
                            ['icon' => 'check', 'title' => 'Plusieurs Méthodes de Paiement', 'description' => 'Support pour 12+ passerelles de paiement'],
                            ['icon' => 'lock', 'title' => 'Transactions Sécurisées', 'description' => 'Normes de sécurité de pointe'],
                            ['icon' => 'trending-up', 'title' => 'Analyse et Rapports', 'description' => 'Informations en temps réel et rapports détaillés'],
                            ['icon' => 'zap', 'title' => 'Paiements Rapides', 'description' => 'Retirez vos gains rapidement'],
                        ],
                    ],
                ],
                'enabled' => true,
                'sort' => 2,
            ],
            [
                'type' => 'stats',
                'data' => [
                    'en' => [
                        'heading' => 'Trusted by Businesses',
                        'stats' => [
                            ['label' => 'Active Users', 'value' => '10,000+'],
                            ['label' => 'Transactions', 'value' => '1M+'],
                            ['label' => 'Countries', 'value' => '50+'],
                            ['label' => 'Support', 'value' => '24/7'],
                        ],
                    ],
                    'fr' => [
                        'heading' => 'De Confiance pour les Entreprises',
                        'stats' => [
                            ['label' => 'Utilisateurs Actifs', 'value' => '10,000+'],
                            ['label' => 'Transactions', 'value' => '1M+'],
                            ['label' => 'Pays', 'value' => '50+'],
                            ['label' => 'Support', 'value' => '24/7'],
                        ],
                    ],
                ],
                'enabled' => true,
                'sort' => 3,
            ],
            [
                'type' => 'cta',
                'data' => [
                    'en' => [
                        'heading' => 'Ready to Get Started?',
                        'description' => 'Join thousands of users managing payments with our platform',
                        'button_text' => 'Create Free Account',
                        'button_url' => '/register',
                        'background_color' => '#3b82f6',
                    ],
                    'fr' => [
                        'heading' => 'Prêt à commencer ?',
                        'description' => 'Rejoignez des milliers d\'utilisateurs qui gèrent les paiements avec notre plateforme',
                        'button_text' => 'Créer un compte gratuit',
                        'button_url' => '/register',
                        'background_color' => '#3b82f6',
                    ],
                ],
                'enabled' => true,
                'sort' => 4,
            ],
            [
                'type' => 'gallery',
                'data' => [
                    'en' => [
                        'heading' => 'Platform Features',
                        'images' => [
                            ['url' => '/images/feature-1.jpg', 'alt' => 'Payment Dashboard'],
                            ['url' => '/images/feature-2.jpg', 'alt' => 'Analytics'],
                            ['url' => '/images/feature-3.jpg', 'alt' => 'Wallet Management'],
                            ['url' => '/images/feature-4.jpg', 'alt' => 'Reports'],
                        ],
                    ],
                    'fr' => [
                        'heading' => 'Fonctionnalités de la Plateforme',
                        'images' => [
                            ['url' => '/images/feature-1.jpg', 'alt' => 'Tableau de Bord des Paiements'],
                            ['url' => '/images/feature-2.jpg', 'alt' => 'Analyses'],
                            ['url' => '/images/feature-3.jpg', 'alt' => 'Gestion du Portefeuille'],
                            ['url' => '/images/feature-4.jpg', 'alt' => 'Rapports'],
                        ],
                    ],
                ],
                'enabled' => true,
                'sort' => 5,
            ],
            [
                'type' => 'logos',
                'data' => [
                    'en' => [
                        'heading' => 'Trusted by Companies',
                        'logos' => [
                            ['url' => '/images/logo-1.png', 'alt' => 'Company 1'],
                            ['url' => '/images/logo-2.png', 'alt' => 'Company 2'],
                            ['url' => '/images/logo-3.png', 'alt' => 'Company 3'],
                            ['url' => '/images/logo-4.png', 'alt' => 'Company 4'],
                        ],
                    ],
                    'fr' => [
                        'heading' => 'De Confiance pour les Entreprises',
                        'logos' => [
                            ['url' => '/images/logo-1.png', 'alt' => 'Entreprise 1'],
                            ['url' => '/images/logo-2.png', 'alt' => 'Entreprise 2'],
                            ['url' => '/images/logo-3.png', 'alt' => 'Entreprise 3'],
                            ['url' => '/images/logo-4.png', 'alt' => 'Entreprise 4'],
                        ],
                    ],
                ],
                'enabled' => true,
                'sort' => 6,
            ],
        ];

        foreach ($sections as $sectionData) {
            PageSection::create([
                ...$sectionData,
                'page_id' => $page->id,
            ]);
        }
    }
}
