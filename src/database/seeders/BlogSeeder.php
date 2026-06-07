<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Tag;
use App\Services\HtmlPurifier;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $tech = BlogCategory::firstOrNew(['slug' => 'technology']);
        $tech->name = ['en' => 'Technology', 'ar' => 'تقنية'];
        $tech->sort = 1;
        $tech->save();

        $legal = BlogCategory::firstOrNew(['slug' => 'legal']);
        $legal->name = ['en' => 'Legal & Compliance'];
        $legal->sort = 2;
        $legal->save();

        $tagContracts = Tag::firstOrNew(['slug' => 'contracts']);
        $tagContracts->name = ['en' => 'Contracts'];
        $tagContracts->save();

        $tagLaravel = Tag::firstOrNew(['slug' => 'laravel']);
        $tagLaravel->name = ['en' => 'Laravel'];
        $tagLaravel->save();

        $tagTips = Tag::firstOrNew(['slug' => 'tips']);
        $tagTips->name = ['en' => 'Tips'];
        $tagTips->save();

        $tagSecurity = Tag::firstOrNew(['slug' => 'security']);
        $tagSecurity->name = ['en' => 'Security'];
        $tagSecurity->save();

        $tagAI = Tag::firstOrNew(['slug' => 'ai']);
        $tagAI->name = ['en' => 'AI'];
        $tagAI->save();

        $posts = [
            [
                'slug' => 'welcome-to-our-blog',
                'category' => $tech,
                'tags' => [$tagTips],
                'published_at' => now()->subDays(30),
                'status' => BlogPost::STATUS_PUBLISHED,
                'title' => ['en' => 'Welcome to Our Blog', 'ar' => 'مرحباً بكم في مدوّنتنا'],
                'excerpt' => ['en' => 'We\'re excited to launch our new blog — a place for guides, updates, and legal insights.', 'ar' => 'يسعدنا إطلاق مدوّنتنا الجديدة.'],
                'body' => [
                    'en' => HtmlPurifier::purify('<p>Welcome to our blog — a space where we share practical guides, product updates, and insights into the legal technology landscape. Whether you are a solo freelancer drafting your first service agreement or a growing business managing dozens of contracts each month, this blog is for you.</p><p>We started this platform because we believe that access to well-structured, legally sound contracts should not require an expensive lawyer on retainer. Smart templates, AI assistance, and a clean workflow can take you most of the way there.</p><blockquote><p>"The best contract is the one that never needs to be enforced — because both parties understood it perfectly from the start."</p></blockquote><p>Over the coming weeks we will publish deep dives into contract clauses, tutorials on using the platform, and interviews with legal professionals about how technology is changing their practice. Subscribe to stay in the loop.</p>'),
                    'ar' => HtmlPurifier::purify('<p>مرحباً بكم في مدوّنتنا — المكان الذي نشارك فيه الأدلة العملية وتحديثات المنتج والرؤى في مجال تكنولوجيا القانون.</p><p>بدأنا هذه المنصة لأننا نؤمن بأن الوصول إلى عقود منظمة وسليمة قانونياً لا ينبغي أن يتطلب محامياً مكلفاً.</p><blockquote><p>"أفضل عقد هو الذي لا يحتاج إلى إنفاذ أبداً — لأن كلا الطرفين فهمه تماماً من البداية."</p></blockquote><p>سنشارك هنا دروساً تعليمية ومقابلات مع متخصصين قانونيين حول كيفية تغيير التكنولوجيا لممارساتهم.</p>'),
                ],
            ],
            [
                'slug' => 'how-to-draft-a-service-agreement',
                'category' => $legal,
                'tags' => [$tagContracts, $tagTips],
                'published_at' => now()->subDays(25),
                'status' => BlogPost::STATUS_PUBLISHED,
                'title' => ['en' => 'How to Draft a Service Agreement That Actually Protects You'],
                'excerpt' => ['en' => 'A step-by-step look at the clauses every freelancer and agency should include in their service contracts.'],
                'body' => [
                    'en' => HtmlPurifier::purify('<p>A service agreement is more than a formality — it is the foundation of every professional engagement. Done right, it sets expectations, limits liability, and gives you a clear path forward if something goes wrong. Done poorly, it leaves you exposed.</p><p>Here are the five clauses we consider non-negotiable in any service agreement.</p><h2>1. Scope of Work</h2><p>Define deliverables precisely. Vague language like "website design" invites scope creep. Instead, specify the number of pages, revision rounds, and what is explicitly excluded.</p><h2>2. Payment Terms</h2><p>State the total fee, deposit amount, milestone schedule, and what happens if an invoice goes unpaid past the due date. A late-fee clause (e.g. 1.5% per month) is a simple deterrent.</p><blockquote><p>"A client who balks at clear payment terms before the project starts will almost certainly dispute an invoice after it ends."</p></blockquote><h2>3. Intellectual Property Assignment</h2><p>Who owns the work product? By default in many jurisdictions, the creator retains copyright until it is explicitly assigned. If the client expects to own what you build, that transfer must be written into the contract — usually contingent on full payment.</p><h2>4. Limitation of Liability</h2><p>Cap your exposure. A reasonable clause limits your total liability to the fees paid under the contract. Without it, a client could theoretically pursue consequential damages far exceeding what they paid you.</p><h2>5. Termination</h2><p>Specify how either party can end the agreement, the notice period required, and what happens to work in progress and payments already made. Ambiguity here is where most disputes begin.</p><p>Use our contract builder to generate a service agreement with all five clauses pre-filled and ready to customise.</p>'),
                ],
            ],
            [
                'slug' => 'ai-contract-drafting-guide',
                'category' => $tech,
                'tags' => [$tagAI, $tagContracts],
                'published_at' => now()->subDays(20),
                'status' => BlogPost::STATUS_PUBLISHED,
                'title' => ['en' => 'AI Contract Drafting: What It Can and Cannot Do'],
                'excerpt' => ['en' => 'AI can dramatically speed up contract drafting — but knowing its limits is just as important as knowing its strengths.'],
                'body' => [
                    'en' => HtmlPurifier::purify('<p>AI-assisted contract drafting has gone from a curiosity to a genuine productivity tool in less than two years. Law firms, freelancers, and SaaS companies are all experimenting with it. But the hype often outpaces the reality. Let\'s look at what these tools actually do well and where human judgment is still essential.</p><h2>What AI does well</h2><p>Modern language models are excellent at generating first drafts from a brief description of your needs. Give the system a few sentences about the parties, the deliverable, and the jurisdiction, and it will produce a coherent, clause-complete draft in seconds. That draft will cover standard provisions — payment, IP, termination, confidentiality — that would take a non-lawyer an hour to assemble from scratch.</p><p>AI is also useful for plain-language summaries. Paste in a dense contract you received from a counterparty and ask the model to explain each clause in plain English. This is not legal advice, but it dramatically lowers the barrier to understanding what you are signing.</p><blockquote><p>"AI is not replacing lawyers. It is eliminating the parts of legal work that never needed a lawyer in the first place."</p></blockquote><h2>Where human judgment still matters</h2><p>Jurisdiction-specific nuances are where AI drafts most often fall short. A non-compete clause that is enforceable in Texas may be void in California. An arbitration clause that works in a B2B context may be unconscionable in a consumer contract. The model does not always know which jurisdiction applies, and even when it does, it may not have the most current case law.</p><p>High-stakes, bespoke agreements — M&amp;A transactions, multi-party joint ventures, complex licensing deals — still require experienced counsel. AI can assist in research and drafting support, but the strategic judgment is irreplaceable.</p><h2>How we use AI on this platform</h2><p>Our AI contract drafter is trained to ask clarifying questions before generating a draft. It will not produce a confidentiality agreement without knowing whether it is mutual or one-way, the term length, and the governing law. The output is a starting point, not a finished product — and we say that clearly in every generated document.</p>'),
                ],
            ],
            [
                'slug' => 'understanding-electronic-signatures',
                'category' => $legal,
                'tags' => [$tagContracts, $tagSecurity],
                'published_at' => now()->subDays(15),
                'status' => BlogPost::STATUS_PUBLISHED,
                'title' => ['en' => 'Electronic Signatures: Legal Status Around the World'],
                'excerpt' => ['en' => 'Are e-signatures legally binding? The short answer is yes — but the details vary significantly by country and document type.'],
                'body' => [
                    'en' => HtmlPurifier::purify('<p>Electronic signatures are now accepted in virtually every major jurisdiction, but the legal framework differs enough that it is worth understanding the basics before you rely on them for important agreements.</p><h2>The United States</h2><p>The ESIGN Act (2000) and UETA give e-signatures the same legal effect as wet ink signatures for the vast majority of commercial and consumer contracts. There are carve-outs — wills, court orders, and certain real-estate documents still require physical signatures in many states — but for business contracts, NDAs, service agreements, and employment documents, an e-signature is fully enforceable.</p><h2>The European Union</h2><p>The eIDAS Regulation defines three tiers of electronic signature: Simple, Advanced (AES), and Qualified (QES). A simple e-signature (typing your name in a box) is legally valid but carries the least evidential weight. A Qualified Electronic Signature, backed by a certificate from an accredited trust service provider, has the same legal effect as a handwritten signature across all EU member states.</p><blockquote><p>"For cross-border EU contracts involving significant value, a Qualified Electronic Signature is the safest choice — it removes ambiguity about enforceability in any member state."</p></blockquote><h2>The United Kingdom</h2><p>Post-Brexit, the UK retained eIDAS principles in domestic law via the Electronic Identification and Trust Services Regulation. E-signatures are valid for most contracts. The Law Commission confirmed in 2019 that e-signatures can be used to execute deeds, provided the formalities around witnessing are met.</p><h2>What this means for your contracts</h2><p>For everyday business contracts — NDAs, service agreements, SaaS subscriptions — a simple e-signature is sufficient in all major markets. For higher-risk documents or cross-border transactions, consider the jurisdiction of each party and whether a higher assurance level is warranted. Our platform records an audit trail — IP address, timestamp, email verification — that significantly strengthens enforceability regardless of the signature tier.</p>'),
                ],
            ],
            [
                'slug' => 'nda-clauses-explained',
                'category' => $legal,
                'tags' => [$tagContracts, $tagTips],
                'published_at' => now()->subDays(10),
                'status' => BlogPost::STATUS_PUBLISHED,
                'title' => ['en' => 'NDA Clauses Explained: What to Watch Out For'],
                'excerpt' => ['en' => 'Non-disclosure agreements look simple but hide complexity in the details. Here is what each clause actually means.'],
                'body' => [
                    'en' => HtmlPurifier::purify('<p>A non-disclosure agreement (NDA) is often the first contract signed in a business relationship — and the one least carefully read. Most people see it as a formality. In practice, a poorly drafted NDA can bind you to obligations you did not intend to accept, or fail to protect information you thought was confidential.</p><h2>Definition of Confidential Information</h2><p>This is the most important clause, and the most variable. Some NDAs define confidential information broadly — anything disclosed, oral or written. Others require that information be marked as confidential to qualify. Broad definitions protect you as the disclosing party. Narrow ones protect you as the receiving party by limiting your obligations.</p><h2>Exclusions</h2><p>Standard exclusions include information that is already publicly known, that the receiving party already knew before disclosure, that they independently developed, or that they received from a third party without restriction. These are reasonable carve-outs. Watch for any attempt to narrow or eliminate them.</p><blockquote><p>"If an NDA has no exclusions, it is either drafted by someone who does not know what they are doing — or by someone who knows exactly what they are doing and is trying to overreach."</p></blockquote><h2>Term</h2><p>How long does the obligation last? Two to three years is common for commercial NDAs. Five years or perpetual confidentiality obligations are more common in employment and IP-sensitive contexts. Know what you are committing to — and whether the term survives termination of the underlying relationship.</p><h2>Permitted Disclosures</h2><p>Most NDAs allow disclosure to employees and contractors on a need-to-know basis, and to legal or financial advisors. Some also require the disclosing party to be notified before any court-ordered disclosure, so they have the opportunity to seek a protective order.</p><h2>Remedies</h2><p>Because damages from a breach of confidentiality can be difficult to quantify, many NDAs include a clause stating that the disclosing party is entitled to seek injunctive relief — a court order stopping the breach — without needing to prove financial harm. This is standard and reasonable. Be cautious of NDAs that also specify liquidated damages in high fixed amounts.</p>'),
                ],
            ],
            [
                'slug' => 'how-to-negotiate-a-contract-like-a-lawyer',
                'category' => $legal,
                'tags' => [$tagContracts, $tagTips],
                'published_at' => now()->subDays(5),
                'status' => BlogPost::STATUS_PUBLISHED,
                'title' => ['en' => 'How to Negotiate a Contract Like a Lawyer (Without Being One)'],
                'excerpt' => ['en' => 'Most people accept contracts as-is because negotiating feels awkward. It shouldn\'t. Here are the practical moves that experienced negotiators use — and that anyone can apply.'],
                'body' => [
                    'en' => HtmlPurifier::purify('<p>The most expensive assumption in business is that a contract sent by the other side is a final offer. It almost never is. Every clause in a standard form contract was put there to protect the party that drafted it. That does not mean the terms are unreasonable — but it does mean that almost everything is open to discussion, and the other side usually expects you to push back on at least some of it.</p><p>You do not need a law degree to negotiate effectively. You need to know which clauses matter, which ones are usually flexible, and how to ask for changes without blowing up the deal.</p><h2>Read it before you sign it</h2><p>This sounds obvious. Most people skip it. A study by Docusign found that the average person spends less than two minutes reviewing a contract before signing. That is not enough time to read a one-page NDA, let alone a multi-page services agreement. Before you negotiate anything, read the whole document once — not to understand every legal nuance, but to find the parts that feel wrong. Trust that feeling. If a clause makes you uncomfortable, it is usually for a good reason.</p><blockquote><p>"The best time to negotiate a contract is before you need it. Once a dispute is in progress, every word is being read by a lawyer looking for reasons it means something different from what you thought."</p></blockquote><h2>The three clauses worth fighting for</h2><p>Most contract disputes cluster around three areas. If you negotiate nothing else, negotiate these.</p><h2>1. Limitation of liability</h2><p>This clause caps how much either party can owe the other if something goes wrong. Standard boilerplate often limits the vendor\'s liability to the fees paid — which sounds fair until you realise that a $500-a-month software subscription could cap liability at $500 even if a data breach costs your business $500,000. Ask for a cap tied to the value of the relationship, not a single month\'s fee. Twelve months of fees is a reasonable starting point.</p><h2>2. Termination for convenience</h2><p>Can you exit the contract if things are not working, even if nobody has technically breached anything? Many vendor agreements require cause to terminate — meaning you are locked in until the end of the term no matter what. Push for a termination-for-convenience clause with a reasonable notice period (30 to 90 days). Most vendors will accept this for contracts under two years.</p><h2>3. Auto-renewal</h2><p>Contracts that renew automatically — often for full-year terms — with only a short opt-out window are a common source of unintended spend. Ask for the auto-renewal to be removed, or for the opt-out window to be extended to at least 60 days before the renewal date. Better yet, ask for a written reminder obligation so the vendor has to notify you before the window closes.</p><h2>How to ask for changes without damaging the relationship</h2><p>The framing matters more than the ask. "I have a few standard points I would like to discuss" lands very differently from "your contract is unacceptable." Most counterparties will engage calmly with the former. A redline with tracked changes and a short covering note explaining your concerns is the most professional approach — it shows you have read the document and thought carefully about it, rather than simply reflexively objecting.</p><p>Pick your battles. If you push back on every clause, you signal that no agreement will satisfy you. Identify the two or three points that actually matter to your business, negotiate those firmly, and be willing to accept the rest. That is how experienced negotiators close deals — not by winning every point, but by winning the right ones.</p>'),
                ],
            ],
            [
                'slug' => 'why-every-business-needs-a-contract-workflow',
                'category' => $tech,
                'tags' => [$tagContracts, $tagTips, $tagAI],
                'published_at' => now()->subDays(2),
                'status' => BlogPost::STATUS_PUBLISHED,
                'title' => ['en' => 'Why Every Growing Business Needs a Real Contract Workflow'],
                'excerpt' => ['en' => 'Spreadsheets and email threads do not scale. Here is how a structured contract workflow saves time, reduces risk, and closes deals faster — and the warning signs that you have already outgrown your current process.'],
                'body' => [
                    'en' => HtmlPurifier::purify('<p>Most companies do not decide to build a contract workflow. They back into one. A founder drafts the first NDA in a word processor. Sales starts keeping a folder of "the good template." Someone makes a spreadsheet to track which deals are signed. Before long, you have a process held together by tribal knowledge and luck — and it works right up until the moment it doesn\'t.</p><h2>The hidden cost of an ad-hoc process</h2><p>When contracts live in inboxes and shared drives, three things quietly go wrong. First, version control collapses — nobody is sure which draft is the latest, and the wrong one gets signed. Second, nothing is searchable — when a customer asks about a renewal clause, someone spends an afternoon digging. Third, there is no audit trail — if a dispute arises, you cannot prove who agreed to what, and when.</p><blockquote><p>"The cost of a bad contract process is invisible until the day it is catastrophic. By then, the expensive part is not the software — it is the deal you lost or the clause you cannot enforce."</p></blockquote><h2>What a real workflow looks like</h2><p>A structured contract workflow has four stages, and each one removes a category of risk:</p><h2>1. Templates, not blank pages</h2><p>Every common agreement — NDA, MSA, SOW, employment offer — starts from a lawyer-reviewed template with the variables clearly marked. Nobody drafts from scratch, so nobody forgets the limitation-of-liability clause.</p><h2>2. Structured drafting</h2><p>Variables are filled in through a guided flow, not by find-and-replace. The system knows that if you pick "monthly billing" the payment clause changes, and it adjusts automatically. This is where AI assistance earns its keep — surfacing the right clause for the right situation.</p><h2>3. Routed signing</h2><p>Contracts are sent for signature in a defined order, with reminders, and every signer is verified. The signed PDF carries a tamper-proof audit certificate showing timestamps, IP addresses, and identity checks.</p><h2>4. A searchable archive</h2><p>Every executed contract is stored, tagged, and full-text searchable. Renewal dates trigger reminders. When due diligence comes, you export the whole archive in minutes instead of weeks.</p><h2>Signs you have outgrown your current process</h2><p>You are ready for a real workflow if any of these sound familiar: you have signed the wrong version of a document at least once; you cannot answer "how many active contracts do we have?" without counting manually; a renewal has lapsed because nobody was watching the date; or onboarding a new hire to "how we do contracts" takes more than five minutes.</p><h2>Where to start</h2><p>You do not need to boil the ocean. Start by moving your three most common agreements into templates. Add guided drafting next, then routed signing. Within a quarter, the process that used to be your biggest source of friction becomes the part of the deal nobody worries about. That is the whole point — good infrastructure is the kind you stop thinking about.</p>'),
                ],
            ],
        ];

        foreach ($posts as $data) {
            $category = $data['category'];
            $tags = $data['tags'];

            $post = BlogPost::firstOrNew(['slug' => $data['slug']]);
            $post->blog_category_id = $category->id;
            $post->title = $data['title'];
            $post->excerpt = $data['excerpt'];
            $post->body = $data['body'];
            $post->status = $data['status'];
            $post->published_at = $data['published_at'];
            $post->save();

            if (! empty($tags)) {
                $post->tags()->sync(collect($tags)->pluck('id')->toArray());
            }
        }
    }
}
