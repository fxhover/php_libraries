<?php
//使用方法
require 'Mail.php';
$mail = new Mail();
$mail->setServer("smtp@126.com", "XXXXX@126.com", "XXXXX"); //设置smtp服务器，普通连接方式
$mail->setServer("smtp.gmail.com", "XXXXX@gmail.com", "XXXXX", 465, true); //设置smtp服务器，到服务器的SSL连接
$mail->setFrom("XXXXX"); //设置发件人
$mail->setReceiver("XXXXX"); //设置收件人，多个收件人，调用多次
$mail->setCc("XXXX"); //设置抄送，多个抄送，调用多次
$mail->setBcc("XXXXX"); //设置秘密抄送，多个秘密抄送，调用多次
$mail->addAttachment("XXXX"); //添加附件，多个附件，调用多次
$mail->setMail("test", "<b>test</b>"); //设置邮件主题、内容
$mail->sendMail(); //发送