<?php
	session_start();
	session_unset();
	session_destroy(); // DETRUIT LES SESSIONS
	setcookie('auth', '', time()-1, '/', null, false, true); // DETRUIT LE COOKIE
	header('location:../index.php'); // REDIRIGE L'UTILISATEUR VERS LA PAGE D'ACCUEIL
?>