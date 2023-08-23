<?php
$db_name='HillDownGameStore_db';
$users_table='user_table';
$string="";


$sqlConnect=new mysqli('localhost', 'archer', 'archer', $db_name);
if (mysqli_connect_errno()) {
    printf("Errore di connessione: %s\n", mysqli_connect_error());
    exit();
}

if(isset($_POST['send'])){
  if ($_POST['send']=='Login') {
    if(!(empty($_POST['email'])) && !(empty($_POST['password']))){
      if(!(preg_match("/^.*@.*/", $_POST['email']))){
        $string.="<p>Inserire l'email non un nickname</p>";
      }
      else{
        $query="SELECT * FROM `{$users_table}` WHERE email=\"{$_POST['email']}\" AND password=\"{$_POST['password']}\";";
        $return=mysqli_query($sqlConnect, $query);
        $row=mysqli_fetch_array($return);

        if($row){
          session_name('HillDownService');
          session_start();
          $_SESSION['id']=$row['id'];
          $_SESSION['email']=$row['email'];
          $_SESSION['nickname']=$row['nickname'];
          $_SESSION['ttk']=100;

            header('Location: StoreHomePage.php');
          }
        else{
          $string.="<p>Login fallito: email o password errata</p>";

        }
      }
    }
    else{
      $string.="<p>Dati mancanti i uno o entrambi i campi email e/o password</p>";
    }
  }
  elseif ($_POST['send']=='Sign In') {
    header('Location: SignIn.php');
  }

  $sqlConnect->close();
}



 ?>

 <?xml version="1.0" encoding="UTF-8"?>
 <!DOCTYPE html
 PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>HillDown Game Store</title>
    <link rel="stylesheet" href="Init_Struct__.css" media="screen">
    <link rel="stylesheet" href="login_.css" media="screen">
  </head>
  <body>
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
    <div class="flexContainer">
      <div class="flexLogin">
        <div class="">
          <img src="logo3.png" alt="">
        </div>
        <div class="login_item">
          <?php if($string!="") echo $string; ?>
          <label for="email">Email:</label>
          <input type="text" name="email" value="">

          <label for="password">Password:</label>
          <input type="password" name="password" value="">
        </div>
        <div class="">
          <input type="submit" name="send" value="Login">
          <input type="submit" name="send" value="Sign In">

        </div>


      </div>

    </div>
  </form>

  </body>
</html>
