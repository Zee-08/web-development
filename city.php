<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "demo_travel");

// Check if the city parameter is set in the URL
if (isset($_GET['city'])) {
    $city = $_GET['city'];

    // Fetch places for the selected city
    $stmt = $conn->prepare("SELECT id, heading, image FROM locations WHERE city = ?");
    $stmt->bind_param("s", $city);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if places are found
    if ($result->num_rows > 0) {
        $cityPlaces = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        echo "<h1>No places found for this city.</h1>";
        exit;
    }
} else {
    echo "<h1>Invalid city selected.</h1>";
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars(ucfirst($city)); ?> - Explore</title>
  <link rel="stylesheet" href="assets/css/style-starter.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<section class="w3l-header-4 header-sticky">
  <?php include("inc/header.php"); ?>
</section>

<section class="w3l-inner-banner-main">
  <div class="about-inner about editContent"></div>
</section>

<section class="city-head" id="about">
  <div class="content-with-photo4-block editContent">
    <div class="container">
      <div class="my-bio">
        <h3 class="mt-lg-4 mt-3">Explore <?php echo htmlspecialchars(ucfirst($city)); ?></h3>
      </div>
    </div>
  </div>
</section>

<section class="city-list mt-4 mb-5">
  <div class="container">
    <div class="row">
      <?php foreach ($cityPlaces as $place): ?>
        <div class="col-md-4 mb-4">
          <div class="card h-100 shadow-sm border-0">
            <img src="admin/place-image/<?php echo htmlspecialchars($place['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($place['heading']); ?>" style="height: 200px; object-fit: cover;">
            <div class="card-body">
              <h5 class="card-title"><?php echo htmlspecialchars($place['heading']); ?></h5>
              <a href="place_details.php?id=<?php echo $place['id']; ?>" class="btn btn-outline-primary btn-sm mt-2">View Details</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<div class="container mb-4">
  <a href="services.php" class="btn btn-outline-dark btn-sm">&larr; Back</a>
</div>

<?php include("inc/footer.php"); ?>
</body>
</html>
