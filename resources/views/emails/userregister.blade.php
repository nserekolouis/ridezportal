<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Welcome</title>
	<style type="text/css" media="screen">

		.ExternalClass * {line-height: 100%}

		/* Début style responsive (via media queries) */

		@media only screen and (max-width: 480px) {
            *[id=email-penrose-conteneur] {width: 100% !important;}
            table[class=resp-full-table] {width: 100%!important; clear: both;}
            td[class=resp-full-td] {width: 100%!important; clear: both;}
            img[class="email-penrose-img-header"] {width:100% !important; max-width: 340px !important;}
        }

        /* Fin style responsive */

	</style>

</head>
<body style="background-color:#ecf0f1">
<div align="center" style="background-color:#ecf0f1;">

		<!-- Début en-tête -->

	<table id="email-penrose-conteneur" width="660" align="center" style="padding:20px 0px;" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td>
				<table width="660" class="resp-full-table" align="center" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="50%" style="text-align:left;">
							<a href="#" style="text-decoration:none;"><h3 style="font-size: 25px;font-family: 'Helvetica Neue', helvetica, arial, sans-serif;font-weight: bold;color: #6B6B6B;margin: 0;"><?php  $siteTitle = Config::get('app.website_title'); echo $siteTitle;  ?>
            </a></h3></a>
						</td>
						<td width="50%" style="text-align:right;">
							<table align="right" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<h5 style="font-size: 20px;font-family: 'Helvetica Neue', helvetica, arial, sans-serif;font-weight: bold;color: #6B6B6B;margin: 0;"><?php echo date("d-m-Y");?></h5>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

		<!-- Fin en-tête -->

	<table id="email-penrose-conteneur" width="660" align="center" style="border-right:1px solid #e2e8ea; border-bottom:1px solid #e2e8ea; border-left:1px solid #e2e8ea; background-color:#ffffff;" border="0" cellspacing="0" cellpadding="0">

		<!-- Début bloc "mise en avant" -->

		<tr>
			<td style="background-color:#2ecc71">
				<table width="660" class="resp-full-table" align="center" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td class="resp-full-td" valign="top" style="padding:20px; text-align:center;">
							<span style="font-size:25px; font-family:'Helvetica Neue', helvetica, arial, sans-serif; font-weight:100; color:#ffffff"><a href="#" style="color:#ffffff; outline:none; text-decoration:none;">Welcome to <?php  $siteTitle = Config::get('app.website_title'); echo $siteTitle;  ?>
            </a>, <?php echo $email_data['name']; ?></a> </span>
						</td>
					</tr>
					<!-- <tr>
						<td width="100%" class="resp-full-td" valign="top" style="padding: 0px 20px 20px 20px;">
							<table align="center" border="0" cellspacing="0" cellpadding="0" style="margin:auto; padding:auto;">
								<tr>
									<td style="background-color:#ffffff; border-radius:3px; padding: 10px 40px;">
										<a style="font-family:'Helvetica Neue', helvetica, arial, sans-serif; text-align: center; text-decoration: none; display:block; color:#2ecc71; font-weight : 200; font-size: 25px;" href="#">J'en profite</a>
									</td>
								</tr>
							</table>
						</td>
					</tr> -->
				</table>
			</td>
		</tr>

	



		<!-- Début article 1 -->

		<tr>
			<td style="border-bottom: 1px solid #e2e8ea">
				<table width="660" class="resp-full-table" align="center" border="0" cellspacing="0" cellpadding="0" style="padding:20px;">
					<tr>
						<td width="100%">
							
							<table width="100%" align="right" class="resp-full-table" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td width="100%" class="resp-full-td" valign="top" style="text-align : justify;">
										<div style="padding: 10px;font-size:22px; font-family:'Helvetica Neue', helvetica, arial, sans-serif; font-weight:100;color: #2ECC71;text-align:center;">Thanks for signing up! We're excited to have you join us and start riding.
										</div>

										<div style="padding: 10px;font-size:12px; font-family:'Helvetica Neue', helvetica, arial, sans-serif; font-weight:100; color:#545454;text-align:center;">Just request a pickup, and in minutes a car will be curbside and ready to take you wherever you need to go.
										</div>

										<div style="padding: 10px;font-size:20px; font-family:'Helvetica Neue', helvetica, arial, sans-serif; font-weight:100;background-color: #2ECC71;color:#fff;text-align:center;">3 Steps to Ride
										</div>
										<br>
										<div style="padding: 10px;font-size:18px; font-family:'Helvetica Neue', helvetica, arial, sans-serif; font-weight:100; color:#545454;">1. Order a Car 
										</div>
										<div style="padding: 0px 10px;font-size:12px; font-family:'Helvetica Neue', helvetica, arial, sans-serif; font-weight:100;color:#545454;">Use the iPhone or Android app to request a ride. 
										</div>

										<br>
										<div style="padding: 10px;font-size:18px; font-family:'Helvetica Neue', helvetica, arial, sans-serif; font-weight:100; color:#545454;">2. Your Driver Comes to You 
										</div>
										<div style="padding: 0px 10px;font-size:12px; font-family:'Helvetica Neue', helvetica, arial, sans-serif; font-weight:100;color:#545454;">Sit back and relax. We'll text you when your <?php /* echo "App Name"; */ echo ucwords(Config::get('app.website_title')) ?> arrives. 
										</div>

										<br>
										<div style="padding: 10px;font-size:18px; font-family:'Helvetica Neue', helvetica, arial, sans-serif; font-weight:100; color:#545454;">3. Hop in & Hop out
										</div>
										<div style="padding: 0px 10px;font-size:12px; font-family:'Helvetica Neue', helvetica, arial, sans-serif; font-weight:100;color:#545454;">After arriving at your destination, we'll charge your credit card on file and email you a receipt.
										</div>
										<br><br>

										<div style="text-align:center"><a href="taxinow.xyz" style="padding: 10px;font-size:18px; font-family:'Helvetica Neue', helvetica, arial, sans-serif; font-weight:100;background-color: #2ECC71;color:#fff;text-align:center;  text-decoration: none;">Go to Site </a></div>
										<br>
									</td>
								</tr>
				
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<!-- Fin article 1 -->

	</table>

	<!-- Début footer -->


	<!-- Fin footer -->

</div>
</body>
</html>