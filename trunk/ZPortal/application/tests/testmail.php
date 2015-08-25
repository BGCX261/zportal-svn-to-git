<?php
include_once 'Zend/Mail.php';
include_once 'Zend/Mail/Transport/Smtp.php';
/**
	 * Sends an activation email to the new user
	 */
	function sendActivationEmail($email,$activationId) {
		
		#$activationUrl = 'http://localhost/index.php/user/activate/activationId/' . $activationId;
		#$bodyText = utf8_encode('Dear User<br>Welcome to ZPortal.<br>In order to activate your account please visit the following link <a href="' . $activationUrl . '">' . $activationUrl . '</a>');
		#$bodyText = utf8_encode('Dear User\nWelcome to ZPortal.\nIn order to activate your account please visit the following link ');
		
		$bodyText='Dear User, welcome to ZPortal.';
		$bodyText.='In order to activate your account, please visit the following link';
		
				
		$config = array ('auth' => 'login', 'username' => 'Eden', 'password' => '!27nov2005' );
		$transport = new Zend_Mail_Transport_Smtp('il-ex1.zend.net', $config);		
		
		$mail = new Zend_Mail();
		$mail->setBodyText ($bodyText);		
		$mail->setFrom ( 'zportal@zend.com', 'ZPortal' );
		$mail->addTo ( $email, $email );
		$mail->setSubject ( 'Welcome to ZPortal' );
		$mail->send($transport);
		
	}
	
			
	sendActivationEmail("eden@zend.com", md5("1234"));
	
?>