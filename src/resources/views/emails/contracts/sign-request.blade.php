<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Signature Required</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:Arial,Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f5;padding:32px 16px;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 16px rgba(0,0,0,0.07);">


      <tr>
        <td style="background:#0f0f0f;padding:24px 32px;border-bottom:2px solid #9acd32;">
          <a href="{{ $appUrl }}" style="color:#9acd32;font-size:20px;font-weight:bold;text-decoration:none;letter-spacing:-0.5px;">
            {{ $appName }}
          </a>
        </td>
      </tr>


      <tr>
        <td style="padding:36px 32px;color:#111827;font-size:15px;line-height:1.65;">
          <p style="margin:0 0 16px;">Hello <strong>{{ $party->name }}</strong>,</p>

          <p style="margin:0 0 16px;">
            You have been invited to review and sign the following contract:
          </p>

          <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:16px 20px;margin:0 0 24px;">
            <div style="font-size:11px;text-transform:uppercase;letter-spacing:0.1em;color:#9ca3af;margin-bottom:4px;">Contract</div>
            <div style="font-size:17px;font-weight:bold;color:#111827;">{{ $contract->title }}</div>
          </div>

          <p style="margin:0 0 24px;color:#4b5563;font-size:14px;">
            Please review the document carefully before signing. By signing, you confirm your agreement to all terms and conditions outlined in the contract.
          </p>

          <table cellpadding="0" cellspacing="0" style="margin:0 0 24px;">
            <tr>
              <td style="background:#9acd32;border-radius:8px;padding:0;">
                <a href="{{ $signUrl }}"
                   style="display:inline-block;background:#9acd32;color:#0f0f0f;padding:14px 32px;border-radius:8px;text-decoration:none;font-weight:700;font-size:15px;letter-spacing:-0.2px;">
                  Review &amp; Sign Contract
                </a>
              </td>
            </tr>
          </table>

          <p style="margin:0 0 8px;font-size:13px;color:#6b7280;">
            Or copy this link into your browser:
          </p>
          <p style="margin:0 0 24px;font-size:12px;color:#9ca3af;word-break:break-all;">
            <a href="{{ $signUrl }}" style="color:#6b7280;">{{ $signUrl }}</a>
          </p>

          <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:14px 18px;font-size:13px;color:#92400e;">
            <strong>Important:</strong> This link is unique to you and should not be shared with others.
          </div>
        </td>
      </tr>


      <tr>
        <td style="padding:20px 32px;background:#f9fafb;border-top:1px solid #e5e7eb;text-align:center;font-size:12px;color:#9ca3af;">
          <p style="margin:0 0 4px;">&copy; {{ $appName }} &mdash; Secure electronic contract signing</p>
          <p style="margin:0;">
            <a href="{{ $appUrl }}" style="color:#9ca3af;text-decoration:none;">{{ $appUrl }}</a>
          </p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>
