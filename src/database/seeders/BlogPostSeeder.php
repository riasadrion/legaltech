<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $categories = BlogCategory::all();
        $admin = Admin::first();

        if ($categories->isEmpty() || ! $admin) {
            return;
        }

        $posts = [
            [
                'title' => ['en' => 'Getting Started with Our Platform', 'fr' => 'Commencer avec notre plateforme'],
                'excerpt' => ['en' => 'Learn the basics of our platform and how to get started.', 'fr' => 'Apprenez les bases de notre plateforme et comment commencer.'],
                'body' => ['en' => 'Welcome to our platform! In this guide, we\'ll walk you through the basics of getting started. First, create an account, verify your email, and you\'re ready to go. Our platform is designed to be intuitive and easy to use for both beginners and advanced users.', 'fr' => 'Bienvenue sur notre plateforme ! Dans ce guide, nous vous expliquerons les bases pour commencer. Créez d\'abord un compte, vérifiez votre e-mail, et vous êtes prêt. Notre plateforme est conçue pour être intuitive et facile à utiliser pour les débutants et les utilisateurs avancés.'],
                'slug' => 'getting-started-with-our-platform',
                'status' => BlogPost::STATUS_PUBLISHED,
                'blog_category_id' => 1,
                'published_at' => now()->subDays(60),
            ],
            [
                'title' => ['en' => 'Advanced Features Guide', 'fr' => 'Guide des fonctionnalités avancées'],
                'excerpt' => ['en' => 'Explore advanced features to maximize your productivity.', 'fr' => 'Explorez les fonctionnalités avancées pour maximiser votre productivité.'],
                'body' => ['en' => 'Now that you\'re familiar with the basics, it\'s time to explore advanced features. Our platform offers powerful tools for automation, analytics, and integrations. Learn how to set up webhooks, custom workflows, and automated reporting to streamline your operations.', 'fr' => 'Maintenant que vous connaissez les bases, il est temps d\'explorer les fonctionnalités avancées. Notre plateforme offre des outils puissants pour l\'automatisation, l\'analyse et les intégrations. Découvrez comment configurer des webhooks, des workflows personnalisés et des rapports automatisés.'],
                'slug' => 'advanced-features-guide',
                'status' => BlogPost::STATUS_PUBLISHED,
                'blog_category_id' => 1,
                'published_at' => now()->subDays(50),
            ],
            [
                'title' => ['en' => 'Understanding Payment Methods', 'fr' => 'Comprendre les méthodes de paiement'],
                'excerpt' => ['en' => 'A comprehensive guide to available payment methods and how to choose the right one.', 'fr' => 'Un guide complet des méthodes de paiement disponibles et comment choisir la bonne.'],
                'body' => ['en' => 'We support multiple payment methods including Stripe, PayPal, Razorpay, and cryptocurrency options. Each method has its own fees and processing times. Choose the method that best fits your needs. Bank transfers offer lower fees but longer processing times, while credit cards are instant but may incur higher fees.', 'fr' => 'Nous supportons plusieurs méthodes de paiement, notamment Stripe, PayPal, Razorpay et les options de cryptomonnaie. Chaque méthode a ses propres frais et délais de traitement. Choisissez la méthode qui correspond le mieux à vos besoins.'],
                'slug' => 'understanding-payment-methods',
                'status' => BlogPost::STATUS_PUBLISHED,
                'blog_category_id' => 2,
                'published_at' => now()->subDays(40),
            ],
            [
                'title' => ['en' => 'Billing and Subscription FAQ', 'fr' => 'FAQ sur la facturation et les abonnements'],
                'excerpt' => ['en' => 'Common questions about billing, subscriptions, and refunds.', 'fr' => 'Questions courantes sur la facturation, les abonnements et les remboursements.'],
                'body' => ['en' => 'Do we offer refunds? Yes, within 30 days of purchase. Can I change my plan? Yes, anytime. What happens when my subscription renews? You\'ll receive an invoice and payment will be processed automatically. Our flexible billing system ensures you\'re always in control.', 'fr' => 'Offrez-vous des remboursements ? Oui, dans les 30 jours suivant l\'achat. Puis-je changer mon plan ? Oui, à tout moment. Que se passe-t-il quand mon abonnement se renouvelle ? Vous recevrez une facture et le paiement sera traité automatiquement.'],
                'slug' => 'billing-subscription-faq',
                'status' => BlogPost::STATUS_PUBLISHED,
                'blog_category_id' => 2,
                'published_at' => now()->subDays(30),
            ],
            [
                'title' => ['en' => 'API Integration Best Practices', 'fr' => 'Meilleures pratiques d\'intégration API'],
                'excerpt' => ['en' => 'Learn best practices for integrating our API into your applications.', 'fr' => 'Apprenez les meilleures pratiques pour intégrer notre API dans vos applications.'],
                'body' => ['en' => 'When integrating with our API, always use HTTPS, validate API responses, implement retry logic, and monitor your API usage. Use OAuth 2.0 for authentication and keep your API keys secure. Rate limiting is implemented to prevent abuse. Always review the API documentation for the latest endpoints and changes.', 'fr' => 'Lors de l\'intégration à notre API, utilisez toujours HTTPS, validez les réponses d\'API, implémentez la logique de nouvelle tentative et surveillez votre utilisation d\'API. Utilisez OAuth 2.0 pour l\'authentification et maintenez vos clés API sécurisées.'],
                'slug' => 'api-integration-best-practices',
                'status' => BlogPost::STATUS_PUBLISHED,
                'blog_category_id' => 1,
                'published_at' => now()->subDays(20),
            ],
            [
                'title' => ['en' => 'Security Best Practices for Your Account', 'fr' => 'Meilleures pratiques de sécurité pour votre compte'],
                'excerpt' => ['en' => 'Protect your account with these essential security measures.', 'fr' => 'Protégez votre compte avec ces mesures de sécurité essentielles.'],
                'body' => ['en' => 'Enable two-factor authentication on your account. Use a strong, unique password. Never share your API keys or credentials. Regularly review your account activity and connected applications. If you notice suspicious activity, change your password immediately and contact our support team. We take security seriously and are here to help.', 'fr' => 'Activez l\'authentification à deux facteurs sur votre compte. Utilisez un mot de passe fort et unique. Ne partagez jamais vos clés API ou vos identifiants. Vérifiez régulièrement votre activité de compte et les applications connectées.'],
                'slug' => 'security-best-practices',
                'status' => BlogPost::STATUS_PUBLISHED,
                'blog_category_id' => 1,
                'published_at' => now()->subDays(10),
            ],
            [
                'title' => ['en' => 'Upcoming Features Roadmap', 'fr' => 'Feuille de route des fonctionnalités à venir'],
                'excerpt' => ['en' => 'Preview of exciting features coming to our platform.', 'fr' => 'Aperçu des fonctionnalités passionnantes à venir sur notre plateforme.'],
                'body' => ['en' => 'We\'re excited to announce upcoming features: Advanced analytics dashboard (June). Marketplace for integrations (July). Mobile apps for iOS and Android (August). Custom branding options (September). We value your input - let us know what features you\'d like to see next!', 'fr' => 'Nous sommes heureux d\'annoncer les fonctionnalités à venir : tableau de bord d\'analyse avancée (juin). Marché pour les intégrations (juillet). Applications mobiles pour iOS et Android (août). Options de marque personnalisée (septembre).'],
                'slug' => 'upcoming-features-roadmap',
                'status' => BlogPost::STATUS_SCHEDULED,
                'blog_category_id' => 2,
                'published_at' => now()->addDays(15),
            ],
        ];

        foreach ($posts as $postData) {
            BlogPost::create([
                ...$postData,
                'author_id' => $admin->id,
            ]);
        }
    }
}
