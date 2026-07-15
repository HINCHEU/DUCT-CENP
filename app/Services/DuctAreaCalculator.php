<?php

namespace App\Services;

class DuctAreaCalculator
{
    public function calculate(string $formulaKey, array $f): float
    {
        switch ($formulaKey) {
            case 'rect_straight':
                return 2 * ((float)($f['A'] ?? 0) / 1000 + (float)($f['B'] ?? 0) / 1000) * ((float)($f['L'] ?? 0) / 1000);

            case 'round_straight':
                return pi() * ((float)($f['D'] ?? 0) / 1000) * ((float)($f['L'] ?? 0) / 1000);

            case 'rect_elbow90':
            case 'rect_elbow45':
                $a = (float)($f['A'] ?? 0);
                $b = (float)($f['B'] ?? 0);
                $r = (float)($f['R'] ?? 0);
                $Rc = $r + $a / 2;
                $multiplier = $formulaKey === 'rect_elbow90' ? (pi() / 2) : (pi() / 4);
                return 2 * ($a + $b) / 1000 * ($multiplier * $Rc / 1000);

            case 'round_elbow90':
            case 'round_elbow45':
                $d = (float)($f['D'] ?? 0);
                $r = (float)($f['R'] ?? 0);
                $Rc = $r + $d / 2;
                $multiplier = $formulaKey === 'round_elbow90' ? (pi() / 2) : (pi() / 4);
                return pi() * $d / 1000 * ($multiplier * $Rc / 1000);

            case 'duct_reducer':
                $a = (float)($f['A'] ?? 0);
                $b = (float)($f['B'] ?? 0);
                $l = (float)($f['L'] ?? 0);
                return 2 * ($a + $b) / 1000 * ($l / 1000 * 1.2);

            case 'rect_to_round':
                $a = (float)($f['A'] ?? 0);
                $b = (float)($f['B'] ?? 0);
                $d = (float)($f['D'] ?? 0);
                $l = (float)($f['L'] ?? 0);
                $pRect = 2 * ($a + $b);
                $pRound = pi() * $d;
                return ($pRect * ($l / 2) + (($pRect + $pRound) / 2) * ($l / 2)) / 1000000;

            case 'butterfly_round':
                $a = (float)($f['A'] ?? 0);
                $b = (float)($f['B'] ?? 0);
                $d = (float)($f['D'] ?? 0);
                $l = (float)($f['L'] ?? 0);
                $r1 = (float)($f['R1'] ?? 0);
                $e = (float)($f['E'] ?? 0);
                $ff = (float)($f['F'] ?? 0);
                $r2 = (float)($f['R2'] ?? 0);
                $leftPerim = pi() * ($d / 1000);
                $leftLen = ($l / 1000) + ($r1 / 1000 * 1.2);
                $leftArea = $leftPerim * $leftLen;
                $rightPerim = 2 * ($e + $ff) / 1000;
                $rightLen = pi() / 2 * $r2 / 1000;
                $rightArea = $rightPerim * $rightLen;
                $neck = 2 * ($a + $b) / 1000 * (($r1 + $r2) / 2 / 1000);
                return $leftArea + $rightArea + $neck;

            case 'butterfly_round_two':
                $a = (float)($f['A'] ?? 0);
                $b = (float)($f['B'] ?? 0);
                $d1 = (float)($f['D1'] ?? 0);
                $l1 = (float)($f['L1'] ?? 0);
                $r1 = (float)($f['R1'] ?? 0);
                $d2 = (float)($f['D2'] ?? 0);
                $l2 = (float)($f['L2'] ?? 0);
                $r2 = (float)($f['R2'] ?? 0);
                $leftPerim = pi() * ($d1 / 1000);
                $leftLen = ($l1 / 1000) + ($r1 / 1000 * 1.2);
                $leftArea = $leftPerim * $leftLen;
                $rightPerim = pi() * ($d2 / 1000);
                $rightLen = ($l2 / 1000) + ($r2 / 1000 * 1.2);
                $rightArea = $rightPerim * $rightLen;
                $neck = 2 * ($a + $b) / 1000 * (($r1 + $r2) / 2 / 1000);
                return $leftArea + $rightArea + $neck;

            case 'butterfly_rect':
                $a = (float)($f['A'] ?? 0);
                $b = (float)($f['B'] ?? 0);
                $c = (float)($f['C'] ?? 0);
                $d2 = (float)($f['D2'] ?? 0);
                $r1 = (float)($f['R1'] ?? 0);
                $e = (float)($f['E'] ?? 0);
                $ff = (float)($f['F'] ?? 0);
                $r2 = (float)($f['R2'] ?? 0);
                return 2 * ($c + $d2) / 1000 * (pi() / 2 * $r1 / 1000) + 2 * ($e + $ff) / 1000 * (pi() / 2 * $r2 / 1000) + 2 * ($a + $b) / 1000 * (($r1 + $r2) / 2 / 1000);

            case 'collar_duct':
                $a = (float)($f['A'] ?? 0);
                $c = (float)($f['C'] ?? 0);
                $l = (float)($f['L'] ?? 0);
                return 2 * ($a + $c) / 1000 * ($l / 1000 * 1.2);

            case 'offset_duct':
                $a = (float)($f['A'] ?? 0);
                $b = (float)($f['B'] ?? 0);
                $c = (float)($f['C'] ?? 0);
                $d2 = (float)($f['D2'] ?? 0);
                $r = (float)($f['R'] ?? 0);
                $l = (float)($f['L'] ?? 0);
                $avgP = (2 * ($a + $b) + 2 * ($c + $d2)) / 2;
                return $avgP * hypot($l, $r) / 1000000;

            case 'offset_duct_straight':
                $a = (float)($f['A'] ?? 0);
                $b = (float)($f['B'] ?? 0);
                $l = (float)($f['L'] ?? 0);
                $r = (float)($f['R'] ?? 0);
                $l1 = (float)($f['L1'] ?? 0);
                $l2 = (float)($f['L2'] ?? 0);
                $middleL = $l - $l1 - $l2;
                $p = 2 * ($a + $b);
                return ($p * $l1 + $p * $l2 + 2 * $b * $middleL + 2 * $a * hypot($middleL, $r)) / 1000000;

            case 'offset_duct_angular':
                $a = isset($f['A']) && $f['A'] !== '' ? (float)$f['A'] : 750;
                $b = isset($f['B']) && $f['B'] !== '' ? (float)$f['B'] : 300;
                $l = isset($f['L']) && $f['L'] !== '' ? (float)$f['L'] : 930;
                $H = isset($f['R']) && $f['R'] !== '' ? (float)$f['R'] : 620;
                $a1Input = isset($f['A1']) && $f['A1'] !== '' ? (float)$f['A1'] : 30;
                $a2Input = isset($f['A2']) && $f['A2'] !== '' ? (float)$f['A2'] : 30;
                $ang1 = min(60, max(-60, $a1Input)) * pi() / 180;
                $ang2 = min(60, max(-60, $a2Input)) * pi() / 180;
                $Rc = isset($f['Rc']) && $f['Rc'] !== '' ? (float)$f['Rc'] : 150;
                
                $b_half = $b / 2;
                $tan1 = abs(tan($ang1));
                $tan2 = abs(tan($ang2));
                $s1 = $b_half * $tan1 + 20;
                $s2 = $b_half * $tan2 + 20;
                $dx = $l - 2 * $b_half * ($tan1 + $tan2) - 40;
                if ($dx < 10) $dx = 10;
                $r = $Rc + $b_half;
                $max_r = ($dx * $dx + $H * $H) / (4 * $H);
                if ($H > 0 && $r > $max_r) { $r = $max_r * 0.99; }
                $dy = $H - 2 * $r;
                $D = hypot($dx, $dy);
                $alpha = atan2($dy, $dx) + asin(2 * $r / $D);
                $tangentL = sqrt(max(0, $D * $D - 4 * $r * $r));
                $centerL = $s1 + $s2 + 2 * $alpha * $r + $tangentL;
                $p = 2 * ($a + $b);
                return ($p * $centerL) / 1000000;

            case 'y_duct':
                $a = (float)($f['A'] ?? 0);
                $b = (float)($f['B'] ?? 0);
                $e = (float)($f['E'] ?? 0);
                $ff = (float)($f['F'] ?? 0);
                $c = (float)($f['C'] ?? 0);
                $d = (float)($f['D'] ?? 0);
                $l = (float)($f['L'] ?? 0);
                $r = (float)($f['R'] ?? 0);
                $mainPerim = 2 * ($a + $b) / 1000;
                $branchPerim = 2 * ($e + $ff) / 1000;
                $sidePerim = 2 * ($c + $d) / 1000;
                return $mainPerim * ($l / 1000) + ($branchPerim + $sidePerim) * (pi() / 2 * $r / 1000);

            case 'r_type':
                $a = (float)($f['A'] ?? 0);
                $b = (float)($f['B'] ?? 0);
                $c = (float)($f['C'] ?? 0);
                $d = (float)($f['D2'] ?? 0);
                $e = (float)($f['E'] ?? 0);
                $ff = (float)($f['F'] ?? 0);
                $l = (float)($f['L'] ?? 0);
                $r = (float)($f['R'] ?? 0);
                return 2 * ($a + $b) / 1000 * ($l / 1000 * 0.5) + 2 * ($c + $d) / 1000 * (pi() / 2 * $r / 1000) + 2 * ($e + $ff) / 1000 * ($l / 1000 * 0.5);

            case 'r_type_round_two':
                $a = (float)($f['A'] ?? 0);
                $b = (float)($f['B'] ?? 0);
                $d1 = (float)($f['D1'] ?? 0);
                $l1 = (float)($f['L1'] ?? 0);
                $l2 = (float)($f['L2'] ?? 0);
                $d2 = (float)($f['D2'] ?? 0);
                $l3 = (float)($f['L3'] ?? 0);
                $r = (float)($f['R'] ?? 0);
                $topPerim = pi() * ($d1 / 1000);
                $topLen = ($l1 / 1000) + ($l2 / 1000) + ($r / 1000 * 1.2);
                $topArea = $topPerim * $topLen;
                $sidePerim = pi() * ($d2 / 1000);
                $sideLen = ($l3 / 1000) + ($r / 1000 * 1.2);
                $sideArea = $sidePerim * $sideLen;
                $neck = 2 * ($a + $b) / 1000 * ($r / 1000 * 1.2);
                return $topArea + $sideArea + $neck;

            case 'plenum_box':
                $a = (float)($f['A'] ?? 0);
                $b = (float)($f['B'] ?? 0);
                $h1 = (float)($f['H1'] ?? 0);
                $c = (float)($f['C'] ?? 0);
                $d = (float)($f['D'] ?? 0);
                $h2 = (float)($f['H2'] ?? 0);
                $d2 = (float)($f['D2'] ?? 0);
                $h3 = (float)($f['H3'] ?? 0);
                $fl = (float)($f['F'] ?? 0);
                $bodyTotal = 2 * ($a * $h2 + $a * $b + $b * $h2) - pi() * pow($d2 / 2, 2) - $c * $d;
                $neckTotal = 2 * ($c + $d) * $h1;
                $connTotal = pi() * $d2 * $h3;
                $flangeTotal = $fl > 0 ? 2 * ($c * $fl + $d * $fl - 2 * $fl * $fl) : 0;
                return ($bodyTotal + $neckTotal + $connTotal + $flangeTotal) / 1000000;

            case 'plenum_top':
                $a = (float)($f['A'] ?? 0);
                $b = (float)($f['B'] ?? 0);
                $h1 = (float)($f['H1'] ?? 0);
                $d = (float)($f['D'] ?? 0);
                $h2 = (float)($f['H2'] ?? 0);
                $fl = isset($f['F']) && $f['F'] !== '' ? (float)$f['F'] : 20;
                $bodyTotal = ($a * $b) + 2 * ($a * $h1) + 2 * ($b * $h1) - pi() * pow($d / 2, 2);
                $flangeTotal = $fl > 0 ? 2 * ($a * $fl + $b * $fl + 2 * $fl * $fl) : 0;
                return ($bodyTotal + pi() * $d * $h2 + $flangeTotal) / 1000000;

            case 'plenum_tapered':
                $a = (float)($f['A'] ?? 0);
                $b = (float)($f['B'] ?? 0);
                $h1 = (float)($f['H1'] ?? 0);
                $c = isset($f['C']) && $f['C'] !== '' ? (float)$f['C'] : $a;
                $d = (float)($f['D'] ?? 0);
                $h2 = (float)($f['H2'] ?? 0);
                $cw = (float)($f['CW'] ?? 0);
                $cd = (float)($f['CD'] ?? 0);
                $ch = (float)($f['CH'] ?? 0);
                $fl = (float)($f['F'] ?? 0);
                
                $ovalArea = max(0, $cw - $cd) * $cd + pi() * pow($cd / 2, 2);
                $ovalPerim = 2 * max(0, $cw - $cd) + pi() * $cd;
                
                $bodyTotal = 2 * ($a * $h1 + $b * $h1) + ($a * $b);
                $neckTotal = 2 * ($c * $h2 + $d * $h2);
                $botFace = ($a * $b) - ($c * $d);
                $flangeTotal = $fl > 0 ? 2 * ($c * $fl + $d * $fl - 2 * $fl * $fl) : 0;
                
                $connectorArea = $ovalPerim * $ch;
                return ($bodyTotal + $botFace + $neckTotal - $ovalArea + $connectorArea + $flangeTotal) / 1000000;

            case 'canvas_round':
                $d = (float)($f['D'] ?? 0);
                $l = (float)($f['L'] ?? 0);
                return pi() * ($d / 1000) * ($l / 1000);

            case 'canvas_rect':
                $a = (float)($f['A'] ?? 0);
                $b = (float)($f['B'] ?? 0);
                $l = (float)($f['L'] ?? 0);
                return 2 * ($a + $b) / 1000 * ($l / 1000);

            case 'fan_conn':
                $a = (float)($f['A'] ?? 0);
                $b = (float)($f['B'] ?? 0);
                $c = (float)($f['C'] ?? 0);
                $d = (float)($f['D2'] ?? 0);
                $l = (float)($f['L'] ?? 0);
                $f1 = (float)($f['F1'] ?? 0);
                $s = (float)($f['S'] ?? 0);
                $l1 = (float)($f['L1'] ?? 0);
                $l2 = (float)($f['L2'] ?? 0);
                $fb = (float)($f['Fb'] ?? 0);
                $fi = (float)($f['Fi'] ?? 0);
                
                if (!$a || !$b || !$c || !$d || !$l || !$l2) return 0;
                
                $slantW = sqrt($l2 * $l2 + pow(($a - $c) / 2, 2));
                $slantH = sqrt($l2 * $l2 + $s * $s);
                $topBot = ($a + $c) / 2 * $slantW / 1000000 * 2;
                $sides = ($b + $d) / 2 * $slantH / 1000000 * 2;
                $inletSect = 2 * ($a + $b) / 1000 * ($l1 / 1000);
                $outFlange = 2 * ($c + $d) / 1000 * ($fb / 1000);
                $stripF1 = ($c * 2 + $d * 2) * $f1 / 1000000;
                return $topBot + $sides + $inletSect + $outFlange + $stripF1;

            case 'wire_mesh':
                $a = (float)($f['A'] ?? 0);
                $b = (float)($f['B'] ?? 0);
                return $a * $b / 1000000;

            case 'transfer_air':
                $w1 = isset($f['W1']) && $f['W1'] !== '' ? (float)$f['W1'] : 900;
                $d1 = isset($f['D1']) && $f['D1'] !== '' ? (float)$f['D1'] : 500;
                $w2 = isset($f['W2']) && $f['W2'] !== '' ? (float)$f['W2'] : 900;
                $h1 = isset($f['H1']) && $f['H1'] !== '' ? (float)$f['H1'] : 350;
                $h2 = isset($f['H2']) && $f['H2'] !== '' ? (float)$f['H2'] : 925;
                $w3 = isset($f['W3']) && $f['W3'] !== '' ? (float)$f['W3'] : 925;
                $g  = isset($f['G']) && $f['G'] !== '' ? (float)$f['G'] : 450;
                $h3 = isset($f['H3']) && $f['H3'] !== '' ? (float)$f['H3'] : 350;
                $h4 = isset($f['H4']) && $f['H4'] !== '' ? (float)$f['H4'] : 925;
                $w4 = isset($f['W4']) && $f['W4'] !== '' ? (float)$f['W4'] : 925;
                $fl = isset($f['FL']) && $f['FL'] !== '' ? (float)$f['FL'] : 50;
                
                $connH = max($h2, $h4);
                
                $rightLegArea = 2 * ($w3 + $d1) * $h2 / 1000000;
                $rightCollarArea = (2 * $d1 * $h1 + 2 * (($w3 + $w1) / 2) * $h1) / 1000000;
                $rightFlangeArea = (2 * ($w1 + 2 * $fl) * $fl + 2 * $d1 * $fl) / 1000000;
                
                $connArea = (2 * $g * $connH + 2 * $g * $d1) / 1000000;
                
                $leftLegArea = 2 * ($w4 + $d1) * $h4 / 1000000;
                $leftCollarArea = (2 * $d1 * $h3 + 2 * (($w4 + $w2) / 2) * $h3) / 1000000;
                $leftFlangeArea = (2 * ($w2 + 2 * $fl) * $fl + 2 * $d1 * $fl) / 1000000;
                
                $rightHole = $w1 * $d1 / 1000000;
                $leftHole = $w2 * $d1 / 1000000;
                
                return $rightLegArea + $rightCollarArea + $rightFlangeArea
                    + $connArea
                    + $leftLegArea + $leftCollarArea + $leftFlangeArea
                    - $rightHole - $leftHole;

            case '4ways':
                $a1 = (float)($f['A1'] ?? 0);
                $b1 = (float)($f['B1'] ?? 0);
                $a4 = (float)($f['A4'] ?? 0);
                $b4 = (float)($f['B4'] ?? 0);
                $a2 = (float)($f['A2'] ?? 0);
                $b2 = (float)($f['B2'] ?? 0);
                $a3 = (float)($f['A3'] ?? 0);
                $b3 = (float)($f['B3'] ?? 0);
                $r1 = (float)($f['R1'] ?? 0);
                $r2 = (float)($f['R2'] ?? 0);
                
                $rAvg = ($r1 + $r2) / 2;
                $leftBranch = 2 * ($a3 + $b3) / 1000 * (pi() / 2 * $r1 / 1000);
                $rightBranch = 2 * ($a2 + $b2) / 1000 * (pi() / 2 * $r2 / 1000);
                $topBranch = 2 * ($a4 + $b4) / 1000 * (pi() / 2 * $rAvg / 1000);
                $bottomBranch = 2 * ($a1 + $b1) / 1000 * (pi() / 2 * $rAvg / 1000);
                $avgPerim = (($a1 + $b1) + ($a4 + $b4)) / 2;
                $centre = 2 * $avgPerim / 1000 * ($rAvg / 1000);
                return $leftBranch + $rightBranch + $topBranch + $bottomBranch + $centre;

            case 'angle_bar':
            case 'angle_bar_u':
                return ((float)($f['L'] ?? 0)) / 1000;

            default:
                return 0.0;
        }
    }
}
