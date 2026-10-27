<?php
require_once __DIR__ . '/helpers.php';

$keyword = is_string($_GET['keyword'] ?? null) ? $_GET['keyword'] : '';
$pattern = '%' . $keyword . '%';
$statement = mysqli_prepare($conx, 'SELECT * FROM produktuak WHERE izena LIKE ? OR deskripzioa LIKE ?');
mysqli_stmt_bind_param($statement, 'ss', $pattern, $pattern);
mysqli_stmt_execute($statement);
$products = mysqli_fetch_all(mysqli_stmt_get_result($statement), MYSQLI_ASSOC);
mysqli_stmt_close($statement);
if (empty($products)) {
?>
<fieldset class="empty-catalog">
    <legend>Ez dago produkturik katalogoan <?php echo htmlText($keyword); ?> deitzen denik</legend>
    <?php if (administrator()): ?>
        <p><a href="index.php?action=updel">Eguneratu katalogoa.</a></p>
    <?php else: ?>
        <h3>Enpresaren hasierako orria</h3>
    <?php endif; ?>
</fieldset>
<?php
} else {
    foreach ($products as $product) {
        $imageUrl = 'images/' . rawurlencode(basename($product['pic']));
?>
<article class="product-card">
    <fieldset class="product-image">
        <a href="<?php echo htmlText($imageUrl); ?>">
            <img src="<?php echo htmlText($imageUrl); ?>" alt="<?php echo htmlText($product['izena']); ?>">
        </a>
    </fieldset>
    <div class="product-details">
        <fieldset><legend>Izena</legend>
            <p><?php echo htmlText($product['izena']); ?> - <?php echo htmlText($product['salneurria']); ?>€</p>
        </fieldset>
        <fieldset><legend>Deskripzioa</legend>
            <p><?php echo htmlText($product['deskripzioa']); ?></p>
        </fieldset>
        <?php if (administrator()): ?>
            <p><a href="index.php?action=description&amp;pic_id=<?php echo (int) $product['id']; ?>">Deskripzioa/Salneurria aldatu</a></p>
        <?php endif; ?>
    </div>
</article>
<?php
    }
}
?>
