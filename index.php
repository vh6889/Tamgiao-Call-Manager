<?php

$debug = 0;

error_reporting($debug ? E_ALL & ~E_NOTICE : 0);
ini_set('display_errors', $debug);

$homePath = __DIR__;

include $homePath.'/includes/loader.php';


if($_route == 'signout')
	signOut($_user);
else if($_route == 'signin')
	require $homePath.'/template/signIn.php';
else {
	require $homePath.'/template/header.php';
	if(file_exists($homePath.'/template/pages/'.$_route.'.php')){
		require $homePath.'/template/pages/'.$_route.'.php';
	} else {

		echo '<div class="alert alert-danger" role="alert">
				  <h4 class="alert-heading">An error occurred!</h4>
				  <p>Oops!!! You have just accessed a link that does not exist on the system or you do not have enough permissions to access.</p>
				  <hr>
				  <p class="mb-0">Please return to the previous page.</p>
				</div>';

	}
	require $homePath.'/template/footer.php';
}




?>