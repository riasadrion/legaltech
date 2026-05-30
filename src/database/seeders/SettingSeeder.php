<?php

namespace Database\Seeders;

use App\Domain\Settings\Facades\Settings;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ── General / Branding ────────────────────────────────────────
            ['key' => 'site.logo',         'value' => 'brand/group-1000003780-EULsmmk0.png',    'type' => 'string', 'group' => 'general'],
            ['key' => 'site.dark_logo',    'value' => 'brand/group-1000003780-1-P0dJof2i.png', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site.favicon',      'value' => 'brand/frame-1000003740-9rBLtRLP.png',   'type' => 'string', 'group' => 'general'],
            ['key' => 'site.font',          'value' => 'system',  'type' => 'string', 'group' => 'general'],
            ['key' => 'site.primary_color', 'value' => '#D4FF3A', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site.custom_css',    'value' => '',        'type' => 'string', 'group' => 'general'],
            ['key' => 'site.maintenance_mode', 'value' => '0',  'type' => 'string',  'group' => 'controls'],

            // ── Controls ──────────────────────────────────────────────────
            ['key' => 'controls.registration_enabled', 'value' => '1', 'type' => 'string', 'group' => 'controls'],
            ['key' => 'controls.email_verification',   'value' => '0', 'type' => 'string', 'group' => 'controls'],
            ['key' => 'controls.sms_verification',     'value' => '0', 'type' => 'string', 'group' => 'controls'],
            ['key' => 'controls.kyc_enabled',          'value' => '0', 'type' => 'string', 'group' => 'controls'],
            ['key' => 'controls.cookie_consent',       'value' => '1', 'type' => 'string', 'group' => 'controls'],
            ['key' => 'controls.policy_agreement',     'value' => '0', 'type' => 'string', 'group' => 'controls'],
            ['key' => 'controls.force_https',          'value' => '0', 'type' => 'string', 'group' => 'controls'],

            // ── Mail ─────────────────────────────────────────────────────
            ['key' => 'mail.driver',           'value' => 'log',              'type' => 'string', 'group' => 'mail'],
            ['key' => 'mail.from_address',     'value' => 'hello@example.com','type' => 'string', 'group' => 'mail'],
            ['key' => 'mail.from_name',        'value' => 'LegalTech',        'type' => 'string', 'group' => 'mail'],
            ['key' => 'mail.smtp.host',        'value' => '',                 'type' => 'string', 'group' => 'mail'],
            ['key' => 'mail.smtp.port',        'value' => '587',              'type' => 'string', 'group' => 'mail'],
            ['key' => 'mail.smtp.username',    'value' => '',                 'type' => 'string', 'group' => 'mail'],
            ['key' => 'mail.smtp.password',    'value' => '',                 'type' => 'string', 'group' => 'mail'],
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
        ];

        foreach ($settings as $s) {
            Settings::set($s['key'], $s['value'], $s['type'], $s['group']);
        }
    }
}
