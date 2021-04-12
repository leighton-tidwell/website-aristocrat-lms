<?php
for($i = 0; $i < 10; $i++){

	$to      = 'leighton.tidwell57@gmail.com';
	$subject = 'Coffee on Friday?';
	$message = 'Hi, Do you want to go out for coffee next friday?';
	$headers = 'From: info@newegg.com' . "\r\n" .
	    'Reply-To: info@newegg.com' . "\r\n";
	
	mail($to, $subject, $message, $headers);
}
?>