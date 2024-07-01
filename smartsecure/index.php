<?php
session_start();
require_once './includes/functions.php';

reconnect_auto();
is_connect();

require_once './includes/header.php';
?>

<h1>SmartSecure</h1>

<h2>Bonjour <?= $_SESSION['auth']->username ?></h2>

    <main class='main'>
        <article class='article'>  
            <button class='button-index' onclick="Vérouiller()">Vérouiller</button>
        </article>
    </main>
    <script>
        function Vérouiller() {
            alert('Arduino en attente !');
        }
    </script>

<?php
require_once './includes/footer.php';
?>