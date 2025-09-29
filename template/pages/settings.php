<?php

if(!isAdmin())
	header('Location: '.$_url);


if(!file_exists(__DIR__.'/admincp/'.$_type.'.php')){

	echo '<div class="alert alert-danger" role="alert">
			  <h4 class="alert-heading">An error occurred!</h4>
			  <p>Oops!!! You have just accessed a link that does not exist on the system or you do not have enough permissions to access.</p>
			  <hr>
			  <p class="mb-0">Please return to the previous page.</p>
			</div>';

	goto end;
} else
	include __DIR__.'/admincp/'.$_type.'.php';


end:

?>