<?php

    date_default_timezone_set('Asia/Taipei');

    $dbhost = '127.0.0.1';
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
        if (isset($_POST['user']) && ($_POST['user'] == 'A' || $_POST['user'] == 'J') ) {
            $user = $_POST['user'];
            $now = date("Y-m-d H:i:s");

            $sql = 'INSERT INTO messages (content, user, datetime) VALUES ("'.$new.'", "'.$user.'", "'.$now.'")';
            if ($mysqli->query($sql) != TRUE) {
                echo "Error: " . $mysqli->error;
            }
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
                font-size: 7px;
            }
            body {
                color: white;
                background-color: black;
            }

            .A {
                color: #71a4f7;
            }

            .J {
                color: #f49aa4;
            }
        </style>
    </head>
    <body>
        <form action="" method="POST">
            <input type="radio" name="user" id="A" value="A" checked />
            <label for="A">霖</label>
            <input type="radio" name="user" id="J" value="J"/>
            <label for="J">嫚</label>
            <textarea name="message" style="display: block;"></textarea>
            <input type="submit" value="送出"/>
        </form>

        <p>
            <h3>-最新訊息-</h3>
            <?php
                $sql = 'SELECT * FROM messages ORDER BY datetime DESC LIMIT 20';
                $results = $mysqli->query($sql);
                while ($message = $results->fetch_assoc() ) {
                    $str = '<div>';
                    $datetime = date("m/d H:i", strtotime($message['datetime']));
                    $str .= '<em class="time">'.$datetime.'</em>';

                    $content = '';
                    if ($message['user'] == 'A') {
                        $content .= '<div class="A">霖: '.$message['content'].'</div>';
                    } else if ($message['user'] == 'J') {
                        $content .= '<div class="J">嫚: '.$message['content'].'</div>';
                    } else {
                        $content .= '<div>'.$message['content'].'</div>';
                    }

                    $str .= $content.'</div>';

                    echo $str;
                }
            ?>
        </p>
    </body>
</html>

<?php
    $mysqli->close();
?>