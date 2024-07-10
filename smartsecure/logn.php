<?php
session_start();

require_once './includes/db.php';
require_once './includes/functions.php';

reconnect_auto();

if (!empty($_POST) && !empty($_POST['username']) && !empty($_POST['password'])) {
    $query = "SELECT * FROM users WHERE (username = :username OR email = :username) AND confirmed_at IS NOT NULL";
    $req = $pdo->prepare($query);
    $req->execute(['username' => $_POST['username']]);
    $user = $req->fetch();

    if ($user && password_verify($_POST['password'], $user->password)) {
        $_SESSION['auth'] = $user;
        $_SESSION['flash']['success'] = "Connexion éffectuée avec sucess";

        if (isset($_POST['remember'])) {
           $remember_token = generateToken(100);
           $query = "UPDATE users SET remember_token = ? WHERE id = ?";
           $pdo->prepare($query)->execute([$remember_token,$user->id]);

           setcookie("remember",$user->id . "::".$remember_token. sha1($user->id ."Ronasdev"),time()+ 60* 60 * 24 * 7);
        }

        header("Location: Index.php");
        exit();
    }else{
        $_SESSION['flash']['danger'] = "Identifiant ou mot de passe incorrect";
    }
}
?>
<?php require_once './includes/header.php'; ?>
<main class="main-form">
    <article class="article-form">
        <h1 class="h1">Se connecter</h1>
        <form action="" method="post">
            <fieldset>
                <div class="div">
                    <label for="pseudo">Nom d'utilisateur ou Email</label>
                    <input type="text" id="pseudo" class="imput" name="username" required>
                </div>
                <div class="div2">
                    <label for="password" class="label">Mot de passe <a href="remember.php">(J'ai oublié mon mot de passe)</a></label>
                    <input type="password" id="password" class="imput" name="password" required>
                </div>
                <div class="div2">
                    <label for="password"> <input type="checkbox" name="remember" value="1"> Se souvenir de moi</label>

                </div>
                <input type="submit" class="imput-login" value="Se connecter" required>
                <a href="register.php" class="create_login">Vous n'avez pas de compte? Créer en un !</a>
            </fieldset>



        </form>
    </article>
</main>

<?php
require_once './includes/footer.php';
?>