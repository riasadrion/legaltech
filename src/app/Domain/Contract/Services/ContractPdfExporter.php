<?php

namespace App\Domain\Contract\Services;

use App\Domain\Contract\Models\Contract;
use App\Facades\Settings;
use Dompdf\Dompdf;
use Dompdf\Options;

class ContractPdfExporter
{
    public function stream(Contract $contract): string
    {
        $html = $this->buildHtml($contract);

        $options = new Options;
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
            $font = $fontMetrics->getFont('DejaVu Serif', 'normal');
            $size = 7.5;
            $color = [0.7, 0.7, 0.7];
            $w = $canvas->get_width();
            $h = $canvas->get_height();
            $canvas->line(54, $h - 32, $w - 54, $h - 32, $color, 0.5);
            $canvas->text(54, $h - 24, config('app.name').' — '.e($contract->title), $font, $size, $color);
            $canvas->text($w - 90, $h - 24, "Page {$pageNumber} of {$pageCount}", $font, $size, $color);
        });

        return $dompdf->output();
    }

    private function buildHtml(Contract $contract): string
    {
        $body = $contract->body_html ?? '';

        // Strip a leading <h1> that just repeats the contract title (header already shows it).
        $body = preg_replace('/^\s*<h1[^>]*>.*?<\/h1>\s*/is', '', $body, 1);

        $siteName = config('app.name');
        $today = now()->format('F j, Y');

        $layout = Settings::string('contracts.pdf_layout');

        if ($layout !== '') {
            return strtr($layout, [
                '{{site_name}}' => e($siteName),
                '{{contract_title}}' => e($contract->title),
                '{{generated_date}}' => e($today),
                '{{contract_body}}' => $body,
                '{{parties_table}}' => $this->renderPartiesRows($contract),
            ]);
        }

        return view('emails.contracts.pdf', [
            'contract' => $contract,
            'siteName' => $siteName,
            'today' => $today,
            'body' => $body,
        ])->render();
    }

    private function renderPartiesRows(Contract $contract): string
    {
        return $contract->parties->map(function ($p) {
            $signed = $p->signed_at
                ? '<span class="signature-status-signed">&#x2713; Signed '.e($p->signed_at->format('M j, Y H:i')).' UTC</span>'
                : '<span class="signature-status-pending">Pending signature</span>';

            return '<tr>'
                .'<td>'.e($p->name).'</td>'
                .'<td class="col-secondary">'.e($p->email).'</td>'
                .'<td class="col-secondary">'.e(ucfirst($p->role)).'</td>'
                .'<td class="col-status">'.$signed.'</td>'
                .'</tr>';
        })->implode('');
    }
}
