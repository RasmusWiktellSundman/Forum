<link rel="stylesheet" href="<?php echo $_ENV['BASE_URL'] ?>/css/auth.css">
<div class="flex" style="gap: 10px">
    <h1>Profil</h1>
    <?php $user->renderProfileImage(); ?>
</div>
<div class="flex" style="gap: 30px">
    <form action="<?php echo $_ENV['BASE_URL'] ?>/profile" method="post" enctype="multipart/form-data" id="profileForm" class="flex flex-column">
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

        <label for="profile_image">Profilbild</label>
        <input type="file" id="profile_image" name="profile_image" accept="image/png, image/jpeg">
        <?php renderError($errors ?? null, 'profile_image') ?>
    
        <?php if(isset($updatedSuccessfully)) { ?>
            <p class="success">Uppdaterat!</p>
        <?php } ?>
        <input type="submit" value="Spara">
    </form>
    <form action="<?php echo $_ENV['BASE_URL'] ?>/update-password" method="post" id="updatePasswordForm" class="flex flex-column">
        <h2>Uppdatera lösenord</h2>
        <p class="error">* Obligatoriskt fält</p>
        <label for="password" class="required">Gammalt Lösenord</label>
        <input type="password" id="old_password" name="old_password" placeholder="Lösenord" required>
        <?php renderError($errors ?? null, 'old_password') ?>
        
        <label for="password" class="required">Nytt Lösenord</label>
        <input type="password" id="new_password" name="new_password" placeholder="Lösenord" required>
        <?php renderError($errors ?? null, 'new_password') ?>
    
        <label for="password_confirm" class="required">Upprepa nytt lösenord</label>
        <input type="password" id="password_confirm" name="password_confirm" placeholder="Upprepa lösenord" required>
    
        <?php if(isset($updatedPasswordSuccessfully)) { ?>
            <p class="success">Lösenord uppdaterat!</p>
        <?php } ?>
        <input type="submit" value="Uppdatera lösenord">
    </form>
    <form action="<?php echo $_ENV['BASE_URL'] ?>/remove-profile-image" method="post" id="removeProfileImage" class="flex flex-column">
        <h2>Ta bort profilbild</h2>

        <?php if(isset($removedProfileImageSuccessfully)) { ?>
            <p class="success">Profilbild borttagen!</p>
        <?php } ?>
        <input type="submit" value="Ta bort profilbild">
    </form>
</div>