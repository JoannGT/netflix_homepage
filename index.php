<?php
	session_start();
	require('src/log.php');
	// ******* RECUPERATION DES CHAMPS RENSEIGNES ***********
	if (!empty($_POST['email']) && !empty($_POST['password'])){
		$email = htmlspecialchars($_POST['email']);
		$password = htmlspecialchars($_POST['password']);

		// TEST DU FORMAT DE L'ADRESSE MAIL + MESSAGE D'ERREUR
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
			header("location:index.php?error=1&message=L'adresse email n'est pas correcte.");
			exit();
		}

		//CRYPTAGE
		$password = "q12".sha1($password)."09lc";

		require('src/connection_db.php');

		// ************* SI L'ADRESSE N'EXISTE PAS *****************
		$req = $bdd->prepare('SELECT COUNT(*) AS numberEmail FROM users WHERE email = ?');
		$req->execute(array($email));

		while($email_verification = $req->fetch()){
			if($email_verification['numberEmail'] != 1){
				header("location:index.php?error=1&message=Impossible de vous authentifier.");
				exit();	
			}
				
		}

		// ********** AUTHENTIFICATION ***********
		$req = $bdd->prepare('SELECT * FROM users WHERE email = ?');
		$req->execute(array($email));

		while($user = $req->fetch()){
			if($password == $user['password']){
				// CREATION DES VARIABLES DE SESSION
				$_SESSION['connect'] = 1;
				$_SESSION['email'] = $user['email'];
				// SI LA CASE "CONNEXION AUTOMATIQUE EST COCHEE" ON CREE UN COOKIE
				if(isset($_POST['auto'])){
					setcookie('auth',$user['secret'], time() + 364*24*3600, '/', null, false, true);
				}

				header('location:index.php?success=1');
				exit();
			} else {
				header("location:index.php?error=1&message=Impossible de vous authentifier.");
				exit();
			}
		}
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
			<?php
			//SI L'UTILISATEUR EST CONNECTE, ON AFFICHE UN BOUTON DECONNEXION ET ON CACHE LE FORMULAIRE D'INSCRIPTION
			if(isset($_SESSION['connect'])){
					echo '<div>Bonjour '.$_SESSION['email'].' !</div>';
					echo '<br> <a href="src/disconnect.php">Déconnexion</a>';
				} else{ ?>
				<h1>S'identifier</h1>
				<?php
					//ON AFFICHE UN MESSAGE D'ERREUR/SUCCESS S'IL EXISTE
					if(isset($_GET['error'])){
						if(isset($_GET['message'])){
							echo '<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';
						}
					}else if (isset($_GET['success'])){
							echo '<div class="alert success"> Vous êtes connecté ! </div>';
					}
				?>
				<form method="post" action="index.php">
				<input type="email" name="email" placeholder="Votre adresse email" required />
				<input type="password" name="password" placeholder="Mot de passe" required />
				<button type="submit">S'identifier</button>
				<label id="option"><input type="checkbox" name="auto" checked />Se souvenir de moi</label>
				</form>
			

				<p class="grey">Première visite sur Netflix ? <a href="inscription.php">Inscrivez-vous</a>.</p>
			<?php } ?>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>
</html>