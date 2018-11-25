<?php include 'header.php'; ?>

<?php 
ob_start();
session_start();
?>

<body>

	<div class="container">	
		<form action="nedmin/netting/islem.php" method="POST" class="form-horizontal checkout" role="form">
			<div class="row">
				<div class="col-md-6">
					<div class="title-bg">
						<div class="title">Üyelik Bilgileri Düzenleme</div>
					</div>

					<?php 
					/*
					if ($_GET['durum']=="sifrefarkli") {?>

						<div class="alert alert-danger">
							<strong>Hata!</strong> Girdiğiniz şifreler eşleşmiyor.
						</div>

					<?php } elseif ($_GET['durum']=="sifreeksik") {?>


						<div class="alert alert-danger">
							<strong>Hata!</strong> Şifreniz minimum 6 karakter uzunluğunda olmalıdır.
						</div>

					<?php } elseif ($_GET['durum']=="kayittekrari") {?>

						<div class="alert alert-danger">
							<strong>Hata!</strong> Bu kullanıcı daha önce kayıt edilmiş.
						</div>

					<?php } elseif ($_GET['durum']=="basarisiz") {?>

						<div class="alert alert-danger">
							<strong>Hata!</strong> Kayıt Yapılamadı Sistem Yöneticisine Danışınız.
						</div>

					<?php }
					*/
					?>

					<?php 

					$kullanicisor=$db->prepare("SELECT * from kullanici where kullanici_mail=:mail");
					$kullanicisor->execute(array(

						'mail'=>$_SESSION['kullanici_mail']

					));

					$kullanicicek=$kullanicisor->fetch(PDO::FETCH_ASSOC);

					?>

					<div class="form-group dob">
						<div class="col-sm-6">
							<input type="text" class="form-control" id="name" name="kullanici_ad" placeholder="Adınız" readonly="" value="<?php echo $kullanicicek['kullanici_ad']; ?>">
						</div>
						<div class="col-sm-6">
							<input type="text" class="form-control" id="last_name" name="kullanici_soyad" placeholder="Soyadınız" readonly="" value="<?php echo $kullanicicek['kullanici_soyad']; ?>">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-12">
							<input type="email" class="form-control" id="email" name="kullanici_mail" placeholder="Mail Adresiniz" readonly="" value="<?php echo $kullanicicek['kullanici_mail']; ?>">
						</div>
					</div>

					<div class="form-group dob">
						<div class="col-sm-6">
							<input type="number" class="form-control" id="tc" name="kullanici_tc" placeholder="Tc Kimlik Numaranızı Giriniz">
						</div>
					</div>

					<div class="form-group dob">
						<div class="col-sm-6">
							<input type="password" class="form-control" name="kullanici_password"    placeholder="Şifrenizi Giriniz...">
						</div>
					</div>


					<!--
					<div class="form-group dob">
						<div class="col-sm-6">
							<input type="password" class="form-control" name="kullanici_passwordone"    placeholder="Şifrenizi Giriniz...">
						</div>
						<div class="col-sm-6">
							<input type="password" class="form-control" name="kullanici_passwordtwo"   placeholder="Şifrenizi Tekrar Giriniz...">
						</div>
					</div>
				-->

				<input type="hidden" name="kullanici_id" value="<?php echo $kullanicicek['kullanici_id']; ?>">
				<!--<input type="hidden" name="kullanici_tc" value="<?php echo $kullanicicek['kullanici_tc']; ?>">-->

				<button class="btn btn-default btn-red" name="hesabimGuncelle">Güncelle</button>
			</div>
			<div class="col-md-6">
				<h4>Buraya Açıklamalar vs gibi şeyler gelecek</h4>
			</div>

				<!-- burası adres kısmı
				<div class="col-md-6">
					<div class="title-bg">
						<div class="title">Your address</div>
					</div>
					<div class="form-group">
						<div class="col-sm-12">
							<input type="text" class="form-control" id="company" placeholder="Company">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-12">
							<input type="text" class="form-control" id="address" placeholder="Address">
						</div>
					</div>
					<div class="form-group dob">
						<div class="col-sm-6">
							<input type="text" class="form-control" id="city" placeholder="city">
						</div>
						<div class="col-sm-6">
							<input type="text" class="form-control" id="postcode" placeholder="Post Code">
						</div>
					</div>
					<div class="form-group dob">
						<div class="col-sm-6">
							<input type="text" class="form-control" id="country" placeholder="country">
						</div>
						<div class="col-sm-6">
							<input type="text" class="form-control" id="state" placeholder="State">
						</div>
					</div>
				</div>
			-->
		</div>
	</form>
	<div class="spacer"></div>
</div>
</div>
<?php include 'footer.php'; ?>