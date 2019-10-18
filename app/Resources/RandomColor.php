<?php
/**
 * RandomColor 1.0.4
 *
 * PHP port of David Merfield JavaScript randomColor
 * https://github.com/davidmerfield/randomColor
 *
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 Damien "Mistic" Sorel
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace App\Resources;

class RandomColor
{
    static public $dictionary = null;

    static public $h;
    static public $s;
    static public $v;

    private function __construct()
    {
    }

    static public function one($options = [])
    {
        $h = self::$h = self::_pickHue($options);
        $s = self::$s = self::_pickSaturation($h, $options);
        $v = self::$v = self::_pickBrightness($h, $s, $options);
        $options['opacity'] = !isset($options['opacity']) ? null : $options['opacity'];

        return self::format(compact('h', 's', 'v'), @$options['format'], $options['opacity']);
    }

    static public function many($count, $options = [])
    {
        $colors = [];

        for ($i = 0; $i < $count; $i++) {
            $colors[] = self::one($options);
        }

        return $colors;
    }

    static public function format($hsv, $format = 'hex', $opacity = '')
    {
        switch ($format) {
            case 'hsv':
                $text = $hsv;
                break;

            case 'hsl':
                $text = self::hsv2hsl($hsv);
                break;

            case 'hslCss':
                $hsl = self::hsv2hsl($hsv);

                $text = 'hsl('.$hsl['h'].','.$hsl['s'].'%,'.$hsl['l'].'%)';
                break;

            case 'rgb':
                $text = self::hsv2rgb($hsv);
                break;

            case 'rgbCss':
                $text = 'rgb('.implode(',', self::hsv2rgb($hsv)).')';
                break;

            case 'rgbaCss':
                $text = 'rgba('.implode(',', self::hsv2rgb($hsv)).')';
                break;

            case 'hex':
            default:
                $text = self::hsv2hex($hsv);
                break;
        }

        if (!empty($opacity)) {
            $text = substr($text, 0, -1);
            $text .= ', '.$opacity.')';
        }

        return $text;
    }

    static private function _pickHue($options)
    {
        $range = self::_getHueRange($options);

        if (empty($range)) {
            return 0;
        }

        $hue = self::_rand($range, $options);

        // Instead of storing red as two separate ranges,
        // we group them, using negative numbers
        if ($hue < 0) {
            $hue = 360 + $hue;
        }

        return $hue;
    }

    static private function _pickSaturation($h, $options)
    {
        if (@$options['hue'] === 'monochrome') {
            return 0;
        }
        if (@$options['luminosity'] === 'random') {
            return self::_rand(array(0, 100), $options);
        }

        $colorInfo = self::_getColorInfo($h);
        $range = $colorInfo['s'];

        switch (@$options['luminosity']) {
            case 'bright':
                $range[0] = 55;
                break;

            case 'dark':
                $range[0] = $range[1] - 10;
                break;

            case 'light':
                $range[1] = 55;
                break;
        }

        return self::_rand($range, $options);
    }

    static private function _pickBrightness($h, $s, $options)
    {
        if (@$options['luminosity'] === 'random') {
            $range = array(0, 100);
        } else {
            $range = array(
                self::_getMinimumBrightness($h, $s),
                100,
            );

            switch (@$options['luminosity']) {
                case 'dark':
                    $range[1] = $range[0] + 20;
                    break;

                case 'light':
                    $range[0] = ($range[1] + $range[0]) / 2;
                    break;
            }
        }

        return self::_rand($range, $options);
    }

    static private function _getHueRange($options)
    {
        $ranges = [];

        if (isset($options['hue'])) {
            if (!is_array($options['hue'])) {
                $options['hue'] = array($options['hue']);
            }

            foreach ($options['hue'] as $hue) {
                if ($hue === 'random') {
                    $ranges[] = array(0, 360);
                } else {
                    if (isset(self::$dictionary[$hue])) {
                        $ranges[] = self::$dictionary[$hue]['h'];
                    } else {
                        if (is_numeric($hue)) {
                            $hue = intval($hue);

                            if ($hue <= 360 && $hue >= 0) {
                                $ranges[] = array($hue, $hue);
                            }
                        }
                    }
                }
            }
        }

        if (($l = count($ranges)) === 0) {
            return array(0, 360);
        } else {
            if ($l === 1) {
                return $ranges[0];
            } else {
                return $ranges[self::_rand(array(0, $l - 1), $options)];
            }
        }
    }

    static private function _getMinimumBrightness($h, $s)
    {
        $colorInfo = self::_getColorInfo($h);
        $bounds = $colorInfo['bounds'];

        for ($i = 0, $l = count($bounds); $i < $l - 1; $i++) {
            $s1 = $bounds[$i][0];
            $v1 = $bounds[$i][1];
            $s2 = $bounds[$i + 1][0];
            $v2 = $bounds[$i + 1][1];

            if ($s >= $s1 && $s <= $s2) {
                $m = ($v2 - $v1) / ($s2 - $s1);
                $b = $v1 - $m * $s1;

                return $m * $s + $b;
            }
        }

        return 0;
    }

    static private function _getColorInfo($h)
    {
        // Maps red colors to make picking hue easier
        if ($h >= 334 && $h <= 360) {
            $h -= 360;
        }

        foreach (self::$dictionary as $color) {
            if ($color['h'] !== null && $h >= $color['h'][0] && $h <= $color['h'][1]) {
                return $color;
            }
        }
    }

    static private function _rand($bounds, $options)
    {
        if (isset($options['prng'])) {
            return $options['prng']($bounds[0], $bounds[1]);
        } else {
            return mt_rand($bounds[0], $bounds[1]);
        }
    }

    static public function hsv2hex($hsv)
    {
        $rgb = self::hsv2rgb($hsv);
        $hex = '#';

        foreach ($rgb as $c) {
            $hex .= str_pad(dechex($c), 2, '0', STR_PAD_LEFT);
        }

        return $hex;
    }

    static public function hsv2hsl($hsv)
    {
        extract($hsv);

        $s = self::$s;
        $v = self::$v;
        $h = self::$h;

        $s /= 100;
        $v /= 100;
        $k = (2 - $s) * $v;

        return [
            'h' => $h,
            's' => round($s * $v / ($k < 1 ? $k : 2 - $k), 4) * 100,
            'l' => $k / 2 * 100,
        ];
    }

    static public function hsv2rgb($hsv)
    {
        extract($hsv);

        $s = self::$s;
        $v = self::$v;
        $h = self::$h;

        $h /= 360;
        $s /= 100;
        $v /= 100;

        $i = floor($h * 6);
        $f = $h * 6 - $i;

        $m = $v * (1 - $s);
        $n = $v * (1 - $s * $f);
        $k = $v * (1 - $s * (1 - $f));

        $r = 1;
        $g = 1;
        $b = 1;

        switch ($i) {
            case 0:
                list($r, $g, $b) = [$v, $k, $m];
                break;
            case 1:
                list($r, $g, $b) = [$n, $v, $m];
                break;
            case 2:
                list($r, $g, $b) = [$m, $v, $k];
                break;
            case 3:
                list($r, $g, $b) = [$m, $n, $v];
                break;
            case 4:
                list($r, $g, $b) = [$k, $m, $v];
                break;
            case 5:
            case 6:
                list($r, $g, $b) = [$v, $m, $n];
                break;
        }

        return [
            'r' => floor($r * 255),
            'g' => floor($g * 255),
            'b' => floor($b * 255),
        ];
    }
}

/*
 * h=hueRange
 * s=saturationRange : bounds[0][0] ; bounds[-][0]
 */
