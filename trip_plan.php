<?php
session_start(); // Start session to pass trips

// Connect to the database
$conn = new mysqli("localhost", "root", "", "demo_travel");

// Fetch the city and days from the form submission
$city = $_POST['city'];
$days = intval($_POST['days']);

// Fetch locations in the selected city and create a mapping of ID to name
$locations = [];
$locationNames = [];
$sql = "SELECT id, name FROM locations WHERE city = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $city);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $locations[] = $row;
    $locationNames[$row['id']] = $row['name'];  // Map ID to name
}

// Fetch routes between locations and create a graph structure
$graph = [];
$sql = "SELECT * FROM Routes";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $graph[$row['source']][] = [
        'destination' => $row['destination'],
        'days_required' => $row['days_required']
    ];
}

// DFS to generate trip options
function findTrips($graph, $start, $daysLeft, $path = [], &$allTrips = [], $locationNames) {
    if (isset($locationNames[$start])) {
        $path[] = $start; // Save by ID (important for review lookup)
    } else {
        return;
    }

    if ($daysLeft <= 0) {
        if (count($path) > 1) {
            $allTrips[] = $path;
        }
        return;
    }

    if (isset($graph[$start])) {
        foreach ($graph[$start] as $neighbor) {
            $nextLocation = $neighbor['destination'];
            $daysRequired = $neighbor['days_required'];

            if ($daysRequired <= $daysLeft) {
                findTrips($graph, $nextLocation, $daysLeft - $daysRequired, $path, $allTrips, $locationNames);
            }
        }
    }
}

$startLocation = $locations[0]['id'];
$totalDays = $days;
$allTrips = [];
findTrips($graph, $startLocation, $totalDays, [], $allTrips, $locationNames);

// Save trips in session
$_SESSION['allTrips'] = $allTrips;
$_SESSION['locationNames'] = $locationNames;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Trip Options</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Trip Options for <?php echo htmlspecialchars($city); ?></h1>
    <?php include("trip_results.php"); ?>
</body>
</html>
