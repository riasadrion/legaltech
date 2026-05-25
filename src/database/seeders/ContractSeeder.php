<?php

namespace Database\Seeders;

use App\Domain\Contract\Models\Contract;
use App\Domain\Contract\Models\ContractParty;
use App\Domain\Payment\Models\Gateway;
use App\Domain\Payment\Models\Payment;
use App\Domain\Wallet\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContractSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'testuser@example.com')->first();

        if (! $user) {
            $this->command->warn('testuser not found — run UserSeeder first.');
            return;
        }

        $gateway = Gateway::where('driver', 'stripe')->first();

        $contracts = [

            // 1 — Fully signed NDA
            [
                'title'       => 'Mutual Non-Disclosure Agreement',
                'status'      => Contract::STATUS_SIGNED,
                'credits_used'=> 1,
                'created_at'  => now()->subDays(24),
                'body_html'   => $this->ndaHtml('Test User', 'Tarreq Innovations Ltd'),
                'parties'     => [
                    ['name' => 'Test User',              'email' => 'testuser@example.com', 'role' => 'signer', 'signed_at' => now()->subDays(22), 'sort' => 1],
                    ['name' => 'Tarreq Innovations Ltd', 'email' => 'tarreq@example.com',  'role' => 'signer', 'signed_at' => now()->subDays(20), 'sort' => 2],
                ],
                'payment' => ['amount' => 89.00, 'offset_days' => 24],
            ],

            // 2 — Sent, one signer done, one pending
            [
                'title'       => 'Freelance Development Services Agreement',
                'status'      => Contract::STATUS_SENT,
                'credits_used'=> 1,
                'created_at'  => now()->subDays(10),
                'body_html'   => $this->serviceAgreementHtml('Test User', 'Riasad Zaman'),
                'parties'     => [
                    ['name' => 'Test User',   'email' => 'testuser@example.com', 'role' => 'signer', 'signed_at' => now()->subDays(8), 'sort' => 1],
                    ['name' => 'Riasad Zaman','email' => 'riasad@example.com',   'role' => 'signer', 'signed_at' => null,              'sort' => 2],
                ],
                'payment' => null,
            ],

            // 3 — Ready, not yet sent
            [
                'title'       => 'SaaS Subscription & License Agreement',
                'status'      => Contract::STATUS_READY,
                'credits_used'=> 1,
                'created_at'  => now()->subDays(5),
                'body_html'   => $this->saasHtml('Test User', 'Cystall Pte. Ltd.'),
                'parties'     => [
                    ['name' => 'Test User',       'email' => 'testuser@example.com', 'role' => 'signer', 'signed_at' => null, 'sort' => 1],
                    ['name' => 'Cystall Pte. Ltd','email' => 'legal@cystall.com',    'role' => 'signer', 'signed_at' => null, 'sort' => 2],
                ],
                'payment' => ['amount' => 89.00, 'offset_days' => 5],
            ],

            // 4 — Draft in progress
            [
                'title'       => 'Content Creator Partnership Agreement',
                'status'      => Contract::STATUS_EDITING,
                'credits_used'=> 0,
                'created_at'  => now()->subDays(2),
                'body_html'   => '<p>Draft content being edited...</p>',
                'parties'     => [
                    ['name' => 'Test User', 'email' => 'testuser@example.com', 'role' => 'signer', 'signed_at' => null, 'sort' => 1],
                ],
                'payment' => null,
            ],

            // 5 — Expired signed contract (older)
            [
                'title'       => 'Consulting Retainer Agreement',
                'status'      => Contract::STATUS_EXPIRED,
                'credits_used'=> 1,
                'created_at'  => now()->subMonths(3),
                'expires_at'  => now()->subMonths(2),
                'body_html'   => $this->retainerHtml('Test User', 'Alpha Ventures'),
                'parties'     => [
                    ['name' => 'Test User',    'email' => 'testuser@example.com', 'role' => 'signer', 'signed_at' => now()->subMonths(3)->addDays(2), 'sort' => 1],
                    ['name' => 'Alpha Ventures','email' => 'legal@alpha.com',     'role' => 'signer', 'signed_at' => now()->subMonths(3)->addDays(3), 'sort' => 2],
                ],
                'payment' => ['amount' => 89.00, 'offset_days' => 90],
            ],
        ];

        foreach ($contracts as $def) {
            $contract = Contract::create([
                'user_id'      => $user->id,
                'title'        => $def['title'],
                'status'       => $def['status'],
                'credits_used' => $def['credits_used'],
                'body_html'    => $def['body_html'],
                'share_token'  => Str::random(48),
                'expires_at'   => $def['expires_at'] ?? null,
                'created_at'   => $def['created_at'],
                'updated_at'   => $def['created_at'],
            ]);

            foreach ($def['parties'] as $party) {
                ContractParty::create([
                    'contract_id' => $contract->id,
                    'name'        => $party['name'],
                    'email'       => $party['email'],
                    'role'        => $party['role'],
                    'token'       => Str::random(64),
                    'signed_at'   => $party['signed_at'],
                    'sort'        => $party['sort'],
                ]);
            }

            // Seed a payment for this subscription period
            if ($def['payment'] && $gateway) {
                $amount     = $def['payment']['amount'];
                $createdAt  = now()->subDays($def['payment']['offset_days']);
                $fee        = $gateway->calculateFee($amount);
                $net        = $amount - $fee;

                $payment = Payment::create([
                    'user_id'     => $user->id,
                    'gateway_id'  => $gateway->id,
                    'amount'      => $amount,
                    'currency'    => 'USD',
                    'fee'         => $fee,
                    'status'      => Payment::STATUS_COMPLETED,
                    'gateway_ref' => 'ref_demo_' . Str::random(8),
                    'meta'        => ['demo' => true, 'plan' => 'Enterprise'],
                    'created_at'  => $createdAt,
                    'updated_at'  => $createdAt,
                ]);

                $newBalance = (float) $user->fresh()->balance + $net;

                Transaction::create([
                    'user_id'       => $user->id,
                    'type'          => Transaction::TYPE_CREDIT,
                    'amount'        => $net,
                    'currency'      => 'USD',
                    'description'   => 'Studio Monthly subscription — ' . $createdAt->format('M Y'),
                    'subject_type'  => Payment::class,
                    'subject_id'    => $payment->id,
                    'balance_after' => $newBalance,
                    'meta'          => ['gateway' => $gateway->driver],
                    'created_at'    => $createdAt,
                ]);

                $user->update(['balance' => $newBalance]);
            }
        }
    }

    private function ndaHtml(string $party1, string $party2): string
    {
        return <<<HTML
<h2>Mutual Non-Disclosure Agreement</h2>
<p>This Mutual Non-Disclosure Agreement ("Agreement") is entered into as of the date last signed below, between <strong>{$party1}</strong> and <strong>{$party2}</strong> (collectively, the "Parties").</p>
<h3>1. Confidential Information</h3>
<p>Each Party may disclose to the other certain confidential and proprietary information ("Confidential Information") in connection with evaluating a potential business relationship. Confidential Information includes any data or information that is proprietary to the disclosing Party and not generally known to the public.</p>
<h3>2. Obligations</h3>
<p>Each Party agrees to: (a) hold the other Party's Confidential Information in strict confidence; (b) not disclose such information to any third parties without prior written consent; (c) use the Confidential Information solely for the purpose of evaluating the potential business relationship.</p>
<h3>3. Term</h3>
<p>This Agreement shall remain in effect for a period of two (2) years from the date of execution, unless earlier terminated by mutual written agreement.</p>
<h3>4. Governing Law</h3>
<p>This Agreement shall be governed by and construed in accordance with the laws of the jurisdiction agreed upon by the Parties.</p>
HTML;
    }

    private function serviceAgreementHtml(string $provider, string $client): string
    {
        return <<<HTML
<h2>Freelance Development Services Agreement</h2>
<p>This Services Agreement ("Agreement") is made between <strong>{$provider}</strong> ("Service Provider") and <strong>{$client}</strong> ("Client").</p>
<h3>1. Services</h3>
<p>Service Provider agrees to deliver web application development services as described in the attached Statement of Work. Services shall be performed remotely unless otherwise agreed in writing.</p>
<h3>2. Compensation</h3>
<p>Client agrees to pay Service Provider at the agreed hourly or project rate. Invoices are due within 14 days of receipt. Late payments incur a 1.5% monthly interest charge.</p>
<h3>3. Intellectual Property</h3>
<p>Upon receipt of full payment, all work product created specifically for Client under this Agreement shall become the sole property of Client. Service Provider retains the right to display the work in their portfolio.</p>
<h3>4. Confidentiality</h3>
<p>Both parties agree to keep confidential any proprietary business information shared during the engagement.</p>
<h3>5. Term & Termination</h3>
<p>Either party may terminate this Agreement with 14 days written notice. Client shall pay for all work completed up to the termination date.</p>
HTML;
    }

    private function saasHtml(string $vendor, string $customer): string
    {
        return <<<HTML
<h2>SaaS Subscription & License Agreement</h2>
<p>This SaaS Subscription and License Agreement ("Agreement") is entered into between <strong>{$vendor}</strong> ("Vendor") and <strong>{$customer}</strong> ("Customer").</p>
<h3>1. License Grant</h3>
<p>Vendor grants Customer a non-exclusive, non-transferable, limited license to access and use the software-as-a-service platform ("Platform") during the Subscription Term solely for Customer's internal business purposes.</p>
<h3>2. Subscription Fees</h3>
<p>Customer agrees to pay the subscription fees as set out in the applicable Order Form. Fees are billed in advance on a monthly or annual basis and are non-refundable.</p>
<h3>3. Data & Privacy</h3>
<p>Vendor will process Customer data in accordance with its Privacy Policy and applicable data protection laws. Customer retains ownership of all Customer data.</p>
<h3>4. Service Levels</h3>
<p>Vendor will use commercially reasonable efforts to maintain Platform availability of 99.5% uptime per calendar month, excluding scheduled maintenance windows.</p>
<h3>5. Limitation of Liability</h3>
<p>In no event shall either party be liable for indirect, incidental, or consequential damages arising from use of the Platform.</p>
HTML;
    }

    private function retainerHtml(string $consultant, string $client): string
    {
        return <<<HTML
<h2>Consulting Retainer Agreement</h2>
<p>This Consulting Retainer Agreement ("Agreement") is entered into between <strong>{$consultant}</strong> ("Consultant") and <strong>{$client}</strong> ("Client").</p>
<h3>1. Retainer Services</h3>
<p>Client engages Consultant to provide strategic consulting services on a monthly retainer basis. Consultant shall make available up to 20 hours per month for Client's requests.</p>
<h3>2. Retainer Fee</h3>
<p>Client shall pay a monthly retainer fee of USD 2,500, due on the first business day of each month. Unused hours do not roll over to the following month.</p>
<h3>3. Additional Hours</h3>
<p>Hours exceeding the monthly retainer shall be billed at Consultant's standard hourly rate as agreed separately in writing.</p>
<h3>4. Term</h3>
<p>This Agreement commenced on the execution date and continued for an initial term of six (6) months, with automatic monthly renewals unless terminated with 30 days written notice.</p>
HTML;
    }
}
