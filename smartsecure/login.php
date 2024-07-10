<?php
session_start();

require_once './includes/db.php';
require_once './includes/functions.php';
require_once 'google/vendor/autoload.php';

reconnect_auto();

$client = new Google_Client();
$client->setClientId('169125003134-5pbbcuigks7n7nfu9cmhoalj8ht3rgtj.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/smartsecure/login.php');
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);

    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    $email = $google_account_info->email;
    $name = $google_account_info->name;
    $id = $google_account_info->id;

    $query = "SELECT * FROM users WHERE email = :email";
    $req = $pdo->prepare($query);
    $req->execute(['email' => $email]);
    $user = $req->fetch();

    if ($user) {
        $_SESSION['auth'] = $user;
    } else {
        $query = "INSERT INTO users (username, email, google_id, confirmed_at) VALUES (:username, :email, :google_id, NOW())";
        $req = $pdo->prepare($query);
        $req->execute([
            'username' => $name,
            'email' => $email,
            'google_id' => $id
        ]);
        $user_id = $pdo->lastInsertId();
        $_SESSION['auth'] = $pdo->query("SELECT * FROM users WHERE id = $user_id")->fetch();
    }

    $_SESSION['flash']['success'] = "Connexion éffectuée avec succès";
    header("Location: index.php");
    exit();
}

if (!empty($_POST) && !empty($_POST['username']) && !empty($_POST['password'])) {
    $query = "SELECT * FROM users WHERE (username = :username OR email = :username) AND confirmed_at IS NOT NULL";
    $req = $pdo->prepare($query);
    $req->execute(['username' => $_POST['username']]);
    $user = $req->fetch();

    if ($user && password_verify($_POST['password'], $user->password)) {
        $_SESSION['auth'] = $user;
        $_SESSION['flash']['success'] = "Connexion éffectuée avec succès";

        if (isset($_POST['remember'])) {
           $remember_token = generateToken(100);
           $query = "UPDATE users SET remember_token = ? WHERE id = ?";
           $pdo->prepare($query)->execute([$remember_token,$user->id]);

           setcookie("remember",$user->id . "::".$remember_token. sha1($user->id ."Ronasdev"),time()+ 60* 60 * 24 * 7);
        }

        header("Location: index.php");
        exit();
    } else {
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
                    <input type="text" id="pseudo" class="imput" name="username" >
                </div>
                <div class="div2">
                    <label for="password" class="label">Mot de passe <a href="remember.php">(J'ai oublié mon mot de passe)</a></label>
                    <input type="password" id="password" class="imput" name="password" >
                </div>
                <div class="div2">
                    <label for="password"> <input type="checkbox" name="remember" value="1"> Se souvenir de moi</label>
                </div>
                <input type="submit" class="imput-login" value="Se connecter" >
                <a href="register.php" class="create_login">Vous n'avez pas de compte? Créer en un !</a>
            </fieldset>
        </form>
        <div>
            <a href="<?php echo $client->createAuthUrl(); ?>">Se connecter avec Google</a>
        </div>

        <div id="g_id_onload"
            data-client_id="169125003134-5pbbcuigks7n7nfu9cmhoalj8ht3rgtj.apps.googleusercontent.com"
            data-context="signin"
            data-ux_mode="popup"
            data-login_uri="http://localhost/smartsecure/login.php"
            data-auto_prompt="false">
        </div>

        <div class="g_id_signin"
            data-type="standard"
            data-shape="rectangular"
            data-theme="filled_blue"
            data-text="signin_with"
            data-size="large"
            data-logo_alignment="left">
        </div>

    </article>
</main>
<?php
require_once './includes/footer.php';
?>

