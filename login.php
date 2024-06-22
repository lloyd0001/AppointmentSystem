<?php
// Include the database connection file
$database = new mysqli("localhost", "root", "", "Appointment dbms");
if ($database->connect_error) {
    die("Connection failed: " . $database->connect_error);
}

// Create a new XML document
$xml = new DOMDocument("1.0", "UTF-8");
$xml->formatOutput = true;

// Create the root element
$root = $xml->createElement("database");
$xml->appendChild($root);

// Query to fetch table names from the database schema
$tableQuery = $database->query("SHOW TABLES");
while ($row = $tableQuery->fetch_row()) {
    $tableName = $row[0];

    // Create an element for the table
    $tableElement = $xml->createElement($tableName);
    $root->appendChild($tableElement);

    // Query to fetch all rows from the current table
    $result = $database->query("SELECT * FROM $tableName");

    // Loop through each row
    while ($row = $result->fetch_assoc()) {
        // Create an element for each row
        $rowElement = $xml->createElement("row");
        $tableElement->appendChild($rowElement);

        // Add each column as a child element
        foreach ($row as $columnName => $columnValue) {
            $columnElement = $xml->createElement($columnName, htmlspecialchars($columnValue));
            $rowElement->appendChild($columnElement);
        }
    }
}

// Save the XML document to a file
$xml->save("data.xml");

echo "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/animations.css">  
    <link rel="stylesheet" href="css/main.css">  
    <link rel="stylesheet" href="css/login.css">
        
    <title>Login</title>
    
    <style>
        body {
            background-image: url('img/bg01.jpg'); /* Path to your background image */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .container {
            background-color: rgba(220, 226, 230, 0.8); /* White background with opacity */
            border-radius: 10px;
            padding: 20px;
            
        }
    </style>
</head>
<body>
    <?php

    //learn from w3schools.com
    //Unset all the server side variables

    session_start();

    $_SESSION["user"] = "";
    $_SESSION["usertype"] = "";
    
    // Set the new timezone
    date_default_timezone_set('Asia/Kolkata');
    $date = date('Y-m-d');

    $_SESSION["date"] = $date;
    

    //import database
    include("connection.php");

    if ($_POST) {

        $email = $_POST['useremail'];
        $password = $_POST['userpassword'];
        
        $error = '<label for="promter" class="form-label"></label>';

        $result = $database->query("select * from webuser where email='$email'");
        if ($result->num_rows == 1) {
            $utype = $result->fetch_assoc()['usertype'];
            if ($utype == 'p') {
                $checker = $database->query("select * from patient where pemail='$email' and ppassword='$password'");
                if ($checker->num_rows == 1) {
                    // Patient dashboard
                    $_SESSION['user'] = $email;
                    $_SESSION['usertype'] = 'p';
                    
                    header('location: User/index.php');
                } else {
                    $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Wrong credentials: Invalid email or password</label>';
                }
            } elseif ($utype == 'a') {
                $checker = $database->query("select * from admin where aemail='$email' and apassword='$password'");
                if ($checker->num_rows == 1) {
                    // Admin dashboard
                    $_SESSION['user'] = $email;
                    $_SESSION['usertype'] = 'a';
                    
                    header('location: admin/index.php');
                } else {
                    $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Wrong credentials: Invalid email or password</label>';
                }
            } elseif ($utype == 'd') {
                $checker = $database->query("select * from doctor where docemail='$email' and docpassword='$password'");
                if ($checker->num_rows == 1) {
                    // Doctor dashboard
                    $_SESSION['user'] = $email;
                    $_SESSION['usertype'] = 'd';
                    header('location: Staff/index.php');
                } else {
                    $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Wrong credentials: Invalid email or password</label>';
                }
            }
        } else {
            $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">We can\'t find any account for this email.</label>';
        }
    } else {
        $error = '<label for="promter" class="form-label">&nbsp;</label>';
    }

    ?>

    <center>
    <div class="container">
        <table border="0" style="margin: 0;padding: 0;width: 60%;">
            <tr>
                <td>
                    <p class="header-text">Welcome Back!</p>
                </td>
            </tr>
        
            <tr>
                <td>
                    <p class="sub-text">Login with your details to continue</p>
                </td>
            </tr>
            <tr>
                <form action="" method="POST">
                <td class="label-td">
                    <label for="useremail" class="form-label">Email: </label>
                </td>
            </tr>
            <tr>
                <td class="label-td">
                    <input type="email" name="useremail" class="input-text" placeholder="Email Address" required>
                </td>
            </tr>
            <tr>
                <td class="label-td">
                    <label for="userpassword" class="form-label">Password: </label>
                </td>
            </tr>
            <tr>
                <td class="label-td">
                    <input type="Password" name="userpassword" class="input-text" placeholder="Password" required>
                </td>
            </tr>
            <tr>
                <td><br>
                <?php echo $error ?>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="submit" value="Login" class="login-btn btn-primary btn">
                </td>
            </tr>
        
            <tr>
                <td>
                    <br>
                    <label for="" class="sub-text" style="font-weight: 280;">Don't have an account&#63; </label>
                    <a href="signup.php" class="hover-link1 non-style-link">Sign Up</a>
                    <br><br><br>
                </td>
            </tr>
            </form>
        </table>
    </div>
    </center>
</body>
</html>
