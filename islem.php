<?php
ob_start();
session_start();

include 'baglan.php';
//echo "deneme";

include '../production/fonksiyon.php';


//yeni kullanıcı ekleme
if (isset($_POST['uyeOl'])) {

	echo $kullanici_mail=htmlspecialchars($_POST['kullanici_mail']);
	echo $kullanici_ad=htmlspecialchars($_POST['kullanici_ad']);
	echo $kullanici_soyad=htmlspecialchars($_POST['kullanici_soyad']);
	echo $kullanici_passwordone=htmlspecialchars($_POST['kullanici_passwordone']);
	echo $kullanici_passwordtwo=htmlspecialchars($_POST['kullanici_passwordtwo']);

	//exit();

	if ($kullanici_passwordone==$kullanici_passwordtwo) 
	{
		if (strlen($kullanici_passwordone)>=6) 
		{	
			//echo "buraya geldiniz";
			//exit();
			// Başlangıç

			$kullanicisor=$db->prepare("select * from kullanici where kullanici_mail=:mail");
			$kullanicisor->execute(array(
				'mail' => $kullanici_mail
			));

			//dönen satır sayısını belirtir
			$say=$kullanicisor->rowCount();

			if ($say==0) {

				//md5 fonksiyonu şifreyi md5 şifreli hale getirir.
				$password=md5($kullanici_passwordone);

				//$kullanici_yetki=1;

			//Kullanıcı kayıt işlemi yapılıyor...
				$kullanicikaydet=$db->prepare("INSERT INTO kullanici SET
					kullanici_ad=:kullanici_ad,
					kullanici_soyad=:kullanici_soyad,
					kullanici_mail=:kullanici_mail,
					kullanici_password=:kullanici_password
					");

				$insert=$kullanicikaydet->execute(array(
					'kullanici_ad' => $kullanici_ad,
					'kullanici_soyad' => $kullanici_soyad,
					'kullanici_mail' => $kullanici_mail,
					'kullanici_password' => $password
					));

				if ($insert) {	

					header("Location:../../index.php?durum=kayitbasarili");

				} else {

					header("Location:../../register.php?durum=basarisiz");
				}

			} else {

				header("Location:../../register.php?durum=kayittekrari");

			}

			// Bitiş
			
		}

		else
		{
			header("location:../../register.php?durum=sifreeksik");
		}
	} 

	else
	{
		header("location:../../register.php?durum=sifrefarkli");
	}


}

//slider ekleme
if (isset($_POST['sliderEkle'])) {

	$uploads_dir = '../../dimg/slider';
	@$tmp_name = $_FILES['slider_resim']["tmp_name"];
	@$name = $_FILES['slider_resim']["name"];
	//resmin isminin benzersiz olması
	$benzersizsayi1=rand(20000,32000);
	$benzersizsayi2=rand(20000,32000);
	$benzersizsayi3=rand(20000,32000);
	$benzersizsayi4=rand(20000,32000);	
	$benzersizad=$benzersizsayi1.$benzersizsayi2.$benzersizsayi3.$benzersizsayi4;
	$refimgyol=substr($uploads_dir, 6)."/".$benzersizad.$name;
	@move_uploaded_file($tmp_name, "$uploads_dir/$benzersizad$name");
	


	$kaydet=$db->prepare("INSERT INTO slider SET
		slider_ad=:slider_ad,
		slider_sira=:slider_sira,
		slider_url=:slider_url,
		slider_durum=:slider_durum,
		slider_resim=:slider_resim
		");
	$insert=$kaydet->execute(array(
		'slider_ad' => $_POST['slider_ad'],
		'slider_sira' => $_POST['slider_sira'],
		'slider_url' => $_POST['slider_url'],
		'slider_durum' => $_POST['slider_durum'],
		'slider_resim' => $refimgyol
	));

	if ($insert) {

		Header("Location:../production/slider-listesi.php?durum=guncellemebasarili");

	} else {

		Header("Location:../production/slider-listesi.php?durum=guncellemebasarisiz");
	}

}

//logo düzenleme işlemi
if (isset($_POST['logoDuzenle'])) {

	$uploads_dir = '../../dimg';

	@$tmp_name = $_FILES['ayarlar_logo']["tmp_name"];
	@$name = $_FILES['ayarlar_logo']["name"];

	$benzersizsayi4=rand(20000,32000);
	$refimgyol=substr($uploads_dir, 6)."/".$benzersizsayi4.$name;

	@move_uploaded_file($tmp_name, "$uploads_dir/$benzersizsayi4$name");

	
	$duzenle=$db->prepare("UPDATE ayarlar SET
		ayarlar_logo=:logo
		WHERE ayarlar_id=0");
	$update=$duzenle->execute(array(
		'logo' => $refimgyol
	));

	if ($update) {

		$resimsilunlink=$_POST['eski_yol'];
		unlink("../../$resimsilunlink");

		Header("Location:../production/genel-ayarlar.php?durum=guncellemebasarili");

	} else {

		Header("Location:../production/genel-ayarlar.php?durum=guncellemebasarisiz");
	}

}


//yonetici paneline giriş işlemleri
if (isset($_POST['yoneticiGiris'])) {
	//echo "merhaba";
	$yonetici_mail = $_POST['yonetici_mail'];
	$yonetici_password = md5($_POST['yonetici_password']);

	$yoneticisor=$db->prepare("SELECT * FROM yonetici where yonetici_mail=:mail and yonetici_password=:password");
	$yoneticisor->execute(array(
		'mail' => $yonetici_mail,
		'password' => $yonetici_password
	));

	$say=$yoneticisor->rowCount();

	if ($say==1) 
	{
		//echo "dogru";
		$_SESSION['yonetici_mail']=$yonetici_mail;
		header("location:../production/index.php");
		exit();
	}
	else
	{
		//echo "yanlis";
		//header("location:../production/deneme.php");
		header("location:../production/login.php?durum=girishatali");
		exit();
	}
}

//genel ayarlar tablo güncelleme işlemleri
if (isset($_POST['genelAyarlarKaydet'])) {
	//echo "dogru yer";
	$ayarlarkaydet=$db->prepare("UPDATE ayarlar SET

		ayarlar_title=:ayarlar_title,
		ayarlar_description=:ayarlar_description,
		ayarlar_keywords=:ayarlar_keywords,
		ayarlar_author=:ayarlar_author
		WHERE ayarlar_id=0");

	$update=$ayarlarkaydet->execute(array(
		'ayarlar_title' => $_POST['ayarlar_title'],
		'ayarlar_description' => $_POST['ayarlar_description'],
		'ayarlar_keywords' => $_POST['ayarlar_keywords'],
		'ayarlar_author' => $_POST['ayarlar_author']
	));

	if ($update) {
		//echo "güncelleme başarılı";
		header("location:../production/genel-ayarlar.php?durum=guncellemebasarili");
	}

	else {
		//echo "güncelleme başarısız";
		header("location:../production/genel-ayarlar.php?durum=guncellemebasarisiz");
	}
} 


//iletişim ayarlar tablo güncelleme işlemleri
if (isset($_POST['iletisimAyarlarKaydet'])) {
	//echo "dogru yer";
	$ayarlarkaydet=$db->prepare("UPDATE ayarlar SET

		ayarlar_tel=:ayarlar_tel,
		ayarlar_gsm=:ayarlar_gsm,
		ayarlar_faks=:ayarlar_faks,
		ayarlar_mail=:ayarlar_mail,
		ayarlar_ilce=:ayarlar_ilce,
		ayarlar_il=:ayarlar_il,
		ayarlar_adres=:ayarlar_adres
		WHERE ayarlar_id=0");

	$update=$ayarlarkaydet->execute(array(
		'ayarlar_tel' => $_POST['ayarlar_tel'],
		'ayarlar_gsm' => $_POST['ayarlar_gsm'],
		'ayarlar_faks' => $_POST['ayarlar_faks'],
		'ayarlar_mail' => $_POST['ayarlar_mail'],
		'ayarlar_ilce' => $_POST['ayarlar_ilce'],
		'ayarlar_il' => $_POST['ayarlar_il'],
		'ayarlar_adres' => $_POST['ayarlar_adres']
	));

	if ($update) {
		//echo "güncelleme başarılı";
		header("location:../production/iletisim-ayarlar.php?durum=guncellemebasarili");
	}

	else {
		//echo "güncelleme başarısız";
		header("location:../production/iletisim-ayarlar.php?durum=guncellemebasarisiz");
	}
} 


//api ayarlar tablo güncelleme işlemleri
if (isset($_POST['apiAyarlarKaydet'])) {
	//echo "dogru yer";
	$ayarlarkaydet=$db->prepare("UPDATE ayarlar SET

		ayarlar_analystic=:ayarlar_analystic,
		ayarlar_maps=:ayarlar_maps,
		ayarlar_zopim=:ayarlar_zopim
		WHERE ayarlar_id=0");

	$update=$ayarlarkaydet->execute(array(
		'ayarlar_analystic' => $_POST['ayarlar_analystic'],
		'ayarlar_maps' => $_POST['ayarlar_maps'],
		'ayarlar_zopim' => $_POST['ayarlar_zopim']
	));

	if ($update) {
		//echo "güncelleme başarılı";
		header("location:../production/api-ayarlar.php?durum=guncellemebasarili");
	}

	else {
		//echo "güncelleme başarısız";
		header("location:../production/api-ayarlar.php?durum=guncellemebasarisiz");
	}
} 


//sosyal ayarlar tablo güncelleme işlemleri
if (isset($_POST['sosyalAyarlarKaydet'])) {
	//echo "dogru yer";
	$ayarlarkaydet=$db->prepare("UPDATE ayarlar SET

		ayarlar_facebook=:ayarlar_facebook,
		ayarlar_twitter=:ayarlar_twitter,
		ayarlar_google=:ayarlar_google,
		ayarlar_youtube=:ayarlar_youtube
		WHERE ayarlar_id=0");

	$update=$ayarlarkaydet->execute(array(
		'ayarlar_facebook' => $_POST['ayarlar_facebook'],
		'ayarlar_twitter' => $_POST['ayarlar_twitter'],
		'ayarlar_google' => $_POST['ayarlar_google'],
		'ayarlar_youtube' => $_POST['ayarlar_youtube']
	));

	if ($update) {
		//echo "güncelleme başarılı";
		header("location:../production/sosyal-ayarlar.php?durum=guncellemebasarili");
	}

	else {
		//echo "güncelleme başarısız";
		header("location:../production/sosyal-ayarlar.php?durum=guncellemebasarisiz");
	}
} 


//mail ayarlar tablo güncelleme işlemleri
if (isset($_POST['mailAyarlarKaydet'])) {
	//echo "dogru yer";
	$ayarlarkaydet=$db->prepare("UPDATE ayarlar SET

		ayarlar_smtphost=:ayarlar_smtphost,
		ayarlar_smtpuser=:ayarlar_smtpuser,
		ayarlar_smtppassword=:ayarlar_smtppassword,
		ayarlar_smtpport=:ayarlar_smtpport
		WHERE ayarlar_id=0");

	$update=$ayarlarkaydet->execute(array(
		'ayarlar_smtphost' => $_POST['ayarlar_smtphost'],
		'ayarlar_smtpuser' => $_POST['ayarlar_smtpuser'],
		'ayarlar_smtppassword' => $_POST['ayarlar_smtppassword'],
		'ayarlar_smtpport' => $_POST['ayarlar_smtpport']
	));

	if ($update) {
		//echo "güncelleme başarılı";
		header("location:../production/mail-ayarlar.php?durum=guncellemebasarili");
	}

	else {
		//echo "güncelleme başarısız";
		header("location:../production/mail-ayarlar.php?durum=guncellemebasarisiz");
	}
} 


//hakkimizda tablo güncelleme işlemleri
if (isset($_POST['hakkimizdaKaydet'])) {
	//echo "dogru yer";
	$hakkimizdakaydet=$db->prepare("UPDATE hakkimizda SET

		hakkimizda_baslik=:hakkimizda_baslik,
		hakkimizda_icerik=:hakkimizda_icerik,
		hakkimizda_video=:hakkimizda_video,
		hakkimizda_vizyon=:hakkimizda_vizyon,
		hakkimizda_misyon=:hakkimizda_misyon
		WHERE hakkimizda_id=0");

	$update=$hakkimizdakaydet->execute(array(
		'hakkimizda_baslik' => $_POST['hakkimizda_baslik'],
		'hakkimizda_icerik' => $_POST['hakkimizda_icerik'],
		'hakkimizda_video' => $_POST['hakkimizda_video'],
		'hakkimizda_vizyon' => $_POST['hakkimizda_vizyon'],
		'hakkimizda_misyon' => $_POST['hakkimizda_misyon']
	));

	if ($update) {
		//echo "güncelleme başarılı";
		header("location:../production/hakkimizda.php?durum=guncellemebasarili");
	}

	else {
		//echo "güncelleme başarısız";
		header("location:../production/hakkimizda.php?durum=guncellemebasarisiz");
	}
}


//kullanıcı düzenleme 
if (isset($_POST['kullaniciDuzenle'])) 
{
	/*echo $_POST['kullanici_ad'];
	echo $_POST['kullaniciim_soyad'];
	echo $_POST['kullaniciim_aciklama'];*/

	$kullanici_id=$_POST['kullanici_id'];

	$kaydet=$db->prepare("UPDATE kullanici set
		kullanici_adsoyad=:kullanici_adsoyad,
		kullanici_tc=:kullanici_tc,
		kullanici_mail=:kullanici_mail,
		kullanici_adres=:kullanici_adres,
		kullanici_gsm=:kullanici_gsm
		where kullanici_id={$_POST['kullanici_id']}
		");

	$insert=$kaydet->execute(array(
		'kullanici_adsoyad'=>$_POST['kullanici_adsoyad'],
		'kullanici_tc'=>$_POST['kullanici_tc'],
		'kullanici_mail'=>$_POST['kullanici_mail'],
		'kullanici_adres'=>$_POST['kullanici_adres'],
		'kullanici_gsm'=>$_POST['kullanici_gsm']
	));


//düzenle butonundan sonra anasayfaya yönlendirme yapmak için bu blokla oynanmalı
	if ($insert) 
	{
	//echo "Kayıt Başarılı";
		Header("Location:../production/kullanici-listesi.php?durum=guncellemebasarili&kullanici_id=$kullanici_id");
		exit;
	}
	else
	{
	//echo "Kayıt Başarısız";
	//Header("Location:duzenle.php?kayit=basarisiz&kullanici_id=$kullanici_id");
		Header("Location:../production/kullanici-listesi.php?durum=guncellemebasarisiz&kullanici_id=$kullanici_id");
		exit;
	}
}


//Kullanıcı tablosundan silme işlemi
if ($_GET['kullanicisil']=="ok") 
{
	//echo "dogru";

	$sil=$db->prepare("DELETE from kullanici where kullanici_id=:id");
	$kontrol=$sil->execute(array(
		'id'=>$_GET['kullanici_id']
	));


	if ($kontrol) 
	{
	//echo "Kayıt Başarılı";
		Header("Location:../production/kullanici-listesi.php?durum=guncellemebasarili");
		exit;
	}
	else
	{
	//echo "Kayıt Başarısız";
		Header("Location:../production/kullanici-listesi.php?durum=guncellemebasarisiz");
		exit;
	}
}


//menu düzenleme 
if (isset($_POST['menuDuzenle'])) 
{
	/*echo $_POST['menu_ad'];
	echo $_POST['menuim_soyad'];
	echo $_POST['menuim_aciklama'];*/

	$menu_id=$_POST['menu_id'];

	$menu_seourl=seo($_POST['menu_ad']);

	$kaydet=$db->prepare("UPDATE menu set
		menu_ad=:menu_ad,
		menu_url=:menu_url,
		menu_sira=:menu_sira,
		menu_detay=:menu_detay,
		menu_seourl=:menu_seourl,
		menu_durum=:menu_durum
		where menu_id={$_POST['menu_id']}
		");

	$insert=$kaydet->execute(array(
		'menu_ad'=>$_POST['menu_ad'],
		'menu_url'=>$_POST['menu_url'],
		'menu_sira'=>$_POST['menu_sira'],
		'menu_detay'=>$_POST['menu_detay'],
		'menu_seourl'=>$menu_seourl,
		'menu_durum'=>$_POST['menu_durum']
	));


//düzenle butonundan sonra anasayfaya yönlendirme yapmak için bu blokla oynanmalı
	if ($insert) 
	{
	//echo "Kayıt Başarılı";
		Header("Location:../production/menu-listesi.php?durum=guncellemebasarili&menu_id=$menu_id");
		exit;
	}
	else
	{
	//echo "Kayıt Başarısız";
	//Header("Location:duzenle.php?kayit=basarisiz&menu_id=$menu_id");
		Header("Location:../production/menu-listesi.php?durum=guncellemebasarisiz&menu_id=$menu_id");
		exit;
	}
}


//menu  silme işlemi
if ($_GET['menusil']=="ok") 
{
	//echo "dogru";

	$sil=$db->prepare("DELETE from menu where menu_id=:id");
	$kontrol=$sil->execute(array(
		'id'=>$_GET['menu_id']
	));


	if ($kontrol) 
	{
	//echo "Kayıt Başarılı";
		Header("Location:../production/menu-listesi.php?durum=guncellemebasarili");
		exit;
	}
	else
	{
	//echo "Kayıt Başarısız";
		Header("Location:../production/menu-listesi.php?durum=guncellemebasarisiz");
		exit;
	}
}


//yeni menü ekleme
if (isset($_POST['menuEkle'])) 
{
	$kaydet=$db->prepare("INSERT INTO menu set
		menu_ad=:menu_ad,
		menu_url=:menu_url,
		menu_sira=:menu_sira,
		menu_durum=:menu_durum,
		menu_detay=:menu_detay
		");

	$insert=$kaydet->execute(array(
		'menu_ad'=>$_POST['menu_ad'],
		'menu_url'=>$_POST['menu_url'],
		'menu_sira'=>$_POST['menu_sira'],
		'menu_durum'=>$_POST['menu_durum'],
		'menu_detay'=>$_POST['menu_detay']
	));


	if ($insert) 
	{
  //echo "Kayıt Başarılı";
		Header("Location:../production/menu-listesi.php?durum=guncellemebasarili");
		exit;
	}
	else
	{
  //echo "Kayıt Başarısız";
		Header("Location:../production/menu-listesi.php?durum=guncellemebasarisiz");
		exit;
	}
}


//slider düzenleme 
if (isset($_POST['sliderDuzenle'])) 
{
	/*echo $_POST['slider_ad'];
	echo $_POST['sliderim_soyad'];
	echo $_POST['sliderim_aciklama'];*/

	$slider_id=$_POST['slider_id'];

	//$slider_seourl=seo($_POST['slider_ad']);

	$kaydet=$db->prepare("UPDATE slider set
		slider_ad=:slider_ad,
		slider_url=:slider_url,
		slider_sira=:slider_sira,
		slider_durum=:slider_durum
		where slider_id={$_POST['slider_id']}
		");

	$insert=$kaydet->execute(array(
		'slider_ad'=>$_POST['slider_ad'],
		'slider_url'=>$_POST['slider_url'],
		'slider_sira'=>$_POST['slider_sira'],
		'slider_durum'=>$_POST['slider_durum']
	));


//düzenle butonundan sonra anasayfaya yönlendirme yapmak için bu blokla oynanmalı
	if ($insert) 
	{
	//echo "Kayıt Başarılı";
		Header("Location:../production/slider-listesi.php?durum=guncellemebasarili&slider_id=$slider_id");
		exit;
	}
	else
	{
	//echo "Kayıt Başarısız";
	//Header("Location:duzenle.php?kayit=basarisiz&slider_id=$slider_id");
		Header("Location:../production/slider-listesi.php?durum=guncellemebasarisiz&slider_id=$slider_id");
		exit;
	}
}


//slider  silme işlemi
if ($_GET['slidersil']=="ok") 
{
	//echo "dogru";

	$sil=$db->prepare("DELETE from slider where slider_id=:id");
	$kontrol=$sil->execute(array(
		'id'=>$_GET['slider_id']
	));


	if ($kontrol) 
	{
	//echo "Kayıt Başarılı";
		Header("Location:../production/slider-listesi.php?durum=guncellemebasarili");
		exit;
	}
	else
	{
	//echo "Kayıt Başarısız";
		Header("Location:../production/slider-listesi.php?durum=guncellemebasarisiz");
		exit;
	}
}


//kullanici paneline giriş işlemleri
if (isset($_POST['kullaniciGirisYap'])) {
	//echo "merhaba";
	$kullanici_mail = htmlspecialchars($_POST['kullanici_mail']);
	$kullanici_password = md5($_POST['kullanici_password']);

	$kullanicisor=$db->prepare("SELECT * FROM kullanici where kullanici_mail=:mail and kullanici_password=:password and kullanici_durum=:durum");
	$kullanicisor->execute(array(
		'mail' => $kullanici_mail,
		'password' => $kullanici_password,
		'durum' => 1
	));

	$say=$kullanicisor->rowCount();

	//echo $kullanici_password;
	//exit();

	if ($say==1) 
	{
		//echo "dogru";
		$_SESSION['kullanici_mail']=$kullanici_mail;
		header("location:../../");
		exit();
	}
	else
	{
		//echo "yanlis";
		//header("location:../production/deneme.php");
		header("location:../../?durum=girishatali");
		exit();
	}
}


//hesap bilgileri güncelleme işlemleri
if (isset($_POST['hesabimGuncelle'])) {
	//echo "dogru yer";
	//echo $_POST['kullanici_password'];
	//exit();

 	echo $kullanici_id=$_POST['kullanici_id']; //exit();
	//$kullanici_tc=$_POST['kullanici_tc'];

	$kullanicikaydet=$db->prepare("UPDATE kullanici SET
		kullanici_password=:kullanici_password,
		kullanici_tc=:kullanici_tc

		WHERE kullanici_id={$_POST['kullanici_id']}
		");

	$update=$kullanicikaydet->execute(array(
		'kullanici_password' => md5($_POST['kullanici_password']),
		'kullanici_tc' => $_POST['$kullanici_tc']
	));

	if ($update) {
		//echo "güncelleme başarılı";
		header("location:../../hesabim.php?durum=guncellemebasarili");
	}

	else {
		//echo "güncelleme başarısız";
		header("location:../../hesabim.php?durum=guncellemebasarisiz");
	}
} 
?>