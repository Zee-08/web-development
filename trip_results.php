<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CHOLO GHURE ASHI</title>
    <link rel="stylesheet" href="assets/css/style-starter.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body id="home">

<section class="w3l-header-4 header-sticky">
    <?php include("inc/header.php"); ?>
</section>

<section class="w3l-inner-banner-main"><div class="about-inner services editContent"></div></section>

<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['allTrips']) || !isset($_SESSION['locationNames'])) {
    echo "<div class='container mt-5'><p>No trip data available.</p></div>";
    exit();
}

$allTrips = $_SESSION['allTrips'];
$locationNames = $_SESSION['locationNames'];

// Get all unique location IDs from trips
$locationIds = array_unique(array_merge(...$allTrips));
$locationIds = array_map('intval', $locationIds);

// DB connection
$conn = new mysqli("localhost", "root", "", "demo_travel");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch average sentiment scores for each location
$idList = implode(',', $locationIds);
$sentimentScores = [];
if (!empty($idList)) {
    $query = "SELECT location_id, AVG(sentiment_score) AS avg_sentiment, AVG(rating) as avg_rating
              FROM reviews 
              WHERE location_id IN ($idList)
              GROUP BY location_id";
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        $sentimentScores[$row['location_id']] = round($row['avg_sentiment'], 2);
    }
    while ($row = $result->fetch_assoc()) {
        $Rating[$row['location_id']] = round($row['avg_rating'], 1);
    }
}

// Score each trip (package)
$scoredTrips = [];
foreach ($allTrips as $trip) {
    $total = 0; $count = 0; $count2=0;
    foreach ($trip as $locId) {
        if (isset($sentimentScores[$locId])) {
            $total += $sentimentScores[$locId];
            $count++;
        }
        if (isset($Rating[$locId])) {
            $total2 += $Rating[$locId];
            $count2++;
        }
    }
    $average = $count ? $total / $count : 0;
    $avgrat = $count2 ? $total2/$count2:0;
    $scoredTrips[] = ['trip' => $trip, 'average_sentiment' => $average,'average_rating'=>$avgrat];
}

// Sort by best sentiment
usort($scoredTrips, fn($a, $b) => $b['average_sentiment'] <=> $a['average_sentiment']);
$topTrips = array_slice($scoredTrips, 0, 3);
?>

<section class="w3l-trip-results" id="services">
    <div class="results-single-page editContent">
        <div class="container">
            <h2 class="mb-4">Best Rated Packages</h2>

            <?php if (!empty($topTrips)): ?>
            <div class="row">
                <?php foreach ($topTrips as $tripData): 
                    $trip = $tripData['trip'];
                    $avgSentiment = round($tripData['average_sentiment'], 2);
                    $avgRating=round($tripData['average_rating'], 1);
                    // Location names
                    $names = array_map(fn($id) => $locationNames[$id], $trip);
                    $highlights = implode(", ", array_slice($names, 0, 3));
                    if (count($names) > 3) $highlights .= ", and more";

                    // Duration calculation
                    $duration = 0;
                    if (count($trip) > 1) {
                        $routeStmt = $conn->prepare("
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
                        $routeStmt->bind_param($types, ...$params);
                        $routeStmt->execute();
                        $routeStmt->bind_result($duration);
                        $routeStmt->fetch();
                        $routeStmt->close();
                    }

                    // First image
                    $firstId = $trip[0];
                    $imgQuery = $conn->query("SELECT image FROM locations WHERE id = $firstId");
                    $imgPath = ($imgQuery && $imgQuery->num_rows > 0) ? $imgQuery->fetch_assoc()['image'] : "";
                ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php if (!empty($imgPath)): ?>
                            <img src="admin/place-image/<?= htmlspecialchars($imgPath) ?>" class="card-img-top" alt="<?= htmlspecialchars($names[0]) ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title">Trip to <?= htmlspecialchars($names[0]) ?></h5>
                            <p><strong>Duration:</strong> <?= $duration ?: "N/A" ?> days</p>
                            <p><strong>Destinations:</strong> <?= count($trip) ?></p>
                            <p><strong>Highlights:</strong> <?= htmlspecialchars($highlights) ?></p>
                            <!--<p><strong>Rating:</strong> <?= $avgRating ?> ★</p>-->
                            <a href="trip_details.php?package=<?= urlencode(implode(",", $trip)) ?>" class="btn btn-outline-primary btn-sm mt-2">View Itinerary</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
                <p>No trip packages available at the moment.</p>
            <?php endif; ?>

            <?php $conn->close(); ?>
        </div>
    </div>
</section>
<div class="container mt-4 text-start">
    <a href="index.php" class="btn btn-outline-dark btn-sm">
        &larr; Back to Home Page
    </a>
</div>
<?php include("inc/footer.php"); ?>
</body>
</html>
