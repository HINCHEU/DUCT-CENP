<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\DuctType;

class DuctTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Rectangular Straight', 'formula_key' => 'rect_straight', 'config' => ['fields' => ['A', 'B', 'L']]],
            ['name' => 'Round Straight', 'formula_key' => 'round_straight', 'config' => ['fields' => ['D', 'L']]],
            ['name' => 'Rectangular Elbow 90', 'formula_key' => 'rect_elbow90', 'config' => ['fields' => ['A', 'B', 'R']]],
            ['name' => 'Rectangular Elbow 45', 'formula_key' => 'rect_elbow45', 'config' => ['fields' => ['A', 'B', 'R']]],
            ['name' => 'Round Elbow 90', 'formula_key' => 'round_elbow90', 'config' => ['fields' => ['D', 'R']]],
            ['name' => 'Round Elbow 45', 'formula_key' => 'round_elbow45', 'config' => ['fields' => ['D', 'R']]],
            ['name' => 'Duct Reducer', 'formula_key' => 'duct_reducer', 'config' => ['fields' => ['A', 'B', 'C', 'D2', 'L']]],
            ['name' => 'Rect to Round', 'formula_key' => 'rect_to_round', 'config' => ['fields' => ['A', 'B', 'D', 'L']]],
            ['name' => 'Butterfly Round', 'formula_key' => 'butterfly_round', 'config' => ['fields' => ['A', 'B', 'D', 'L', 'R1', 'E', 'F', 'R2']]],
            ['name' => 'Butterfly Round Two', 'formula_key' => 'butterfly_round_two', 'config' => ['fields' => ['A', 'B', 'D1', 'L1', 'R1', 'D2', 'L2', 'R2']]],
            ['name' => 'Butterfly Rect', 'formula_key' => 'butterfly_rect', 'config' => ['fields' => ['A', 'B', 'C', 'D2', 'R1', 'E', 'F', 'R2']]],
            ['name' => 'Collar Duct', 'formula_key' => 'collar_duct', 'config' => ['fields' => ['A', 'B', 'C', 'D2', 'L']]],
            ['name' => 'Offset Duct', 'formula_key' => 'offset_duct', 'config' => ['fields' => ['A', 'B', 'C', 'D2', 'R', 'L']]],
            ['name' => 'Offset Duct Straight', 'formula_key' => 'offset_duct_straight', 'config' => ['fields' => ['A', 'B', 'R', 'L', 'L1', 'L2']]],
            ['name' => 'Offset Duct Angular', 'formula_key' => 'offset_duct_angular', 'config' => ['fields' => ['A', 'B', 'R', 'L', 'Rc', 'A1', 'A2']]],
            ['name' => 'Y Duct', 'formula_key' => 'y_duct', 'config' => ['fields' => ['A', 'B', 'E', 'F', 'C', 'D', 'R', 'L']]],
            ['name' => 'R Type', 'formula_key' => 'r_type', 'config' => ['fields' => ['A', 'B', 'C', 'D2', 'E', 'F', 'R', 'L']]],
            ['name' => 'R Type Round Two', 'formula_key' => 'r_type_round_two', 'config' => ['fields' => ['A', 'B', 'D1', 'L1', 'L2', 'D2', 'L3', 'R']]],
            ['name' => 'Plenum Box', 'formula_key' => 'plenum_box', 'config' => ['fields' => ['A', 'B', 'H2', 'C', 'D', 'H1', 'D2', 'H3', 'F']]],
            ['name' => 'Plenum Top', 'formula_key' => 'plenum_top', 'config' => ['fields' => ['A', 'B', 'H1', 'D', 'H2', 'F']]],
            ['name' => 'Plenum Tapered', 'formula_key' => 'plenum_tapered', 'config' => ['fields' => ['A', 'B', 'H1', 'C', 'D', 'H2', 'CW', 'CD', 'CH', 'F']]],
            ['name' => 'Canvas Round', 'formula_key' => 'canvas_round', 'config' => ['fields' => ['D', 'L', 'F']]],
            ['name' => 'Canvas Rect', 'formula_key' => 'canvas_rect', 'config' => ['fields' => ['A', 'B', 'L', 'F']]],
            ['name' => 'Fan Connection', 'formula_key' => 'fan_conn', 'config' => ['fields' => ['A', 'B', 'C', 'D2', 'L', 'F1', 'S', 'L1', 'L2', 'Fb', 'Fi']]],
            ['name' => 'Wire Mesh', 'formula_key' => 'wire_mesh', 'config' => ['fields' => ['A', 'B', 'C', 'OL']]],
            ['name' => 'Transfer Air', 'formula_key' => 'transfer_air', 'config' => ['fields' => ['W1', 'D1', 'H1', 'H2', 'W3', 'G', 'W4', 'H4', 'H3', 'W2', 'FL']]],
            ['name' => '4 Ways', 'formula_key' => '4ways', 'config' => ['fields' => ['A1', 'B1', 'A4', 'B4', 'A2', 'B2', 'A3', 'B3', 'R1', 'R2']]],
            ['name' => 'Angle Bar', 'formula_key' => 'angle_bar', 'config' => ['fields' => ['L', 'HD', 'Dist', 'Size']]],
            ['name' => 'Angle Bar U', 'formula_key' => 'angle_bar_u', 'config' => ['fields' => ['L', 'HD', 'Dist', 'Size']]],
        ];

        foreach ($types as $type) {
            DuctType::create($type);
        }
    }
}
