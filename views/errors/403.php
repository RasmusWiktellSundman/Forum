<link rel="stylesheet" href="<?php echo $_ENV['BASE_URL'] ?>/css/error.css">
<h1>403 - Åtkomst nekad</h1>
<?php
if(!App\Lib\Auth::isLoggedIn()) {
    echo "<p>Du behöver vara inloggad</p>";
}
?>