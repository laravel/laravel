<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2015 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category    PHPExcel
 * @package        PHPExcel_Reader_Excel2007
 * @copyright    Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license        http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version        ##VERSION##, ##DATE##
 */

/**
 * PHPExcel_Reader_Excel2007_Chart
 *
 * @category    PHPExcel
 * @package        PHPExcel_Reader_Excel2007
 * @copyright    Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Reader_Excel2007_Chart
{
    private static function getAttribute($component, $name, $format)
    {
        $attributes = $component->attributes();
        if (isset($attributes[$name])) {
            if ($format == 'string') {
                return (string) $attributes[$name];
            } elseif ($format == 'integer') {
                return (integer) $attributes[$name];
            } elseif ($format == 'boolean') {
                return (boolean) ($attributes[$name] === '0' || $attributes[$name] !== 'true') ? false : true;
            } else {
                return (float) $attributes[$name];
            }
        }
        return null;
    }


    private static function readColor($color, $background = false)
    {
        if (isset($color["rgb"])) {
            return (string)$color["rgb"];
        } elseif (isset($color["indexed"])) {
            return PHPExcel_Style_Color::indexedColor($color["indexed"]-7, $background)->getARGB();
        }
    }

    public static function readChart($chartElements, $chartName)
    {
        $namespacesChartMeta = $chartElements->getNamespaces(true);
        $chartElementsC = $chartElements->children($namespacesChartMeta['c']);

        $XaxisLabel = $YaxisLabel = $legend = $title = null;
        $dispBlanksAs = $plotVisOnly = null;

        foreach ($chartElementsC as $chartElementKey => $chartElement) {
            switch ($chartElementKey) {
                case "chart":
                    foreach ($chartElement as $chartDetailsKey => $chartDetails) {
                        $chartDetailsC = $chartDetails->children($namespacesChartMeta['c']);
                        switch ($chartDetailsKey) {
                            case "plotArea":
                                $plotAreaLayout = $XaxisLable = $YaxisLable = null;
                                $plotSeries = $plotAttributes = array();
                                foreach ($chartDetails as $chartDetailKey => $chartDetail) {
                                    switch ($chartDetailKey) {
                                        case "layout":
                                            $plotAreaLayout = self::chartLayoutDetails($chartDetail, $namespacesChartMeta, 'plotArea');
                                            break;
                                        case "catAx":
                                            if (isset($chartDetail->title)) {
                                                $XaxisLabel = self::chartTitle($chartDetail->title->children($namespacesChartMeta['c']), $namespacesChartMeta, 'cat');
                                            }
                                            break;
                                        case "dateAx":
                                            if (isset($chartDetail->title)) {
                                                $XaxisLabel = self::chartTitle($chartDetail->title->children($namespacesChartMeta['c']), $namespacesChartMeta, 'cat');
                                            }
                                            break;
                                        case "valAx":
                                            if (isset($chartDetail->title)) {
                                                $YaxisLabel = self::chartTitle($chartDetail->title->children($namespacesChartMeta['c']), $namespacesChartMeta, 'cat');
                                            }
                                            break;
                                        case "barChart":
                                        case "bar3DChart":
                                            $barDirection = self::getAttribute($chartDetail->barDir, 'val', 'string');
                                            $plotSer = self::chartDataSeries($chartDetail, $namespacesChartMeta, $chartDetailKey);
                                            $plotSer->setPlotDirection($barDirection);
                                            $plotSeries[] = $plotSer;
                                            $plotAttributes = self::readChartAttributes($chartDetail);
                                            break;
                                        case "lineChart":
                                        case "line3DChart":
                                            $plotSeries[] = self::chartDataSeries($chartDetail, $namespacesChartMeta, $chartDetailKey);
                                            $plotAttributes = self::readChartAttributes($chartDetail);
                                            break;
                                        case "areaChart":
                                        case "area3DChart":
                                            $plotSeries[] = self::chartDataSeries($chartDetail, $namespacesChartMeta, $chartDetailKey);
                                            $plotAttributes = self::readChartAttributes($chartDetail);
                                            break;
                                        case "doughnutChart":
                                        case "pieChart":
                                        case "pie3DChart":
                                            $explosion = isset($chartDetail->ser->explosion);
                                            $plotSer = self::chartDataSeries($chartDetail, $namespacesChartMeta, $chartDetailKey);
                                            $plotSer->setPlotStyle($explosion);
                                            $plotSeries[] = $plotSer;
                                            $plotAttributes = self::readChartAttributes($chartDetail);
                                            break;
                                        case "scatterChart":
                                            $scatterStyle = self::getAttribute($chartDetail->scatterStyle, 'val', 'string');
                                            $plotSer = self::chartDataSeries($chartDetail, $namespacesChartMeta, $chartDetailKey);
                                            $plotSer->setPlotStyle($scatterStyle);
                                            $plotSeries[] = $plotSer;
                                            $plotAttributes = self::readChartAttributes($chartDetail);
                                            break;
                                        case "bubbleChart":
                                            $bubbleScale = self::getAttribute($chartDetail->bubbleScale, 'val', 'integer');
                                            $plotSer = self::chartDataSeries($chartDetail, $namespacesChartMeta, $chartDetailKey);
                                            $plotSer->setPlotStyle($bubbleScale);
                                            $plotSeries[] = $plotSer;
                                            $plotAttributes = self::readChartAttributes($chartDetail);
                                            break;
                                        case "radarChart":
                                            $radarStyle = self::getAttribute($chartDetail->radarStyle, 'val', 'string');
                                            $plotSer = self::chartDataSeries($chartDetail, $namespacesChartMeta, $chartDetailKey);
                                            $plotSer->setPlotStyle($radarStyle);
                                            $plotSeries[] = $plotSer;
                                            $plotAttributes = self::readChartAttributes($chartDetail);
                                            break;
                                        case "surfaceChart":
                                        case "surface3DChart":
                                            $wireFrame = self::getAttribute($chartDetail->wireframe, 'val', 'boolean');
                                            $plotSer = self::chartDataSeries($chartDetail, $namespacesChartMeta, $chartDetailKey);
                                            $plotSer->setPlotStyle($wireFrame);
                                            $plotSeries[] = $plotSer;
                                            $plotAttributes = self::readChartAttributes($chartDetail);
                                            break;
                                        case "stockChart":
                                            $plotSeries[] = self::chartDataSeries($chartDetail, $namespacesChartMeta, $chartDetailKey);
                                            $plotAttributes = self::readChartAttributes($plotAreaLayout);
                                            break;
                                    }
                                }
                                if ($plotAreaLayout == null) {
                                    $plotAreaLayout = new PHPExcel_Chart_Layout();
                                }
                                $plotArea = new PHPExcel_Chart_PlotArea($plotAreaLayout, $plotSeries);
                                self::setChartAttributes($plotAreaLayout, $plotAttributes);
                                break;
                            case "plotVisOnly":
                                $plotVisOnly = self::getAttribute($chartDetails, 'val', 'string');
                                break;
                            case "dispBlanksAs":
                                $dispBlanksAs = self::getAttribute($chartDetails, 'val', 'string');
                                break;
                            case "title":
                                $title = self::chartTitle($chartDetails, $namespacesChartMeta, 'title');
                                break;
                            case "legend":
                                $legendPos = 'r';
                                $legendLayout = null;
                                $legendOverlay = false;
                                foreach ($chartDetails as $chartDetailKey => $chartDetail) {
                                    switch ($chartDetailKey) {
                                        case "legendPos":
                                            $legendPos = self::getAttribute($chartDetail, 'val', 'string');
                                            break;
                                        case "overlay":
                                            $legendOverlay = self::getAttribute($chartDetail, 'val', 'boolean');
                                            break;
                                        case "layout":
                                            $legendLayout = self::chartLayoutDetails($chartDetail, $namespacesChartMeta, 'legend');
                                            break;
                                    }
                                }
                                $legend = new PHPExcel_Chart_Legend($legendPos, $legendLayout, $legendOverlay);
                                break;
                        }
                    }
            }
        }
        $chart = new PHPExcel_Chart($chartName, $title, $legend, $plotArea, $plotVisOnly, $dispBlanksAs, $XaxisLabel, $YaxisLabel);

        return $chart;
    }

    private static function chartTitle($titleDetails, $namespacesChartMeta, $type)
    {
        $caption = array();
        $titleLayout = null;
        foreach ($titleDetails as $titleDetailKey => $chartDetail) {
            switch ($titleDetailKey) {
                case "tx":
                    $titleDetails = $chartDetail->rich->children($namespacesChartMeta['a']);
                    foreach ($titleDetails as $titleKey => $titleDetail) {
                        switch ($titleKey) {
                            case "p":
                                $titleDetailPart = $titleDetail->children($namespacesChartMeta['a']);
                                $caption[] = self::parseRichText($titleDetailPart);
                        }
                    }
                    break;
                case "layout":
                    $titleLayout = self::chartLayoutDetails($chartDetail, $namespacesChartMeta);
                    break;
            }
        }

        return new PHPExcel_Chart_Title($caption, $titleLayout);
    }

    private static function chartLayoutDetails($chartDetail, $namespacesChartMeta)
    {
        if (!isset($chartDetail->manualLayout)) {
            return null;
        }
        $details = $chartDetail->manualLayout->children($namespacesChartMeta['c']);
        if (is_null($details)) {
            return null;
        }
        $layout = array();
        foreach ($details as $detailKey => $detail) {
//            echo $detailKey, ' => ',self::getAttribute($detail, 'val', 'string'),PHP_EOL;
            $layout[$detailKey] = self::getAttribute($detail, 'val', 'string');
        }
        return new PHPExcel_Chart_Layout($layout);
    }

    private static function chartDataSeries($chartDetail, $namespacesChartMeta, $plotType)
    {
        $multiSeriesType = null;
        $smoothLine = false;
        $seriesLabel = $seriesCategory = $seriesValues = $plotOrder = array();

        $seriesDetailSet = $chartDetail->children($namespacesChartMeta['c']);
        foreach ($seriesDetailSet as $seriesDetailKey => $seriesDetails) {
            switch ($seriesDetailKey) {
                case "grouping":
                    $multiSeriesType = self::getAttribute($chartDetail->grouping, 'val', 'string');
                    break;
                case "ser":
                    $marker = null;
                    foreach ($seriesDetails as $seriesKey => $seriesDetail) {
                        switch ($seriesKey) {
                            case "idx":
                                $seriesIndex = self::getAttribute($seriesDetail, 'val', 'integer');
                                break;
                            case "order":
                                $seriesOrder = self::getAttribute($seriesDetail, 'val', 'integer');
                                $plotOrder[$seriesIndex] = $seriesOrder;
                                break;
                            case "tx":
                                $seriesLabel[$seriesIndex] = self::chartDataSeriesValueSet($seriesDetail, $namespacesChartMeta);
                                break;
                            case "marker":
                                $marker = self::getAttribute($seriesDetail->symbol, 'val', 'string');
                                break;
                            case "smooth":
                                $smoothLine = self::getAttribute($seriesDetail, 'val', 'boolean');
                                break;
                            case "cat":
                                $seriesCategory[$seriesIndex] = self::chartDataSeriesValueSet($seriesDetail, $namespacesChartMeta);
                                break;
                            case "val":
                                $seriesValues[$seriesIndex] = self::chartDataSeriesValueSet($seriesDetail, $namespacesChartMeta, $marker);
                                break;
                            case "xVal":
                                $seriesCategory[$seriesIndex] = self::chartDataSeriesValueSet($seriesDetail, $namespacesChartMeta, $marker);
                                break;
                            case "yVal":
                                $seriesValues[$seriesIndex] = self::chartDataSeriesValueSet($seriesDetail, $namespacesChartMeta, $marker);
                                break;
                        }
                    }
            }
        }
        return new PHPExcel_Chart_DataSeries($plotType, $multiSeriesType, $plotOrder, $seriesLabel, $seriesCategory, $seriesValues, $smoothLine);
    }


    private static function chartDataSeriesValueSet($seriesDetail, $namespacesChartMeta, $marker = null, $smoothLine = false)
    {
        if (isset($seriesDetail->strRef)) {
            $seriesSource = (string) $seriesDetail->strRef->f;
            $seriesData = self::chartDataSeriesValues($seriesDetail->strRef->strCache->children($namespacesChartMeta['c']), 's');

            return new PHPExcel_Chart_DataSeriesValues('String', $seriesSource, $seriesData['formatCode'], $seriesData['pointCount'], $seriesData['dataValues'], $marker, $smoothLine);
        } elseif (isset($seriesDetail->numRef)) {
            $seriesSource = (string) $seriesDetail->numRef->f;
            $seriesData = self::chartDataSeriesValues($seriesDetail->numRef->numCache->children($namespacesChartMeta['c']));

            return new PHPExcel_Chart_DataSeriesValues('Number', $seriesSource, $seriesData['formatCode'], $seriesData['pointCount'], $seriesData['dataValues'], $marker, $smoothLine);
        } elseif (isset($seriesDetail->multiLvlStrRef)) {
            $seriesSource = (string) $seriesDetail->multiLvlStrRef->f;
            $seriesData = self::chartDataSeriesValuesMultiLevel($seriesDetail->multiLvlStrRef->multiLvlStrCache->children($namespacesChartMeta['c']), 's');
            $seriesData['pointCount'] = count($seriesData['dataValues']);

            return new PHPExcel_Chart_DataSeriesValues('String', $seriesSource, $seriesData['formatCode'], $seriesData['pointCount'], $seriesData['dataValues'], $marker, $smoothLine);
        } elseif (isset($seriesDetail->multiLvlNumRef)) {
            $seriesSource = (string) $seriesDetail->multiLvlNumRef->f;
            $seriesData = self::chartDataSeriesValuesMultiLevel($seriesDetail->multiLvlNumRef->multiLvlNumCache->children($namespacesChartMeta['c']), 's');
            $seriesData['pointCount'] = count($seriesData['dataValues']);

            return new PHPExcel_Chart_DataSeriesValues('String', $seriesSource, $seriesData['formatCode'], $seriesData['pointCount'], $seriesData['dataValues'], $marker, $smoothLine);
        }
        return null;
    }


    private static function chartDataSeriesValues($seriesValueSet, $dataType = 'n')
    {
        $seriesVal = array();
        $formatCode = '';
        $pointCount = 0;

        foreach ($seriesValueSet as $seriesValueIdx => $seriesValue) {
            switch ($seriesValueIdx) {
                case 'ptCount':
                    $pointCount = self::getAttribute($seriesValue, 'val', 'integer');
                    break;
                case 'formatCode':
                    $formatCode = (string) $seriesValue;
                    break;
                case 'pt':
                    $pointVal = self::getAttribute($seriesValue, 'idx', 'integer');
                    if ($dataType == 's') {
                        $seriesVal[$pointVal] = (string) $seriesValue->v;
                    } else {
                        $seriesVal[$pointVal] = (float) $seriesValue->v;
                    }
                    break;
            }
        }

        return array(
            'formatCode'    => $formatCode,
            'pointCount'    => $pointCount,
            'dataValues'    => $seriesVal
        );
    }

    private static function chartDataSeriesValuesMultiLevel($seriesValueSet, $dataType = 'n')
    {
        $seriesVal = array();
        $formatCode = '';
        $pointCount = 0;

        foreach ($seriesValueSet->lvl as $seriesLevelIdx => $seriesLevel) {
            foreach ($seriesLevel as $seriesValueIdx => $seriesValue) {
                switch ($seriesValueIdx) {
                    case 'ptCount':
                        $pointCount = self::getAttribute($seriesValue, 'val', 'integer');
                        break;
                    case 'formatCode':
                        $formatCode = (string) $seriesValue;
                        break;
                    case 'pt':
                        $pointVal = self::getAttribute($seriesValue, 'idx', 'integer');
                        if ($dataType == 's') {
                            $seriesVal[$pointVal][] = (string) $seriesValue->v;
                        } else {
                            $seriesVal[$pointVal][] = (float) $seriesValue->v;
                        }
                        break;
                }
            }
        }

        return array(
            'formatCode'    => $formatCode,
            'pointCount'    => $pointCount,
            'dataValues'    => $seriesVal
        );
    }

    private static function parseRichText($titleDetailPart = null)
    {
        $value = new PHPExcel_RichText();

        foreach ($titleDetailPart as $titleDetailElementKey => $titleDetailElement) {
            if (isset($titleDetailElement->t)) {
                $objText = $value->createTextRun((string) $titleDetailElement->t);
            }
            if (isset($titleDetailElement->rPr)) {
                if (isset($titleDetailElement->rPr->rFont["val"])) {
                    $objText->getFont()->setName((string) $titleDetailElement->rPr->rFont["val"]);
                }

                $fontSize = (self::getAttribute($titleDetailElement->rPr, 'sz', 'integer'));
                if (!is_null($fontSize)) {
                    $objText->getFont()->setSize(floor($fontSize / 100));
                }

                $fontColor = (self::getAttribute($titleDetailElement->rPr, 'color', 'string'));
                if (!is_null($fontColor)) {
                    $objText->getFont()->setColor(new PHPExcel_Style_Color(self::readColor($fontColor)));
                }

                $bold = self::getAttribute($titleDetailElement->rPr, 'b', 'boolean');
                if (!is_null($bold)) {
                    $objText->getFont()->setBold($bold);
                }

                $italic = self::getAttribute($titleDetailElement->rPr, 'i', 'boolean');
                if (!is_null($italic)) {
                    $objText->getFont()->setItalic($italic);
                }

                $baseline = self::getAttribute($titleDetailElement->rPr, 'baseline', 'integer');
                if (!is_null($baseline)) {
                    if ($baseline > 0) {
                        $objText->getFont()->setSuperScript(true);
                    } elseif ($baseline < 0) {
                        $objText->getFont()->setSubScript(true);
                    }
                }

                $underscore = (self::getAttribute($titleDetailElement->rPr, 'u', 'string'));
                if (!is_null($underscore)) {
                    if ($underscore == 'sng') {
                        $objText->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
                    } elseif ($underscore == 'dbl') {
                        $objText->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_DOUBLE);
                    } else {
                        $objText->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_NONE);
                    }
                }

                $strikethrough = (self::getAttribute($titleDetailElement->rPr, 's', 'string'));
                if (!is_null($strikethrough)) {
                    if ($strikethrough == 'noStrike') {
                        $objText->getFont()->setStrikethrough(false);
                    } else {
                        $objText->getFont()->setStrikethrough(true);
                    }
                }
            }
        }

        return $value;
    }

    private static function readChartAttributes($chartDetail)
    {
        $plotAttributes = array();
        if (isset($chartDetail->dLbls)) {
            if (isset($chartDetail->dLbls->howLegendKey)) {
                $plotAttributes['showLegendKey'] = self::getAttribute($chartDetail->dLbls->showLegendKey, 'val', 'string');
            }
            if (isset($chartDetail->dLbls->showVal)) {
                $plotAttributes['showVal'] = self::getAttribute($chartDetail->dLbls->showVal, 'val', 'string');
            }
            if (isset($chartDetail->dLbls->showCatName)) {
                $plotAttributes['showCatName'] = self::getAttribute($chartDetail->dLbls->showCatName, 'val', 'string');
            }
            if (isset($chartDetail->dLbls->showSerName)) {
                $plotAttributes['showSerName'] = self::getAttribute($chartDetail->dLbls->showSerName, 'val', 'string');
            }
            if (isset($chartDetail->dLbls->showPercent)) {
                $plotAttributes['showPercent'] = self::getAttribute($chartDetail->dLbls->showPercent, 'val', 'string');
            }
            if (isset($chartDetail->dLbls->showBubbleSize)) {
                $plotAttributes['showBubbleSize'] = self::getAttribute($chartDetail->dLbls->showBubbleSize, 'val', 'string');
            }
            if (isset($chartDetail->dLbls->showLeaderLines)) {
                $plotAttributes['showLeaderLines'] = self::getAttribute($chartDetail->dLbls->showLeaderLines, 'val', 'string');
            }
        }

        return $plotAttributes;
    }

    private static function setChartAttributes($plotArea, $plotAttributes)
    {
        foreach ($plotAttributes as $plotAttributeKey => $plotAttributeValue) {
            switch ($plotAttributeKey) {
                case 'showLegendKey':
                    $plotArea->setShowLegendKey($plotAttributeValue);
                    break;
                case 'showVal':
                    $plotArea->setShowVal($plotAttributeValue);
                    break;
                case 'showCatName':
                    $plotArea->setShowCatName($plotAttributeValue);
                    break;
                case 'showSerName':
                    $plotArea->setShowSerName($plotAttributeValue);
                    break;
                case 'showPercent':
                    $plotArea->setShowPercent($plotAttributeValue);
                    break;
                case 'showBubbleSize':
                    $plotArea->setShowBubbleSize($plotAttributeValue);
                    break;
                case 'showLeaderLines':
                    $plotArea->setShowLeaderLines($plotAttributeValue);
                    break;
            }
        }
    }
}
