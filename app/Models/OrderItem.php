<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'duct_type_id', 'dimensions', 'quantity', 'quantity_delivered', 'surface_area', 'total_area', 'fabrication_status', 'thickness', 'canvas_flange', 'inner_strut', 'remarks'];

    protected function casts(): array
    {
        return [
            'dimensions' => 'json',
            'quantity' => 'integer',
            'quantity_delivered' => 'integer',
            'surface_area' => 'decimal:3',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getFormattedDimensionsAttribute()
    {
        if (!$this->dimensions) return '';
        $f = $this->dimensions;
        $key = optional($this->ductType)->formula_key;

        $v = function($k) use ($f) {
            return $f[strtolower($k)] ?? $f[strtoupper($k)] ?? '';
        };

        switch ($key) {
            case 'rect_straight':
                return "{$v('A')}x{$v('B')}xL{$v('L')}";
            case 'round_straight':
                return "Ø{$v('D')}xL{$v('L')}";
            case 'rect_elbow90':
            case 'rect_elbow45':
                return "{$v('A')}x{$v('B')}xR{$v('R')}";
            case 'round_elbow90':
            case 'round_elbow45':
                return "Ø{$v('D')}xR{$v('R')}";
            case 'duct_reducer':
                return "{$v('A')}x{$v('B')}->{$v('C')}x{$v('D2')}xL{$v('L')}";
            case 'rect_to_round':
                return "{$v('A')}x{$v('B')}->Ø{$v('D')}xL{$v('L')}";
            case 'butterfly_round':
                return "{$v('A')}x{$v('B')}->Ø{$v('D')}xR{$v('R1')}x{$v('L')}<->{$v('E')}x{$v('F')}xR{$v('R2')}";
            case 'butterfly_round_two':
                return "{$v('A')}x{$v('B')}->Ø{$v('D1')}xR{$v('R1')}x{$v('L1')}<->Ø{$v('D2')}xR{$v('R2')}x{$v('L2')}";
            case 'butterfly_rect':
                return "{$v('A')}x{$v('B')}->{$v('C')}x{$v('D2')}xR{$v('R1')}<->{$v('E')}x{$v('F')}xR{$v('R2')}";
            case 'collar_duct':
                return "{$v('A')}x{$v('B')}->{$v('C')}x{$v('D2')}xL{$v('L')}";
            case 'offset_duct':
                return "{$v('A')}x{$v('B')}->{$v('C')}x{$v('D2')}xR{$v('R')}xL{$v('L')}";
            case 'offset_duct_straight':
                return "{$v('A')}x{$v('B')}xR{$v('R')}xL{$v('L')} (L1:{$v('L1')},L2:{$v('L2')})";
            case 'offset_duct_angular':
                return "{$v('A')}x{$v('B')}xR{$v('R')}xL{$v('L')} (Rc:{$v('Rc')},A1:{$v('A1')},A2:{$v('A2')})";
            case 'y_duct':
                return "({$v('A')}x{$v('B')})->({$v('C')}x{$v('D')})x({$v('E')}x{$v('F')}xR{$v('R')})xL{$v('L')}";
            case 'r_type':
                return "{$v('A')}x{$v('B')}->{$v('E')}x{$v('F')}<->{$v('C')}x{$v('D2')}xR{$v('R')}xL{$v('L')}";
            case 'r_type_round_two':
                return "{$v('A')}x{$v('B')}->Ø{$v('D1')}xL{$v('L1')}<->Ø{$v('D2')}xL{$v('L3')}xR{$v('R')}";
            case 'plenum_box':
                return "{$v('A')}x{$v('B')}xH{$v('H2')} Neck:{$v('C')}x{$v('D')}xH{$v('H1')} Conn:Ø{$v('D2')}xH{$v('H3')}";
            case 'plenum_top':
                return "{$v('A')}x{$v('B')}xH{$v('H1')} Conn:Ø{$v('D')}xH{$v('H2')}";
            case 'plenum_tapered':
                return "{$v('A')}x{$v('B')}xH{$v('H1')} Neck:{$v('C')}x{$v('D')}xH{$v('H2')} Conn:{$v('CW')}x{$v('CD')}";
            case 'canvas_round':
                return "Ø{$v('D')}xL{$v('L')} FL:{$v('F')}";
            case 'canvas_rect':
                return "{$v('A')}x{$v('B')}xL{$v('L')} FL:{$v('F')}";
            case 'fan_conn':
                return "{$v('A')}x{$v('B')} TO {$v('C')}x{$v('D2')}xL{$v('L')}";
            case 'wire_mesh':
                return "{$v('A')}x{$v('B')}@{$v('C')} OL:{$v('OL')}";
            case 'transfer_air':
                return "{$v('W1')}x{$v('D1')}";
            case '4ways':
                return "{$v('A1')}x{$v('B1')}->{$v('A4')}x{$v('B4')}<->{$v('A2')}x{$v('B2')}xR{$v('R2')}<->{$v('A3')}x{$v('B3')}xR{$v('R1')}";
            case 'angle_bar':
            case 'angle_bar_u':
                return "{$v('Size')}x{$v('Size')}xL{$v('L')} Hole:Ø{$v('HD')} Dist:{$v('Dist')}";
            default:
                $parts = [];
                foreach ($f as $k => $val) {
                    $parts[] = strtoupper($k) . ':' . $val;
                }
                return implode(' ', $parts);
        }
    }
    public function ductType()
    {
        return $this->belongsTo(DuctType::class);
    }
}
