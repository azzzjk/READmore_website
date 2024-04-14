<?php

include 'config.php';
session_start();

if(isset($_POST['submit'])){

   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));
   // $email = input_escape($conn, $_POST['email']);
   // $pass = input_escape($conn, md5($_POST['password']));

   $select_users = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' AND password = '$pass'") or die('query failed');

   function input_escape($data) {
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      return $data; 
  }
  

   if(mysqli_num_rows($select_users) > 0){

      

      $row = mysqli_fetch_assoc($select_users);

      if($row['user_type'] == 'admin'){

         $_SESSION['admin_name'] = $row['name'];
         $_SESSION['admin_email'] = $row['email'];
         $_SESSION['admin_id'] = $row['id'];
         header('location:admin_page.php');

      }elseif($row['user_type'] == 'user'){

         $_SESSION['user_name'] = $row['name'];
         $_SESSION['user_email'] = $row['email'];
         $_SESSION['user_id'] = $row['id'];
         header('location:home.php');

      }

   }else{
      $message[] = 'incorrect email or password!';
   }


   // Нууц үг сэргээх код
   if(isset($_POST['reset_password'])){
      $email = mysqli_real_escape_string($conn, $_POST['email']);
      $new_password = mysqli_real_escape_string($conn, md5($_POST['new_password']));

      $update_query = mysqli_query($conn, "UPDATE users SET password = '$new_password' WHERE email = '$email'") or die('update query failed');
      
      if($update_query){
         $message[] = 'Нууц үг амжилттай солигдлоо!';
      }else{
         $message[] = 'Нууц үг солих явцад алдаа гарлаа!';
      }

   }  
}

?> 

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Нэвтрэх</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  
   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>
   
<div class="form-container">

   <form action="" method="post">
      <h3>Нэвтрэх</h3>
      <input type="email" name="email" placeholder="Мэйл хаягаа оруулна уу" required class="box">
      <!-- <input type="password" name="password" placeholder="Нууц үгээ оруулна уу" required class="box"> -->
      <!-- <input type="password" name="password" id="passwordInput" placeholder="Нууц үгээ оруулна уу" required class="box with-icon"> -->

      <div class="password-wrapper">
          <input type="password" name="password" id="passwordInput" placeholder="Нууц үгээ оруулна уу" required class="box">
          <span id="togglePassword" class="toggle-password"><i class="fas fa-eye"></i></span>
       </div>
       
       <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
       <script>
          const passwordInput = document.getElementById('passwordInput');
          const togglePassword = document.getElementById('togglePassword');
 
          togglePassword.addEventListener('click', function() {
             const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
             passwordInput.setAttribute('type', type);
             togglePassword.querySelector('i').classList.toggle('fa-eye-slash');
          });
       </script>
      <input type="submit" name="submit" value="Нэвтрэх" class="btn">
      <p>Бүртгүүлээгүй юм биш биз? <a href="register.php">Бүртгүүлэх</a></p>
      <p><a href="reset_password.php">Forget password</a></p>
   
   
</form>
 </body>
</html>

