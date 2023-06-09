<?php
use App\Lib\Auth;
?>

<h1>Kategorier</h1>
<?php if(Auth::isLoggedIn() && Auth::user()->isAdmin()) { ?>
    <button id="createCategoryButton">Skapa kategori</button>
    <!-- Skapar modal (popup) för skapande av ny kategori -->
    <dialog id="createCategoryModal" <?php echo isset($showCategoryModal) ? 'showModal' : '' ?>>
        <div class="modal-body">
            <h2>Skapa kategori</h2>
            <form action="<?php echo $_ENV['BASE_URL'] ?>/category" method="post">
                <p class="error">* Obligatoriskt fält</p>
                <label for="title" class="required">Titel</label>
                <input type="text" id="title" name="title" placeholder="Titel" required value="<?php echo $previous['title'] ?? '' ; ?>">
                <?php renderError($errors ?? null, 'title') ?>
        
                <label for="description" class="">Beskrivning</label>
                <input type="text" id="description" name="description" placeholder="Beskrivning" value="<?php echo $previous['description'] ?? '' ; ?>">
                <?php renderError($errors ?? null, 'description') ?>
        
                <div style="display: flex;">
                    <label for="show_in_navigation">Visa i navigation</label>
                    <input type="checkbox" id="show_in_navigation" name="show_in_navigation" placeholder="Beskrivning" <?php echo isset($previous['show_in_nav']) ? 'checked' : ""; ?>>
                </div>
        
                <div style="display: flex; gap: 10px">
                    <button value="cancel" type="button">Avbryt</button>
                    <input type="submit" value="Skapa">
                </div>
            </form>
        </div>
    </dialog>
<?php } ?>
<table>
    <thead>
        <th>Titel</th>
        <th>Senaste inlägg</th>
    </thead>
    <tbody>
    <?php
    foreach ($categories as $category) {
        ?>
        
        <tr onclick="window.location = 'category/<?php renderText($category->getId()); ?>'" style="cursor: pointer;">
            <td><?php renderText($category->getTitle()) ?></td>
            <td>Kommer snart</td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>

<script src="js/main.js"></script>