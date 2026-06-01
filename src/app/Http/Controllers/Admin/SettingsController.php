<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Settings\Facades\Settings;
use App\Domain\Upload\FileUploader;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function update(Request $request, FileUploader $uploader, $tab = 'general')
    {
        match ($tab) {
            'general' => $this->updateGeneral($request),
            'brand' => $this->updateBrand($request, $uploader),
            'seo' => $this->updateSeo($request),
            'email' => $this->updateEmail($request),
            'sms' => $this->updateSms($request),
            'storage' => $this->updateStorage($request),
            'queue' => $this->updateQueue($request),
            'integrations' => $this->updateIntegrations($request),
            'controls' => $this->updateControls($request),
            'social' => $this->updateSocial($request),
            'ai' => $this->updateAi($request),
            default => null,
        };

        $hash = $request->input('_tab', $tab);
        $base = strtok(url()->previous(), '#');

        return redirect($base.'#'.$hash)->with('success', 'Settings saved.');
    }

    private function updateGeneral(Request $request): void
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:100'],
            'site_tagline' => ['nullable', 'string', 'max:200'],
            'site_email' => ['nullable', 'email', 'max:150'],
            'site_phone' => ['nullable', 'string', 'max:50'],
            'site_timezone' => ['required', 'timezone'],
            'site_currency' => ['required', 'string', 'size:3'],
            'site_currency_symbol' => ['required', 'string', 'max:10'],
        ]);

        Settings::set('site.name', $data['site_name'], 'string', 'general');
        Settings::set('site.tagline', $data['site_tagline'] ?? '', 'string', 'general');
        Settings::set('site.email', $data['site_email'] ?? '', 'string', 'general');
        Settings::set('site.phone', $data['site_phone'] ?? '', 'string', 'general');
        Settings::set('site.timezone', $data['site_timezone'], 'string', 'general');
        Settings::set('site.currency', strtoupper($data['site_currency']), 'string', 'general');
        Settings::set('site.currency_symbol', $data['site_currency_symbol'], 'string', 'general');
    }

    private function updateBrand(Request $request, FileUploader $uploader): void
    {
        $data = $request->validate([
            'site_font'      => ['nullable', 'string', 'max:100'],
            'site_logo'      => ['nullable', 'file', 'image', 'max:2048'],
            'site_dark_logo' => ['nullable', 'file', 'image', 'max:2048'],
            'site_favicon'   => ['nullable', 'file', 'image', 'max:512'],
            'site_og_image'  => ['nullable', 'file', 'image', 'max:2048'],
            'custom_css'     => ['nullable', 'string', 'max:65535'],
            'primary_color'  => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        Settings::set('site.font', $data['site_font'] ?? 'system', 'string', 'general');
        Settings::set('site.primary_color', $data['primary_color'] ?? '#D4FF3A', 'string', 'general');

        $css = $this->sanitizeCss($data['custom_css'] ?? '');
        Settings::set('site.custom_css', $css, 'string', 'general');
        file_put_contents(base_path('../assets/static/css/custom.css'), $css);

        if ($request->hasFile('site_logo')) {
            try {
                $this->deleteOldUpload(Settings::string('site.logo'));
                $meta = $uploader->store($request->file('site_logo'), 'brand');
                Settings::set('site.logo', $meta->url, 'string', 'general');
            } catch (\Throwable $e) {
                Log::error('Logo upload failed: '.$e->getMessage());
                throw $e;
            }
        }

        if ($request->hasFile('site_dark_logo')) {
            try {
                $this->deleteOldUpload(Settings::string('site.dark_logo'));
                $meta = $uploader->store($request->file('site_dark_logo'), 'brand');
                Settings::set('site.dark_logo', $meta->url, 'string', 'general');
            } catch (\Throwable $e) {
                Log::error('Dark logo upload failed: '.$e->getMessage());
                throw $e;
            }
        }

        if ($request->hasFile('site_favicon')) {
            try {
                $this->deleteOldUpload(Settings::string('site.favicon'));
                $meta = $uploader->store($request->file('site_favicon'), 'brand', maxWidth: 80);
                Settings::set('site.favicon', $meta->url, 'string', 'general');
            } catch (\Throwable $e) {
                Log::error('Favicon upload failed: '.$e->getMessage());
                throw $e;
            }
        }

        if ($request->hasFile('site_og_image')) {
            try {
                $this->deleteOldUpload(Settings::string('site.og_image'));
                $meta = $uploader->store($request->file('site_og_image'), 'brand');
                Settings::set('site.og_image', $meta->url, 'string', 'general');
            } catch (\Throwable $e) {
                Log::error('OG image upload failed: '.$e->getMessage());
                throw $e;
            }
        }
    }

    private function updateSeo(Request $request): void
    {
        $data = $request->validate([
            'footer_text' => ['nullable', 'string', 'max:500'],
            'meta_description' => ['nullable', 'string', 'max:300'],
        ]);

        Settings::set('site.footer_text', $data['footer_text'] ?? '', 'string', 'general');
        Settings::set('site.meta_description', $data['meta_description'] ?? '', 'string', 'general');
    }

    private function updateEmail(Request $request): void
    {
        $data = $request->validate([
            'mail_from_address' => ['required', 'email'],
            'mail_from_name' => ['required', 'string', 'max:100'],
            'mail_driver' => ['required', 'string'],
            'mail_smtp_host' => ['nullable', 'string'],
            'mail_smtp_port' => ['nullable', 'string'],
            'mail_smtp_username' => ['nullable', 'string'],
            'mail_smtp_password' => ['nullable', 'string'],
            'mail_smtp_encryption' => ['nullable', 'string'],
            'mail_mailgun_domain' => ['nullable', 'string'],
            'mail_mailgun_secret' => ['nullable', 'string'],
            'mail_mailgun_endpoint' => ['nullable', 'string'],
            'mail_ses_key' => ['nullable', 'string'],
            'mail_ses_secret' => ['nullable', 'string'],
            'mail_ses_region' => ['nullable', 'string'],
            'mail_postmark_token' => ['nullable', 'string'],
            'mail_resend_key' => ['nullable', 'string'],
        ]);

        Settings::set('mail.from_address', $data['mail_from_address'], 'string', 'mail');
        Settings::set('mail.from_name', $data['mail_from_name'], 'string', 'mail');
        Settings::set('mail.driver', $data['mail_driver'], 'string', 'mail');
        Settings::set('mail.smtp.host', $data['mail_smtp_host'] ?? '', 'string', 'mail');
        Settings::set('mail.smtp.port', $data['mail_smtp_port'] ?? '', 'string', 'mail');
        Settings::set('mail.smtp.username', $data['mail_smtp_username'] ?? '', 'string', 'mail');
        if (!empty($data['mail_smtp_password'])) {
            Settings::set('mail.smtp.password', $data['mail_smtp_password'], 'string', 'mail');
        }
        Settings::set('mail.smtp.encryption', $data['mail_smtp_encryption'] ?? '', 'string', 'mail');
        Settings::set('mail.mailgun.domain', $data['mail_mailgun_domain'] ?? '', 'string', 'mail');
        Settings::set('mail.mailgun.secret', $data['mail_mailgun_secret'] ?? '', 'string', 'mail');
        Settings::set('mail.mailgun.endpoint', $data['mail_mailgun_endpoint'] ?? '', 'string', 'mail');
        Settings::set('mail.ses.key', $data['mail_ses_key'] ?? '', 'string', 'mail');
        Settings::set('mail.ses.secret', $data['mail_ses_secret'] ?? '', 'string', 'mail');
        Settings::set('mail.ses.region', $data['mail_ses_region'] ?? '', 'string', 'mail');
        Settings::set('mail.postmark.token', $data['mail_postmark_token'] ?? '', 'string', 'mail');
        Settings::set('mail.resend.key', $data['mail_resend_key'] ?? '', 'string', 'mail');
    }

    private function updateSms(Request $request): void
    {
        $data = $request->validate([
            'sms_driver' => ['required', 'string'],
            'sms_twilio_sid' => ['nullable', 'string'],
            'sms_twilio_token' => ['nullable', 'string'],
            'sms_twilio_from' => ['nullable', 'string'],
            'sms_vonage_api_key' => ['nullable', 'string'],
            'sms_vonage_api_secret' => ['nullable', 'string'],
            'sms_vonage_from' => ['nullable', 'string'],
            'sms_messagebird_api_key' => ['nullable', 'string'],
            'sms_messagebird_from' => ['nullable', 'string'],
            'sms_textmagic_username' => ['nullable', 'string'],
            'sms_textmagic_api_key' => ['nullable', 'string'],
        ]);

        Settings::set('sms.driver', $data['sms_driver'], 'string', 'sms');
        Settings::set('sms.twilio.sid', $data['sms_twilio_sid'] ?? '', 'string', 'sms');
        Settings::set('sms.twilio.token', $data['sms_twilio_token'] ?? '', 'string', 'sms');
        Settings::set('sms.twilio.from', $data['sms_twilio_from'] ?? '', 'string', 'sms');
        Settings::set('sms.vonage.api_key', $data['sms_vonage_api_key'] ?? '', 'string', 'sms');
        Settings::set('sms.vonage.api_secret', $data['sms_vonage_api_secret'] ?? '', 'string', 'sms');
        Settings::set('sms.vonage.from', $data['sms_vonage_from'] ?? '', 'string', 'sms');
        Settings::set('sms.messagebird.api_key', $data['sms_messagebird_api_key'] ?? '', 'string', 'sms');
        Settings::set('sms.messagebird.from', $data['sms_messagebird_from'] ?? '', 'string', 'sms');
        Settings::set('sms.textmagic.username', $data['sms_textmagic_username'] ?? '', 'string', 'sms');
        Settings::set('sms.textmagic.api_key', $data['sms_textmagic_api_key'] ?? '', 'string', 'sms');
    }

    private function updateStorage(Request $request): void
    {
        $data = $request->validate([
            'storage_driver' => ['required', 'string'],
            'storage_s3_key' => ['nullable', 'string'],
            'storage_s3_secret' => ['nullable', 'string'],
            'storage_s3_region' => ['nullable', 'string'],
            'storage_s3_bucket' => ['nullable', 'string'],
            'storage_s3_endpoint' => ['nullable', 'string'],
            'storage_r2_key' => ['nullable', 'string'],
            'storage_r2_secret' => ['nullable', 'string'],
            'storage_r2_bucket' => ['nullable', 'string'],
            'storage_r2_endpoint' => ['nullable', 'string'],
            'storage_wasabi_key' => ['nullable', 'string'],
            'storage_wasabi_secret' => ['nullable', 'string'],
            'storage_wasabi_region' => ['nullable', 'string'],
            'storage_wasabi_bucket' => ['nullable', 'string'],
            'storage_do_spaces_key' => ['nullable', 'string'],
            'storage_do_spaces_secret' => ['nullable', 'string'],
            'storage_do_spaces_region' => ['nullable', 'string'],
            'storage_do_spaces_bucket' => ['nullable', 'string'],
            'storage_do_spaces_endpoint' => ['nullable', 'string'],
        ]);

        Settings::set('storage.driver', $data['storage_driver'], 'string', 'storage');
        Settings::set('storage.s3.key', $data['storage_s3_key'] ?? '', 'string', 'storage');
        Settings::set('storage.s3.secret', $data['storage_s3_secret'] ?? '', 'string', 'storage');
        Settings::set('storage.s3.region', $data['storage_s3_region'] ?? '', 'string', 'storage');
        Settings::set('storage.s3.bucket', $data['storage_s3_bucket'] ?? '', 'string', 'storage');
        Settings::set('storage.s3.endpoint', $data['storage_s3_endpoint'] ?? '', 'string', 'storage');
        Settings::set('storage.r2.key', $data['storage_r2_key'] ?? '', 'string', 'storage');
        Settings::set('storage.r2.secret', $data['storage_r2_secret'] ?? '', 'string', 'storage');
        Settings::set('storage.r2.bucket', $data['storage_r2_bucket'] ?? '', 'string', 'storage');
        Settings::set('storage.r2.endpoint', $data['storage_r2_endpoint'] ?? '', 'string', 'storage');
        Settings::set('storage.wasabi.key', $data['storage_wasabi_key'] ?? '', 'string', 'storage');
        Settings::set('storage.wasabi.secret', $data['storage_wasabi_secret'] ?? '', 'string', 'storage');
        Settings::set('storage.wasabi.region', $data['storage_wasabi_region'] ?? '', 'string', 'storage');
        Settings::set('storage.wasabi.bucket', $data['storage_wasabi_bucket'] ?? '', 'string', 'storage');
        Settings::set('storage.do_spaces.key', $data['storage_do_spaces_key'] ?? '', 'string', 'storage');
        Settings::set('storage.do_spaces.secret', $data['storage_do_spaces_secret'] ?? '', 'string', 'storage');
        Settings::set('storage.do_spaces.region', $data['storage_do_spaces_region'] ?? '', 'string', 'storage');
        Settings::set('storage.do_spaces.bucket', $data['storage_do_spaces_bucket'] ?? '', 'string', 'storage');
        Settings::set('storage.do_spaces.endpoint', $data['storage_do_spaces_endpoint'] ?? '', 'string', 'storage');
    }

    private function updateQueue(Request $request): void
    {
        $data = $request->validate([
            'queue_enabled' => ['required', 'in:0,1'],
        ]);

        Settings::set('queue.enabled', $data['queue_enabled'], 'string', 'queue');
    }

    private function updateControls(Request $request): void
    {
        $data = $request->validate([
            'registration_enabled' => ['nullable', 'in:0,1'],
            'email_verification' => ['nullable', 'in:0,1'],
            'sms_verification' => ['nullable', 'in:0,1'],
            'kyc_enabled' => ['nullable', 'in:0,1'],
            'policy_agreement' => ['nullable', 'in:0,1'],
            'maintenance_mode' => ['nullable', 'in:0,1'],
            'force_https' => ['nullable', 'in:0,1'],
            'cookie_consent' => ['nullable', 'in:0,1'],
        ]);

        Settings::set('controls.registration_enabled', $data['registration_enabled'] ?? '0', 'string', 'controls');
        Settings::set('controls.email_verification', $data['email_verification'] ?? '0', 'string', 'controls');
        Settings::set('controls.sms_verification', $data['sms_verification'] ?? '0', 'string', 'controls');
        Settings::set('controls.kyc_enabled', $data['kyc_enabled'] ?? '0', 'string', 'controls');
        Settings::set('controls.policy_agreement', $data['policy_agreement'] ?? '0', 'string', 'controls');
        Settings::set('site.maintenance_mode', $data['maintenance_mode'] ?? '0', 'string', 'controls');
        Settings::set('controls.force_https', $data['force_https'] ?? '0', 'string', 'controls');
        Settings::set('controls.cookie_consent', $data['cookie_consent'] ?? '0', 'string', 'controls');
    }

    private function updateIntegrations(Request $request): void
    {
        $data = $request->validate([
            'turnstile_enabled' => ['nullable', 'in:0,1'],
            'turnstile_site_key' => ['nullable', 'string', 'max:200'],
            'turnstile_secret_key' => ['nullable', 'string', 'max:200'],
            'ga_enabled' => ['nullable', 'in:0,1'],
            'ga_measurement_id' => ['nullable', 'string', 'max:50'],
            'tawk_enabled' => ['nullable', 'in:0,1'],
            'tawk_property_id' => ['nullable', 'string', 'max:100'],
            'tawk_widget_id' => ['nullable', 'string', 'max:100'],
        ]);

        Settings::set('integrations.turnstile.enabled', $data['turnstile_enabled'] ?? '0', 'string', 'integrations');
        Settings::set('integrations.turnstile.site_key', $data['turnstile_site_key'] ?? '', 'string', 'integrations');
        Settings::set('integrations.turnstile.secret_key', $data['turnstile_secret_key'] ?? '', 'string', 'integrations');
        Settings::set('integrations.ga.enabled', $data['ga_enabled'] ?? '0', 'string', 'integrations');
        Settings::set('integrations.ga.measurement_id', $data['ga_measurement_id'] ?? '', 'string', 'integrations');
        Settings::set('integrations.tawk.enabled', $data['tawk_enabled'] ?? '0', 'string', 'integrations');
        Settings::set('integrations.tawk.property_id', $data['tawk_property_id'] ?? '', 'string', 'integrations');
        Settings::set('integrations.tawk.widget_id', $data['tawk_widget_id'] ?? '', 'string', 'integrations');
    }

    private function updateSocial(Request $request): void
    {
        $data = $request->validate([
            'social_google_enabled'          => ['nullable', 'in:0,1'],
            'social_google_client_id'        => ['nullable', 'string', 'max:500'],
            'social_google_client_secret'    => ['nullable', 'string', 'max:500'],
            'social_facebook_enabled'        => ['nullable', 'in:0,1'],
            'social_facebook_client_id'      => ['nullable', 'string', 'max:500'],
            'social_facebook_client_secret'  => ['nullable', 'string', 'max:500'],
            'social_twitter_enabled'         => ['nullable', 'in:0,1'],
            'social_twitter_client_id'       => ['nullable', 'string', 'max:500'],
            'social_twitter_client_secret'   => ['nullable', 'string', 'max:500'],
            'social_linkedin_enabled'        => ['nullable', 'in:0,1'],
            'social_linkedin_client_id'      => ['nullable', 'string', 'max:500'],
            'social_linkedin_client_secret'  => ['nullable', 'string', 'max:500'],
        ]);

        foreach (['google', 'facebook', 'twitter', 'linkedin'] as $provider) {
            Settings::set('social.'.$provider.'.enabled',       $data['social_'.$provider.'_enabled']       ?? '0',  'string', 'social');
            Settings::set('social.'.$provider.'.client_id',     $data['social_'.$provider.'_client_id']     ?? '',   'string', 'social');
            Settings::set('social.'.$provider.'.client_secret', $data['social_'.$provider.'_client_secret'] ?? '',   'string', 'social');
        }
    }

    private function sanitizeCss(string $css): string
    {
        $css = strip_tags($css);
        $css = preg_replace('/<\?.*?\?>/s', '', $css);
        $css = preg_replace('/url\s*\(\s*["\']?\s*(?:javascript|data|vbscript):/i', 'url(#', $css);
        $css = preg_replace('/expression\s*\(/i', '', $css);
        $css = preg_replace('/@import\b/i', '', $css);
        $css = preg_replace('/-moz-binding\s*:/i', '', $css);
        $css = preg_replace('/\bbehavior\s*:/i', '', $css);

        return trim($css);
    }

    private function updateAi(Request $request): void
    {
        $data = $request->validate([
            'ai_openai_key'        => ['nullable', 'string', 'max:200'],
            'ai_openai_model'      => ['required', 'string', 'max:60'],
            'ai_openai_max_tokens' => ['required', 'integer', 'min:100', 'max:4000'],
            'ai_openai_temperature'=> ['required', 'numeric', 'min:0', 'max:2'],
        ]);

        if (!empty($data['ai_openai_key'])) {
            Settings::set('ai.openai_key', $data['ai_openai_key'], 'string', 'ai');
        }
        Settings::set('ai.openai_model', $data['ai_openai_model'], 'string', 'ai');
        Settings::set('ai.openai_max_tokens', (int) $data['ai_openai_max_tokens'], 'integer', 'ai');
        Settings::set('ai.openai_temperature', (float) $data['ai_openai_temperature'], 'string', 'ai');
    }

    private function deleteOldUpload(?string $url): void
    {
        if (! $url) {
            return;
        }

        $needle = '/assets/uploads/';
        $pos = strpos($url, $needle);
        if ($pos === false) {
            return;
        }

        $relativePath = substr($url, $pos + strlen($needle));
        Storage::disk('uploads')->delete($relativePath);
    }
}
