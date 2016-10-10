<?php
/**
 * Created by PhpStorm.
 * User: nhw2h8s
 * Date: 7/2/14
 * Time: 5:45 PM
 */

abstract class PHPExcel_Chart_Properties
{
    const
        EXCEL_COLOR_TYPE_STANDARD = 'prstClr',
        EXCEL_COLOR_TYPE_SCHEME = 'schemeClr',
        EXCEL_COLOR_TYPE_ARGB = 'srgbClr';

    const
        AXIS_LABELS_LOW = 'low',
        AXIS_LABELS_HIGH = 'high',
        AXIS_LABELS_NEXT_TO = 'nextTo',
        AXIS_LABELS_NONE = 'none';

    const
        TICK_MARK_NONE = 'none',
        TICK_MARK_INSIDE = 'in',
        TICK_MARK_OUTSIDE = 'out',
        TICK_MARK_CROSS = 'cross';

    const
        HORIZONTAL_CROSSES_AUTOZERO = 'autoZero',
        HORIZONTAL_CROSSES_MAXIMUM = 'max';

    const
        FORMAT_CODE_GENERAL = 'General',
        FORMAT_CODE_NUMBER = '#,##0.00',
        FORMAT_CODE_CURRENCY = '$#,##0.00',
        FORMAT_CODE_ACCOUNTING = '_($* #,##0.00_);_($* (#,##0.00);_($* "-"??_);_(@_)',
        FORMAT_CODE_DATE = 'm/d/yyyy',
        FORMAT_CODE_TIME = '[$-F400]h:mm:ss AM/PM',
        FORMAT_CODE_PERCENTAGE = '0.00%',
        FORMAT_CODE_FRACTION = '# ?/?',
        FORMAT_CODE_SCIENTIFIC = '0.00E+00',
        FORMAT_CODE_TEXT = '@',
        FORMAT_CODE_SPECIAL = '00000';

    const
        ORIENTATION_NORMAL = 'minMax',
        ORIENTATION_REVERSED = 'maxMin';

    const
        LINE_STYLE_COMPOUND_SIMPLE = 'sng',
        LINE_STYLE_COMPOUND_DOUBLE = 'dbl',
        LINE_STYLE_COMPOUND_THICKTHIN = 'thickThin',
        LINE_STYLE_COMPOUND_THINTHICK = 'thinThick',
        LINE_STYLE_COMPOUND_TRIPLE = 'tri',

        LINE_STYLE_DASH_SOLID = 'solid',
        LINE_STYLE_DASH_ROUND_DOT = 'sysDot',
        LINE_STYLE_DASH_SQUERE_DOT = 'sysDash',
        LINE_STYPE_DASH_DASH = 'dash',
        LINE_STYLE_DASH_DASH_DOT = 'dashDot',
        LINE_STYLE_DASH_LONG_DASH = 'lgDash',
        LINE_STYLE_DASH_LONG_DASH_DOT = 'lgDashDot',
        LINE_STYLE_DASH_LONG_DASH_DOT_DOT = 'lgDashDotDot',

        LINE_STYLE_CAP_SQUARE = 'sq',
        LINE_STYLE_CAP_ROUND = 'rnd',
        LINE_STYLE_CAP_FLAT = 'flat',

        LINE_STYLE_JOIN_ROUND = 'bevel',
        LINE_STYLE_JOIN_MITER = 'miter',
        LINE_STYLE_JOIN_BEVEL = 'bevel',

        LINE_STYLE_ARROW_TYPE_NOARROW = null,
        LINE_STYLE_ARROW_TYPE_ARROW = 'triangle',
        LINE_STYLE_ARROW_TYPE_OPEN = 'arrow',
        LINE_STYLE_ARROW_TYPE_STEALTH = 'stealth',
        LINE_STYLE_ARROW_TYPE_DIAMOND = 'diamond',
        LINE_STYLE_ARROW_TYPE_OVAL = 'oval',

        LINE_STYLE_ARROW_SIZE_1 = 1,
        LINE_STYLE_ARROW_SIZE_2 = 2,
        LINE_STYLE_ARROW_SIZE_3 = 3,
        LINE_STYLE_ARROW_SIZE_4 = 4,
        LINE_STYLE_ARROW_SIZE_5 = 5,
        LINE_STYLE_ARROW_SIZE_6 = 6,
        LINE_STYLE_ARROW_SIZE_7 = 7,
        LINE_STYLE_ARROW_SIZE_8 = 8,
        LINE_STYLE_ARROW_SIZE_9 = 9;

