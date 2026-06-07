<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Seeder;

class CmsSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSections();
        $this->seedPages();
        $this->seedMenus();
    }

    private function seedSections(): void
    {
        $sections = [
            [
                'id' => 1,
                'type' => 'hero',
                'enabled' => true,
                'sort' => 0,
                'data' => '{"pill_url":"/features","pill_text":"AI-powered legal document automation","pill_label":"New","subheading":"Generate contracts, NDAs, privacy policies and more. Reviewed by lawyers, customised for your business.","primary_url":"/register","primary_text":"Get Started Free","secondary_url":"/#how-it-works","heading_line_1":"Contracts,","heading_line_2":"and signed","secondary_text":"See How It Works","heading_italic_1":"drafted","heading_italic_2":"in minutes.","subheading_emphasis":"Trusted by 2,000+ businesses"}',
            ],
            [
                'id' => 2,
                'type' => 'logos',
                'enabled' => true,
                'sort' => 1,
                'data' => '{"logos":[{"alt":"Northbeam","url":"sections/logo-northbeam.png"},{"alt":"Meridian","url":"sections/logo-meridian.png"},{"alt":"Helix","url":"sections/logo-helix.png"},{"alt":"Kairos","url":"sections/logo-kairos.png"},{"alt":"Atelier","url":"sections/logo-atelier.png"},{"alt":"Quill","url":"sections/logo-quill.png"},{"alt":"Sablecourt","url":"sections/logo-sablecourt.png"},{"alt":"Petrichor","url":"sections/logo-petrichor.png"}],"heading":"Trusted by leading teams"}',
            ],
            [
                'id' => 3,
                'type' => 'workflow',
                'enabled' => true,
                'sort' => 2,
                'data' => '{"steps":[{"tag":"brief","body":"Type a sentence, paste an email thread, or upload a deal memo. LegalDocs extracts the structure.","title":"Describe the deal."},{"tag":"draft","body":"Clauses are selected from your library, jurisdiction is set, and every variable is filled in.","title":"AI assembles the contract."},{"tag":"review","body":"Share with counterparties, redline in real time, and accept AI-suggested fallbacks.","title":"Negotiate & redline."},{"tag":"sign","body":"Sequential or parallel signing, eIDAS qualified, with a tamper-proof audit certificate attached to the PDF.","title":"Both parties sign."}],"cta_url":"#pricing","eyebrow":"Workflow","heading":"From <span class=\"italic-display\">brief<\/span> to <span class=\"italic-display\">binding<\/span><br>in four moves.","cta_text":"See it in action"}',
            ],
            [
                'id' => 4,
                'type' => 'pricing',
                'enabled' => true,
                'sort' => 3,
                'data' => '{"eyebrow":"Pricing","heading":"Pay <span class=\"italic-display\">per contract.<\/span><br>Or pay <span class=\"italic-display\">monthly.<\/span>","subheading":"Two ways to run LegalDocs. Switch anytime — your library and audit history follow you.","tab_yearly":"Yearly · save 30%","tab_monthly":"Monthly · save 20%","tab_per_contract":"Pay per contract"}',
            ],
            [
                'id' => 5,
                'type' => 'why_us',
                'enabled' => true,
                'sort' => 4,
                'data' => '{"body":"Most legal tools were built for legal teams. LegalDocs was built for the whole company — so the lawyers can focus on the deals that actually need a lawyer.","cards":[{"body":"Every AI suggestion ships with a citation to the clause it was learned from.","dark":"no","icon":"ri-shield-keyhole-fill","title":"Defensible"},{"body":"Trained on 4M+ executed contracts across 38 jurisdictions. Not a wrapper.","dark":"yes","icon":"ri-sparkling-2-fill","title":"Genuinely smart"},{"body":"Run one contract a month or ten thousand. Same speed, same audit-grade trail.","dark":"yes","icon":"ri-flashlight-fill","title":"Built to scale"},{"body":"Talk to a licensed paralegal in under 4 minutes, any business day.","dark":"no","icon":"ri-customer-service-2-fill","title":"Real humans"}],"stats":[{"label":"faster from brief to executed contract","value":"94<span class=\"text-ink-400 text-2xl\">%<\/span>"},{"label":"average annual savings per legal seat","value":"€38k"},{"label":"across 1,200+ G2 & Capterra reviews","value":"4.9<span class=\"text-ink-400 text-2xl\">\/5<\/span>"},{"label":"contracts lost. Ever. We have receipts.","value":"0"}],"eyebrow":"Why LegalDocs","heading":"Engineered by lawyers.<br><span class=\"italic-display\">Loved by everyone else.<\/span>"}',
            ],
            [
                'id' => 6,
                'type' => 'testimonials',
                'enabled' => true,
                'sort' => 5,
                'data' => '{"eyebrow":"Customer stories","heading":"The receipt drawer of <span class=\"italic-display\">happy counsel.<\/span>","rating_text":"4.9 · 1,247 reviews on G2","testimonials":[{"name":"Mara Vukov","role":"General Counsel · Northbeam","quote":"We replaced a four-person redlining bottleneck with a single LegalDocs workspace. Our reseller deals close in days instead of months — and the lawyers finally sleep."},{"name":"Adrien Chen","role":"Head of Ops · Helix\/Law","quote":"The clause library alone paid for itself in three weeks. Our NDAs are out the door in 90 seconds flat."},{"name":"Sara Aoun","role":"Solo Practitioner · Beirut","quote":"I run a one-woman practice. LegalDocs is the only reason I have a weekend."},{"name":"Priya Raman","role":"VP Legal · Kairos AI","quote":"The audit trail saved us in two due-diligence calls. That alone is worth the seat."},{"name":"Tomás Iglesias","role":"COO · Sablecourt","quote":"Counsel signs once, the AI handles the next 80%. Our cycle time dropped 71% in a quarter."},{"name":"Florent Ndiaye","role":"Partner · Atelier 14","quote":"I expected another e-sign tool. What I got was a junior associate that never sleeps."}]}',
            ],
            [
                'id' => 7,
                'type' => 'faq',
                'enabled' => true,
                'sort' => 6,
                'data' => '{"items":[{"answer":"Yes. Every clause LegalDocs drafts is sourced from a curated, lawyer-reviewed library — not free-form model output. You see exactly which clauses were used, what jurisdiction they map to, and you can override any of them before signature.","question":"Is the AI safe to use on real, binding contracts?"},{"answer":"LegalDocs supports eIDAS (EU\/UK), ESIGN & UETA (US), ZertES (Switzerland), and 30+ other regulated frameworks. Qualified Electronic Signatures are available on Studio and Firm plans.","question":"Are LegalDocs signatures legally binding in my country?"},{"answer":"Pay-per-contract is ideal if you ship fewer than ~6 contracts per month — you only pay when you generate and send one. Monthly subscriptions unlock unlimited drafts, redlines, and templates, and are billed at a flat rate.","question":"What is the difference between pay-per-contract and monthly?"},{"answer":"Never, unless you explicitly opt in. Customer data is logically isolated, encrypted with per-tenant keys, and excluded from any training corpus by default.","question":"Do you train your AI on my contracts?"},{"answer":"Absolutely. Drag in your .docx files, and LegalDocs will parse them into structured clauses you can reuse across the workspace. Most teams import their first 20 templates in under an hour.","question":"Can I import my existing templates and playbooks?"},{"answer":"You can export every contract, audit certificate, and template as PDF + .docx at any time — including for 90 days after cancellation. No lock-in, no goodbye fee.","question":"What happens to my data if I cancel?"}],"eyebrow":"Questions","heading":"Things people <span class=\"italic-display\">actually ask<\/span> before they buy."}',
            ],
            [
                'id' => 8,
                'type' => 'cta',
                'enabled' => true,
                'sort' => 7,
                'data' => '{"pill_text":"4,200+ teams already shipping faster with LegalDocs","subheading":"Start your 14-day free trial. No credit card. Generate, sign, and send your first contract before lunch.","primary_url":"\/register","primary_text":"Start free trial","secondary_url":"\/contact","heading_line_1":"Your next contract","heading_line_2":"is one sentence away.","secondary_text":"Book a 20-min demo"}',
            ],
            [
                'id' => 10,
                'type' => 'blog',
                'enabled' => true,
                'sort' => 99,
                'data' => '{"eyebrow":"From the desk","heading":"Thinking on <span class=\"italic-display\">contracts,<\/span><br>law & <span class=\"italic-display\">AI.<\/span>","link_url":"\/blog","link_text":"All articles"}',
            ],
            // Policy / static pages
            [
                'id' => 11,
                'type' => 'custom_html',
                'enabled' => true,
                'sort' => 0,
                'data' => '{"content":"<h2>1. Information We Collect<\/h2><p>We collect information you provide directly to us when you create an account, use our services, or contact us for support. This includes your name, email address, payment information, and any contract data you upload or create using LegalDocs.<\/p><h2>2. How We Use Your Information<\/h2><p>We use the information we collect to provide, maintain, and improve our services, process transactions, send transactional and promotional communications, and comply with legal obligations. We do not sell your personal data to third parties.<\/p><h2>3. Data Storage and Security<\/h2><p>Your data is stored on servers located in the European Union. We implement industry-standard encryption (AES-256 at rest, TLS 1.3 in transit) and conduct regular security audits. Contract data is isolated per account and never used to train AI models without explicit consent.<\/p><h2>4. Cookies<\/h2><p>We use essential cookies to operate the platform and optional analytics cookies to understand how our service is used. You can manage your cookie preferences at any time through the cookie settings panel.<\/p><h2>5. Data Retention<\/h2><p>We retain your account data for as long as your account is active. Upon account deletion, personal data is purged within 30 days, except where retention is required by law. Contract data can be exported before deletion.<\/p><h2>6. Your Rights<\/h2><p>Depending on your location, you may have the right to access, correct, delete, or export your personal data. To exercise these rights, contact us at privacy@legaldocs.app. We will respond within 30 days.<\/p><h2>7. Third-Party Services<\/h2><p>We use a limited number of third-party services to operate our platform, including payment processors and email delivery providers. Each is bound by a Data Processing Agreement and may only process your data as instructed by us.<\/p><h2>8. Changes to This Policy<\/h2><p>We may update this Privacy Policy from time to time. We will notify you of material changes by email or by displaying a prominent notice in the application. Continued use of the service after changes constitutes acceptance.<\/p><h2>9. Contact<\/h2><p>For privacy-related questions, contact our Data Protection Officer at privacy@legaldocs.app.<\/p>"}',
            ],
            [
                'id' => 12,
                'type' => 'custom_html',
                'enabled' => true,
                'sort' => 0,
                'data' => '{"content":"<h2>1. Acceptance of Terms<\/h2><p>By accessing or using LegalDocs, you agree to be bound by these Terms of Service and our Privacy Policy. If you do not agree, do not use the service.<\/p><h2>2. Description of Service<\/h2><p>LegalDocs provides AI-assisted contract drafting, review, and management tools. The service is provided on a subscription basis. We reserve the right to modify, suspend, or discontinue any part of the service with reasonable notice.<\/p><h2>3. Account Responsibilities<\/h2><p>You are responsible for maintaining the confidentiality of your account credentials and for all activity that occurs under your account. You must notify us immediately of any unauthorised access. You may not share your account with others or use the service for any unlawful purpose.<\/p><h2>4. Acceptable Use<\/h2><p>You agree not to use LegalDocs to draft documents intended to facilitate fraud, to circumvent legal obligations, or to harm third parties. We reserve the right to suspend accounts that violate this policy without prior notice.<\/p><h2>5. Intellectual Property<\/h2><p>All content, features, and functionality of LegalDocs are owned by us and protected by copyright, trademark, and other intellectual property laws. You retain full ownership of contracts and documents you create using the service.<\/p><h2>6. AI-Generated Content Disclaimer<\/h2><p>LegalDocs uses artificial intelligence to assist with legal document drafting. AI-generated output does not constitute legal advice. You are responsible for reviewing all documents before execution.<\/p><h2>7. Payment and Billing<\/h2><p>Paid subscriptions are billed in advance on a monthly or annual basis. All fees are non-refundable except as set out in our Refund Policy. We reserve the right to change pricing with 30 days notice to existing subscribers.<\/p><h2>8. Limitation of Liability<\/h2><p>To the maximum extent permitted by law, LegalDocs shall not be liable for any indirect, incidental, special, or consequential damages arising from your use of the service. Our total liability shall not exceed the amount paid by you in the twelve months preceding the claim.<\/p><h2>9. Governing Law<\/h2><p>These terms are governed by the laws of England and Wales. Any disputes shall be subject to the exclusive jurisdiction of the courts of England and Wales.<\/p><h2>10. Contact<\/h2><p>For questions about these terms, contact us at legal@legaldocs.app.<\/p>"}',
            ],
            [
                'id' => 13,
                'type' => 'custom_html',
                'enabled' => true,
                'sort' => 0,
                'data' => '{"content":"<h2>Our Refund Commitment<\/h2><p>We want you to be completely satisfied with LegalDocs. If you are not happy with your purchase, we offer a straightforward refund process designed to be fair to all customers.<\/p><h2>14-Day Money-Back Guarantee<\/h2><p>All new paid subscriptions are eligible for a full refund within 14 days of the initial payment, no questions asked. To request a refund within this window, contact our support team at billing@legaldocs.app with your account email and order reference.<\/p><h2>Annual Subscriptions<\/h2><p>For annual subscriptions cancelled after the 14-day window, we will issue a pro-rated refund for the unused months remaining on your subscription, calculated from the date of your cancellation request.<\/p><h2>Monthly Subscriptions<\/h2><p>Monthly subscriptions cancelled after the 14-day window are not eligible for a pro-rated refund. Your access will continue until the end of the current billing period, after which it will not renew.<\/p><h2>Non-Refundable Items<\/h2><p>Add-on purchases, one-time fees, and payments made more than 90 days before the refund request are not eligible for refund. Accounts suspended for violation of our Terms of Service are not eligible for any refund.<\/p><h2>How to Request a Refund<\/h2><p>Email billing@legaldocs.app with your name, account email, and reason for the refund. We process all eligible refund requests within 5 business days. Funds are returned to the original payment method and may take 3-10 business days to appear depending on your bank.<\/p><h2>Contact<\/h2><p>For billing questions, contact billing@legaldocs.app.<\/p>"}',
            ],
            [
                'id' => 14,
                'type' => 'custom_html',
                'enabled' => true,
                'sort' => 0,
                'data' => '{"content":"<h2>What Are Cookies<\/h2><p>Cookies are small text files placed on your device when you visit a website. They are widely used to make websites work efficiently, remember your preferences, and provide information to site owners about how the site is being used.<\/p><h2>How We Use Cookies<\/h2><p>LegalDocs uses cookies for three purposes: to operate the platform (essential cookies), to remember your preferences such as language and theme (functional cookies), and to understand how users interact with the service (analytics cookies).<\/p><h2>Essential Cookies<\/h2><p>These cookies are strictly necessary for the platform to function and cannot be disabled. They include session management, CSRF protection tokens, and authentication state. Without these cookies, the service cannot operate.<\/p><table><thead><tr><th>Cookie<\/th><th>Purpose<\/th><th>Duration<\/th><\/tr><\/thead><tbody><tr><td>session<\/td><td>Maintains your login session<\/td><td>Until browser closes<\/td><\/tr><tr><td>XSRF-TOKEN<\/td><td>Security protection against cross-site request forgery<\/td><td>2 hours<\/td><\/tr><tr><td>locale<\/td><td>Remembers your language preference<\/td><td>1 year<\/td><\/tr><\/tbody><\/table><h2>Analytics Cookies<\/h2><p>With your consent, we use privacy-respecting analytics to understand which features are most used and where users encounter difficulty. We do not use Google Analytics. Our analytics provider does not build individual user profiles or sell data.<\/p><h2>Managing Cookies<\/h2><p>You can control cookies through the cookie preferences panel, accessible via the footer of any page. You can also manage cookies through your browser settings. Note that disabling essential cookies will prevent you from using the service.<\/p><h2>Contact<\/h2><p>For cookie-related questions, contact privacy@legaldocs.app.<\/p>"}',
            ],
            [
                'id' => 15,
                'type' => 'contact',
                'enabled' => true,
                'sort' => 0,
                'data' => '[]',
            ],
            // Contact page FAQ section
            [
                'id' => 29,
                'type' => 'faq',
                'enabled' => true,
                'sort' => 2,
                'data' => '{"items":[{"answer":"We aim to respond to every message within one business day. Priority support customers receive a response within 4 hours during business hours.","question":"How quickly will I hear back?"},{"answer":"Yes. You can book a 20-minute live demo with our team directly from the link in your reply, or request one in your message and we\'ll send available slots.","question":"Can I book a demo instead?"},{"answer":"Absolutely. Reach out with a short description of your use case and team size, and we\'ll put together a custom plan and quote for you.","question":"Do you offer custom or enterprise plans?"},{"answer":"Existing customers get the fastest help by signing in and opening a support ticket from the dashboard — it routes straight to our support team with your account context attached.","question":"I\'m already a customer — where do I get support?"},{"answer":"Yes. For security disclosures, partnership enquiries, or press, mention the topic in your message and it will be routed to the right team.","question":"Can I reach a specific team?"}],"eyebrow":"Common questions","heading":"Before you <span class=\"italic-display\">reach out.<\/span>"}',
            ],
            // About page sections
            [
                'id' => 20,
                'type' => 'why_us',
                'enabled' => true,
                'sort' => 1,
                'data' => '{"body":"We started LegalDocs because we watched brilliant people lose weeks of their lives to contract bureaucracy. Founders waiting on NDAs. Freelancers chasing signature. In-house counsel buried in redlines that should have taken an hour. We thought the law deserved better tooling.","cards":[{"body":"Built by a team of lawyers, engineers, and product designers who have lived both sides of the contract table.","dark":"no","icon":"ri-team-line","title":"Practitioners first"},{"body":"Every feature ships with a legal opinion. We never build something we wouldn\'t sign ourselves.","dark":"yes","icon":"ri-shield-keyhole-fill","title":"Legally sound"},{"body":"Used in 38+ jurisdictions, with local counsel reviewing templates for each market we enter.","dark":"yes","icon":"ri-global-line","title":"Global reach"},{"body":"We publish our uptime, our security posture, and our AI model card. No black boxes.","dark":"no","icon":"ri-eye-line","title":"Radically transparent"}],"stats":[{"label":"countries with active users","value":"38"},{"label":"contracts executed on the platform","value":"1.2M"},{"label":"average NPS from legal teams","value":"71"},{"label":"founded","value":"2022"}],"eyebrow":"Our story","heading":"Built by lawyers,<br><span class=\"italic-display\">for everyone who isn\'t.<\/span>"}',
            ],
            [
                'id' => 21,
                'type' => 'testimonials',
                'enabled' => true,
                'sort' => 2,
                'data' => '{"eyebrow":"What teams say","heading":"Trusted by counsel<br><span class=\"italic-display\">on six continents.<\/span>","rating_text":"4.9 · 1,247 reviews on G2","testimonials":[{"name":"Mara Vukov","role":"General Counsel · Northbeam","quote":"We replaced a four-person redlining bottleneck with a single LegalDocs workspace. Our reseller deals close in days instead of months — and the lawyers finally sleep."},{"name":"Adrien Chen","role":"Head of Ops · Helix\/Law","quote":"The clause library alone paid for itself in three weeks. Our NDAs are out the door in 90 seconds flat."},{"name":"Sara Aoun","role":"Solo Practitioner · Beirut","quote":"I run a one-woman practice. LegalDocs is the only reason I have a weekend."},{"name":"Priya Raman","role":"VP Legal · Kairos AI","quote":"The audit trail saved us in two due-diligence calls. That alone is worth the seat."},{"name":"Tomás Iglesias","role":"COO · Sablecourt","quote":"Counsel signs once, the AI handles the next 80%. Our cycle time dropped 71% in a quarter."},{"name":"Florent Ndiaye","role":"Partner · Atelier 14","quote":"I expected another e-sign tool. What I got was a junior associate that never sleeps."}]}',
            ],
            [
                'id' => 22,
                'type' => 'cta',
                'enabled' => true,
                'sort' => 3,
                'data' => '{"pill_text":"Join 4,200+ teams shipping faster","subheading":"Start your 14-day free trial. No credit card required. Your first contract in minutes.","primary_url":"\/register","primary_text":"Start free trial","secondary_url":"\/contact","heading_line_1":"Ready to close","heading_line_2":"faster?","secondary_text":"Talk to us"}',
            ],
            // Pricing page sections
            [
                'id' => 23,
                'type' => 'pricing',
                'enabled' => true,
                'sort' => 0,
                'data' => '{"eyebrow":"Pricing","heading":"Simple, <span class=\"italic-display\">honest<\/span> pricing.","subheading":"Pay per contract when you\'re starting out. Switch to a monthly plan when it makes sense. No lock-in, no surprises.","tab_yearly":"Yearly · save 30%","tab_monthly":"Monthly · save 20%","tab_per_contract":"Pay per contract"}',
            ],
            [
                'id' => 24,
                'type' => 'faq',
                'enabled' => true,
                'sort' => 1,
                'data' => '{"items":[{"answer":"Pay-per-contract is ideal if you ship fewer than ~6 contracts per month. Monthly plans are better if you draft frequently — unlimited drafts, redlines, and templates at a flat rate.","question":"Which plan is right for me?"},{"answer":"All new paid subscriptions are eligible for a full refund within 14 days of the initial payment. Annual plans cancelled after that window receive a pro-rated refund for unused months.","question":"What is your refund policy?"},{"answer":"Yes. You can upgrade or downgrade at any time. When you upgrade, you\'re billed the prorated difference. When you downgrade, the change takes effect at the next billing cycle.","question":"Can I change my plan later?"},{"answer":"Never. Customer data is logically isolated, encrypted with per-tenant keys, and excluded from any AI training corpus by default.","question":"Do you train your AI on my contracts?"},{"answer":"You can export every contract, audit certificate, and template as PDF + .docx at any time — including for 90 days after cancellation.","question":"What happens to my data if I cancel?"},{"answer":"We accept all major credit and debit cards, bank transfers, and select cryptocurrency options depending on your region.","question":"What payment methods do you accept?"}],"eyebrow":"Pricing FAQ","heading":"Questions about <span class=\"italic-display\">plans & billing.<\/span>"}',
            ],
            [
                'id' => 25,
                'type' => 'testimonials',
                'enabled' => true,
                'sort' => 2,
                'data' => '{"eyebrow":"Why teams choose us","heading":"The numbers<br><span class=\"italic-display\">speak for themselves.<\/span>","rating_text":"4.9 · 1,247 reviews on G2","testimonials":[{"name":"Priya Raman","role":"VP Legal · Kairos AI","quote":"The audit trail saved us in two due-diligence calls. That alone is worth the seat."},{"name":"Adrien Chen","role":"Head of Ops · Helix\/Law","quote":"The clause library alone paid for itself in three weeks. Our NDAs are out the door in 90 seconds flat."},{"name":"Tomás Iglesias","role":"COO · Sablecourt","quote":"Counsel signs once, the AI handles the next 80%. Our cycle time dropped 71% in a quarter."},{"name":"Mara Vukov","role":"General Counsel · Northbeam","quote":"We replaced a four-person redlining bottleneck with a single LegalDocs workspace. Our reseller deals close in days instead of months — and the lawyers finally sleep."},{"name":"Sara Aoun","role":"Solo Practitioner · Beirut","quote":"I run a one-woman practice. LegalDocs is the only reason I have a weekend."},{"name":"Florent Ndiaye","role":"Partner · Atelier 14","quote":"I expected another e-sign tool. What I got was a junior associate that never sleeps."}]}',
            ],
            [
                'id' => 26,
                'type' => 'cta',
                'enabled' => true,
                'sort' => 3,
                'data' => '{"pill_text":"No credit card required","subheading":"Generate, sign, and send your first contract before lunch. Cancel anytime.","primary_url":"\/register","primary_text":"Start free trial","secondary_url":"\/contact","heading_line_1":"Start today,","heading_line_2":"cancel anytime.","secondary_text":"Book a demo"}',
            ],
        ];

        foreach ($sections as $s) {
            PageSection::firstOrCreate(
                ['id' => $s['id']],
                [
                    'type' => $s['type'],
                    'data' => json_decode($s['data'], true),
                    'enabled' => $s['enabled'],
                    'sort' => $s['sort'],
                ]
            );
        }
    }

    private function seedPages(): void
    {
        $pages = [
            [
                'slug' => 'home',
                'title' => ['en' => 'Home'],
                'status' => 'published',
                'is_system' => true,
                'section_ids' => [1, 2, 3, 4, 5, 6, 7, 10, 8],
            ],
            [
                'slug' => 'privacy-policy',
                'title' => ['en' => 'Privacy Policy'],
                'status' => 'published',
                'is_system' => true,
                'section_ids' => [11],
            ],
            [
                'slug' => 'terms',
                'title' => ['en' => 'Terms of Service'],
                'status' => 'published',
                'is_system' => true,
                'section_ids' => [12],
            ],
            [
                'slug' => 'refund-policy',
                'title' => ['en' => 'Refund Policy'],
                'status' => 'published',
                'is_system' => true,
                'section_ids' => [13],
            ],
            [
                'slug' => 'cookie-policy',
                'title' => ['en' => 'Cookie Policy'],
                'status' => 'published',
                'is_system' => true,
                'section_ids' => [14],
            ],
            [
                'slug' => 'contact',
                'title' => ['en' => 'Contact'],
                'status' => 'published',
                'is_system' => true,
                'section_ids' => [15, 5, 29],
            ],
            [
                'slug' => 'about',
                'title' => ['en' => 'About Us'],
                'status' => 'published',
                'is_system' => true,
                'section_ids' => [20, 21, 22],
            ],
            [
                'slug' => 'pricing',
                'title' => ['en' => 'Pricing'],
                'status' => 'published',
                'is_system' => true,
                'section_ids' => [23, 24, 25, 26],
            ],
        ];

        foreach ($pages as $data) {
            $page = Page::firstOrNew(['slug' => $data['slug']]);
            $page->title = $data['title'];
            $page->status = $data['status'];
            $page->is_system = $data['is_system'];
            $page->section_ids = $data['section_ids'];
            $page->save();
        }
    }

    private function seedMenus(): void
    {
        $menus = [
            [
                'handle' => 'header',
                'name' => ['en' => 'Header'],
                'items' => [
                    ['label' => ['en' => 'Home'],     'url' => '/',          'parent' => null, 'sort' => 0],
                    ['label' => ['en' => 'About'],    'url' => '/about',   'parent' => null, 'sort' => 1],
                    ['label' => ['en' => 'Pricing'],  'url' => '/pricing', 'parent' => null, 'sort' => 2],
                    ['label' => ['en' => 'Blog'],     'url' => '/blog',      'parent' => null, 'sort' => 3],
                    ['label' => ['en' => 'Contact'],  'url' => '/contact',   'parent' => null, 'sort' => 4],
                ],
            ],
            [
                'handle' => 'footer',
                'name' => ['en' => 'Footer'],
                'items' => [
                    // Product column
                    ['label' => ['en' => 'Product'],      'url' => null,            'parent' => null,      'sort' => 0, 'key' => 'product'],
                    ['label' => ['en' => 'Features'],     'url' => '/#features',    'parent' => 'product', 'sort' => 0],
                    ['label' => ['en' => 'How it works'], 'url' => '/#workflow',    'parent' => 'product', 'sort' => 1],
                    ['label' => ['en' => 'Pricing'],      'url' => '/pricing',    'parent' => 'product', 'sort' => 2],
                    ['label' => ['en' => 'FAQ'],          'url' => '/#faq',         'parent' => 'product', 'sort' => 3],
                    // Resources column
                    ['label' => ['en' => 'Resources'],    'url' => null,            'parent' => null,        'sort' => 1, 'key' => 'resources'],
                    ['label' => ['en' => 'Blog'],         'url' => '/blog',         'parent' => 'resources', 'sort' => 0],
                    ['label' => ['en' => 'Contact'],      'url' => '/contact',      'parent' => 'resources', 'sort' => 1],
                    ['label' => ['en' => 'Support'],      'url' => '/contact',      'parent' => 'resources', 'sort' => 2],
                    // Company column
                    ['label' => ['en' => 'Company'],      'url' => null,            'parent' => null,      'sort' => 2, 'key' => 'company'],
                    ['label' => ['en' => 'About'],        'url' => '/about',      'parent' => 'company', 'sort' => 0],
                    ['label' => ['en' => 'Pricing'],      'url' => '/pricing',    'parent' => 'company', 'sort' => 1],
                    ['label' => ['en' => 'Contact'],      'url' => '/contact',      'parent' => 'company', 'sort' => 2],
                    // Legal column
                    ['label' => ['en' => 'Legal'],        'url' => null,            'parent' => null,    'sort' => 3, 'key' => 'legal'],
                    ['label' => ['en' => 'Terms'],        'url' => '/terms',          'parent' => 'legal', 'sort' => 0],
                    ['label' => ['en' => 'Privacy'],      'url' => '/privacy-policy', 'parent' => 'legal', 'sort' => 1],
                    ['label' => ['en' => 'Refund'],       'url' => '/refund-policy',  'parent' => 'legal', 'sort' => 2],
                    ['label' => ['en' => 'Cookies'],      'url' => '/cookie-policy',  'parent' => 'legal', 'sort' => 3],
                ],
            ],
            [
                'handle' => 'mobile',
                'name' => ['en' => 'Mobile'],
                'items' => [],
            ],
        ];

        foreach ($menus as $def) {
            $menu = Menu::firstOrNew(['handle' => $def['handle']]);
            $menu->name = $def['name'];
            $menu->save();

            if ($menu->allItems()->count() > 0) {
                continue;
            }

            $parents = [];

            foreach ($def['items'] as $item) {
                $parentId = null;
                if (! empty($item['parent']) && isset($parents[$item['parent']])) {
                    $parentId = $parents[$item['parent']]->id;
                }

                $menuItem = new MenuItem;
                $menuItem->menu_id = $menu->id;
                $menuItem->parent_id = $parentId;
                $menuItem->label = $item['label'];
                $menuItem->url = $item['url'];
                $menuItem->sort = $item['sort'];
                $menuItem->save();

                if (! empty($item['key'])) {
                    $parents[$item['key']] = $menuItem;
                }
            }
        }
    }
}
