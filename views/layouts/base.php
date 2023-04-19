<?php

use App\Lib\Auth;
use App\Models\User;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $_ENV['BASE_URL'] ?>/css/style.css">
    <title>Test</title>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="<?php echo $_ENV['BASE_URL'] ?>">Hem</a></li>
                <!-- TODO: Kategorier från databas -->
                <?php
                    if(Auth::isLoggedIn()) {
                        ?>
                        <li><a href="<?php echo $_ENV['BASE_URL'] ?>/logout">Logga ut</a></li>
                        <?php
                        User::getTableName();
                    } else {
                        ?>
                        <li><a href="<?php echo $_ENV['BASE_URL'] ?>/login">Logga in</a></li>
                        <li><a href="<?php echo $_ENV['BASE_URL'] ?>/register">Registrera</a></li>
                        <?php
                    }
                ?>
            </ul>
        </nav>
    </header>
    <main>
        <?php echo $content ?>
    </main>
    <footer>
        <p class="copyright">© 2022 Rasmus Wiktell Sundman</p>
    </footer>
</body>
</html>