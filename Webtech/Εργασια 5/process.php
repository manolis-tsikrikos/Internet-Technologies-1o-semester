<?php
session_start();

if (!isset($_SESSION['candidates'])) {
    $_SESSION['candidates'] = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $scores = $_POST['scores'];

    if (strtoupper($name) === "ΤΕΛΟΣ") {
        if (count($_SESSION['candidates']) === 0) {
            echo "Δεν υπάρχουν υποψήφιοι για ανάλυση.";
            exit;
        }

        $highestAverage = 0;
        $highestCandidates = [];
        $successfulCount = 0;
        $overallAverage = 0;

        foreach ($_SESSION['candidates'] as $candidate) {
            $average = $candidate['average'];
            $overallAverage += $average;

            if ($average > 60) {
                $successfulCount++;
            }

            if ($average > $highestAverage) {
                $highestAverage = $average;
                $highestCandidates = [$candidate['name']];
            } elseif ($average === $highestAverage) {
                $highestCandidates[] = $candidate['name'];
            }
        }

        $overallAverage /= $totalCandidates;
        $successRate = ($successfulCount / $totalCandidates) * 100;

        echo "<h1> Αποτελέσματα: </h1>";
        if (count($highestCandidates) === 1) {
            echo "Υποψήφιος με τον μεγαλύτερο μέσο όρο: " . $highestCandidates[0] . "<br>";
        } else {
            echo "Πλήθος υποψηφίων με τον μεγαλύτερο μέσο όρο: " . count($highestCandidates) . "<br>";
        }
        echo "Ποσοστό επιτυχόντων: " . number_format($successRate, 2) . "%<br>";
        echo "Συνολικός μέσος όρος: " . number_format($overallAverage, 2) . "<br>";

        $file = fopen("results.txt", "w");
        foreach ($_SESSION['candidates'] as $candidate) {
            fwrite($file, "Όνομα: " . $candidate['name'] . "\n");
            fwrite($file, "Βαθμοί: " . implode(", ", $candidate['scores']) . "\n");
            fwrite($file, "Μέσος όρος: " . number_format($candidate['average'], 2) . "\n\n");
        }
        fwrite($file, "Συνολικός μέσος όρος για όλους τους υποψηφίους: " . number_format($overallAverage, 2) . "\n");
        fclose($file);

        session_destroy();
        exit;
    }

    $average = array_sum($scores) / count($scores);
    $status = $average > 60 ? "ΕΠΙΤΥΧΩΝ" : "ΑΠΟΤΥΧΩΝ";

    $_SESSION['candidates'][] = [
        'name' => $name,
        'scores' => $scores,
        'average' => $average
    ];

    echo "<h1> Αποτελέσματα Υποψηφίου: </h1>";
    echo "Όνομα: $name <br>";
    echo "Μέσος όρος: " . number_format($average, 2) . "<br>";
    echo "Κατάσταση: $status<br>";
}
?>
