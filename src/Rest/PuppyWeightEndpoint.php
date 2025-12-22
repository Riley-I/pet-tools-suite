<?php
declare(strict_types=1);

namespace PetTools\Rest;

use PetTools\Domain\Calculator\PuppyWeightCalculator;
use PetTools\Infrastructure\Cache;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

final class PuppyWeightEndpoint
{
    private PuppyWeightCalculator $calculator;
    private Cache $cache;

    public function __construct(PuppyWeightCalculator $calculator, Cache $cache)
    {
        $this->calculator = $calculator;
        $this->cache = $cache;
    }

    public function register_routes(): void
    {
        register_rest_route('pet-tools/v1', '/puppy-weight', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handle'],
            'permission_callback' => '__return_true', // public endpoint (adjust if needed)
            'args'                => [
                'age_weeks' => [
                    'required'          => true,
                    'type'              => 'integer',
                    'sanitize_callback' => [$this, 'sanitize_int'],
                ],
                'weight_lbs' => [
                    'required'          => true,
                    'type'              => 'number',
                    'sanitize_callback' => [$this, 'sanitize_float'],
                ],
                'size_class' => [
                    'required'          => true,
                    'type'              => 'string',
                    'sanitize_callback' => [$this, 'sanitize_size_class'],
                ],
                'sex' => [
                    'required'          => false,
                    'type'              => 'string',
                    'sanitize_callback' => [$this, 'sanitize_sex'],
                ],
            ],
        ]);
    }

    public function handle(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $ageWeeks   = (int) $request->get_param('age_weeks');
        $weightLbs  = (float) $request->get_param('weight_lbs');
        $sizeClass  = (string) $request->get_param('size_class');
        $sex        = $request->get_param('sex');
        $sex        = is_string($sex) ? $sex : null;

        // Validation (keep strict here; domain layer stays clean and predictable)
        if ($ageWeeks < 1 || $ageWeeks > 104) {
            return new WP_Error('pettools_invalid_age', 'age_weeks must be between 1 and 104.', ['status' => 400]);
        }

        if ($weightLbs <= 0 || $weightLbs > 500) {
            return new WP_Error('pettools_invalid_weight', 'weight_lbs must be greater than 0 and realistic.', ['status' => 400]);
        }

        $allowed = ['toy', 'small', 'medium', 'large', 'giant'];
        if (!in_array($sizeClass, $allowed, true)) {
            return new WP_Error('pettools_invalid_size_class', 'size_class must be one of: toy, small, medium, large, giant.', ['status' => 400]);
        }

        $cacheKey = $this->cacheKey([
            'age_weeks'  => $ageWeeks,
            'weight_lbs' => $weightLbs,
            'size_class' => $sizeClass,
            'sex'        => $sex,
        ]);

        $cached = $this->cache->get($cacheKey);
        if (is_array($cached)) {
            return new WP_REST_Response([
                'cached' => true,
                'data'   => $cached,
            ], 200);
        }

        $result = $this->calculator->predictAdultWeightLbs(
            $weightLbs,
            $ageWeeks,
            $sizeClass,
            $sex
        );

        // Cache 6 hours (tweak later)
        $this->cache->set($cacheKey, $result, 6 * 60 * 60);

        return new WP_REST_Response([
            'cached' => false,
            'data'   => $result,
        ], 200);
    }

    private function cacheKey(array $payload): string
    {
        // Stable key from sanitized inputs
        return 'puppy_weight_' . md5(wp_json_encode($payload) ?: '');
    }

    public function sanitize_int(mixed $value): int
    {
        return (int) $value;
    }

    public function sanitize_float(mixed $value): float
    {
        return (float) $value;
    }

    public function sanitize_size_class(mixed $value): string
    {
        $v = strtolower(trim((string) $value));
        return preg_replace('/[^a-z]/', '', $v) ?? $v;
    }

    public function sanitize_sex(mixed $value): string
    {
        $v = strtolower(trim((string) $value));
        // Keep it flexible for now; clamp to safe chars.
        return preg_replace('/[^a-z]/', '', $v) ?? $v;
    }
}
