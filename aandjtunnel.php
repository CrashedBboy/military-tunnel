<?php

    // phpmailer
    use PHPMailer\PHPMailer\PHPMailer;
    require 'vendor/autoload.php';

    // settings and secrets
    require 'setting.php';

    date_default_timezone_set($timezone);

    // mailer settings
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->CharSet = 'UTF-8';
    $mail->SMTPDebug = 0; // production mode
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->SMTPSecure = 'tls';
    $mail->SMTPAuth = true;

    // SMTP Auth
    $mail->Username = $auth_google['user'];
    $mail->Password = $auth_google['password'];

    // create database connection
    $mysqli = new mysqli(
        $database['host'],
        $database['user'],
        $database['password'],
        $database['name']
    );

    if (mysqli_connect_errno() ) {
        echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
    }

    $mysqli->query("SET NAMES utf8");

    if (isset($_POST['message']) && $_POST['message'] != '') {
        $new_content = $_POST['message'];

        if (isset($_POST['user']) && ($_POST['user'] == 'A' || $_POST['user'] == 'J') ) {
            $new_user = $_POST['user'];
            $new_time = date("Y-m-d H:i:s");

            $sql = 'INSERT INTO messages (content, user, datetime) VALUES ("'.$new_content.'", "'.$new_user.'", "'.$new_time.'")';

            if ($mysqli->query($sql) != TRUE) {
                echo "Error: " . $mysqli->error;

            } else {
                // notify joyce
                if ($new_user == 'A') {
                    // Recipients
                    $mail->setFrom($users['austin'], 'Austin');
                    $mail->addAddress($users['joyce'], 'JoyceHuang');

                    // Mail Content
                    $full_url = $home_url.'?default=j';
                    $mail->isHTML(true);
                    $mail->Subject = '來自Military Tunnel的新訊息';
                    $mail->Body = '<a href="'.$full_url.'" style="color: #164187;">'.$new_content.'</a>';
                    $mail->AltBody = $new_content;

                    if (!$mail->send()) {
                        echo "Mailer Error: " . $mail->ErrorInfo;
                    }
                }
            }
        }
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link rel="icon" href="favicon/16x16.png" type="image/png" sizes="16x16" />
        <link rel="icon" href="favicon/32x32.png" type="image/png" sizes="32x32" />
        <title>A&J's Tunnel</title>
        <style>
            .time {
                color: grey;
                font-size: 0.7rem;
            }
            body {
                color: white;
                background-color: rgb(30, 30, 30);
                font-family: 微軟正黑體;
            }
            .A {
                color: #71a4f7;
            }

            .J {
                color: #f49aa4;
            }
            #editor {
                width: 100%;
                border-radius: 5px;
            }
            #submit {
                border-radius: 2px;
                color: white;
                background-color: grey;
                border: none;
                padding: 0.2rem 3rem 0.2rem 3rem;
                font-family: 微軟正黑體;
            }
        </style>
    </head>

    <?php
        // set up default user
        $austin_checked = '';
        $joyce_checked = '';
        if (isset($_GET['default']) && $_GET['default'] == 'j') {
            $joyce_checked = 'checked';
        } else {
            $austin_checked = 'checked';
        }
    ?>

    <body>
        <form action="" method="POST">
            <input type="radio" name="user" id="A" value="A" <?php echo $austin_checked; ?> />
            <label for="A">霖</label>
            <input type="radio" name="user" id="J" value="J" <?php echo $joyce_checked; ?> />
            <label for="J">嫚</label>
            <textarea id="editor" name="message"></textarea>
            <input id="submit" type="submit" value="送出"/>
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