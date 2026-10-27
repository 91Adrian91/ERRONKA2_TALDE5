<?php
require_once __DIR__ . '/helpers.php';

if (signedIn()) {
    echo '<div class="centered"><h5>You are already logged in</h5></div>';
} elseif (isset($_POST['submit'])) {
    $username = (string) ($_POST['username'] ?? '');
    $statement = mysqli_prepare($conx, 'SELECT username, password FROM users WHERE username = ? LIMIT 1');
    mysqli_stmt_bind_param($statement, 's', $username);
    mysqli_stmt_execute($statement);
    $credentials = mysqli_fetch_assoc(mysqli_stmt_get_result($statement));
    mysqli_stmt_close($statement);
    if ($credentials && password_verify((string) ($_POST['password'] ?? ''), $credentials['password'])) {
        session_regenerate_id(true);
        $_SESSION['username'] = $credentials['username'];
        $_SESSION['admin'] = 1;
        unset($_SESSION['password']);
        redirectPage('index.php');
    }
    redirectPage('index.php?action=login');
} else {
?>
<div class="centered">
    <fieldset class="compact-form">
        <legend>Login</legend>
        <form action="index.php?action=login" method="post">
            <p><label for="login-username">Username/Email:</label>
            <input type="text" name="username" id="login-username" autocomplete="username" required></p>
            <p><label for="login-password">Password:</label>
            <input type="password" name="password" id="login-password" autocomplete="current-password" required></p>
            <input type="submit" name="submit" value="Login">
        </form>
    </fieldset>
</div>
<?php
}
?>
