<link rel="stylesheet" href="<?php echo $_ENV['BASE_URL'] ?>/css/auth.css">
<h1>Profil</h1>
<form action="<?php echo $_ENV['BASE_URL'] ?>/profile" method="post" id="loginForm">
    <p class="error">* Obligatoriskt fält</p>
    <label for="accountId">KontoID</label>    
    <input type="text" id="accountId" value="<?php echo ($user->getId()) ; ?>" disabled>
    
    <label for="email" class="required">E-post</label>
    <input type="email" id="email" name="email" placeholder="E-post" required value="<?php echo ($previous['email'] ?? $user->getEmail()) ; ?>">
    <?php renderError($errors ?? null, 'email') ?>
    
    <br>
    <label for="username" class="required">Användarnamn</label>
    <input type="text" id="username" name="username" placeholder="Användarnamn" required value="<?php echo $previous['username'] ?? $user->getUsername(); ?>">
    <?php renderError($errors ?? null, 'username') ?>
    
    <br>
    <label for="firstname" class="required">Förnamn</label>
    <input type="text" id="firstname" name="firstname" placeholder="Förnamn" required value="<?php echo $previous['firstname'] ?? $user->getFirstname(); ?>">
    <?php renderError($errors ?? null, 'firstname') ?>
    
    <br>
    <label for="lastname" class="required">Efternamn</label>
    <input type="text" id="lastname" name="lastname" placeholder="Efternamn" required value="<?php echo $previous['lastname'] ?? $user->getLastname() ; ?>">
    <?php renderError($errors ?? null, 'lastname') ?>

    <input type="submit" value="Spara">
</form>