<link rel="stylesheet" href="<?php echo $_ENV['BASE_URL'] ?>/css/auth.css">
<h1>Profil</h1>
<div class="flex" style="gap: 30px">
    <form action="<?php echo $_ENV['BASE_URL'] ?>/profile" method="post" id="profileForm" class="flex flex-column">
        <h2>Kontoinformation</h2>
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
    <form action="<?php echo $_ENV['BASE_URL'] ?>/update-password" method="post" id="updatePasswordForm" class="flex flex-column">
        <h2>Uppdatera lösenord</h2>
        <p class="error">* Obligatoriskt fält</p>
        <label for="password" class="required">Gammalt Lösenord</label>
        <input type="password" id="old_password" name="old_password" placeholder="Lösenord" required>
        <?php renderError($errors ?? null, 'new_password') ?>
        
        <label for="password" class="required">Nytt Lösenord</label>
        <input type="password" id="new_password" name="new_password" placeholder="Lösenord" required>
        <?php renderError($errors ?? null, 'new_password') ?>
    
        <label for="password_confirm" class="required">Upprepa nytt lösenord</label>
        <input type="password" id="password_confirm" name="password_confirm" placeholder="Upprepa lösenord" required>
    
        <input type="submit" value="Uppdatera lösenord">
    </form>
</div>