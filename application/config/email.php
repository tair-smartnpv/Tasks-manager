<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$config['protocol'] = 'smtp';
//$config['smtp_host'] = 'ssl://smtp.gmail.com';
$config['smtp_host'] = '127.0.0.1';
$config['smtp_port'] = 1025;
$config['smtp_timeout'] = '7';
//$config['smtp_user'] = $email;
//$config['smtp_pass'] = $pass;
$config['charset'] = 'utf-8';
$config['mailtype'] = 'html';
$config['newline'] = "\r\n";
$config['wordwrap'] = TRUE;
//$this->email->initialize($config);
//$this->email->set_mailtype("html");
//$this->email->from($email, $email);
//$this->email->to($email);
//$this->email->subject($subject);
//$this->email->message($message);
//$this->email->send();