    const
        SHADOW_PRESETS_NOSHADOW = null,
        SHADOW_PRESETS_OUTER_BOTTTOM_RIGHT = 1,
        SHADOW_PRESETS_OUTER_BOTTOM = 2,
        SHADOW_PRESETS_OUTER_BOTTOM_LEFT = 3,
        SHADOW_PRESETS_OUTER_RIGHT = 4,
        SHADOW_PRESETS_OUTER_CENTER = 5,
        SHADOW_PRESETS_OUTER_LEFT = 6,
        SHADOW_PRESETS_OUTER_TOP_RIGHT = 7,
        SHADOW_PRESETS_OUTER_TOP = 8,
        SHADOW_PRESETS_OUTER_TOP_LEFT = 9,
        SHADOW_PRESETS_INNER_BOTTTOM_RIGHT = 10,
        SHADOW_PRESETS_INNER_BOTTOM = 11,
        SHADOW_PRESETS_INNER_BOTTOM_LEFT = 12,
        SHADOW_PRESETS_INNER_RIGHT = 13,
        SHADOW_PRESETS_INNER_CENTER = 14,
        SHADOW_PRESETS_INNER_LEFT = 15,
        SHADOW_PRESETS_INNER_TOP_RIGHT = 16,
        SHADOW_PRESETS_INNER_TOP = 17,
        SHADOW_PRESETS_INNER_TOP_LEFT = 18,
        SHADOW_PRESETS_PERSPECTIVE_BELOW = 19,
        SHADOW_PRESETS_PERSPECTIVE_UPPER_RIGHT = 20,
        SHADOW_PRESETS_PERSPECTIVE_UPPER_LEFT = 21,
        SHADOW_PRESETS_PERSPECTIVE_LOWER_RIGHT = 22,
        SHADOW_PRESETS_PERSPECTIVE_LOWER_LEFT = 23;

    protected function getExcelPointsWidth($width)
    {
        return $width * 12700;
    }

    protected function getExcelPointsAngle($angle)
    {
        return $angle * 60000;
    }

    protected function getTrueAlpha($alpha)
    {
        return (string) 100 - $alpha . '000';
    }

    protected function setColorProperties($color, $alpha, $type)
    {
        return array(
            'type' => (string) $type,
            'value' => (string) $color,
            'alpha' => (string) $this->getTrueAlpha($alpha)
        );
    }

    protected function getLineStyleArrowSize($array_selector, $array_kay_selector)
    {
        $sizes = array(
            1 => array('w' => 'sm', 'len' => 'sm'),
            2 => array('w' => 'sm', 'len' => 'med'),
            3 => array('w' => 'sm', 'len' => 'lg'),
            4 => array('w' => 'med', 'len' => 'sm'),
            5 => array('w' => 'med', 'len' => 'med'),
            6 => array('w' => 'med', 'len' => 'lg'),
            7 => array('w' => 'lg', 'len' => 'sm'),
            8 => array('w' => 'lg', 'len' => 'med'),
            9 => array('w' => 'lg', 'len' => 'lg')
        );

        return $sizes[$array_selector][$array_kay_selector];
    }

