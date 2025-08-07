<?php

namespace App\Helpers;

class InspectionCalculator
{
    public static function vr1($mmq_1l, $mmq_5l): ?bool
    {
        if (is_null($mmq_1l) && is_null($mmq_5l)) return null;
        if (abs($mmq_1l) > 12 || abs($mmq_5l) > 12) return false;
        return true;
    }

    public static function vr2($mpe_5l, $repeat1): ?bool
    {
        if (!is_null($mpe_5l) && !is_null($repeat1)) {
            return (abs($mpe_5l) > 15 || abs($repeat1) > 60) ? false : true;
        } elseif (!is_null($mpe_5l)) {
            return abs($mpe_5l) > 15 ? false : true;
        } elseif (!is_null($repeat1)) {
            return abs($repeat1) > 60 ? false : true;
        }
        return null;
    }

    public static function vr3($r14, $r, $s, $t): ?bool
    {
        if (is_null($r14)) return null;

        $threshold = 0.0015 * $r14 * 1000;
        $values = array_filter([$r, $s, $t], fn($v) => is_numeric($v));

        if (count($values) === 0) return null;

        foreach ($values as $v) {
            if ($v <= $threshold) return true;
        }
        return false;
    }

    public static function vr4($r14, $r, $s, $t): ?bool
    {
        if (is_null($r14)) return null;

        $threshold = 0.03 * $r14 * 1000;
        $values = array_filter([$r, $s, $t], fn($v) => is_numeric($v));

        if (count($values) === 0) return null;

        $max = max($values);
        $min = min($values);
        return ($max - $min) <= $threshold;
    }

    public static function vr5($r14, $r, $s, $t): ?bool
    {
        if (is_null($r14)) return null;

        $values = array_filter([$r, $s, $t], fn($v) => is_numeric($v));
        if (count($values) < 1) return null;

        $diff = max($values) - min($values);

        return match ($r14) {
            5 => $diff <= 6,
            10 => $diff <= 12,
            20 => $diff <= 24,
            default => false
        };
    }

    public static function sns(array $vrs): array
    {
        if (in_array(null, $vrs, true)) {
            return ['SNS_True' => null, 'SNS_False' => null];
        }

        $anyFail = in_array(false, $vrs, true);
        $sns_true = $anyFail ? 0 : 1;
        $sns_false = $sns_true == 1 ? 0 : 1;

        return ['SNS_True' => $sns_true, 'SNS_False' => $sns_false];
    }
}
