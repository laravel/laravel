<?php

namespace App\Http\Controllers;

use Amenadiel\JpGraph\Graph\Graph;
use Amenadiel\JpGraph\Plot\AccBarPlot;
use Amenadiel\JpGraph\Plot\BarPlot;
use Amenadiel\JpGraph\Plot\GroupBarPlot;
use Amenadiel\JpGraph\Plot\LinePlot;
use Amenadiel\JpGraph\Plot\PlotLine;
use Amenadiel\JpGraph\Themes\UniversalTheme;
use Amenadiel\JpGraph\Text\Text;
use Amenadiel\JpGraph\Graph\CanvasGraph;

class GraphsController extends Controller
{
    public static function getTrend($x, $y)
    {
        $n = count($x);

        // Convertir fechas a timestamp para cálculos
        $xTimestamp = array_map(function ($date) {
            return strtotime($date); // Convertir fecha a timestamp
        }, $x);

        // Calcular la suma de los productos de x por y, la suma de x al cuadrado, etc.
        $sumX = array_sum($xTimestamp);
        $sumY = array_sum($y);
        $sumXY = 0;
        $sumX2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumXY += $xTimestamp[$i] * $y[$i];
            $sumX2 += $xTimestamp[$i] * $xTimestamp[$i];
        }

        // Calcular la pendiente (m) y el intercepto (b)
        $m = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $b = ($sumY * $sumX2 - $sumX * $sumXY) / ($n * $sumX2 - $sumX * $sumX);

        // Crear los valores de la línea de tendencia
        $trend = [];
        foreach ($xTimestamp as $x) {
            $trend[] = $m * $x + $b;
        }

