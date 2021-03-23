<?php
session_start(); // ON INDIQUE A PHP QUE NOUS ALLONS UTILISER LES VARIABLES DE SESSION
require('src/log.php'); // ON INCLUE LE CODE DE LA PAGE LOG.PHP
if(isset($_SESSION['connect'])){ // SI L'UTILISATEUR EST CONNECTÉ
	header('location:index.php'); // ON LE REDIRIGE VERS LA PAGE PRINCIPALE
	exit(); // ON STOP LE CODE
}
	// ******* RECUPERATION DES CHAMPS RENSEIGNES ***********
	if(isset($_POST['email'])&& isset($_POST['password'])&& isset($_POST['password_two'])){ 
		$email = htmlspecialchars($_POST['email']); 
		$password = htmlspecialchars($_POST['password']);
		$password_two = htmlspecialchars($_POST['password_two']);

		// TEST DES MOT DE PASSE + MESSAGE D'ERREUR
		if ($password != $password_two){ 
			header("location:inscription.php?error=1&message=Les mot de passe ne sont pas identiques."); 
			exit();
		} 
		// TEST DU FORMAT DE L'ADRESSE MAIL + MESSAGE D'ERREUR
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			header("location:inscription.php?error=1&message=L'adresse email est incorrect.");
			exit();
		}
		// CONNEXION A LA BASE DE DONNEES
		require('src/connection_db.php');

		// ************* SI L'ADRESSE EST DEJA UTILISEE *****************
		$req = $bdd->prepare('SELECT COUNT(*) AS emailNumber FROM users WHERE email = ?');
		$req->execute(array($email));
		while($emailValidation = $req->fetch()){
			if ($emailValidation['emailNumber'] != 0){
				header("location:inscription.php?error=1&message=L'adresse email est déjà utilisée par un autre utilisateur.");
				exit();
			}
		}

		// HASH DE LA CLE SECRETE
		$secret = sha1($email).time();
		$secret = sha1($secret).time();

		//CRYPTAGE DU MOT DE PASSE
		$password = "q12".sha1($password)."09lc";

		//INSERTION DES VALEURS dans la base de données
		$req = $bdd->prepare('INSERT INTO users(email, password, secret) VALUES(?,?,?)');
		$req->execute(array($email,$password,$secret));
		// REDIRECTION DE L'UTILISATEUR AVEC LA METHODE GET
		header('location:inscription.php?success=1');
		exit();
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Netflix</title>
	<link rel="stylesheet" type="text/css" href="design/default.css">
	<link rel="icon" type="image/pngn" href="img/favicon.png">
</head>
<body>

	<?php include('src/header.php'); ?>
	
	<section>
		<div id="login-body">
			<h1>S'inscrire</h1>
			<?php
				// AFFICHER LE MESSAGE D'ERREUR/SUCCESS S'IL EXISTE
				if(isset($_GET['error'])){
					if(isset($_GET['message'])){
						echo '<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';
					}
				} else if (isset($_GET['success'])){
					echo '<div class="alert success"> Vous êtes désormais inscrit. <a href="index.php"> Connectez-vous </a> </div>';
				}
			?>
			<form method="post" action="inscription.php">
				<input type="email" name="email" placeholder="Votre adresse email" required />
				<input type="password" name="password" placeholder="Mot de passe" required />
				<input type="password" name="password_two" placeholder="Retapez votre mot de passe" required />
				<button type="submit">S'inscrire</button>
			</form>

			<p class="grey">Déjà sur Netflix ? <a href="index.php">Connectez-vous</a>.</p>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>
</html>