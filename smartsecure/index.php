<?php
session_start();
require_once './includes/functions.php';

reconnect_auto();
is_connect();


require_once './includes/header.php';
?>

<?php if (isset($_SESSION['auth'])) : ?>
        <a href="logout.php">Se deconnecter</a>
    <?php else : ?>
        <a href="login.php">Se connecter</a>
    <?php endif; ?>

<?php
    if (!isset($_SESSION['button'])) {
    $_SESSION['button'] = "Vérouiller";
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_SESSION['button'] === "Vérouiller") {
        $_SESSION['button'] = "Déverouiller";
    } else {
        $_SESSION['button'] = "Vérouiller";
    }
}

?>

<h2>Bonjour <?= $_SESSION['auth']->username ?></h2>

    <main class='main'>
        <article class='article'>  
            <form method="post">
                <button class='button-index' type="submit"><?php echo $_SESSION['button']; ?></button>
            </from>
        </article>
    </main>



<?php
require_once './includes/footer.php';
?>