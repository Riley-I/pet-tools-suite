<?php
declare(strict_types=1);

namespace PetTools\Domain\Calculator;

final class PuppyWeightCalculator
{
    private GrowthModel $model;

    public function __construct(?GrowthModel $model = null)
    {
        $this->model = $model ?? new GrowthModel();
    }

    /**
     * @param float  $currentWeightLbs
     * @param int    $ageWeeks
     * @param string $sizeClass toy|small|medium|large|giant
     * @param string|null $sex optional future use
     *
     * @return array{
     *   adult_weight_lbs_min: float,
     *   adult_weight_lbs_max: float,
     *   adult_weight_lbs_est: float,
     *   multiplier: float,
     *   age_weeks: int,
     *   weight_lbs: float,
     *   size_class: string
     * }
     */
    public function predictAdultWeightLbs(
        float $currentWeightLbs,
        int $ageWeeks,
        string $sizeClass,
        ?string $sex = null
    ): array {
        // Basic internal sanity (REST boundary will do stricter validation later).
        $currentWeightLbs = max(0.1, $currentWeightLbs);
        $ageWeeks = max(1, $ageWeeks);
        $sizeClass = strtolower(trim($sizeClass));

        $multiplier = $this->model->adultMultiplier($ageWeeks, $sizeClass);
        $estimate = $currentWeightLbs * $multiplier;

        $rangeFactor = $this->model->rangeFactor($sizeClass);
        $min = $estimate * (1.0 - $rangeFactor);
        $max = $estimate * (1.0 + $rangeFactor);

        return [
            'adult_weight_lbs_min' => $this->round1($min),
            'adult_weight_lbs_max' => $this->round1($max),
            'adult_weight_lbs_est' => $this->round1($estimate),
            'multiplier'           => $this->round2($multiplier),
            'age_weeks'            => $ageWeeks,
            'weight_lbs'           => $this->round1($currentWeightLbs),
            'size_class'           => $sizeClass,
        ];
    }

    private function round1(float $n): float
    {
        return round($n, 1);
    }

    private function round2(float $n): float
    {
        return round($n, 2);
    }
}
