<?php

    include 'config.php';

// Функцыг оруулж ирэх
function isStrongPassword($password) {
    // Нууц үгийн урт нь 8-с их байх шаардлагатай
    if (strlen($password) < 8) {
        return false;
    }

    
    // Тоон тэмдэгт, жижиг үсэг, том үсэгтэй нууц үг
    if (!preg_match('/[0-9]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password)) {
        return false;
    }

    // Тусгай тэмдэгттэй нууц үг
    $special_chars = '!@#$%^&*()_-=+;:,.?';
    if (strpbrk($password, $special_chars) === false) {
        return false;
    }

    // Бүх шалгах амжилттай бол true буцаана
    return true;
}

if(isset($_POST['submit'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));
   $cpass = mysqli_real_escape_string($conn, md5($_POST['cpassword']));
   $user_type = $_POST['user_type'];

   // $name = input_escape($conn, $_POST['name']);
   // $email = input_escape($conn, $_POST['email']);
   
   //    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
   //       $emailErr = "Invalid e-mail format";
   //    }
   // $pass = input_escape($conn, md5($_POST['password']));
   // $cpass = input_escape($conn, md5($_POST['cpassword']));
   
   $select_users = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'") or die('query failed');

   function input_escape ($data){
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
   }

   if(mysqli_num_rows($select_users) > 0){
      $message[] = 'user already exist!';
   }
   else{
      if($pass != $cpass){
         $message[] = 'confirm password not matched!';
      }
      else{
         // Нууц үгийг шалгаж, strong password мөн эсэхийг шалгана
         if (isStrongPassword($_POST['password'])) {

             // Хэрэв админ эрхтэй хэрэглэгч нэг ч байгаа бол зөвхөн 1 л хэрэглэгчийг админаар бүртгэж авна
             if($user_type == 'admin') {
               $admin_count = mysqli_query($conn, "SELECT COUNT(*) AS admin_count FROM users WHERE user_type = 'admin'") or die('query failed');
               $admin_count_row = mysqli_fetch_assoc($admin_count);
               $admin_count_value = $admin_count_row['admin_count'];
               if($admin_count_value == 0) {
                  mysqli_query($conn, "INSERT INTO `users`(name, email, password, user_type) VALUES('$name', '$email', '$cpass', '$user_type')") or die('query failed');
                  $message[] = 'registered successfully!';
                  header('location:login.php');
               }
               else {
                  $message[] = 'Admin user already exists!';
               }
            }
            else {
               mysqli_query($conn, "INSERT INTO `users`(name, email, password, user_type) VALUES('$name', '$email', '$cpass', '$user_type')") or die('query failed');
               $message[] = 'registered successfully!';
               header('location:login.php');
            }

         } 
         else {
            $message[] = 'Weak password !!!';
         }
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
   <title>register</title>

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
      <h3>Бүртгүүлэх</h3>
      <input type="text" name="name" placeholder="Нэрээ оруулна уу" required class="box">
      <input type="email" name="email" placeholder="Мэйл хаягаа оруулна уу" required class="box">
      <!-- <input type="password" name="password" placeholder="Нууц үгээ оруулна уу" required class="box"> -->
      <!-- <input type="password" name="cpassword" placeholder="Нууц үгээ баталгаажуулна уу" required class="box"> -->

      <input type="password" name="password" id="passwordInput" placeholder="Нууц үгээ оруулна уу" required class="box">
<span id="togglePassword" class="toggle-password"><i class="fas fa-eye"></i></span>
<script>
   const passwordInput = document.getElementById('passwordInput');
   const togglePassword = document.getElementById('togglePassword');

   togglePassword.addEventListener('click', function() {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      togglePassword.querySelector('i').classList.toggle('fa-eye-slash');
   });
</script>

<input type="password" name="cpassword" id="cpasswordInput" placeholder="Нууц үгээ баталгаажуулна уу" required class="box"  class="fas fa-eye">
<span id="ctogglePassword" class="toggle-password"><i class="fas fa-eye"></i></span>
<script>
   const cpasswordInput = document.getElementById('cpasswordInput');
   const ctogglePassword = document.getElementById('ctogglePassword');

   ctogglePassword.addEventListener('click', function() {
      const type = cpasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      cpasswordInput.setAttribute('type', type);
      ctogglePassword.querySelector('i').classList.toggle('fa-eye-slash');
   });
</script>













      <select name="user_type" class="box">
         <option value="user">Хэрэглэгч</option>
         <option value="admin">Админ</option>
      </select>
      <input type="submit" name="submit" value="Бүртгүүлэх" class="btn">
      <p>Та бүртгэлтэй юу? <a href="login.php">Нэвтрэх</a></p>
   </form>

</div>

</body>
</html>