        return array(
            "value" => $trend,
            "pend" => $m,
        );
    }

    public static function strokeGraph($graph)
    {
        $filename = FileController::getFileNameAsUnixTime("jpg", 5, "MINUTES");
        $path = public_path() . FileController::$AUTODESTROY_DIR;
        // Si la carpeta no existe, crearla
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        $graph->Stroke($path . "/{$filename}"); // Guarda el gráfico en un archivo JPG

        return $filename;
    }

    private static function getBaseGraph($dates)
    {
        // Setup the graph
        $graph = new Graph(1024, 400);
        $graph->SetScale("textlin");

        $theme_class = new UniversalTheme();

        $graph->SetTheme($theme_class);
        //$graph->img->SetAntiAliasing(false);
        //$graph->title->Set('Filled Y-grid');
        $graph->SetBox(false);

        $graph->SetMargin(40, 20, 36, 63);

        $graph->img->SetAntiAliasing();

        $graph->yaxis->HideZeroLabel();
        $graph->yaxis->HideLine(false);
        $graph->yaxis->HideTicks(false, false);

        $graph->xgrid->Show();
        $graph->xgrid->SetLineStyle("solid");
        $graph->xaxis->SetTickLabels($dates);
        $graph->xgrid->SetColor('#E3E3E3');
        // Set the angle for the labels to 90 degrees
        $graph->xaxis->SetLabelAngle(90);

        $graph->legend->SetFrameWeight(1);

        /*
        $icon = new IconPlot();
        $icon->SetCountryFlag('cuba');
        $icon->SetAnchor('left', 'top');
        $graph->Add($icon);
         */

        return $graph;
    }

    public static function generateLinesGraph($dates, $array)
    {
        $graph = GraphsController::getBaseGraph($dates);

        $configuredYs = false;

        //$receiveds, $confirmeds, $sents, $balances
        foreach ($array as $serie) {
            $p = new LinePlot($serie["values"]);
            if (isset($serie["style"])) {
                $p->SetStyle($serie["style"]);
            }
            $p->SetWeight($serie["weight"]);
            $graph->Add($p);
            $p->SetColor($serie["color"]);
            if (isset($serie["label"])) {
                $p->SetLegend($serie["label"]);
            }
            if (isset($serie["y"])) {
                if (!$configuredYs) {
                    $graph->SetMargin(40, 40, 40, 63);
                    $graph->SetY2Scale('lin');
                    $graph->y2axis->SetColor('teal'); // Color para el eje secundario
                    $configuredYs = true;
                }
                for ($i = 0; $i < count($serie["y"]); $i++) {
                    $p2 = new LinePlot($serie["y"][$i]["values"]);
                    $p2->SetColor($serie["y"][$i]["color"]);
                    if (isset($serie["y"][$i]["weight"])) {
                        $p2->SetWeight($serie["y"][$i]["weight"]);
                    }
                    $graph->AddY2($p2);
                }
            }

            if (isset($serie["trend"])) {
                $trend = GraphsController::getTrend($dates, $serie["values"]);
                // Crear la línea de tendencia
                $trend_line = new LinePlot($trend["value"]);
                if (isset($serie["trend"]["style"])) {
                    $trend_line->SetStyle($serie["trend"]["style"]);
                }
                $trend_line->SetWeight($serie["trend"]["weight"]);
                $graph->Add($trend_line);
                // Establecer el color según la pendiente
                if ($trend["pend"] >= 0) {
                    $trend_line->SetColor($serie["trend"]["color"]["positive"]);
                } else {
                    $trend_line->SetColor($serie["trend"]["color"]["negative"]);
                }
            }

        }

        return GraphsController::strokeGraph($graph);
    }

    public static function generateGroupBarsGraph($dates, $array)
    {
        $graph = GraphsController::getBaseGraph($dates);

        foreach ($array as $serie) {
            if (!isset($serie["trend"])) {
                $group = array();
                for ($i = 0; $i < count($serie["values"]); $i++) {
                    if (is_array($serie["values"][$i][0])) {
                        $accumulated = array();
                        for ($j = 0; $j < count($serie["values"][$i]); $j++) {
                            $plot = new BarPlot($serie["values"][$i][$j]);
                            $plot->SetFillColor($serie["color"][$i][$j]);
                            $plot->SetColor($serie["color"][$i][$j]);
                            $plot->value->SetColor(isset($serie["y"]) ? "teal" : "black");
                            $plot->value->Show();
                            if (isset($serie["label"]) && isset($serie["label"][$i]) && isset($serie["label"][$i][$j])) {
                                $plot->SetLegend($serie["label"][$i][$j]);
                            }
                            $accumulated[] = $plot;
                        }
                        $plot = new AccBarPlot($accumulated);
                        //$plot->value->Show();
                    } else {
                        $plot = new BarPlot($serie["values"][$i]);
                        $plot->SetFillColor($serie["color"][$i]);
                        $plot->SetColor($serie["color"][$i]);
                        //$plot->SetShadow();
                        $plot->value->SetColor(isset($serie["y"]) ? "teal" : "black");
                        //$plot->value->SetFormat('%.1f EUR');
                        $plot->value->Show();
                        if (isset($serie["label"]) && isset($serie["label"][$i])) {
                            $plot->SetLegend($serie["label"][$i]);
                        }
                    }
                    $group[] = $plot;
                }
                // Create the grouped bar plot
                $groupplot = new GroupBarPlot($group);

                if (isset($serie["y"])) {
                    $graph->SetMargin(40, 40, 40, 63);
                    $graph->SetY2Scale('lin');
                    $graph->y2axis->SetColor('teal'); // Color para el eje secundario
                    $graph->AddY2($groupplot);
                } else {
                    $graph->Add($groupplot);
                }
            } else {
                if (isset($serie["trend"]["y"])) {
                    $graph->SetMargin(40, 40, 40, 63);
                    $graph->SetY2Scale('lin');
                    $graph->y2axis->SetColor('teal'); // Color para el eje secundario
                }

                for ($i = 0; $i < count($serie["values"]); $i++) {
                    $plot = new LinePlot($serie["values"][$i]);
                    $plot->SetColor($serie["color"][$i]);
                    if (isset($serie["label"]) && isset($serie["label"][$i])) {
                        $plot->SetLegend($serie["label"][$i]);
                    }
                    if (isset($serie["trend"]["style"])) {
                        $plot->SetStyle($serie["trend"]["style"]);
                    }
                    $plot->SetWeight($serie["trend"]["weight"]);
                    $plot->SetBarCenter();
                    if (isset($serie["trend"]["y"])) {
                        $graph->AddY2($plot);
                    } else {
                        $graph->Add($plot);
                    }

                }
            }

        }

        return GraphsController::strokeGraph($graph);
    }

    /**
     * Crea un comprobante en base a un arreglo de datos
     * @param mixed $transaction ["date","id","name","amount","to","rate","usd"]
     * @return int
     */
    public static function generateComprobantGraph($transaction, $sensitive = false)
    {
        $tc = new TextController();

        $coin = "";
        // Establecer la imagen de fondo
        $backgroundPath = public_path('comprobant-eur.jpg');
        switch (strtoupper($transaction["coin"])) {
            case "EUR":
                $coin = "€";
                break;
            default:
                $coin = "$";
                $backgroundPath = public_path('comprobant-usa.jpg');
                break;
        }
        // Verificar que la imagen exista
        if (!file_exists($backgroundPath)) {
            throw new \Exception("El archivo de fondo no existe en: " . $backgroundPath);
        }


        // Configuración del canvas
        $graph = new CanvasGraph(742, 1280, 'auto');

        // Desactivar el fondo blanco automático
        $graph->SetFrame(false);
        $graph->SetBox(false);

        // Configurar márgenes (opcional, ajusta según necesites)
        $graph->SetMargin(5, 11, 6, 11);

        // Configurar el fondo (usar BGIMG_FILLPLOT para mejor resultado)
        $graph->SetBackgroundImage($backgroundPath, BGIMG_FILLPLOT);

        // Inicializar el frame sin fondo blanco
        $graph->InitFrame();

        $text = new Text("+ " . $coin . $transaction["amount"], 701, 40);
        $text->SetFont(FF_ARIAL, FS_BOLD, 35);
        $text->SetColor('black'); // Color del texto
        $text->Align('right', 'top');
        $text->Stroke($graph->img);

        $text = new Text($tc->str_pad("IBAN " . $transaction["to"], 40, " ", -12), 701, 100);
        $text->SetFont(FF_ARIAL, FS_NORMAL, 18);
        $text->SetColor('gray'); // Color del texto
        $text->Align('right', 'top');
        $text->Stroke($graph->img);

        $text = new Text($tc->str_pad($transaction["name"], 27), 30, 320);
        $text->SetFont(FF_ARIAL, FS_BOLD, 28);
        $text->SetColor('black'); // Color del texto
        $text->Align('left', 'top');
        $text->Stroke($graph->img);

        $text = new Text($tc->str_pad("Fecha " . $transaction["date"], 61, " ", -12), 701, 440);
        $text->SetFont(FF_ARIAL, FS_NORMAL, 20);
        $text->SetColor('gray'); // Color del texto
        $text->Align('right', 'top');
        $text->Stroke($graph->img);

        $text = new Text($tc->str_pad("Id " . $transaction["id"], 40, " ", -5), 50, 535);
        $text->SetFont(FF_ARIAL, FS_NORMAL, 20);
        $text->SetColor('gray'); // Color del texto
        $text->Align('left', 'top');
        $text->Stroke($graph->img);

        $text = new Text($tc->str_pad("Total pagado " . $coin . $transaction["amount"], 65, " ", -5), 701, 630);
        $text->SetFont(FF_ARIAL, FS_NORMAL, 20);
        $text->SetColor('gray'); // Color del texto
        $text->Align('right', 'top');
        $text->Stroke($graph->img);

        $text = new Text($tc->str_pad("Comisión {$coin}0.00", 70, " ", -5), 701, 725);
        $text->SetFont(FF_ARIAL, FS_NORMAL, 20);
        $text->SetColor('gray'); // Color del texto
        $text->Align('right', 'top');
        $text->Stroke($graph->img);

        $text = new Text($tc->str_pad("Tasa de cambio " . $transaction["rate"], 57, " ", -5), 701, 820);
        if ($sensitive)
            $text = new Text($tc->str_pad("Tasa de cambio " . $tc->hide_password($transaction["rate"], 2), 58, " ", -5), 701, 820);
        $text->SetFont(FF_ARIAL, FS_NORMAL, 20);
        $text->SetColor('gray'); // Color del texto
        $text->Align('right', 'top');
        $text->Stroke($graph->img);

        $text = new Text($tc->str_pad("Acreditado $" . $transaction["usd"], 63, " ", -5), 701, 915);
        if ($sensitive)
            $text = new Text($tc->str_pad("Acreditado " . $tc->hide_password($transaction["usd"], 2), 65, " ", -5), 701, 915);
        $text->SetFont(FF_ARIAL, FS_NORMAL, 20);
        $text->SetColor('gray'); // Color del texto
        $text->Align('right', 'top');
        $text->Stroke($graph->img);

        $text = new Text($transaction["name"], 701, 1110);
        $text->SetFont(FF_ARIAL, FS_NORMAL, 20);
        $text->SetColor('gray'); // Color del texto
        $text->Align('right', 'top');
        $text->Stroke($graph->img);

        return GraphsController::strokeGraph($graph);
    }
}