RandomColor::$dictionary = [
    'monochrome' => [
        'bounds' => [[0, 0], [100, 0]],
        'h'      => null,
        's'      => [0, 100],
    ],
    'red'        => [
        'bounds' => [
            [20, 100], [30, 92], [40, 89], [50, 85], [60, 78], [70, 70], [80, 60],
            [90, 55], [100, 50],
        ],
        'h'      => [-26, 18],
        's'      => [20, 100],
    ],
    'orange'     => [
        'bounds' => [
            [20, 100], [30, 93], [40, 88], [50, 86], [60, 85], [70, 70], [100, 70],
        ],
        'h'      => [19, 46],
        's'      => [20, 100],
    ],
    'yellow'     => [
        'bounds' => [
            [25, 100], [40, 94], [50, 89], [60, 86], [70, 84], [80, 82], [90, 80],
            [100, 75],
        ],
        'h'      => [47, 62],
        's'      => [25, 100],
    ],
    'green'      => [
        'bounds' => [
            [30, 100], [40, 90], [50, 85], [60, 81], [70, 74], [80, 64], [90, 50],
            [100, 40],
        ],
        'h'      => [63, 178],
        's'      => [30, 100],
    ],
    'blue'       => [
        'bounds' => [
            [20, 100], [30, 86], [40, 80], [50, 74], [60, 60], [70, 52], [80, 44],
            [90, 39], [100, 35],
        ],
        'h'      => [179, 257],
        's'      => [20, 100],
    ],
    'purple'     => [
        'bounds' => [
            [20, 100], [30, 87], [40, 79], [50, 70], [60, 65], [70, 59], [80, 52],
            [90, 45], [100, 42],
        ],
        'h'      => [258, 282],
        's'      => [20, 100],
    ],
    'pink'       => [
        'bounds' => [
            [20, 100], [30, 90], [40, 86], [60, 84], [80, 80], [90, 75], [100, 73],
        ],
        'h'      => [283, 334],
        's'      => [20, 100],
    ],
];