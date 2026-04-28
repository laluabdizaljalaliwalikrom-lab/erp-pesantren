<?php

declare(strict_types=1);

namespace App\Helpers;

class NumberHelper
{
    /**
     * Convert number to Indonesian words (Terbilang).
     */
    public static function terbilang(float|int $amount): string
    {
        $amount = (float) abs($amount);
        $words = [
            '', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'
        ];
        
        $result = '';

        if ($amount < 12) {
            $result = $words[(int)$amount];
        } elseif ($amount < 20) {
            $result = self::terbilang($amount - 10) . ' belas';
        } elseif ($amount < 100) {
            $result = self::terbilang((int)($amount / 10)) . ' puluh ' . self::terbilang($amount % 10);
        } elseif ($amount < 200) {
            $result = ' seratus ' . self::terbilang($amount - 100);
        } elseif ($amount < 1000) {
            $result = self::terbilang((int)($amount / 100)) . ' ratus ' . self::terbilang($amount % 100);
        } elseif ($amount < 2000) {
            $result = ' seribu ' . self::terbilang($amount - 1000);
        } elseif ($amount < 1000000) {
            $result = self::terbilang((int)($amount / 1000)) . ' ribu ' . self::terbilang($amount % 1000);
        } elseif ($amount < 1000000000) {
            $result = self::terbilang((int)($amount / 1000000)) . ' juta ' . self::terbilang($amount % 1000000);
        } elseif ($amount < 1000000000000) {
            $result = self::terbilang((int)($amount / 1000000000)) . ' milyar ' . self::terbilang($amount % 1000000000);
        } elseif ($amount < 1000000000000000) {
            $result = self::terbilang((int)($amount / 1000000000000)) . ' trilyun ' . self::terbilang($amount % 1000000000000);
        }

        return trim(preg_replace('/\s+/', ' ', $result));
    }
}
