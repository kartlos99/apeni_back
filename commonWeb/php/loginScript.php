<?php
namespace Apeni\JWT;


session_start();
$error = '';
//require_once 'config.php';

if (isset($_POST['submit'])) {

    if (empty($_POST['username']) || empty($_POST['password'])) {
        $error = "enter user & password!";
    } else {

        $subName = $_POST['username'];
        $subPass = $_POST['password'];

        // print_r($_POST);
//        $conn = mysqli_connect(HOST, DB_user, DB_pass, DB_name);

        $subName = mysqli_real_escape_string($con, $subName);
        $subPass = mysqli_real_escape_string($con, $subPass);

        $sql = "SELECT id, username, name, type FROM `users` 
        WHERE 
            `active` = 1 AND
            `username` = '$subName' AND 
            `pass` = '$subPass'";

        $result = mysqli_query($con, $sql);

        if (mysqli_num_rows($result) == 1) {

            $userData = mysqli_fetch_assoc($result);

            //$subpass = $db_f->hash_password($subpass);

                $_SESSION['firstname'] = $userData['name'];
                $_SESSION['userID'] = $userData['id'];
                $_SESSION['usertype'] = $userData['type'];
                $_SESSION['username_exp'] = $subName;
                $_SESSION['username'] = $userData['username'];

            $payload = [
                'iat' => time(),
                'iss' => 'localhost',
                'exp' => time() + 12 * 60 * 60,
                'userID' => $userData['id'],
                'userType' => $userData['type'],
                'username' => $userData['username']
            ];

            $token = JWT::encode($payload, SECRET_KEY);

            $_SESSION['tkn'] = $token;

//                if ($results['passExp'] < 0) {
//                    $_SESSION['username'] = $subName;
//                } else {
//                    $url = "http" . ((!empty($_SERVER['HTTPS'])) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
//                    $url = str_replace('login.php', 'changepass.php', $url);
//                    header("Location: $url");
//                }

                // print_r($_SESSION);

                // $url = "http" . ((!empty($_SERVER['HTTPS'])) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
                // $url = str_replace('login.php', 'administrator/page1.php', $url);
                // $error = $url;
                // header("Location: $url");


        } else {
            $error = "can't identify user!";
        }

        mysqli_close($con);
    }

}