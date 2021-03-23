<?php
// VERIFIER S'IL EXISTE UN COOKIE ET SI L'UTILISATEUR N'EST PAS CONNECTÉ
if (isset($_COOKIE['auth']) && !isset($_SESSION['connect'])){
	$secret = htmlspecialchars($_COOKIE['auth']); // ON RECUPERE LA VALEUR "SECRET" ET ON LA STOCK DANS UNE VARIABLE

	//VERIFICATION
	require('connection_db.php'); // ON SE CONNECTE A LA BASE DE DONNEES

	$req = $bdd->prepare('SELECT COUNT(*) AS numberAccount FROM user WHERE secret = ?'); // ON LANCE UNE REQUETE
	$req->execute(array($secret)); // ON EXECUTE LA REQUETE

	while($user = $req->fetch()){ // TANT QU'IL Y A UNE LIGNE DANS LA BASE ON EXECUTE LE CODE
		if ($user['numberAccount'] == 1) { // S'IL TROUVE LA VALEUR RECHERCHEE
			$reqUser = $bdd->prepare('SELECT * FROM users WHERE secret = ?'); // ON LANCE UNE REQUETE
			$reqUser->execute(array($secret)); // ON EXECUTE LA REQUETE

			while($userAccount = $reqUser->fetch()){ // TANT QU'IL Y A UNE LIGNE DANS LA BASE ON EXECUTE LE CODE
				$_SESSION['connect'] = 1;  // ON CREE LA SESSION
				$_SESSION['email'] = $userAccount['email']; // ON STOCK L'EMAIL DE L'UTILISATEUR DANS LA SESSION
			}
		}
	}
}