<link rel="stylesheet" href="<?php echo $_ENV['BASE_URL'] ?>/css/auth.css">
<h1>Registrera användare</h1>
<form action="<?php echo $_ENV['BASE_URL'] ?>/register" method="post" id="loginForm">
    <p class="error">* Obligatoriskt fält</p>
    <label for="email" class="required">E-post</label>
    <input type="email" id="email" name="email" placeholder="E-post" required value="<?php echo $previous['email'] ?? '' ; ?>">
    <?php renderError($errors ?? null, 'email') ?>
    
    <br>
    <label for="username" class="required">Användarnamn</label>
    <input type="text" id="username" name="username" placeholder="Användarnamn" required value="<?php echo $previous['username'] ?? '' ; ?>">
    <?php renderError($errors ?? null, 'username') ?>
    
    <br>
    <label for="firstname" class="required">Förnamn</label>
    <input type="text" id="firstname" name="firstname" placeholder="Förnamn" required value="<?php echo $previous['firstname'] ?? '' ; ?>">
    <?php renderError($errors ?? null, 'firstname') ?>
    
    <br>
    <label for="lastname" class="required">Efternamn</label>
    <input type="text" id="lastname" name="lastname" placeholder="Efternamn" required value="<?php echo $previous['lastname'] ?? '' ; ?>">
    <?php renderError($errors ?? null, 'lastname') ?>

    <br>
    <label for="password" class="required">Lösenord</label>
    <input type="password" id="password" name="password" placeholder="Lösenord" required>
    <?php renderError($errors ?? null, 'password') ?>

    <br>
    <label for="password_confirm" class="required">Upprepa lösenord</label>
    <input type="password" id="password_confirm" name="password_confirm" placeholder="Upprepa lösenord" required>

    <input type="submit" value="Skapa konto">
    <a href="./login">Logga in</a>
</form>