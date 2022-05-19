<?php
require "db.php";
require "sessions.php";

$USERNAME_ERROR = $PASSWORD_ERROR = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST["username"];
    $password = $_POST["password"];

    if (empty($username)) {
        $USERNAME_ERROR = "Username is required";
    }
    if (empty($password) || strlen($password) < 8) {
        $PASSWORD_ERROR = "Password is required and must be at least 8 characters long";
    }

    if (empty($USERNAME_ERROR) && empty($PASSWORD_ERROR)) {
        if (!empty(run_sql_query("SELECT username FROM users WHERE username=$1", $username))) {
            $USERNAME_ERROR = "This username is already taken";
        } else {
            $result = run_sql_query("INSERT INTO users (username, password_hash) VALUES ($1,$2)", $username, hash('sha256', $password));
            $session = save_session($username);
            setcookie('session', $session);
            header("Location: /");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="main.css">
</head>

<body>
    <div id="header" class="centered">
        <h1>Exchange <span id='hawk'>Hawk</span></h1>
    </div>

    <div class="centered">
        <h2><b>Create an account to proceed</b></h2>
        <form action="/signup.php" method="POST">
            <?php echo "$USERNAME_ERROR<br>" ?>
            <input class="roundcorners" type="text" name="username" placeholder="Username" /><br><br>
            <?php echo "$PASSWORD_ERROR<br>" ?>
            <input class="roundcorners" type="password" name="password" placeholder="Password" /><br><br>
            <input class="roundcorners" type="submit" value="Submit" /><br>
        </form>
    </div>
</body>

</html>