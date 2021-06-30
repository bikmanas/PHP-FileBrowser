<?php
session_start();

if (isset($_GET['action']) and $_GET['action'] == 'logout') {
    unset($_SESSION['username']);
    unset($_SESSION['password']);
    unset($_SESSION['logged_in']);
    print('Logged out!');
}

$msg = '';
if (isset($_POST['login']) && !empty($_POST['username']) && !empty($_POST['password'])) {
    if ($_POST['username'] == 'Povilas' && $_POST['password'] == '1234') {
        $_SESSION['logged_in'] = true;
        $_SESSION['timeout'] = time();
        $_SESSION['username'] = $_POST['username'];
        header('Location: index.php');
        exit;
    } else {
        $msg = 'Wrong username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
</head>

<body>
    <h2>Enter Username and Password</h2>
</body>
<div>
    <?php
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
        print('<h1>You can only see this if you are logged in!</h1>');
    }
    ?>
</div>
<div>
    <form class="login" action="./login.php" method="post">
        <h4><?php echo $msg; ?></h4>
        <input type="text" name="username" placeholder="Povilas" required autofocus></br>
        <input type="password" name="password" placeholder="1234" required>
        <div>
            <button class="btn-login" type="submit" name="login">Login</button></br>
        </div>
    </form>
</div>

</html>