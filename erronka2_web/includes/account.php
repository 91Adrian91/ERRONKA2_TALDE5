<?php
require_once __DIR__ . '/helpers.php';
requireAccount();

const ACCOUNT_PAGE = 'index.php?action=account';
$username = (string) $_SESSION['username'];
if (isset($_GET['changepass']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = inputText($_POST, 'newpass');
    $statement = mysqli_prepare($conx, 'SELECT password FROM users WHERE username = ? LIMIT 1');
    mysqli_stmt_bind_param($statement, 's', $username);
    mysqli_stmt_execute($statement);
    $credentials = mysqli_fetch_assoc(mysqli_stmt_get_result($statement));
    mysqli_stmt_close($statement);
    if (!$credentials || !password_verify(inputText($_POST, 'currentpass'), $credentials['password'])
        || !validPassword($password) || $password !== inputText($_POST, 'confnewpass')) {
        throw new RequestException('Oraingo pasahitza eta pasahitz berriak egiaztatu.');
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $statement = mysqli_prepare($conx, 'UPDATE users SET password = ? WHERE username = ?');
    mysqli_stmt_bind_param($statement, 'ss', $hash, $username);
    mysqli_stmt_execute($statement);
    mysqli_stmt_close($statement);
    destroyLoginSession();
    redirectPage('index.php');
}
if (isset($_GET['adduser']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    requireAccount(true);
    $newUser = trim(inputText($_POST, 'newuser'));
    $password = inputText($_POST, 'newuserpass');
    if (!filter_var($newUser, FILTER_VALIDATE_EMAIL) || mb_strlen($newUser) > 30 || !validPassword($password)) {
        throw new RequestException('Kontu berriaren datuak ez dira zuzenak.');
    }
    requireUniqueUser($conx, $newUser);
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $statement = mysqli_prepare($conx, 'INSERT INTO users (username, password, izena, abizena, hiria, lurraldea, herrialdea, postakodea, telefonoa, irudia) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $blank = '';
    $zero = 0;
    mysqli_stmt_bind_param($statement, 'sssssssiis', $newUser, $hash, $blank, $blank, $blank, $blank, $blank, $zero, $zero, $blank);
    mysqli_stmt_execute($statement);
    mysqli_stmt_close($statement);
    redirectPage(ACCOUNT_PAGE);
}
if (isset($_GET['deleteuser']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    requireAccount(true);
    $deleteUser = inputText($_GET, 'deleteuser');
    if ($deleteUser !== $username) {
        $statement = mysqli_prepare($conx, 'DELETE FROM users WHERE username = ?');
        mysqli_stmt_bind_param($statement, 's', $deleteUser);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
    }
    redirectPage(ACCOUNT_PAGE);
}
?>
<div class="account-layout">
    <fieldset class="compact-form">
        <legend>Change Password</legend>
        <form action="index.php?action=account&amp;changepass=1" method="post">
            <?php echo csrfField(); ?>
            <p><label for="current-password">Oraingo pasahitza:</label>
            <input type="password" name="currentpass" id="current-password" autocomplete="current-password" required></p>
            <p><label for="new-password">New Password:</label>
            <input type="password" name="newpass" id="new-password" minlength="8" autocomplete="new-password" required></p>
            <p><label for="confirm-password">Confirm New Password:</label>
            <input type="password" name="confnewpass" id="confirm-password" minlength="8" autocomplete="new-password" required></p>
            <input type="submit" value="Change">
        </form>
    </fieldset>
    <?php if (administrator()): ?>
    <div>
        <fieldset class="compact-form">
            <legend>Add User</legend>
            <form action="index.php?action=account&amp;adduser=1" method="post">
            <?php echo csrfField(); ?>
                <p><label for="new-user">New user's username:</label>
                <input type="text" name="newuser" id="new-user" maxlength="30" required></p>
                <p><label for="new-user-password">New user's password:</label>
                <input type="password" name="newuserpass" id="new-user-password" minlength="8" autocomplete="new-password" required></p>
                <input type="submit" value="Add">
            </form>
        </fieldset>
        <table class="user-table">
            <caption>Erabiltzaileak</caption>
            <thead><tr><th scope="col">Erabiltzailea</th><th scope="col">Ekintza</th></tr></thead>
            <tbody>
            <?php
            $users = mysqli_query($conx, 'SELECT username FROM users');
            while ($user = mysqli_fetch_assoc($users)) {
            ?>
                <tr><td><?php echo htmlText($user['username']); ?></td><td>
                <?php if ($user['username'] !== $username): ?>
                    <form action="index.php?action=account&amp;deleteuser=<?php echo htmlText(rawurlencode($user['username'])); ?>" method="post">
            <?php echo csrfField(); ?>
                        <button type="submit">Ezabatu <?php echo htmlText($user['username']); ?></button>
                    </form>
                <?php else: ?>
                    Uneko kontua
                <?php endif; ?>
                </td></tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
