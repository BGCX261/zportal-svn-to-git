<?php
/**
 * Zend Portal
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @author 
 */

include_once 'Zend/Mail/Protocol/Smtp/Auth/Login.php';
include_once 'Zend/Mail.php';
include_once 'Zend/Mail/Transport/Smtp.php';
include_once 'Zend/Config/Ini.php';
include_once 'Zend/Auth/Result.php';
include_once 'application/models/Users.php';
include_once 'Zend/Auth/Adapter/Interface.php';

class AuthUserAdapter implements Zend_Auth_Adapter_Interface
{

    private $name;

    private $password;

    /**
     * Sets username and password for authentication
     *
     * @return void
     */
    public function __construct($name, $password)
    {
        $this->name = $name;
        $this->password = $password;
    }

    /**
     * Performs an authentication attempt
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        $config = new Zend_Config_Ini('../config/zportal.ini', 'mail');
        $mailConfig = array('auth' => 'login' , 'username' => $this->name , 'password' => $this->password);
        $login = new Zend_Mail_Protocol_Smtp_Auth_Login($config->mail->get('server'), null, $mailConfig);
        $login->connect();
        try {
            $login->helo("localhost");
        } catch (Exception $e) {
            // unauth user
            $result = Zend_Auth_Result::FAILURE;
            $identity = $this->name;
            $message = 'Authentication failed. Please check your login details or call system admin.';
            return new Zend_Auth_Result($result, $identity, array($message));
        }
        
        // create result array
        $users = new Users();
        $email = strtolower($this->name . "@zend.com");
        $user = $users->getByEmail($email);
        
        // if first time visit  
        if (!$user) {
            // add record to users
            $users->addUser(array('email' => $email , 'role' => 'member'));
            $user = $users->getByEmail($email);
            
            // send welcome page
            $bodyHtml = 'Dear User<br>Welcome to ZPortal.<br>';
            $config = new Zend_Config_Ini('../config/zportal.ini', 'mail');
            $transport = new Zend_Mail_Transport_Smtp($config->mail->get('server'), $mailConfig);
            $mail = new Zend_Mail();
            $mail->setBodyText("See html attachment");
            $mail->setBodyHtml($bodyHtml, 'UTF-8', Zend_Mime::ENCODING_BASE64);
            $mail->setFrom('zportal@zend.com', 'ZPortal');
            $mail->addTo($email, $email);
            $mail->setSubject('Welcome to ZPortal');
            $mail->send($transport);
        }
        $result = Zend_Auth_Result::SUCCESS;
        $identity = $user;
        $message = '';
        
        return new Zend_Auth_Result($result, $identity, array($message));
    }
}

?>