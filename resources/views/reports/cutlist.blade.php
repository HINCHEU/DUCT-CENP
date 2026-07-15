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
    .sig-section { width: 100%; margin-top: 50px; }
    .sig-box { width: 24%; display: inline-block; text-align: center; font-size: 12px; }
    .sig-line { border-top: 2px solid #1B3F8B; height: 10px; margin-bottom: 12px; margin-left: 10px; margin-right: 10px; }
    .sig-label { font-weight: 600; color: #1B3F8B; letter-spacing: 0.5px; }
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
      @endphp
      <h2>Duct Fabrication List</h2>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Duct Type</th>
            <th style="text-align:center">Thickness</th>
            <th>Dimensions</th>
            <th style="text-align:center">Qty</th>
            <th style="text-align:right">Area/unit (m²)</th>
            <th style="text-align:right">Total (m²)</th>
          </tr>
        </thead>
        <tbody>
          @foreach($ducts as $index => $it)
            <tr>
              <td>{{ $index + 1 }}</td>
              <td>{{ $it->ductType->name }}</td>
              <td style="text-align:center">{{ $it->thickness }} mm</td>
              <td style="font-family:monospace">
                @foreach($it->dimensions as $k => $v)
                    {{ $k }}:{{ $v }}
                @endforeach
              </td>
              <td style="text-align:center">{{ $it->quantity }}</td>
              <td style="text-align:right">{{ number_format($it->surface_area, 2) }}</td>
              <td style="text-align:right;font-weight:700">{{ number_format($it->total_area, 2) }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <td colspan="4">Total Ducts</td>
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
      @endphp
      <h2>Support Materials List</h2>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Support Type</th>
            <th style="text-align:center">Thickness</th>
            <th>Dimensions</th>
            <th style="text-align:center">Qty</th>
            <th style="text-align:right">Length/unit (m)</th>
            <th style="text-align:right">Total (m)</th>
          </tr>
        </thead>
        <tbody>
          @foreach($supports as $index => $it)
            <tr>
              <td>{{ $index + 1 }}</td>
              <td>{{ $it->ductType->name }}</td>
              <td style="text-align:center">{{ $it->thickness }} mm</td>
              <td style="font-family:monospace">
                @foreach($it->dimensions as $k => $v)
                    {{ $k }}:{{ $v }}
                @endforeach
              </td>
              <td style="text-align:center">{{ $it->quantity }}</td>
              <td style="text-align:right">{{ number_format($it->surface_area, 2) }}</td>
              <td style="text-align:right;font-weight:700">{{ number_format($it->total_area, 2) }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <td colspan="4">Total Supports</td>
            <td style="text-align:center;color:#fff">{{ $sQty }} nos</td>
            <td></td>
            <td style="text-align:right;color:#fff;font-size:14px">{{ number_format($sTot, 2) }} m</td>
          </tr>
        </tfoot>
      </table>
    @endif

    <div class="sig-section">
      <div class="sig-box">
        <div class="sig-line"></div>
        <div class="sig-label">Prepared By</div>
        <div style="font-size: 11px; color: #555; margin-top: 4px; line-height: 1.4;">
            {{ optional($order->creator)->name }}<br>
            {{ $order->submitted_at ? $order->submitted_at->format('d/m/Y') : $order->created_at->format('d/m/Y') }}
        </div>
      </div>
      <div class="sig-box">
        <div class="sig-line"></div>
        <div class="sig-label">Checked By</div>
        <div style="font-size: 11px; color: #555; margin-top: 4px; line-height: 1.4;">
            {{ optional($order->approver)->name }}<br>
            {{ $order->approved_at ? $order->approved_at->format('d/m/Y') : '' }}
        </div>
      </div>
      <div class="sig-box">
        <div class="sig-line"></div>
        <div class="sig-label">Transported By</div>
      </div>
      <div class="sig-box">
        <div class="sig-line"></div>
        <div class="sig-label">Received By</div>
      </div>
    </div>
  </div>
</body>
</html>
