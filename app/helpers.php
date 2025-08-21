<?php
function cleanText($text) {
    return mb_convert_encoding($text, 'UTF-8', 'UTF-8');
}
if (!function_exists('numberToThaiText')) {
    function numberToThaiText($number) {
        $number = number_format($number, 2, '.', '');
        $parts = explode('.', $number);
        $integer = $parts[0];
        $decimal = $parts[1];

        $thaiNumber = [
            '0' => 'ศูนย์', '1' => 'หนึ่ง', '2' => 'สอง', '3' => 'สาม', '4' => 'สี่',
            '5' => 'ห้า', '6' => 'หก', '7' => 'เจ็ด', '8' => 'แปด', '9' => 'เก้า'
        ];

        $thaiPlace = [
            '', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน'
        ];

        $result = '';
        $len = strlen($integer);

        if ($integer == 0) {
            $result = 'ศูนย์';
        } else {
            for ($i = 0; $i < $len; $i++) {
                $digit = $integer[$i];
                $place = $len - $i - 1;

                if ($digit != '0') {
                    if ($place == 1 && $digit == '1' && $len > 1) {
                        $result .= 'สิบ';
                    } elseif ($place == 1 && $digit == '2' && $len > 1) {
                        $result .= 'ยี่สิบ';
                    } elseif ($place == 0 && $digit == '1' && $len > 1 && $integer[$i-1] != '0') {
                        $result .= 'เอ็ด';
                    } else {
                        $result .= $thaiNumber[$digit];
                        if ($place > 0) {
                            $result .= $thaiPlace[$place];
                        }
                    }
                }
            }
        }

        $result .= 'บาท';

        if ($decimal != '00') {
            if ($decimal[0] != '0') {
                if ($decimal[0] == '1') {
                    $result .= 'สิบ';
                } elseif ($decimal[0] == '2') {
                    $result .= 'ยี่สิบ';
                } else {
                    $result .= $thaiNumber[$decimal[0]] . 'สิบ';
                }
            }

            if ($decimal[1] != '0') {
                if ($decimal[1] == '1' && $decimal[0] != '0') {
                    $result .= 'เอ็ด';
                } else {
                    $result .= $thaiNumber[$decimal[1]];
                }
            }

            $result .= 'สตางค์';
        } else {
            $result .= 'ถ้วน';
        }

        return $result;
    }
}