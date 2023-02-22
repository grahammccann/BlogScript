<?php
    ob_start();
    session_start();
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-db-connection.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-functions.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-header.php");
?>

<main>

    <?php
	
    if (isset($_SESSION['member'])) {
        header('Location: dashboard.php');
        exit;        
    }
    ?>

    <?php
    if (isset($_POST['submitLogin'])) {
    
        $errors = [];

        $username = trim(strtolower($_POST['admin_username']));
        $password = trim(strtolower($_POST['admin_password']));

        if (empty($username) || empty($password)) {
            $errors[] = "Your <strong>username</strong> or <strong>password</strong> field was empty!";
        } 

        $memberInfo = DB::getInstance()->selectOne(
            "
            SELECT  DISTINCT member_username
            FROM    `members`
            WHERE   `member_username` = :member_username
            AND     `member_password` = :member_password",
            [
                'member_username' => $username,
                'member_password' => $password
            ]
        );

        if ($memberInfo == null) {
            $errors[] = "Your <strong>credentials</strong> were not found in the database!";
        }
        
        if (!empty($errors) > 0) {
            
            foreach($errors as $error) {
                echo "<div class=\"alert alert-danger\" role=\"alert\"><i class=\"fas fa-exclamation-triangle\"></i> {$error}</div>";
            }
            
        } else {
            
            $_SESSION['member'] = $username;        
            header('Location: dashboard.php');
        }       
    }
	
    ?>
	
	<h1 class="text-center">Login</h1>

	<div class="d-flex justify-content-center">
		
		<form action="login.php" class="w-50" method="post"> 

			<div class="mb-3">
				<label class="form-label"><strong>Username:</strong></label>
				<input type="text" class="form-control" id="admin_username" name="admin_username" placeholder="" required>
			</div>

			<div class="mb-3">
				<label class="form-label"><strong>Password:</strong></label>
				<input type="password" class="form-control" id="admin_password" name="admin_password" placeholder="" required>
			</div>
			
			<div class="mb-3 text-center">
				<a href="<?= urlFull(); ?>recovery.php" class="text-decoration-none"><small>Forgot your password?</small></a>
			</div>

			<div class="d-grid">
				<button class="btn btn-primary btn-lg" id="submitLogin" name="submitLogin" type="submit"><i class="fas fa-sign-in-alt"></i> Login</button>
			</div>

		</form>
		
	</div>
	
</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>