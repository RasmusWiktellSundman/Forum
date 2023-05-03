<h1>Skapa kategori</h1>
<form action="<?php echo $_ENV['BASE_URL'] ?>/category" method="post">
    <p class="error">* Obligatoriskt fält</p>
    <label for="title" class="required">Titel</label>
    <input type="text" id="title" name="title" placeholder="Titel" required value="<?php echo $previous['title'] ?? '' ; ?>">
    <?php renderError($errors ?? null, 'title') ?>

    <label for="description" class="">Beskrivning</label>
    <input type="text" id="description" name="description" placeholder="Beskrivning" value="<?php echo $previous['description'] ?? '' ; ?>">
    <?php renderError($errors ?? null, 'description') ?>

    <label for="show_in_navigation">Visa i navigation</label>
    <input type="checkbox" id="show_in_navigation" name="show_in_navigation" placeholder="Beskrivning" value="<?php isset($previous['show_in_nav']) ? 'on' : "off"; ?>">

    <input type="submit" value="Skapa">
</form>