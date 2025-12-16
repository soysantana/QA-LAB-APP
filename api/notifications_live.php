<?php
@ob_clean();
require_once('../config/load.php');

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(E_ALL);

/* =========================
   HELPERS
========================= */

function timeAgo($datetime)
{
    $ts = strtotime($datetime);
    if (!$ts) return '';

    $diff = time() - $ts;

    if ($diff < 60) return "hace {$diff}s";
    if ($diff < 3600) return "hace " . floor($diff / 60) . " min";
    if ($diff < 86400) return "hace " . floor($diff / 3600) . " h";

    return "hace " . floor($diff / 86400) . " días";
}

/* Compatibilidad PHP < 8 */
function startsWith($haystack, $needle)
{
    return substr($haystack, 0, strlen($needle)) === $needle;
}

function routeFor($type, $tracking)
{
    $T = strtoupper(trim($type));

    $map = [
        'AL'  => '../reviews/atterberg-limit.php',
        'BTS' => '../reviews/brazilian.php',
        'HY'  => '../reviews/hydrometer.php',
        'DHY' => '../reviews/double-hydrometer.php',
        'PLT' => '../reviews/point-Load.php',
        'SND' => '../reviews/soundness.php',
        'SP'  => '../reviews/standard-proctor.php',
        'UCS' => '../reviews/unixial-compressive.php',

        'MC_OVEN'          => '../reviews/moisture-oven.php',
        'MC_MICROWAVE'     => '../reviews/moisture-microwave.php',
        'MC_CONSTANT_MASS' => '../reviews/moisture-constant-mass.php',
        'MC_SCALE'         => '../reviews/moisture-scale.php',

        'SG'        => '../reviews/specific-gravity.php',
        'SG_COARSE' => '../reviews/specific-gravity-coarse-aggregates.php',
        'SG_FINE'   => '../reviews/specific-gravity-fine-aggregate.php',

        'GS'      => '../reviews/grain-size.php',
        'GS_FF'   => '../reviews/grain-size-fine-filter.php',
        'GS_CF'   => '../reviews/grain-size-coarse-filter.php',
        'GS_LPF'  => '../reviews/grain-size-lpf.php',
        'GS_UTF'  => '../reviews/grain-size-upstream-transition-fill.php',
    ];

    if (isset($map[$T])) {
        return $map[$T] . "?id=" . urlencode($tracking);
    }

    /* 👇 reemplazo seguro de str_starts_with */
    if (startsWith($T, 'GS')) {
        return "../reviews/grain-size-full.php?id=" . urlencode($tracking);
    }

    return "#";
}

/* =========================
   DATA
========================= */

$repeat = find_by_sql("
    SELECT Sample_ID, Sample_Number, Test_Type, Start_Date, Tracking, Comment
    FROM test_repeat
    ORDER BY Start_Date DESC
    LIMIT 10
");

$reviewed = find_by_sql("
    SELECT Sample_ID, Sample_Number, Test_Type, Start_Date, Tracking
    FROM test_reviewed
    WHERE Signed != 1
    ORDER BY Start_Date DESC
    LIMIT 10
");

$items = [];

foreach ($repeat as $r) {
    $items[] = [
        "type"  => "repeat",
        "title" => $r['Sample_ID']."-".$r['Sample_Number']." (".$r['Test_Type'].")",
        "msg"   => $r['Comment'] ?: "Ensayo enviado a repetición",
        "time"  => timeAgo($r['Start_Date']),
        "icon"  => "bi-exclamation-triangle-fill text-warning",
        "url"   => routeFor($r['Test_Type'], $r['Tracking'])
    ];
}

foreach ($reviewed as $r) {
    $items[] = [
        "type"  => "review",
        "title" => $r['Sample_ID']."-".$r['Sample_Number']." (".$r['Test_Type'].")",
        "msg"   => "Pendiente de firma",
        "time"  => timeAgo($r['Start_Date']),
        "icon"  => "bi-check-circle text-success",
        "url"   => routeFor($r['Test_Type'], $r['Tracking'])
    ];
}

/* Ordena por fecha real (no texto) */
usort($items, function ($a, $b) {
    return strtotime($b['time']) <=> strtotime($a['time']);
});

echo json_encode([
    "count" => count($items),
    "items" => $items
], JSON_UNESCAPED_UNICODE);

exit;
