<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "demo_travel");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Check if ID is set and valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $locationId = $_GET['id'];

    // Fetch location details
    $stmt = $conn->prepare("SELECT * FROM locations WHERE id = ?");
    $stmt->bind_param("i", $locationId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $location = $result->fetch_assoc();

        // Now fetch reviews for this location
        $stmtReviews = $conn->prepare("SELECT rating, review, review_date FROM reviews WHERE location_id = ? ORDER BY review_date DESC");
        $stmtReviews->bind_param("i", $locationId);
        $stmtReviews->execute();
        $reviewsResult = $stmtReviews->get_result();

    } else {
        echo "<h2 class='text-center mt-5'>No details found for this location.</h2>";
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
        // Sanitize inputs
        $locationIdPost = intval($_POST['location_id']);
        $rating = intval($_POST['rating']);
        $reviewText = trim($_POST['review']);
        $reviewDate = date('Y-m-d');

        // Validate rating
        if ($rating < 1 || $rating > 5) {
            echo "<p class='text-danger'>Rating must be between 1 and 5.</p>";
        } elseif (empty($reviewText)) {
            echo "<p class='text-danger'>Review cannot be empty.</p>";
        } else {
            // Insert review into DB
            $stmtInsert = $conn->prepare("INSERT INTO reviews (location_id, rating, review, review_date) VALUES (?, ?, ?, ?)");
            $stmtInsert->bind_param("iiss", $locationIdPost, $rating, $reviewText, $reviewDate);

            if ($stmtInsert->execute()) {
                echo "<p class='text-success'>Thank you for your review!</p>";
                
            } else {
                echo "<p class='text-danger'>Error submitting review. Please try again later.</p>";
            }
        }
    }

} else {
    echo "<h2 class='text-center mt-5'>Invalid place selected.</h2>";
    exit;
}
?>


<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($location['heading']); ?> - Details</title>
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

<section class="place-details py-5">
  <div class="container">
    <h2 class="mb-4"><?php echo htmlspecialchars($location['heading']); ?></h2>
    
    <img src="admin/place-image/<?php echo htmlspecialchars($location['image']); ?>" alt="<?php echo htmlspecialchars($location['heading']); ?>" class="img-fluid mb-4 rounded shadow-sm" style="max-height: 500px; object-fit: cover;">

    <p><?php echo html_entity_decode($location['details']); ?></p>

    <div class="mt-4">
      <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">&larr; Go Back</a>
    </div>
  </div>
</section>
<section class="review-form mt-5">
    <div class="container">
  <h4>Leave a Review</h4>
  <form method="POST" action="">
    <input type="hidden" name="location_id" value="<?= htmlspecialchars($locationId) ?>">

    <div class="mb-3">
      <label for="rating" class="form-label">Rating (1 to 5):</label>
      <input type="number" id="rating" name="rating" min="1" max="5" required class="form-control">
    </div>

    <div class="mb-3">
      <label for="review" class="form-label">Your Review:</label>
      <textarea id="review" name="review" rows="4" required class="form-control"></textarea>
    </div>

    <button type="submit" name="submit_review" class="btn btn-primary">Submit Review</button>
  </form>
  </div>
</section>

<section class="reviews mt-5">
    <div class="container">
        <h4>Reviews</h4>
        <?php if ($reviewsResult->num_rows > 0): ?>
            <ul class="list-group">
                <?php while ($review = $reviewsResult->fetch_assoc()): ?>
                    <li class="list-group-item">
                        <span class="text-warning"><?= str_repeat('★', (int)$review['rating']) ?></span>
                        <small class="text-muted"> on <?= date("M d, Y", strtotime($review['review_date'])) ?></small>
                        <p><?= nl2br(htmlspecialchars($review['review'])) ?></p>
                    </li>
                <?php endwhile; ?>
            </ul>
            <?php else: ?>
            <p>No reviews yet for this place.</p>
        <?php endif; ?>
    </div>
</section>

<?php include("inc/footer.php"); ?>
</body>
</html>
