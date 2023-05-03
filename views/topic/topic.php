<?php
use App\Lib\Auth;
?>

<link rel="stylesheet" href="<?php echo $_ENV['BASE_URL'] ?>/css/topic.css">

<h1><?php renderText($topic->getTitle()); ?></h1>
<h2><b>Kategori: </b><?php renderText($topic->getCategory()->getTitle()); ?></h2>
<section id="posts">
    <?php
    foreach ($posts as $post) {
        ?>
        <div class="post">
            <div class="header">
                <p class="author"><?php renderText($post->getAuthor()->getDisplayName()) ?></p>
                <p class="publish_date"><?php renderText($post->getCreatedAt()->format("Y-m-d H:i:s")) ?></p>
            </div>
            <p><?php echo nl2br(htmlspecialchars($post->getMessage())) ?></p>
        </div>
        <?php
    }
    if(Auth::isLoggedIn()) {
        ?>
        <h3 id="message-form-title">Skapa inlägg</h3>
        <form id="message-form" action="<?php echo $_ENV['BASE_URL']; ?>/topic/<?php renderText($topic->getId()); ?>" method="post">
            <textarea type="text" name="message" id="message" rows="10" placeholder="Meddelande"><?php echo $previous['message'] ?? '' ; ?></textarea>
            <?php renderError($errors ?? null, 'message') ?>
            <input type="submit" value="Skapa inlägg">
        </form>
        <?php
    }
    ?>
</section>