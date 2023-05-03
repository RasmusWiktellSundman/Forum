<h1>Skapa tråd</h1>
<form action="<?php echo $_ENV['BASE_URL'] ?>/topic" method="post">
    <label for="category">Kategori</label>
    <select name="category" id="category">
        <?php
            /** @var App\Models\Category[] $categories */
            foreach ($categories as $category) {
                // Kollar om kategorin ska visas som standard
                $default = false;
                if((isset($specifiedCategory) && $specifiedCategory == $category) || 
                    isset($previous) && $previous['category'] == $category) {
                    $default = true;
                }
                echo "<option value=\"".htmlspecialchars($category->getId())."\" " . ($default ? "selected" : "") . ">".htmlspecialchars($category->getTitle())."</option>";
            }
        ?>
    </select>
    <?php renderError($errors ?? null, 'category') ?>

    <label class="required" for="title">Titel</label>
    <input type="text" name="title" id="title" placeholder="Titel" value="<?php echo $previous['title'] ?? '' ; ?>" require>
    <?php renderError($errors ?? null, 'title') ?>

    <label class="required" for="message">Inlägg</label>
    <textarea name="message" id="message" placeholder="Inlägg" require><?php echo $previous['message'] ?? '' ; ?></textarea>
    <?php renderError($errors ?? null, 'message') ?>

    <input type="submit" value="Skapa">
</form>