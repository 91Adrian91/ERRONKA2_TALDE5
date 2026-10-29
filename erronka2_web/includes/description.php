<?php
require_once __DIR__ . '/helpers.php';
requireAccount(true);

$id = filter_var($_GET['pic_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false) {
    http_response_code(400);
    echo 'Produktuaren identifikatzailea ez da zuzena.';
    return;
}
$statement = mysqli_prepare($conx, 'SELECT * FROM produktuak WHERE id = ?');
mysqli_stmt_bind_param($statement, 'i', $id);
mysqli_stmt_execute($statement);
$product = mysqli_fetch_assoc(mysqli_stmt_get_result($statement));
mysqli_stmt_close($statement);
if (!$product) {
    http_response_code(404);
    echo 'Produktua ez da aurkitu.';
    return;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = inputText($_POST, 'deskripzioa');
    $priceText = inputText($_POST, 'salneurria');
    $price = $priceText === '' ? (float) $product['salneurria'] : filter_var($priceText, FILTER_VALIDATE_FLOAT);
    if (mb_strlen($description) > 150 || $price === false || !is_finite($price) || $price < 0 || $price > 1000000) {
        http_response_code(400);
        echo 'Salneurria ez da zuzena.';
        return;
    }
    $statement = mysqli_prepare($conx, 'UPDATE produktuak SET deskripzioa = ?, salneurria = ? WHERE id = ?');
    mysqli_stmt_bind_param($statement, 'sdi', $description, $price, $id);
    mysqli_stmt_execute($statement);
    mysqli_stmt_close($statement);
    redirectPage('index.php');
}
?>
<fieldset class="compact-form">
    <legend>Deskripzioa</legend>
    <h4><?php echo htmlText($product['izena']); ?> - <?php echo htmlText($product['salneurria']); ?>€</h4>
    <img src="images/<?php echo htmlText(rawurlencode(basename($product['pic']))); ?>" alt="<?php echo htmlText($product['izena']); ?>">
    <form action="index.php?action=description&amp;pic_id=<?php echo $id; ?>" method="post">
            <?php echo csrfField(); ?>
        <p><label for="description-text">Deskripzioa:</label>
        <textarea name="deskripzioa" id="description-text" cols="50" rows="10"><?php echo htmlText($product['deskripzioa']); ?></textarea></p>
        <p><label for="description-price">Salneurri berria:</label>
        <input type="number" name="salneurria" id="description-price" min="0" step="0.01"></p>
        <input type="submit" value="Aldatu">
    </form>
</fieldset>
