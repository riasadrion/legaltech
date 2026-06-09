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
    /* Dompdf 3.x honors body margin per physical page (it ignores @page top margin
       on continuation pages); footer is drawn inside the bottom margin via page_script. */
    margin: 60px 72px 64px 72px;
  }
  .page {
    padding: 0;
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
  table.parties td {
    padding: 8px 12px;
    border-bottom: 1px solid #e5e5e5;
  }
  table.parties td.col-secondary {
    color: #555;
  }
  table.parties td.col-status {
    font-size: 11px;
  }
  .signature-status-signed {
    color: #2d7a22;
  }
  .signature-status-pending {
    color: #999;
  }
</style>
</head>
<body>
<div class="page">
  <div class="header">
    <div class="header-site">{{ $siteName }}</div>
    <div class="header-title">{{ $contract->title }}</div>
    <div class="header-meta">Generated {{ $today }}</div>
  </div>
  <div class="contract-body">
    <?php echo $body; ?>
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
        @foreach($contract->parties as $p)
        <tr>
            <td>{{ $p->name }}</td>
            <td class="col-secondary">{{ $p->email }}</td>
            <td class="col-secondary">{{ ucfirst($p->role) }}</td>
            <td class="col-status">
                @if($p->signed_at)
                    <span class="signature-status-signed">&#x2713; Signed {{ $p->signed_at->format('M j, Y H:i') }} UTC</span>
                @else
                    <span class="signature-status-pending">Pending signature</span>
                @endif
            </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
