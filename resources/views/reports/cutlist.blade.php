<!DOCTYPE html>
<html>
<head>
  <title>CE&P Duct Fabrication Report</title>
  <style>
    body { font-family: 'Helvetica', 'Arial', sans-serif; margin: 0; color: #0d1a3a; }
    .hdr { background: #0d2050; padding: 16px 28px; width: 100%; border-bottom: 4px solid #D72B2B; }
    .logo { font-weight: 800; font-size: 28px; color: #fff; display: inline-block; vertical-align: middle; }
    .logo-corp { font-size: 14px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #fff; display: inline-block; vertical-align: middle; margin-left: 20px;}
    .content { padding: 24px 28px; }
    h2 { font-size: 22px; font-weight: 700; color: #1B3F8B; margin-bottom: 4px; border-bottom: 1px solid #dde3f0; padding-bottom: 5px;}
    .meta { font-size: 14px; color: #555; margin-bottom: 30px; }
    table { width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 30px; }
    th { background: #1B3F8B; color: #fff; padding: 10px 12px; text-align: left; font-size: 12px; letter-spacing: 0.5px; text-transform: uppercase; }
    td { border-bottom: 1px solid #dde3f0; padding: 9px 12px; }
    tr:nth-child(even) td { background: #f4f6fb; }
    tfoot td { font-weight: 700; background: #1B3F8B; color: #fff; }
    .sig-table { width: 100%; border-collapse: collapse; margin-top: 50px; table-layout: fixed; margin-bottom: 0; }
    .sig-col { border: 1px solid #1B3F8B; width: 25%; vertical-align: top; padding: 0; background-color: #fff !important; }
    .sig-title { text-align: center; padding: 8px 0; font-size: 12px; font-weight: 600; color: #1B3F8B; margin: 0 10px; border-bottom: 1px solid #1B3F8B; letter-spacing: 0.5px; }
    .sig-space { height: 80px; }
    .sig-details { margin: 0 10px; border-top: 1px solid #1B3F8B; padding: 8px 0; font-size: 11px; color: #555; line-height: 1.4; text-align: left; }
  </style>
</head>
<body>
  <div class="hdr">
    <div class="logo">CE&P</div>
    <div class="logo-corp">Corporation<br><span style="font-size:10px;font-style:italic;color:#ccc;">optimize your investment</span></div>
  </div>
  
  <div class="content">
    @php
      $totalQty = $order->items->sum('quantity');
    @endphp
    
    <div style="float:right; text-align:right; font-size:14px; color:#555;">
      <strong>Order No:</strong> {{ $order->order_number }}<br>
      <strong>Site:</strong> {{ $order->site->name }}<br>
      <strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}
    </div>
    
    <div class="meta">
      <strong>Report Generated:</strong> {{ now()->format('M d, Y h:i A') }}<br>
      <strong>Total Items:</strong> {{ $order->items->count() }} type(s) · {{ $totalQty }} nos total
    </div>
    <div style="clear:both;"></div>

    @if($ducts->count() > 0)
      @php
        $dTot = $ducts->sum('total_area');
        $dQty = $ducts->sum('quantity');
        $ductsList = $ducts->values();
      @endphp
      <h2>Duct Fabrication List</h2>
      <table>
        <thead>
          <tr>
            <th style="width:30px">#</th>
            <th>Duct Type</th>
            <th style="text-align:center">Thickness</th>
            <th>Dimensions</th>
            <th>Description</th>
            <th style="text-align:center">Qty</th>
            <th style="text-align:right">Area/unit (m²)</th>
            <th style="text-align:right">Total (m²)</th>
          </tr>
        </thead>
        <tbody>
          @foreach($ductsList as $it)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>{{ $it->ductType->name }}</td>
              <td style="text-align:center">{{ $it->thickness }} mm</td>
              <td style="font-family:monospace">
                {{ $it->formatted_dimensions }}
              </td>
              <td style="font-size:11px; color:#444; font-style:{{ $it->remarks ? 'italic' : 'normal' }}">{{ $it->remarks ?? '—' }}</td>
              <td style="text-align:center">{{ $it->quantity }}</td>
              <td style="text-align:right">{{ number_format($it->surface_area, 2) }}</td>
              <td style="text-align:right;font-weight:700">{{ number_format($it->total_area, 2) }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <td colspan="5">Total Ducts</td>
            <td style="text-align:center;color:#fff">{{ $dQty }} nos</td>
            <td></td>
            <td style="text-align:right;color:#fff;font-size:14px">{{ number_format($dTot, 2) }} m²</td>
          </tr>
        </tfoot>
      </table>
    @endif

    @if($supports->count() > 0)
      @php
        $sTot = $supports->sum('total_area');
        $sQty = $supports->sum('quantity');
        $supportsList = $supports->values();
      @endphp
      <h2>Support Materials List</h2>
      <table>
        <thead>
          <tr>
            <th style="width:30px">#</th>
            <th>Support Type</th>
            <th style="text-align:center">Thickness</th>
            <th>Dimensions</th>
            <th>Description</th>
            <th style="text-align:center">Qty</th>
            <th style="text-align:right">Length/unit (m)</th>
            <th style="text-align:right">Total (m)</th>
          </tr>
        </thead>
        <tbody>
          @foreach($supportsList as $it)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>{{ $it->ductType->name }}</td>
              <td style="text-align:center">{{ $it->thickness }} mm</td>
              <td style="font-family:monospace">
                {{ $it->formatted_dimensions }}
              </td>
              <td style="font-size:11px; color:#444; font-style:{{ $it->remarks ? 'italic' : 'normal' }}">{{ $it->remarks ?? '—' }}</td>
              <td style="text-align:center">{{ $it->quantity }}</td>
              <td style="text-align:right">{{ number_format($it->surface_area, 2) }}</td>
              <td style="text-align:right;font-weight:700">{{ number_format($it->total_area, 2) }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <td colspan="5">Total Supports</td>
            <td style="text-align:center;color:#fff">{{ $sQty }} nos</td>
            <td></td>
            <td style="text-align:right;color:#fff;font-size:14px">{{ number_format($sTot, 2) }} m</td>
          </tr>
        </tfoot>
      </table>
    @endif

    @php
      $cName = optional($order->creator)->name ?? '';
      $cLen = strlen($cName);
      $cSize = $cLen > 18 ? '9px' : ($cLen > 14 ? '10px' : '11px');

      $aName = optional($order->approver)->name ?? '';
      $aLen = strlen($aName);
      $aSize = $aLen > 18 ? '9px' : ($aLen > 14 ? '10px' : '11px');
    @endphp
    <table class="sig-table">
      <tr>
        <td class="sig-col" style="width:33.33%">
          <div class="sig-title">Prepared By</div>
          <div class="sig-space"></div>
          <div class="sig-details">
            Name: <span style="font-size: {{ $cSize }}; white-space: nowrap; letter-spacing: -0.2px;">{{ $cName }}</span><br>
            Date: {{ $order->submitted_at ? $order->submitted_at->format('d/M/Y') : $order->created_at->format('d/M/Y') }}
          </div>
        </td>
        <td class="sig-col" style="width:33.33%">
          <div class="sig-title">approved By</div>
          <div class="sig-space"></div>
          <div class="sig-details">
            Name: <span style="font-size: {{ $aSize }}; white-space: nowrap; letter-spacing: -0.2px;">{{ $aName }}</span><br>
            Date: {{ $order->approved_at ? $order->approved_at->format('d/M/Y') : '' }}
          </div>
        </td>
        <td class="sig-col" style="width:33.34%">
          <div class="sig-title">Confirmed by</div>
          <div class="sig-space"></div>
          <div class="sig-details">
            Name: <br>
            Date:
          </div>
        </td>
      </tr>
    </table>
  </div>
</body>
</html>
