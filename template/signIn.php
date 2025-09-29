<?php

  if($_user['id'])
    header('Location: index.php');
  
  $user = isset($_POST['username']) ? trim($_POST['username']) : '';
  $pass = isset($_POST['password']) ? trim($_POST['password']) : '';
  if(isset($_POST['submit'])){
    if(signIn($user,$pass) == true){
      header('Location: index.php');
    } else {
      $_statusmessage['type'] = 'danger';
      $_statusmessage['message'] = 'Authentication Failed!';
    }    
  }


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php echo $lang['login-system']; ?></title>
    <!-- meta tags -->
    <base href="<?php echo $_url; ?>">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- /meta tags -->
    <!-- custom style sheet -->
    <link href="template/assets/css/login.css" rel="stylesheet" type="text/css" />
    <!-- /custom style sheet -->
    <!-- fontawesome css -->
    <link href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" rel="stylesheet" />
    <!-- /fontawesome css -->
    <!-- google fonts-->
    <link href="//w3layouts.sharepoint.com///fonts.googleapis.com/css?family=Raleway:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <!-- /google fonts-->

</head>
<body>
<body>
    <div class=" w3l-login-form">
        <h2><?php echo $lang['login-system']; ?></h2>
        <form method="POST" autocomplete="off">
			      <?php if(!empty($_statusmessage)): ?>
              <div class="alert alert-<?php echo $_statusmessage["type"]; ?> lert-dismissible fade show" role="alert" style="color:#fff;">
                <?php echo $_statusmessage["message"]; ?>
              </div>

            <?php endif; ?>
            <div class=" w3l-form-group">
                <label><?php echo $lang['username']; ?>:</label>
                <div class="group">
                    <i class="fas fa-user"></i>
                    <input type="text" class="form-control" placeholder="<?php echo $lang['username']; ?>" name="username" required="required" />
                </div>
            </div>
            <div class=" w3l-form-group">
                <label><?php echo $lang['password']; ?>:</label>
                <div class="group">
                    <i class="fas fa-unlock"></i>
                    <input type="password" class="form-control" placeholder="<?php echo $lang['password']; ?>" name="password" required="required" />
                </div>
            </div>
            <div class="forgot">                
                <p><input type="checkbox" checked><?php echo  $lang['remember'] ?></p>
            </div>
            <button type="submit" name="submit"><?php echo $lang['login'] ?></button>
			      <input type="hidden" name="route" value="signin">
        </form>
    </div>
</body>

</html>