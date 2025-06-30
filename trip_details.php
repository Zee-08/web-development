<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_GET['package']) || !isset($_SESSION['locationNames'])) {
    echo "<div class='container mt-5'><p>Trip details unavailable.</p></div>";
    exit();
}

$locationNames = $_SESSION['locationNames'];
$trip = array_map('intval', explode(',', $_GET['package']));

// Database connection
$conn = new mysqli("localhost", "root", "", "demo_travel");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get sentiment scores
$placeholders = implode(',', $trip);
$query = "SELECT location_id, AVG(sentiment_score) AS avg_sentiment FROM reviews WHERE location_id IN ($placeholders) GROUP BY location_id";
$sentimentScores = [];
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $sentimentScores[$row['location_id']] = round($row['avg_sentiment'], 2);
}

// Calculate total duration
$duration = 0;
if (count($trip) > 1) {
    $stmt = $conn->prepare("
        SELECT SUM(days_required) AS total_days 
        FROM routes 
        WHERE (source = ? AND destination = ?) " . 
        str_repeat(" OR (source = ? AND destination = ?)", count($trip) - 2)
    );
    $types = str_repeat("ii", count($trip) - 1);
    $params = [];
    for ($i = 0; $i < count($trip) - 1; $i++) {
        $params[] = $trip[$i];
        $params[] = $trip[$i + 1];
    }
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $stmt->bind_result($duration);
    $stmt->fetch();
    $stmt->close();
}

// Get image for banner
$firstId = $trip[0];
$imgResult = $conn->query("SELECT image FROM locations WHERE id = $firstId");
$imgPath = ($imgResult && $imgResult->num_rows > 0) ? $imgResult->fetch_assoc()['image'] : "";

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Trip Details - Cholo Ghure Ashi</title>
    <link rel="stylesheet" href="assets/css/style-starter.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include("inc/header.php"); ?>

<section class="w3l-trip-details-banner">
    <div class="container py-4">
        <br>
        <h2 class="mb-3">Your Customized Travel Package</h2>

        <?php if ($imgPath): ?>
            <img src="admin/place-image/<?= htmlspecialchars($imgPath) ?>" class="img-fluid rounded mb-3" alt="Banner image">
        <?php endif; ?>
        <p><strong>Total Duration:</strong> <?= $duration ?: "N/A" ?> days</p>
        <p><strong>Total Stops:</strong> <?= count($trip) ?></p>
        <p><strong>Overall Rating:</strong>
            <?php
            $ratings = array_intersect_key($sentimentScores, array_flip($trip));
            echo count($ratings) ? round(array_sum($ratings) / count($ratings), 2) . " ★" : "N/A";
            ?>
        </p>
    </div>
</section>

<section class="w3l-itinerary-details py-4">
    <div class="container">
        <h4>Day-wise Itinerary</h4>
        <div class="row">
            <?php foreach ($trip as $index => $locId): ?>
                <div class="col-md-6 mb-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Spot <?= $index + 1 ?>: <?= htmlspecialchars($locationNames[$locId]) ?></h5>
                            <p class="card-text">
                                <?= isset($sentimentScores[$locId]) ? "Rated " . $sentimentScores[$locId] . " ★ by visitors." : "No recent feedback available." ?>
                            </p>
                            <a href="place_details.php?id=<?= $locId ?>" class="btn btn-sm btn-outline-secondary">Know More</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<div class="container mt-4 text-start">
    <a href="trip_results.php" class="btn btn-outline-dark btn-sm">
        &larr; Back to Results
    </a>
</div>


<?php include("inc/footer.php"); ?>

</body>
</html>
