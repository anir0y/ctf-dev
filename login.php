<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: welcome.php");
    exit;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password FROM users WHERE username = :username";
        
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Check if username exists, if yes then verify password
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        $id = $row["id"];
                        $username = $row["username"];
                        $hashed_password = $row["password"];
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            // Redirect user to welcome page
                            header("location: welcome.php");
                        } else{
                            // Display an error message if password is not valid
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $username_err = "No account found with that username.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }
    }
    
    // Close connection
    unset($pdo);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <main>
  <article class="main-container">
    <div class="login-wrapper">
      <section class="form-wrapper">
        <header class="form-header">
          <a href="#"><i class="fas fa-arrow-left gray"></i></a>
          <a href="register.php" class="register-link gray">Register</a>
        </header>

        <!-- Contenedor del formulario y sus mensajes -->
        <div class="form-container">

          <!-- Mensaje de login -->
          <div class="form-messages">
            <h2>Login</h2>
            <p>Welcome! Please, fill username and password to sign in into your account.</p>
          </div>
          
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="form">
          
            <input type="text" name="username" class="form-control" value="<?php echo $username; ?>" placeholder= "UserName ">
                <span class="help-block"><?php echo $username_err; ?></span>
            <input type="password" name="password" class="form-control" placeholder="Type your password">
                <span class="help-block"><?php echo $password_err; ?></span>

            <a href="#" class="forgot-pass">Forgot your password?</a>
            <input type="submit" class="login-button" value="Login">
            
          </form>
          <hr class="separator">

          <!-- Otras opciones de inicio login -->
          <div class="login-options">
            <button class="button">Sign up</button>

            

          </div>

        </div>

      </section>

      <!-- Contenido del lado derecho -->
      <section class="image-wrapper">

        <div class="image-message">

          <div class="image-hr-container">
            <hr class="image-hr">
          </div>

          <div class="image-text">
            <h2>Start your journey now</h2>
            <p>Start create your amazing website with us! Login into your account now and huwala.</p>
          </div>

        </div>

      </section>
    </div>
  </article>
</main>

          
</body>
</html>
