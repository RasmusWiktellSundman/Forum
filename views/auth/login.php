<link rel="stylesheet" href="<?php echo $_ENV['BASE_URL'] ?>/css/auth.css">
<h1>Logga in</h1>
<form action="<?php echo $_ENV['BASE_URL'] ?>/login" method="post" id="loginForm">
    <label for="email" class="required">E-post</label>
    <input type="email" id="email" name="email" placeholder="E-post" required value="<?php echo $previous['email'] ?? '' ; ?>">
    <?php renderError($errors ?? null, 'email') ?>
    
    <br>
    <label for="password" class="required">Lösenord</label>
    <input type="password" id="password" name="password" placeholder="Lösenord" required value="<?php echo $previous['password'] ?? '' ; ?>">
    <?php renderError($errors ?? null, 'password') ?>
    
    <!-- Generella inloggningsfel -->
    <?php renderError($errors ?? null, 'login') ?>

    <input type="submit" value="Logga in">
    <a href="./register">Skapa konto</a>
</form>