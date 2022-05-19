<?php
require "sessions.php";
require "request.php";
include_once("db.php");

$STOCK_ERROR = "";

if (!isset($_COOKIE["session"]) || empty(get_user_by_session($_COOKIE["session"]))) {
    header("Location: /signin.php");
    exit();
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $USER = get_user_by_session($_COOKIE["session"]);
        $ticker = $_POST["ticker"];

        if (get_resource("http://parser:88/get-price/" . $ticker) === []) {
            $STOCK_ERROR = "This stock does not exist";
        } else {

            # Disgusting check if the returned value is truthy
            if (run_sql_query("SELECT EXISTS (SELECT symbol FROM stocks WHERE symbol = $1);", $ticker)[0] != 't') {
                run_sql_query("INSERT INTO stocks VALUES ($1);", $ticker);
            }

            if (run_sql_query("SELECT EXISTS (SELECT stock_symbol FROM user_stocks WHERE stock_symbol = $1);", $ticker)[0] != 'f') {
                $STOCK_ERROR = "You are already tracking this stock";
            } else {
                # Add ticker to user
                run_sql_query("INSERT INTO user_stocks VALUES ($1,$2);", $USER[0], $ticker);

                header("Location: /");
                exit();
            }
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
    <div>
        <center>
            <h1>Add a new stock</h1>
            <form action="/newstock.php" method="post">
                <?php echo "$STOCK_ERROR<br>" ?>
                <input class="roundcorners" type="text" maxlength="5" placeholder="Ticker name (max 5 symbols)" name="ticker" />
                <br><br>
                <input class="roundcorners" type="submit" value="Add">
            </form>
            <br>
            <a href="/">Back to main</a>
        </center>
    </div>

</body>

</html>