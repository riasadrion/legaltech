<?php

namespace Database\Seeders;

use App\Facades\Settings;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $contractPdfLayout = $this->contractPdfLayout();

        $settings = [
            // ── General / Branding ────────────────────────────────────────
            ['key' => 'site.logo',         'value' => 'brand/logo-light.png', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site.dark_logo',    'value' => 'brand/logo-dark.png',  'type' => 'string', 'group' => 'general'],
            ['key' => 'site.favicon',      'value' => 'brand/favicon.png',    'type' => 'string', 'group' => 'general'],
            ['key' => 'site.og_image',     'value' => 'brand/og-image.png',   'type' => 'string', 'group' => 'general'],
            ['key' => 'site.font',          'value' => 'system',  'type' => 'string', 'group' => 'general'],
            ['key' => 'site.primary_color', 'value' => '#e57e38', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site.custom_css',    'value' => '',        'type' => 'string', 'group' => 'general'],
            ['key' => 'site.maintenance_mode', 'value' => '0',  'type' => 'string',  'group' => 'controls'],

            // ── Controls ──────────────────────────────────────────────────
            ['key' => 'controls.registration_enabled', 'value' => '1', 'type' => 'string', 'group' => 'controls'],
            ['key' => 'controls.email_verification',   'value' => '1', 'type' => 'string', 'group' => 'controls'],
            ['key' => 'controls.sms_verification',     'value' => '0', 'type' => 'string', 'group' => 'controls'],
            ['key' => 'controls.kyc_enabled',          'value' => '0', 'type' => 'string', 'group' => 'controls'],
            ['key' => 'controls.cookie_consent',       'value' => '1', 'type' => 'string', 'group' => 'controls'],
            ['key' => 'controls.policy_agreement',     'value' => '0', 'type' => 'string', 'group' => 'controls'],
            ['key' => 'controls.force_https',          'value' => '0', 'type' => 'string', 'group' => 'controls'],

            // ── Mail ─────────────────────────────────────────────────────
            ['key' => 'mail.driver',           'value' => 'smtp',             'type' => 'string', 'group' => 'mail'],
            ['key' => 'mail.from_address',     'value' => 'no-reply@cystall.com', 'type' => 'string', 'group' => 'mail'],
            ['key' => 'mail.from_name',        'value' => 'LegalDocs',        'type' => 'string', 'group' => 'mail'],
            ['key' => 'mail.smtp.host',        'value' => 'cystall.com',      'type' => 'string', 'group' => 'mail'],
            ['key' => 'mail.smtp.port',        'value' => '25',               'type' => 'string', 'group' => 'mail'],
            ['key' => 'mail.smtp.username',    'value' => 'no-reply@cystall.com', 'type' => 'string', 'group' => 'mail'],
            ['key' => 'mail.smtp.password',    'value' => 'No@2026$$#',       'type' => 'string', 'group' => 'mail'],
            ['key' => 'mail.smtp.encryption',  'value' => '',                 'type' => 'string', 'group' => 'mail'],
            ['key' => 'mail.mailgun.domain',   'value' => '',                 'type' => 'string', 'group' => 'mail'],
            ['key' => 'mail.mailgun.secret',   'value' => '',                 'type' => 'string', 'group' => 'mail'],
            ['key' => 'mail.mailgun.endpoint', 'value' => 'api.mailgun.net',  'type' => 'string', 'group' => 'mail'],
            ['key' => 'mail.ses.key',          'value' => '',                 'type' => 'string', 'group' => 'mail'],
            ['key' => 'mail.ses.secret',       'value' => '',                 'type' => 'string', 'group' => 'mail'],
            ['key' => 'mail.ses.region',       'value' => 'us-east-1',        'type' => 'string', 'group' => 'mail'],
            ['key' => 'mail.postmark.token',   'value' => '',                 'type' => 'string', 'group' => 'mail'],
            ['key' => 'mail.resend.key',       'value' => '',                 'type' => 'string', 'group' => 'mail'],

            // ── AI ───────────────────────────────────────────────────────
            ['key' => 'ai.openai_model',       'value' => 'gpt-4o-mini', 'type' => 'string',  'group' => 'ai'],
            ['key' => 'ai.openai_max_tokens',  'value' => 800,           'type' => 'integer', 'group' => 'ai'],
            ['key' => 'ai.openai_temperature', 'value' => '0.3',         'type' => 'string',  'group' => 'ai'],
            ['key' => 'ai.openai_key',         'value' => '',            'type' => 'string',  'group' => 'ai'],

            // ── Social login ─────────────────────────────────────────────
            ['key' => 'social.google.enabled',         'value' => '0', 'type' => 'string', 'group' => 'social'],
            ['key' => 'social.google.client_id',       'value' => '',  'type' => 'string', 'group' => 'social'],
            ['key' => 'social.google.client_secret',   'value' => '',  'type' => 'string', 'group' => 'social'],
            ['key' => 'social.facebook.enabled',       'value' => '0', 'type' => 'string', 'group' => 'social'],
            ['key' => 'social.facebook.client_id',     'value' => '',  'type' => 'string', 'group' => 'social'],
            ['key' => 'social.facebook.client_secret', 'value' => '',  'type' => 'string', 'group' => 'social'],
            ['key' => 'social.twitter.enabled',        'value' => '0', 'type' => 'string', 'group' => 'social'],
            ['key' => 'social.twitter.client_id',      'value' => '',  'type' => 'string', 'group' => 'social'],
            ['key' => 'social.twitter.client_secret',  'value' => '',  'type' => 'string', 'group' => 'social'],
            ['key' => 'social.linkedin.enabled',       'value' => '0', 'type' => 'string', 'group' => 'social'],
            ['key' => 'social.linkedin.client_id',     'value' => '',  'type' => 'string', 'group' => 'social'],
            ['key' => 'social.linkedin.client_secret', 'value' => '',  'type' => 'string', 'group' => 'social'],

            // ── Integrations ─────────────────────────────────────────────
            ['key' => 'integrations.turnstile.enabled',    'value' => '1',                                    'type' => 'string', 'group' => 'integrations'],
            ['key' => 'integrations.turnstile.site_key',   'value' => '0x4AAAAAADcMgJq4g_NzeVMx',             'type' => 'string', 'group' => 'integrations'],
            ['key' => 'integrations.turnstile.secret_key', 'value' => '0x4AAAAAADcMgFGhGZ5KapU94R46PKnktbQ',  'type' => 'string', 'group' => 'integrations'],
            ['key' => 'integrations.ga.enabled',           'value' => '0',                                    'type' => 'string', 'group' => 'integrations'],
            ['key' => 'integrations.ga.measurement_id',    'value' => '',                                     'type' => 'string', 'group' => 'integrations'],
            ['key' => 'integrations.tawk.enabled',         'value' => '0',                                    'type' => 'string', 'group' => 'integrations'],
            ['key' => 'integrations.tawk.property_id',     'value' => '6a1d189e26d0321c2b699bae',             'type' => 'string', 'group' => 'integrations'],
            ['key' => 'integrations.tawk.widget_id',       'value' => '1jq0qgbkj',                            'type' => 'string', 'group' => 'integrations'],

            // ── Contracts ─────────────────────────────────────────────────
            ['key' => 'contracts.pdf_layout', 'value' => $contractPdfLayout, 'type' => 'string', 'group' => 'contracts'],
        ];

        foreach ($settings as $s) {
            Settings::set($s['key'], $s['value'], $s['type'], $s['group']);
        }
    }

    /**
     * Global HTML/CSS layout used to wrap exported contract PDFs.
     * Tokens: {{site_name}}, {{contract_title}}, {{generated_date}}, {{contract_body}}, {{parties_table}}
     */
    private function contractPdfLayout(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: "DejaVu Serif", Georgia, serif;
    font-size: 12pt;
    line-height: 1.7;
    color: #1a1a1a;
    background: #fff;
    margin: 60px 72px 64px 72px;
  }
  .page { padding: 0; }
  .header {
    border-bottom: 2px solid #1a1a1a;
    padding-bottom: 18px;
    margin-bottom: 32px;
  }
  .header-site {
    font-size: 9pt;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: #777;
  }
  .header-title {
    font-size: 22pt;
    font-weight: bold;
    margin-top: 6px;
    line-height: 1.2;
  }
  .header-meta {
    font-size: 9pt;
    color: #888;
    margin-top: 6px;
  }
  .contract-body h1, .contract-body h2, .contract-body h3 {
    font-family: "DejaVu Serif", Georgia, serif;
    font-weight: bold;
    margin-top: 24px;
    margin-bottom: 8px;
    color: #111;
  }
  .contract-body h2 { font-size: 13pt; }
  .contract-body h3 { font-size: 12pt; }
  .contract-body p  { margin-bottom: 12px; text-align: justify; }
  .contract-body ul, .contract-body ol { margin: 8px 0 12px 24px; }
  .contract-body li { margin-bottom: 4px; }
  .signatures {
    margin-top: 40px;
    border-top: 1px solid #ccc;
    padding-top: 24px;
  }
  .signatures-title {
    font-size: 9pt;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #888;
    margin-bottom: 12px;
  }
  table.parties { width: 100%; border-collapse: collapse; font-size: 10pt; }
  table.parties th {
    background: #f5f5f5;
    padding: 8px 12px;
    text-align: left;
    font-size: 9pt;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #666;
    border-bottom: 2px solid #ddd;
  }
  table.parties td { padding: 8px 12px; border-bottom: 1px solid #e5e5e5; }
  table.parties td.col-secondary { color: #555; }
  table.parties td.col-status { font-size: 11px; }
  .signature-status-signed { color: #2d7a22; }
  .signature-status-pending { color: #999; }
</style>
</head>
<body>
<div class="page">
  <div class="header">
    <div class="header-site">{{site_name}}</div>
    <div class="header-title">{{contract_title}}</div>
    <div class="header-meta">Generated {{generated_date}}</div>
  </div>
  <div class="contract-body">
    {{contract_body}}
  </div>
  <div class="signatures">
    <div class="signatures-title">Parties &amp; Signatures</div>
    <table class="parties">
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        {{parties_table}}
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
HTML;
    }
}
