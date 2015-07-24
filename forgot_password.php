<?php

	include("config.php");
	include("db.php");
	//include("functions.php");
	include("vendor/autoload.php");

if (!empty($_POST)){

		$error = "";

		$email = trim(strip_tags($_POST['email']));

	//VALIDATION Vide/Valide/longueur
	if(empty($email)){
		$error = "Veuillez entrer votre email ! ";
	}

	elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			$error = "Votre email n'est pas valide !";
	}

	elseif(strlen($email) > 100){
		$error = "Votre email est long !";
	}

	//Si valide : email présent ? 
	if ($error == ""){
		$sql = "SELECT * 
				FROM users 
				WHERE email = :email";
		$sth = $dbh->prepare($sql);
		$sth->execute(array(":email" => $email));
		$user = $sth->fetch();
	}

	//SI on le trouve
	if ($user){
				//token pour l'utilisateur
				$factory = new RandomLib\Factory;
				$generator = $factory->getGenerator(new SecurityLib\Strength(SecurityLib\Strength::MEDIUM));
				$token = $generator->generateString(80, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-');

				$expiry = date("Y-m-d H:i:s", strtotime("+ 1 day"));

				$sql = "UPDATE users 
						SET token = :token 
						token_expery = :expiry 
						date_modified = NOW()
						WHERE id = :id";

				$hashedToken = password_hash($token, PASSWORD_DEFAULT);

				$sth = $dbh->prepare($sql);
				$sth->bindValue(":token", $hashedToken);
				$sth->bindValue(":expiry", $expiry);
				$sth->bindValue(":id", $user['id']);

				if ($sth->execute()){

					//on génère le lien complet
					$resetLink = ROOTURL . "/forgot_password_2.php?token=$token&email=$email";
					

					//instance de PHPMailer
					$mail = getConfiguredMailer();

					//qui envoie, et qui reçoit
					$mail->setFrom('jerome.hillion@gmail.com', 'Jeromewf3');
					$mail->addAddress('alexandrewf3@gmail.com', 'Alexandrewf3'); //retirer en prod
					$mail->addAddress($email, $user['username']);

					//sujet 
					$mail->Subject = 'Oubli du mot de passe chez WF3 ?';

					//message 
					$mail->Body = 
					'<p>Vous avez oublié votre mot de passe ?<br />
					<a href="'.$resetLink.'">
					Cliquez ici pour créer un nouveau mot de passe</a></p>
					<p>Si vous n\'êtes pas à l\'origine de cette demande, vous pouvez ignorer ce message</p>';


					if (!$mail->send()) {
						$error = "Une erreur de survenue. Le mail n'a pas été envoyé !";
					} else {
						$message = "Allez voir vos mails !";
					}
				}


}


}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Mot de passe oublié</title>
</head>
<body>
	
	<h1>Mot de passe oublié</h1>

	<p>Veuillez entrer l'adresse email utilisée lors de votre inscription.</p>
	<p>Nous y enverrons un message permettant de créer un nouveau mot de passe.</p>
	<form method="POST">
		<input type="text" name="email" placeholder="Votre email" />
		<input type="submit" value="OK" />
	</form>
	<div>
	<?php 
		if (!empty($error)){
			echo $error;
		}
	?>
	</div>
	<div>
	<?php 
		if (!empty($message)){
			echo $message;
		}
	?>
	</div>

</body>
</html>