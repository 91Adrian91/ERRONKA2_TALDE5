<?php
require_once __DIR__ . '/helpers.php';
requireAccount(true);

const PRODUCT_IMAGE_DIRECTORY = __DIR__ . '/../images';
if (isset($_GET['pic_id']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_var($_GET['pic_id'], FILTER_VALIDATE_INT);
    if ($id === false || $id < 1) {
        http_response_code(400);
        echo 'Produktuaren identifikatzailea ez da zuzena.';
        return;
    }
    $statement = mysqli_prepare($conx, 'SELECT pic FROM produktuak WHERE id = ?');
    mysqli_stmt_bind_param($statement, 'i', $id);
    mysqli_stmt_execute($statement);
    $product = mysqli_fetch_assoc(mysqli_stmt_get_result($statement));
    mysqli_stmt_close($statement);
    if ($product) {
        $statement = mysqli_prepare($conx, 'DELETE FROM produktuak WHERE id = ?');
        mysqli_stmt_bind_param($statement, 'i', $id);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
        $image = PRODUCT_IMAGE_DIRECTORY . '/' . basename($product['pic']);
        // Irudi partekatuak ez dira ezabatzen; jatorrizko katalogoak irudi bera erabiltzen du.
        if (preg_match('/^[a-f0-9]{32}\.(jpg|png|gif|webp)$/', basename($image)) && is_file($image)) {
            unlink($image);
        }
    }
    redirectPage('index.php?action=updel');
}
if (isset($_GET['upload']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = (string) ($_POST['izena'] ?? '');
    $description = (string) ($_POST['deskripzioa'] ?? '');
    $price = filter_var($_POST['salneurria'] ?? null, FILTER_VALIDATE_FLOAT);
    $stock = filter_var($_POST['stock'] ?? null, FILTER_VALIDATE_INT);
    if ($name === '' || $price === false || $price < 0 || $stock === false || $stock < 0) {
        http_response_code(400);
        echo 'Produktuaren datuak ez dira zuzenak.';
        return;
    }
    $image = imageUpload('upfile', PRODUCT_IMAGE_DIRECTORY);
    $statement = mysqli_prepare($conx, 'INSERT INTO produktuak (izena, deskripzioa, salneurria, pic, stock) VALUES (?, ?, ?, ?, ?)');
    mysqli_stmt_bind_param($statement, 'ssdsi', $name, $description, $price, $image, $stock);
    mysqli_stmt_execute($statement);
    mysqli_stmt_close($statement);
    redirectPage('index.php?action=updel');
}
?>
<div class="account-layout">
    <fieldset class="compact-form">
        <legend>Produktu berria</legend>
        <form enctype="multipart/form-data" action="index.php?action=updel&amp;upload=1" method="post">
            <p><label for="product-name">Izena:</label><input type="text" name="izena" id="product-name" required></p>
            <p><label for="product-description">Deskripzioa:</label><input type="text" name="deskripzioa" id="product-description"></p>
            <p><label for="product-price">Salneurria:</label><input type="number" name="salneurria" id="product-price" min="0" step="0.01" required></p>
            <p><label for="product-stock">Stock:</label><input type="number" name="stock" id="product-stock" min="0" required></p>
            <p><label for="product-image">Irudia:</label><input type="file" name="upfile" id="product-image" accept="image/png,image/jpeg,image/gif,image/webp"></p>
            <input type="submit" value="Igo">
        </form>
    </fieldset>
    <section aria-label="Produktuak ezabatu">
        <?php
        $products = mysqli_query($conx, 'SELECT * FROM produktuak');
        while ($product = mysqli_fetch_assoc($products)) {
        ?>
            <article>
                <h3><?php echo htmlText($product['izena']); ?></h3>
                <img src="images/<?php echo htmlText(rawurlencode(basename($product['pic']))); ?>" alt="<?php echo htmlText($product['izena']); ?>">
                <form action="index.php?action=updel&amp;pic_id=<?php echo (int) $product['id']; ?>" method="post">
                    <button type="submit">Ezabatu <?php echo htmlText($product['izena']); ?></button>
                </form>
            </article>
        <?php } ?>
    </section>
</div>
