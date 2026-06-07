<?php

namespace Database\Seeders;

use App\Domain\Contract\Models\ContractTemplate;
use App\Domain\Contract\Models\ContractTemplateCategory;
use Illuminate\Database\Seeder;

class ContractTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $cat = fn (string $slug) => ContractTemplateCategory::where('slug', $slug)->value('id');

        $templates = [

            // ── NDA ──────────────────────────────────────────────────────────
            [
                'category_slug' => 'non-disclosure-agreements',
                'name' => 'Mutual Non-Disclosure Agreement',
                'description' => 'Two-way NDA protecting confidential information shared between both parties.',
                'credits_cost' => 1,
                'sort' => 1,
                'body' => $this->mutualNdaBody(),
                'fields' => [
                    ['label' => 'Party A Name',       'key' => 'party_a_name',    'type' => 'text',   'placeholder' => 'Acme Inc',            'required' => true,  'sort' => 1],
                    ['label' => 'Party A Address',    'key' => 'party_a_address', 'type' => 'textarea', 'placeholder' => '123 Main St, City',   'required' => true,  'sort' => 2],
                    ['label' => 'Party B Name',       'key' => 'party_b_name',    'type' => 'text',   'placeholder' => 'Beta Corp',           'required' => true,  'sort' => 3],
                    ['label' => 'Party B Address',    'key' => 'party_b_address', 'type' => 'textarea', 'placeholder' => '456 High St, City',  'required' => true,  'sort' => 4],
                    ['label' => 'Purpose',            'key' => 'purpose',         'type' => 'textarea', 'placeholder' => 'Evaluating a potential business partnership', 'required' => true, 'sort' => 5],
                    ['label' => 'Term (years)',       'key' => 'term_years',      'type' => 'number', 'placeholder' => '2',                   'required' => true,  'sort' => 6],
                    ['label' => 'Governing Law',      'key' => 'governing_law',   'type' => 'text',   'placeholder' => 'England and Wales',   'required' => true,  'sort' => 7],
                    ['label' => 'Effective Date',     'key' => 'effective_date',  'type' => 'date',   'placeholder' => '',                    'required' => true,  'sort' => 8],
                ],
            ],

            // ── Employment ───────────────────────────────────────────────────
            [
                'category_slug' => 'employment-hr',
                'name' => 'Employment Offer Letter',
                'description' => 'Formal offer letter outlining role, compensation, start date, and key terms.',
                'credits_cost' => 1,
                'sort' => 1,
                'body' => $this->employmentOfferBody(),
                'fields' => [
                    ['label' => 'Employer Name',      'key' => 'employer_name',   'type' => 'text',   'placeholder' => 'Acme Inc',            'required' => true,  'sort' => 1],
                    ['label' => 'Employee Name',      'key' => 'employee_name',   'type' => 'text',   'placeholder' => 'Jane Smith',          'required' => true,  'sort' => 2],
                    ['label' => 'Job Title',          'key' => 'job_title',       'type' => 'text',   'placeholder' => 'Senior Developer',    'required' => true,  'sort' => 3],
                    ['label' => 'Start Date',         'key' => 'start_date',      'type' => 'date',   'placeholder' => '',                    'required' => true,  'sort' => 4],
                    ['label' => 'Annual Salary',      'key' => 'annual_salary',   'type' => 'text',   'placeholder' => '$80,000',             'required' => true,  'sort' => 5],
                    ['label' => 'Probation Period',   'key' => 'probation_period', 'type' => 'text',   'placeholder' => '90 days',             'required' => true,  'sort' => 6],
                    ['label' => 'Work Location',      'key' => 'work_location',   'type' => 'text',   'placeholder' => 'Remote / New York',   'required' => true,  'sort' => 7],
                    ['label' => 'Governing Law',      'key' => 'governing_law',   'type' => 'text',   'placeholder' => 'New York',            'required' => true,  'sort' => 8],
                ],
            ],

            // ── Freelance ────────────────────────────────────────────────────
            [
                'category_slug' => 'freelance-services',
                'name' => 'Freelance Service Agreement',
                'description' => 'Covers scope of work, payment terms, IP ownership, and termination for freelance engagements.',
                'credits_cost' => 1,
                'sort' => 1,
                'body' => $this->freelanceServiceBody(),
                'fields' => [
                    ['label' => 'Client Name',        'key' => 'client_name',     'type' => 'text',   'placeholder' => 'Acme Inc',            'required' => true,  'sort' => 1],
                    ['label' => 'Freelancer Name',    'key' => 'freelancer_name', 'type' => 'text',   'placeholder' => 'John Doe',            'required' => true,  'sort' => 2],
                    ['label' => 'Project Description', 'key' => 'project_desc',    'type' => 'textarea', 'placeholder' => 'Design and build a marketing website', 'required' => true, 'sort' => 3],
                    ['label' => 'Total Fee',          'key' => 'total_fee',       'type' => 'text',   'placeholder' => '$5,000',              'required' => true,  'sort' => 4],
                    ['label' => 'Payment Schedule',   'key' => 'payment_schedule', 'type' => 'textarea', 'placeholder' => '50% upfront, 50% on delivery', 'required' => true, 'sort' => 5],
                    ['label' => 'Delivery Date',      'key' => 'delivery_date',   'type' => 'date',   'placeholder' => '',                    'required' => true,  'sort' => 6],
                    ['label' => 'Governing Law',      'key' => 'governing_law',   'type' => 'text',   'placeholder' => 'California',          'required' => true,  'sort' => 7],
                    ['label' => 'Effective Date',     'key' => 'effective_date',  'type' => 'date',   'placeholder' => '',                    'required' => true,  'sort' => 8],
                ],
            ],

            // ── SaaS ─────────────────────────────────────────────────────────
            [
                'category_slug' => 'saas-software',
                'name' => 'SaaS Subscription Agreement',
                'description' => 'End-user subscription terms for a software-as-a-service product.',
                'credits_cost' => 1,
                'sort' => 1,
                'body' => $this->saasBody(),
                'fields' => [
                    ['label' => 'Provider Name',      'key' => 'provider_name',   'type' => 'text',   'placeholder' => 'Acme SaaS Ltd',       'required' => true,  'sort' => 1],
                    ['label' => 'Customer Name',      'key' => 'customer_name',   'type' => 'text',   'placeholder' => 'Beta Corp',           'required' => true,  'sort' => 2],
                    ['label' => 'Product Name',       'key' => 'product_name',    'type' => 'text',   'placeholder' => 'Acme Platform',       'required' => true,  'sort' => 3],
                    ['label' => 'Subscription Fee',   'key' => 'subscription_fee', 'type' => 'text',   'placeholder' => '$99/month',           'required' => true,  'sort' => 4],
                    ['label' => 'Billing Cycle',      'key' => 'billing_cycle',   'type' => 'select', 'placeholder' => '',                    'required' => true,  'sort' => 5, 'options' => ['Monthly', 'Annual']],
                    ['label' => 'Start Date',         'key' => 'start_date',      'type' => 'date',   'placeholder' => '',                    'required' => true,  'sort' => 6],
                    ['label' => 'Governing Law',      'key' => 'governing_law',   'type' => 'text',   'placeholder' => 'England and Wales',   'required' => true,  'sort' => 7],
                ],
            ],

        ];

        foreach ($templates as $data) {
            $categoryId = $cat($data['category_slug']);
            if (! $categoryId) {
                continue;
            }

            $template = ContractTemplate::firstOrCreate(
                ['name' => $data['name']],
                [
                    'category_id' => $categoryId,
                    'description' => $data['description'],
                    'credits_cost' => $data['credits_cost'],
                    'sort' => $data['sort'],
                    'active' => true,
                    'body' => $data['body'],
                ]
            );

            if ($template->wasRecentlyCreated) {
                foreach ($data['fields'] as $field) {
                    $template->fields()->create([
                        'label' => $field['label'],
                        'key' => $field['key'],
                        'type' => $field['type'],
                        'placeholder' => $field['placeholder'],
                        'required' => $field['required'],
                        'sort' => $field['sort'],
                        'options' => $field['options'] ?? null,
                        'group' => null,
                    ]);
                }
            }
        }
    }

    private function mutualNdaBody(): string
    {
        return <<<'HTML'
<h2>MUTUAL NON-DISCLOSURE AGREEMENT</h2>
<p>This Mutual Non-Disclosure Agreement ("Agreement") is entered into as of <strong>{{effective_date}}</strong> between <strong>{{party_a_name}}</strong>, located at {{party_a_address}} ("Party A"), and <strong>{{party_b_name}}</strong>, located at {{party_b_address}} ("Party B").</p>

<h3>1. Purpose</h3>
<p>The parties wish to explore {{purpose}} and may disclose confidential information to each other for this purpose.</p>

<h3>2. Confidential Information</h3>
<p>Each party may disclose confidential, proprietary, or trade secret information ("Confidential Information"). The receiving party agrees to hold such information in strict confidence and not disclose it to third parties.</p>

<h3>3. Obligations</h3>
<p>Each party shall: (a) protect the other's Confidential Information with at least the same degree of care it uses for its own; (b) use it solely for the stated purpose; (c) limit access to employees with a need to know.</p>

<h3>4. Term</h3>
<p>This Agreement shall remain in effect for <strong>{{term_years}} year(s)</strong> from the Effective Date.</p>

<h3>5. Governing Law</h3>
<p>This Agreement is governed by the laws of <strong>{{governing_law}}</strong>.</p>

<p><strong>{{party_a_name}}</strong> &nbsp;&nbsp;&nbsp;&nbsp; <strong>{{party_b_name}}</strong></p>
HTML;
    }

    private function employmentOfferBody(): string
    {
        return <<<'HTML'
<h2>EMPLOYMENT OFFER LETTER</h2>
<p>Date: <strong>{{start_date}}</strong></p>
<p>Dear <strong>{{employee_name}}</strong>,</p>
<p>We are pleased to offer you the position of <strong>{{job_title}}</strong> at <strong>{{employer_name}}</strong>, commencing on <strong>{{start_date}}</strong>.</p>

<h3>Compensation</h3>
<p>Your annual base salary will be <strong>{{annual_salary}}</strong>, paid in accordance with the company's standard payroll schedule.</p>

<h3>Location</h3>
<p>Your primary work location will be <strong>{{work_location}}</strong>.</p>

<h3>Probationary Period</h3>
<p>Your employment is subject to a probationary period of <strong>{{probation_period}}</strong>, during which either party may terminate employment with one week's notice.</p>

<h3>Governing Law</h3>
<p>This offer is governed by the laws of <strong>{{governing_law}}</strong>.</p>

<p>Please sign and return this letter to confirm your acceptance.</p>
<p>Sincerely,<br><strong>{{employer_name}}</strong></p>
HTML;
    }

    private function freelanceServiceBody(): string
    {
        return <<<'HTML'
<h2>FREELANCE SERVICE AGREEMENT</h2>
<p>This Agreement is entered into as of <strong>{{effective_date}}</strong> between <strong>{{client_name}}</strong> ("Client") and <strong>{{freelancer_name}}</strong> ("Freelancer").</p>

<h3>1. Services</h3>
<p>Freelancer agrees to perform the following services: <strong>{{project_desc}}</strong></p>

<h3>2. Payment</h3>
<p>Client agrees to pay a total fee of <strong>{{total_fee}}</strong> as follows: {{payment_schedule}}.</p>

<h3>3. Delivery</h3>
<p>Freelancer shall deliver completed work by <strong>{{delivery_date}}</strong>.</p>

<h3>4. Intellectual Property</h3>
<p>Upon receipt of full payment, Freelancer assigns all rights in the work product to Client.</p>

<h3>5. Termination</h3>
<p>Either party may terminate with 14 days written notice. Client shall pay for all work completed to date.</p>

<h3>6. Governing Law</h3>
<p>This Agreement is governed by the laws of <strong>{{governing_law}}</strong>.</p>
HTML;
    }

    private function saasBody(): string
    {
        return <<<'HTML'
<h2>SAAS SUBSCRIPTION AGREEMENT</h2>
<p>This Agreement is entered into as of <strong>{{start_date}}</strong> between <strong>{{provider_name}}</strong> ("Provider") and <strong>{{customer_name}}</strong> ("Customer").</p>

<h3>1. Services</h3>
<p>Provider grants Customer access to <strong>{{product_name}}</strong> on a subscription basis.</p>

<h3>2. Subscription Fee</h3>
<p>Customer agrees to pay <strong>{{subscription_fee}}</strong> on a <strong>{{billing_cycle}}</strong> basis, starting <strong>{{start_date}}</strong>.</p>

<h3>3. Acceptable Use</h3>
<p>Customer shall not reverse-engineer, resell, or use the platform for unlawful purposes.</p>

<h3>4. Data & Privacy</h3>
<p>Provider will process Customer data solely to deliver the service, in accordance with its Privacy Policy.</p>

<h3>5. Termination</h3>
<p>Either party may terminate with 30 days notice. No refunds for partial billing periods.</p>

<h3>6. Limitation of Liability</h3>
<p>Provider's total liability shall not exceed fees paid in the prior 3 months.</p>

<h3>7. Governing Law</h3>
<p>This Agreement is governed by the laws of <strong>{{governing_law}}</strong>.</p>
HTML;
    }
}
