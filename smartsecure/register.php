<?php
session_start();
require_once('./includes/db.php');
require_once('./includes/functions.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/vendor/autoload.php'; 

if (!empty($_POST)) {
    $errors = [];

    // Pseudo
    if (empty($_POST['username']) || !preg_match("#^[a-zA-Z0-9_]+$#", $_POST['username'])) {
        $errors['username'] = "Votre pseudo n'est pas valide";
    } else {
        $query = "SELECT * FROM users WHERE username = ?";
        $req = $pdo->prepare($query);
        $req->execute([$_POST['username']]);
        if ($req->fetch()) {
            $errors['username'] = "Ce pseudo n'est plus disponible";
        }
    }

    // Email
    if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Votre email n'est pas valide";
    } else {
        $query = "SELECT * FROM users WHERE email = ?";
        $req = $pdo->prepare($query);
        $req->execute([$_POST['email']]);
        if ($req->fetch()) {
            $errors['email'] = "Cet email est déjà pris";
        }
    }

    // Password
    if (empty($_POST['password']) || $_POST['password'] !== $_POST['password_confirm']) {
        $errors['password'] = "Vous devez rentrer un mot de passe valide et confirmé";
    }

    if (empty($errors)) {
        $query = "INSERT INTO users(username,email,password,confirmation_token) VALUES(?,?,?,?)";
        $req = $pdo->prepare($query);
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $token = generateToken(100);

        $req->execute([$_POST['username'], $_POST['email'], $password, $token]);
        $userId = $pdo->lastInsertId();

        
        $recipientEmail = $_POST['email'];
        $recipientName = $_POST['username'];
        $senderEmail = 'contact.smartsecure@gmail.com'; 
        $senderName = 'SmartSecure'; 
        $subject = "Confirmation du compte";
        $adressRoot ="192.168.1.20";
        $message = "Afin de confirmer votre compte, merci de cliquer sur ce lien\n\nhttp://$adressRoot/smartsecure/confirm.php?id=$userId&token=$token";

        
        $mail = new PHPMailer(true);

        try {
            
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $senderEmail;
            $mail->Password = 'yvlz eekv gsgs wafx'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Destinataire et expéditeur
            $mail->setFrom($senderEmail, $senderName); 
            $mail->addAddress($recipientEmail, $recipientName);

            // Contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = '<h1>Bonjour ' . $recipientName . '</h1><p>Afin de confirmer votre compte, merci de cliquer sur ce lien : </p><p><a href="http://localhost/smartsecure/confirm.php?id=' . $userId . '&token=' . $token . '">Confirmer mon compte</a></p>';
            $mail->AltBody = "Bonjour " . $recipientName . ",\n\nAfin de confirmer votre compte, merci de cliquer sur ce lien :\nhttp://localhost/smartsecure/confirm.php?id=" . $userId . "&token=" . $token;

            // Envoyer l'email
            $mail->send();
            $_SESSION['flash']['success'] = "Compte créé avec succès. Veuillez vérifier votre boîte mail afin de confirmer votre compte.";
        } catch (Exception $e) {
            echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        header("Location: login.php");
        exit();
    }
}
?>
<?php
require_once './includes/header.php';
?>
<main class="main-form">
    <article class="article-form">
        <h1>S'inscrire</h1>
        <form action="" method="post">
            <fieldset>
                <div class="div2">
                    <label for="pseudo">Nom d'utilisateur</label>
                    <input type="text" id="pseudo" class="imput" name="username" required>
                </div>
                <div class="div2">
                    <label for="email">Email</label>
                    <input type="email" id="email" class="imput" name="email" required>
                </div>
                <div class="div2">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" class="imput" name="password" required>
                </div>
                <div class="div2">
                    <label for="password">Confirmer votre mot de passe</label>
                    <input type="password" id="password" class="imput" name="password_confirm" required>
                </div>
                <input type="submit" class="imput-login" value="S'inscrire">
                <a href="login.php" class="create_login">Vous avez déjà un compte? Connectez vous !</a>
            </fieldset>
        </form>
    </article>
</main>
<?php
require_once './includes/footer.php';
?>
