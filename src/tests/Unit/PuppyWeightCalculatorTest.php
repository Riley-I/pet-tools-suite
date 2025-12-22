<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PetTools\Domain\Calculator\PuppyWeightCalculator;

final class PuppyWeightCalculatorTest extends TestCase
{
    public function testPredictAdultWeightReturnsExpectedKeys(): void
    {
        $calc = new PuppyWeightCalculator();
        $result = $calc->predictAdultWeightLbs(10.0, 12, 'medium');

        $this->assertArrayHasKey('adult_weight_lbs_min', $result);
        $this->assertArrayHasKey('adult_weight_lbs_max', $result);
        $this->assertArrayHasKey('adult_weight_lbs_est', $result);
        $this->assertArrayHasKey('multiplier', $result);
        $this->assertSame(12, $result['age_weeks']);
        $this->assertSame('medium', $result['size_class']);
    }

    public function testPredictionIsReasonableRange(): void
    {
        $calc = new PuppyWeightCalculator();
        $result = $calc->predictAdultWeightLbs(8.0, 12, 'small');

        $this->assertGreaterThan(8.0, $result['adult_weight_lbs_est']); // adult should be > current
        $this->assertLessThan($result['adult_weight_lbs_max'], $result['adult_weight_lbs_est']);
        $this->assertGreaterThan($result['adult_weight_lbs_min'], $result['adult_weight_lbs_est']);
    }

    public function testWeightIsClampedToPositive(): void
    {
        $calc = new PuppyWeightCalculator();
        $result = $calc->predictAdultWeightLbs(-5.0, 12, 'medium');

        $this->assertGreaterThan(0.0, $result['weight_lbs']);
        $this->assertGreaterThan(0.0, $result['adult_weight_lbs_est']);
    }
}