    protected function getShadowPresetsMap($shadow_presets_option)
    {
        $presets_options = array(
            //OUTER
            1 => array(
                'effect' => 'outerShdw',
                'blur' => '50800',
                'distance' => '38100',
                'direction' => '2700000',
                'algn' => 'tl',
                'rotWithShape' => '0'
            ),
            2 => array(
                'effect' => 'outerShdw',
                'blur' => '50800',
                'distance' => '38100',
                'direction' => '5400000',
                'algn' => 't',
                'rotWithShape' => '0'
            ),
            3 => array(
                'effect' => 'outerShdw',
                'blur' => '50800',
                'distance' => '38100',
                'direction' => '8100000',
                'algn' => 'tr',
                'rotWithShape' => '0'
            ),
            4 => array(
                'effect' => 'outerShdw',
                'blur' => '50800',
                'distance' => '38100',
                'algn' => 'l',
                'rotWithShape' => '0'
            ),
            5 => array(
                'effect' => 'outerShdw',
                'size' => array(
                    'sx' => '102000',
                    'sy' => '102000'
                )
                ,
                'blur' => '63500',
                'distance' => '38100',
                'algn' => 'ctr',
                'rotWithShape' => '0'
            ),
            6 => array(
                'effect' => 'outerShdw',
                'blur' => '50800',
                'distance' => '38100',
                'direction' => '10800000',
                'algn' => 'r',
                'rotWithShape' => '0'
            ),
            7 => array(
                'effect' => 'outerShdw',
                'blur' => '50800',
                'distance' => '38100',
                'direction' => '18900000',
                'algn' => 'bl',
                'rotWithShape' => '0'
            ),
            8 => array(
                'effect' => 'outerShdw',
                'blur' => '50800',
                'distance' => '38100',
                'direction' => '16200000',
                'rotWithShape' => '0'
            ),
            9 => array(
                'effect' => 'outerShdw',
                'blur' => '50800',
                'distance' => '38100',
                'direction' => '13500000',
                'algn' => 'br',
                'rotWithShape' => '0'
            ),
            //INNER
            10 => array(
                'effect' => 'innerShdw',
                'blur' => '63500',
                'distance' => '50800',
                'direction' => '2700000',
            ),
            11 => array(
                'effect' => 'innerShdw',
                'blur' => '63500',
                'distance' => '50800',
                'direction' => '5400000',
            ),
            12 => array(
                'effect' => 'innerShdw',
                'blur' => '63500',
                'distance' => '50800',
                'direction' => '8100000',
            ),
            13 => array(
                'effect' => 'innerShdw',
                'blur' => '63500',
                'distance' => '50800',
            ),
            14 => array(
                'effect' => 'innerShdw',
                'blur' => '114300',
            ),
            15 => array(
                'effect' => 'innerShdw',
                'blur' => '63500',
                'distance' => '50800',
                'direction' => '10800000',
            ),
            16 => array(
                'effect' => 'innerShdw',
                'blur' => '63500',
                'distance' => '50800',
                'direction' => '18900000',
            ),
            17 => array(
                'effect' => 'innerShdw',
                'blur' => '63500',
                'distance' => '50800',
                'direction' => '16200000',
            ),
            18 => array(
                'effect' => 'innerShdw',
                'blur' => '63500',
                'distance' => '50800',
                'direction' => '13500000',
            ),
            //perspective
            19 => array(
                'effect' => 'outerShdw',
                'blur' => '152400',
                'distance' => '317500',
                'size' => array(
                    'sx' => '90000',
                    'sy' => '-19000',
                ),
                'direction' => '5400000',
                'rotWithShape' => '0',
            ),
            20 => array(
                'effect' => 'outerShdw',
                'blur' => '76200',
                'direction' => '18900000',
                'size' => array(
                    'sy' => '23000',
                    'kx' => '-1200000',
                ),
                'algn' => 'bl',
                'rotWithShape' => '0',
            ),
            21 => array(
                'effect' => 'outerShdw',
                'blur' => '76200',
                'direction' => '13500000',
                'size' => array(
                    'sy' => '23000',
                    'kx' => '1200000',
                ),
                'algn' => 'br',
                'rotWithShape' => '0',
            ),
            22 => array(
                'effect' => 'outerShdw',
                'blur' => '76200',
                'distance' => '12700',
                'direction' => '2700000',
                'size' => array(
                    'sy' => '-23000',
                    'kx' => '-800400',
                ),
                'algn' => 'bl',
                'rotWithShape' => '0',
            ),
            23 => array(
                'effect' => 'outerShdw',
                'blur' => '76200',
                'distance' => '12700',
                'direction' => '8100000',
                'size' => array(
                    'sy' => '-23000',
                    'kx' => '800400',
                ),
                'algn' => 'br',
                'rotWithShape' => '0',
            ),
        );

        return $presets_options[$shadow_presets_option];
    }

    protected function getArrayElementsValue($properties, $elements)
    {
        $reference = & $properties;
        if (!is_array($elements)) {
            return $reference[$elements];
        } else {
            foreach ($elements as $keys) {
                $reference = & $reference[$keys];
            }
            return $reference;
        }
        return $this;
    }
}
