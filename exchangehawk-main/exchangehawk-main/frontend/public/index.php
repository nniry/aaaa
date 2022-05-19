<?php
require "sessions.php";
include_once("db.php");

$STOCK = null;

if (!isset($_COOKIE["session"]) || empty(get_user_by_session($_COOKIE["session"]))) {
    header("Location: /signin.php");
    exit();
} else {
    $USER = get_user_by_session($_COOKIE["session"]);
    $USER_STOCKS = fetch_rows("WITH user_stcks AS (SELECT * FROM user_stocks WHERE user_id=$1) SELECT symbol FROM user_stcks JOIN stocks ON stock_symbol=stocks.symbol", $USER[0]);
    if (empty($USER_STOCKS)) {
        $USER_STOCKS = array();
    }

    # If some stock is in query params, open it
    if (isset($_GET["symbol"]) && run_sql_query("SELECT EXISTS (SELECT * FROM stocks WHERE symbol = $1)", $_GET["symbol"])[0] == 't') {
        $STOCK = $_GET["symbol"];
    }

    if (isset($_GET["clearstocks"])) {
        run_sql_query("DELETE FROM user_stocks WHERE user_id = $1", $USER[0]);
        header("Location: /");
        exit();
    }

    if (isset($_GET["signout"])) {
        unset($_COOKIE['session']);
        setcookie('session', '', time() - 3600, '/');
        header("Location: /signin.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="main.css">
    <script src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
    <script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>
</head>

<body>
    <div id="header">
        <h1 style="margin: 5px;">Exchange <span id='hawk'>Hawk</span></h1>
        <span><b>User</b>: <?php echo $USER[0] ?></span>&nbsp;(<a href="/?signout=true">Sign out</a>)<br>
        <span><b>Stocks tracked</b>: <?php echo count($USER_STOCKS) ?></span>
        <br>
        <div id="actions-list">
            <a href='/newstock.php' class="shortlink">Track a new stock</a>
            <a href='/?clearstocks=true' class="shortlink">Clear tracked stocks</a>
        </div>
        <small>Please select stock from the list below to view its price change
            <br>
            <select style="width: 100%; height: 50px; font-size: 20px; text-align: center;" onchange="location = '?symbol=' + this.value;">
                <option>--</option>
                <?php
                foreach ($USER_STOCKS as $stock) {
                    if ($stock["symbol"] == $STOCK) {
                        echo "<option selected='true' value='" . $stock["symbol"] . "'>" . $stock["symbol"] . "</a></option>";
                    } else {
                        echo "<option value='" . $stock["symbol"] . "'>" . $stock["symbol"] . "</a></option>";
                    }
                }
                ?>
            </select>
    </div>

    <div style="margin-top: 20%;">
        <?php
        if (isset($STOCK)) {
            echo '<div id="chartContainer"></div>';
        }
        ?>
</body>

<script>
    window.onload = function() {

        var dataPoints = [];

        var chart = new CanvasJS.Chart("chartContainer", {
            animationEnabled: true,
            theme: "light",
            zoomEnabled: true,
            title: {
                text: "<?php $STOCK ?>"
            },
            axisY: {
                title: "Price",
                titleFontSize: 24,
                prefix: "$"
            },
            data: [{
                type: "line",
                yValueFormatString: "$#,##0.00",
                dataPoints: dataPoints
            }]
        });

        function addData(data) {
            for (var key in data) {
                dataPoints.push({
                    x: new Date(key),
                    y: data[key]
                });
            }
            chart.render();
        }

        $.getJSON("http://127.0.0.1:88/get-price/<?php echo $STOCK; ?>", addData);

    }
</script>

</html>