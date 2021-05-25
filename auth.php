<?php
session_start();

$errors = false;

$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
$fp = fopen("./data/users.txt", "r");



if ($username && $password) {

    while ($data = fgetcsv($fp)) {
        if ($data[0] == $username && $data[1] == sha1($password)) {
            $_SESSION['loggedin'] = true;
            $_SESSION['user'] = $username;
            $_SESSION['role'] = $data[2];
            header('location:index.php');
        }
    }
    if (!isset($_SESSION['loggedin'])) {
        $errors = true;
    }
}


if (isset($_GET['logout'])) {
    $_SESSION['loggedin'] = false;
    $_SESSION['role'] = false;
    $_SESSION['user'] = false;
    session_destroy();
    header('location:index.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/milligram/1.4.1/milligram.css">
    <style>
        body {
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="column column-60 column-offset-20">
                <h2>Login Here! </h2>
            </div>
        </div>


        <div class="row">
            <div class="column column-60 column-offset-20">
                <?php
                if (true == $errors) {
                    echo "<blockquote>Username and password didn't match</blockquote>";
                }
                ?>
                <form action="" method="post">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username">

                    <label for="password">Password</label>
                    <input type="password" name="password" id="password">

                    <button type="submit" class="button-primary" name="submit">Login</button>
                </form>
            </div>

        </div>
    </div>
</body>

</html>