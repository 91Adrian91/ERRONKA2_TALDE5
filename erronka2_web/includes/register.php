<?php
require_once __DIR__ . '/helpers.php';

$data = ['email' => '', 'firstname' => '', 'lastname' => '', 'postcode' => '', 'city' => '', 'stateProv' => '', 'country' => '', 'telephone' => ''];
$error = '';
if (isset($_POST['data']) && is_array($_POST['data'])) {
    foreach ($data as $key => $value) {
        $data[$key] = inputText($_POST['data'], $key);
    }
    $data['email'] = trim($data['email']);
    $password = inputText($_POST['data'], 'password');
    $limits = ['firstname' => 30, 'lastname' => 60, 'city' => 60, 'stateProv' => 60, 'country' => 30];
    foreach ($limits as $key => $limit) {
        if (mb_strlen($data[$key]) > $limit) {
            throw new RequestException('Eremu batek baimendutako luzera gainditzen du.');
        }
    }
    if (!preg_match('/^[0-9]{1,5}$/', $data['postcode']) || !preg_match('/^[0-9]{1,9}$/', $data['telephone'])) {
        throw new RequestException('Postakodea edo telefonoa ez da zuzena.');
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($data['email']) > 30 || !validPassword($password) || $password !== inputText($_POST['data'], 'password2')) {
        $error = 'Helbide elektronikoa eta pasahitzak egiaztatu (gutxienez 8 karaktere).';
    } else {
        requireUniqueUser($conx, $data['email']);
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $image = imageUpload('imagen', __DIR__ . '/../perfiles');
        $statement = mysqli_prepare($conx, 'INSERT INTO users (username, password, izena, abizena, hiria, lurraldea, herrialdea, postakodea, telefonoa, irudia) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($statement, 'ssssssssss', $data['email'], $hash, $data['firstname'], $data['lastname'], $data['city'], $data['stateProv'], $data['country'], $data['postcode'], $data['telephone'], $image);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
        redirectPage('index.php');
    }
}
?>
<div class="register registration-form">
    <h2>Erregistroa egin</h2>
    <p><?php echo htmlText($error); ?></p>
    <form action="index.php?action=register" method="post" enctype="multipart/form-data">
            <?php echo csrfField(); ?>
        <p><label for="register-email">Email/username:</label><input type="email" name="data[email]" id="register-email" value="<?php echo htmlText($data['email']); ?>" maxlength="30" autocomplete="username" required></p>
        <p><label for="register-firstname">Izena:</label><input type="text" name="data[firstname]" id="register-firstname" value="<?php echo htmlText($data['firstname']); ?>"></p>
        <p><label for="register-lastname">Abizena:</label><input type="text" name="data[lastname]" id="register-lastname" value="<?php echo htmlText($data['lastname']); ?>"></p>
        <p><label for="register-city">Hiria:</label><input type="text" name="data[city]" id="register-city" value="<?php echo htmlText($data['city']); ?>"></p>
        <p><label for="register-state">Lurraldea:</label><input type="text" name="data[stateProv]" id="register-state" value="<?php echo htmlText($data['stateProv']); ?>"></p>
        <p><label for="register-country">Herrialdea:</label><input type="text" name="data[country]" id="register-country" value="<?php echo htmlText($data['country']); ?>"></p>
        <p><label for="register-postcode">Postakodea:</label><input type="text" name="data[postcode]" id="register-postcode" value="<?php echo htmlText($data['postcode']); ?>" required></p>
        <p><label for="register-telephone">Telefonoa:</label><input type="tel" name="data[telephone]" id="register-telephone" value="<?php echo htmlText($data['telephone']); ?>" required></p>
        <p><label for="register-password">Pasahitza (8–72 byte):</label><input type="password" name="data[password]" id="register-password" minlength="8" maxlength="72" autocomplete="new-password" required></p>
        <p><label for="register-confirm">Pasahitza errepikatu:</label><input type="password" name="data[password2]" id="register-confirm" minlength="8" autocomplete="new-password" required></p>
        <p><label for="register-image">Irudia:</label><input type="file" name="imagen" id="register-image" accept="image/png,image/jpeg,image/gif,image/webp"></p>
        <button type="reset">Clear</button>
        <input type="submit" name="submit" value="Submit">
    </form>
</div>
