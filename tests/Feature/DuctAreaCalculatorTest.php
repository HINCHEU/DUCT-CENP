<?php

namespace Tests\Feature;

use App\Services\DuctAreaCalculator;
use Tests\TestCase;

class DuctAreaCalculatorTest extends TestCase
{
    protected DuctAreaCalculator $calculator;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new DuctAreaCalculator();
    }
    
    public function test_calculate_rect_straight()
    {
        // 2 * (A + B)/1000 * L/1000
        $f = ['A' => 500, 'B' => 300, 'L' => 1000];
        $area = $this->calculator->calculate('rect_straight', $f);
        $expected = 2 * ((500 + 300) / 1000) * (1000 / 1000);
        $this->assertEquals($expected, $area);
    }
    
    public function test_calculate_round_straight()
    {
        // pi * D/1000 * L/1000
        $f = ['D' => 500, 'L' => 1000];
        $area = $this->calculator->calculate('round_straight', $f);
        $expected = pi() * (500 / 1000) * (1000 / 1000);
        $this->assertEquals($expected, $area);
    }
    
    public function test_calculate_rect_elbow90()
    {
        $f = ['A' => 500, 'B' => 300, 'R' => 150];
        $area = $this->calculator->calculate('rect_elbow90', $f);
        $Rc = 150 + 500 / 2;
        $expected = 2 * (500 + 300) / 1000 * ((pi() / 2) * $Rc / 1000);
        $this->assertEquals($expected, $area);
    }
}
