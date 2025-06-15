<?php
function hitungWMA($data, $periode) {
    $bobot = range($periode, 1);
    $totalBobot = array_sum($bobot);
    $wma = 0;

    for ($i = 0; $i < $periode; $i++) {
        $wma += $data[$i] * $bobot[$i];
    }

    return $wma / $totalBobot;
}
?>
