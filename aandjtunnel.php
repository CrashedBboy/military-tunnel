<?php

    date_default_timezone_set('Asia/Taipei');

    $dbhost = ':)';
    $dbuser = 'military';
    $dbpass = 'militarysucks';
    $dbname = 'military';

    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    $mysqli->query("SET NAMES utf8");

    if (mysqli_connect_errno() ) {
        echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
    }

    if (isset($_POST['message']) && $_POST['message'] != '') {
        $new = $_POST['message'];
        $sql = 'INSERT INTO messages (content, datetime) VALUES ("'.$new.'", "'.date("Y-m-d H:i:s").'")';

        if ($mysqli->query($sql) != TRUE) {
            echo "Error: " . $mysqli->error;
        }
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>A&J's Tunnel</title>
        <style>
            .time {
                color: grey;
                font-size: 0.5em;
            }
        </style>
    </head>
    <body>
        <form action="" method="POST">
            <textarea name="message" style="display: block;"></textarea>
            <input type="submit" value="Send"/>
        </form>

        <p>
            <h2>Latest Messages</h2>
            <?php
                $sql = 'SELECT * FROM messages ORDER BY datetime DESC LIMIT 20';
                $results = $mysqli->query($sql);
                while ($message = $results->fetch_assoc() ) {
                    $datetime = date("m/d H:i", strtotime($message['datetime']));
                    echo '<div><em class="time">'.$datetime.'</em><div>'.$message['content'].'</div></div>';
                }
            ?>
        </p>
    </body>
</html>

<?php
    $mysqli->close();
?>