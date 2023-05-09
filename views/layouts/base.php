<?php

use App\Lib\Auth;
use App\Models\Category;
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
                <li><a href="<?php echo $_ENV['BASE_URL'] ?>" class="<?php if($_SERVER['REQUEST_URI'] == '/') echo 'active' ?>">Hem</a></li>
                <?php
                foreach (Category::getAll() as $category) {
                    if($category->getShowInNavigation()) {
                        $title = htmlspecialchars($category->getTitle());
                        $id = htmlspecialchars($category->getId());
                        ?>
                        <li><a href="<?php echo $_ENV['BASE_URL'] ?>/category/<?php echo $id; ?>" class="<?php if(str_starts_with($_SERVER['REQUEST_URI'], '/category/'.$id)) echo 'active' ?>"><?php echo $title; ?></a></li>
                        <?php
                    }
                }
                if(Auth::isLoggedIn()) {
                    ?>
                    <li><a href="<?php echo $_ENV['BASE_URL'] ?>/profile" class="<?php if($_SERVER['REQUEST_URI'] == '/profile') echo 'active' ?>">Profil</a></li>
                    <li><a href="<?php echo $_ENV['BASE_URL'] ?>/logout">Logga ut</a></li>
                    <?php
                } else {
                    ?>
                    <li><a href="<?php echo $_ENV['BASE_URL'] ?>/login" class="<?php if($_SERVER['REQUEST_URI'] == '/login') echo 'active' ?>">Logga in</a></li>
                    <li><a href="<?php echo $_ENV['BASE_URL'] ?>/register" class="<?php if($_SERVER['REQUEST_URI'] == '/register') echo 'active' ?>">Registrera</a></li>
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
        <p class="copyright">© 2023 Rasmus Wiktell Sundman</p>
    </footer>
</body>
</html>