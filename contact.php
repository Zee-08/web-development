
<!doctype html>
<html lang="en">
  	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<title>CHOLO GHURE ASHI</title>

		<!-- Template CSS -->
		<link rel="stylesheet" href="assets/css/style-starter.css">
		<link href="//fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
		<link href="//fonts.googleapis.com/css2?family=Roboto:wght@300;400&display=swap" rel="stylesheet">
	</head>
  	<body id="home">
		<section class=" w3l-header-4 header-sticky">
			<!--header-->
			<?php 
				include("inc/header.php");
			?>
			<!--/header-->
		</section>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

		<!-- Latest compiled JavaScript -->
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
		

		<!-- breadcrumbs -->
		<section class="w3l-inner-banner-main">
			<div class="about-inner contact">
				
		
		</div>
		</section>
		<!-- breadcrumbs //-->
		<section class="w3l-contact-info-main" id="contact">
			<div class="contact-sec	editContent">
				<div class="container">
					<div class="map-content-9 mb-5 ">
						<form action="contactconnection.php" method="post">
							<div class="twice-two">
								<input type="text" class="form-control" name="Name" id="w3lName" placeholder="Name" required="">
								<input type="email" class="form-control" name="Email" id="w3lSender" placeholder="Email" required="">
								<input type="text" class="form-control" name="Subject" id="w3lSubject" placeholder="Subject" required="">
							</div>
							<textarea class="form-control" id="Message" name="Message" placeholder="Message" required=""></textarea>

							<div class="text-right">
								<button type="submit" name="submit" class="btn btn-contact">Send Message</button>
							</div>
						</form>
					</div>

					<div class="d-grid contact-view">

						<div class="cont-details">
							<h3 class="sub-title">Quick Contact</h3> 
							<p class="para mt-3 mb-4">Don't want to wait? Reach out to us. We are available from 10am to 7pm daily!</p>
							<div class="cont-top">
								<div class="cont-left text-center">
									<span class="fa text-secondary"></span>
								</div>
								<div class="cont-right">
									<p class="para"><a href="tel:+44 99 555 42">+91 9999999999</a></p>
									<p class="para"><a href="mailto:chologhureashi@contact.com" class="mail">chologhureashi@contact.com</a></p>
									<p class="para"> Chowbaga Rd, Mundapara, <br> Chak Kolarkhal, <br> Kolkata, West Bengal 700107</p>
								</div>
							</div>
							
						</div>
						
						<div class="map-iframe ">
							<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3685.6737520551587!2d88.41589267436923!3d22.51642057953176!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a02740a6095d3f7%3A0xac556ea09ce96171!2sThe%20Heritage%20Academy!5e0!3m2!1sen!2sin!4v1734882265002!5m2!1sen!2sin" width="100%" height="300" frameborder="0" style="border: 0px; pointer-events: none;" allowfullscreen=""></iframe>
						</div>
					</div>

				</div>
			</div>
		</section>
		<!--footer-->
			<?php 
			include("inc/footer.php");
			?>
		<!--/footer-->

		
	</body>

</html>
