<?php
	session_start();
?>

<!doctype html>
<html lang="FR_fr">
<head>
	<meta charset="UTF-8">
	<title>Connexion</title>
	<body>
		<h1>Connectez-vous</h1>
		<fieldset>
			<form method="POST" id="Connexion">
                <label for="email">Email</label>
                <input type="text" name="email" id="email">

                <label for="password">Mot de passe</label>
                <input type="password" name="password" id="password">
                <button type="submit">Se connecter</button>
			</form>
			<a href="forgot_password">Mot de passe oublié</a>
		</fieldset>

	</body>
