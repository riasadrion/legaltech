<?php

namespace App\Domain\Contract\Services;

use App\Domain\Contract\Models\Contract;
use Dompdf\Dompdf;
use Dompdf\Options;

class ContractPdfExporter
{
    public function stream(Contract $contract): string
    {
        $html = $this->buildHtml($contract);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', false);
        $options->set('defaultFont', 'DejaVu Serif');
        $options->set('isRemoteEnabled', false);
        $options->set('chroot', storage_path());

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $canvas = $dompdf->getCanvas();
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) use ($contract) {
            $font  = $fontMetrics->getFont('DejaVu Serif', 'normal');
            $size  = 7.5;
            $color = [0.7, 0.7, 0.7];
            $w     = $canvas->get_width();
            $h     = $canvas->get_height();
            $canvas->line(54, $h - 32, $w - 54, $h - 32, $color, 0.5);
            $canvas->text(54, $h - 24, config('app.name').' — '.e($contract->title), $font, $size, $color);
            $canvas->text($w - 90, $h - 24, "Page {$pageNumber} of {$pageCount}", $font, $size, $color);
        });

        return $dompdf->output();
    }

    private function buildHtml(Contract $contract): string
    {
        $siteName = config('app.name');
        $today    = now()->format('F j, Y');
        $body     = $contract->body_html ?? '';

        $parties = $contract->parties->map(function ($p) {
            $signed = $p->signed_at
                ? '<span style="color:#2d7a22;">✓ Signed ' . $p->signed_at->format('M j, Y H:i') . ' UTC</span>'
                : '<span style="color:#999;">Pending signature</span>';
            return "<tr>
                <td style='padding:8px 12px; border-bottom:1px solid #e5e5e5;'>{$p->name}</td>
                <td style='padding:8px 12px; border-bottom:1px solid #e5e5e5; color:#555;'>{$p->email}</td>
                <td style='padding:8px 12px; border-bottom:1px solid #e5e5e5; color:#555;'>" . ucfirst($p->role) . "</td>
                <td style='padding:8px 12px; border-bottom:1px solid #e5e5e5; font-size:11px;'>{$signed}</td>
            </tr>";
        })->implode('');

        return <<<HTML
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
  }
  .page {
    padding: 60px 72px;
  }
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
  .contract-body ul, .contract-body ol {
    margin: 8px 0 12px 24px;
  }
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
  table.parties {
    width: 100%;
    border-collapse: collapse;
    font-size: 10pt;
  }
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
</style>
</head>
<body>
<div class="page" style="padding-bottom:60px;">
  <div class="header">
    <div class="header-site">{$siteName}</div>
    <div class="header-title">{$contract->title}</div>
    <div class="header-meta">Generated {$today}</div>
  </div>
  <div class="contract-body">
    {$body}
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
        {$parties}
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
HTML;
    }
}
