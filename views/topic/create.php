<h1>Skapa tråd</h1>
<form action="<?php echo $_ENV['BASE_URL'] ?>/topic" method="post">
    <label for="category">Kategori</label>
    <select name="category" id="category">
        <?php
            /** @var App\Models\Category[] $categories */
            foreach ($categories as $category) {
                // Kollar om kategorin ska visas som standard
                $default = false;
                if($specifiedCategory == $category) {
                    $default = true;
                }
                echo "<option value=\"".htmlspecialchars($category->getId())."\" " . ($default ? "selected" : "") . ">".htmlspecialchars($category->getTitle())."</option>";
            }
        ?>
    </select>

    <label class="required" for="title">Titel</label>
    <input type="text" name="title" id="title" placeholder="Titel" require>

    <label class="required" for="message">Inlägg</label>
    <textarea name="message" id="message" placeholder="Inlägg" require></textarea>

    <input type="submit" value="Skapa">
</form>