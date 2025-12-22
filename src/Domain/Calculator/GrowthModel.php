<?php
declare(strict_types=1);

namespace PetTools\Domain\Calculator;

/**
 * GrowthModel defines the adjustable assumptions used by the calculator.
 * Keep this WordPress-agnostic and easy to extend (breed presets later).
 */
final class GrowthModel
{
    /**
     * Returns a multiplier that maps current weight at a given age to an estimated adult weight.
     * This is intentionally a simple placeholder model for v0.1.0.
     *
     * @param int $ageWeeks
     * @param string $sizeClass one of: toy|small|medium|large|giant
     */
    public function adultMultiplier(int $ageWeeks, string $sizeClass): float
    {
        // Guardrails: keep in a reasonable range for early development.
        $ageWeeks = max(8, min($ageWeeks, 52));

        // Placeholder multipliers by size class at ~12 weeks baseline.
        // Larger breeds tend to have a longer growth window.
        $baseBySize = [
            'toy'   => 3.2,
            'small' => 3.6,
            'medium'=> 4.0,
            'large' => 4.6,
            'giant' => 5.2,
        ];

        $base = $baseBySize[$sizeClass] ?? $baseBySize['medium'];

        // Age adjustment: older puppies have less remaining growth, so multiplier decreases over time.
        // Simple linear decay from week 8 to week 52.
        $t = ($ageWeeks - 8) / (52 - 8); // 0..1
        $decay = 1.0 - (0.55 * $t);      // ends at 0.45 of base

        return max(1.2, $base * $decay);
    }

    /**
     * Simple range factor to produce min/max predictions.
     */
    public function rangeFactor(string $sizeClass): float
    {
        $factorBySize = [
            'toy'   => 0.12,
            'small' => 0.14,
            'medium'=> 0.16,
            'large' => 0.18,
            'giant' => 0.20,
        ];

        return $factorBySize[$sizeClass] ?? 0.16;
    }
}
