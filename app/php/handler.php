<?php
// smtp libs

function smtpmail($mail_to, $subject, $message, $headers='') {

	//настройки smtp
	$config['smtp_username'] = 'info@gunaevart.ru';  //mail на хостинге ISPManager.
	$config['smtp_password'] = '123';  //пароль.
	$config['smtp_from']     = 'Test Mailbox'; //���� ��� - ��� ��� ������ �����. ����� ���������� ��� ��������� � ���� "�� ����".
	
	$config['smtp_host']     = 'localhost';  //������ ��� �������� ����� (��� ����� �������� ������ �� ���������).
	$config['smtp_port']     = '25'; // ���� ������. �� �������, ���� �� �������.
	$config['smtp_debug']    = false;  //���� �� ������ ������ ��������� ������, ������� true ������ false.
	$config['smtp_charset']  = 'UTF-8';   //��������� ���������.

	$SEND =   "Date: ".date("D, d M Y H:i:s") . " UT\r\n";
	$SEND .=   'Subject: =?'.$config['smtp_charset'].'?B?'.base64_encode($subject)."=?=\r\n";
	if ($headers) $SEND .= $headers."\r\n\r\n";
	else
	{
			$SEND .= "Reply-To: ".$config['smtp_username']."\r\n";
			$SEND .= "MIME-Version: 1.0\r\n";
			$SEND .= "Content-Type: text/plain; charset=\"".$config['smtp_charset']."\"\r\n";
			$SEND .= "Content-Transfer-Encoding: 8bit\r\n";
			$SEND .= "From: \"".$config['smtp_from']."\" <".$config['smtp_username'].">\r\n";
			$SEND .= "To: $mail_to <$mail_to>\r\n";
			$SEND .= "X-Priority: 3\r\n\r\n";
	}
	$SEND .=  $message."\r\n";
	 if( !$socket = fsockopen($config['smtp_host'], $config['smtp_port'], $errno, $errstr, 30) ) {
		if ($config['smtp_debug']) echo $errno."<br>".$errstr;
		return false;
	 }

		if (!server_parse($socket, "220", __LINE__)) return false;

		fputs($socket, "EHLO " . $config['smtp_host'] . "\r\n");
		if (!server_parse($socket, "250", __LINE__)) {
		   if ($config['smtp_debug']) echo '<p>�� ���� ��������� EHLO!</p>';
		   fclose($socket);
		   return false;
		}
		fputs($socket, "AUTH LOGIN\r\n");
		if (!server_parse($socket, "334", __LINE__)) {
		   if ($config['smtp_debug']) echo '<p>�� ���� ����� ����� �� ������ �����������!</p>';
		   fclose($socket);
		   return false;
		}
		fputs($socket, base64_encode($config['smtp_username']) . "\r\n");
		if (!server_parse($socket, "334", __LINE__)) {
		   if ($config['smtp_debug']) echo '<p>����� ����������� �� ��� ������ ��������!</p>';
		   fclose($socket);
		   return false;
		}
		fputs($socket, base64_encode($config['smtp_password']) . "\r\n");
		if (!server_parse($socket, "235", __LINE__)) {
		   if ($config['smtp_debug']) echo '<p>������ �� ��� ������ �������� ��� ������! ������ �����������!</p>';
		   fclose($socket);
		   return false;
		}
		fputs($socket, "MAIL FROM: <".$config['smtp_username'].">\r\n");
		if (!server_parse($socket, "250", __LINE__)) {
		   if ($config['smtp_debug']) echo '<p>�� ���� ��������� ������� MAIL FROM:</p>';
		   fclose($socket);
		   return false;
		}
		fputs($socket, "RCPT TO: <" . $mail_to . ">\r\n");

		if (!server_parse($socket, "250", __LINE__)) {
		   if ($config['smtp_debug']) echo '<p>�� ���� ��������� ������� RCPT TO:</p>';
		   fclose($socket);
		   return false;
		}
		fputs($socket, "DATA\r\n");

		if (!server_parse($socket, "354", __LINE__)) {
		   if ($config['smtp_debug']) echo '<p>�� ���� ��������� ������� DATA!</p>';
		   fclose($socket);
		   return false;
		}
		fputs($socket, $SEND."\r\n.\r\n");

		if (!server_parse($socket, "250", __LINE__)) {
		   if ($config['smtp_debug']) echo '<p>�� ���� ��������� ���� ������. ������ �� ���� ����������!</p>';
		   fclose($socket);
		   return false;
		}
		fputs($socket, "QUIT\r\n");
		fclose($socket);
		return TRUE;
}
function server_parse($socket, $response, $line = __LINE__) {
	global $config;
while (substr($server_response, 3, 1) != ' ') {
	if (!($server_response = fgets($socket, 256))) {
			   if ($config['smtp_debug']) echo "<p>�������� � ��������� �����!</p>$response
$line
";
			   return false;
			}
}
if (!(substr($server_response, 0, 3) == $response)) {
	   if ($config['smtp_debug']) echo "<p>�������� � ��������� �����!</p>$response
$line
";
	   return false;
	}
return true;
}



function smtpmassmail($mail_to, $subject, $message, $headers='')
{
$mailaddresses=explode(",",$mail_to);
foreach ($mailaddresses as $mailaddress) smtpmail($mailaddress,$subject,$message,$headers);
}

// получаем данные от hendler.php


parse_str($_POST['orderlist'], $orderlist);
parse_str($_POST['userdata'], $userdata);

// /*
// $orderlist - массив со списком заказа
// $userdata - данные заказчика
// */

// // При желании, можно посмотреть полученные данные, записав их в файл:
// // file_put_contents('cart_data_log.txt', var_export($orderlist, 1) . "\r\n");
// // file_put_contents('cart_data_log.txt', var_export($userdata, 1), FILE_APPEND);


// // Формируем таблицу с заказанными товарами
$tbl = '<table style="width: 100%; border-collapse: collapse;">
	<tr>
		<th style="width: 1%; border: 1px solid #333333; padding: 5px;">ID</th>
		<th style="width: 1%; border: 1px solid #333333; padding: 5px;"></th>
		<th style="border: 1px solid #333333; padding: 5px;">Наименование</th>
		<th style="border: 1px solid #333333; padding: 5px;">Цена</th>
		<th style="border: 1px solid #333333; padding: 5px;">Кол-во</th>
	</tr>';
$total_sum = 0;
foreach($orderlist as $id => $item_data) {
	$total_sum += (float)$item_data['count'] * (float)$item_data['price'];
	$tbl .= '
	<tr>
		<td style="border: 1px solid #333333; padding: 5px;">'.$item_data['id'].'</td>
		<td style="border: 1px solid #333333;"><img src="'.$item_data['img'].'" alt="" style="max-width: 64px; max-height: 64px;"></td>
		<td style="border: 1px solid #333333; padding: 5px;">'.$item_data['title'].'</td>
		<td style="border: 1px solid #333333; padding: 5px;">'.$item_data['price'].'</td>
		<td style="border: 1px solid #333333; padding: 5px;">'.$item_data['count'].'</td>
	</tr>';
}
$tbl .= '<tr>
		<td  style="border: 1px solid #333333; padding: 5px;" colspan="3">Итого:</td>
		<td style="border: 1px solid #333333; padding: 5px;"><b>'.$total_sum.'</b></td>
		<td style="border: 1px solid #333333;">&nbsp;</td>
	</tr>
</table>';

// Заголовки

   
   $headers = 'Content-type: text/html; charset="utf-8"'; // Обязательный заголовок. Кодировку изменить при необходимости'
   $headers .= 'MIME-Version: 1.0'; // Обязательный заголовок'
   $headers .= 'From: Best Shop <noreply@best-shop.piva.net>'; // От кого
   $headers .= 'X-Mailer: PHP/'.phpversion();

// Отправка

 $to = '89106867977@mail.ru';
 $subject = 'Заказ с сайта';
 $message = '
<html>
<head>
<title>'.$subject.'</title>
</head>
<body>
<p>Информация о заказчике:</p>
 <ul>
	 <li><b>Ф.И.О.:</b> '.$userdata['user_name'].'</li>
	 <li><b>Тел.:</b> '.$userdata['user_phone'].'</li>
	 <li><b>Email:</b> '.$userdata['user_mail'].'</li>
	 <li><b>Адрес:</b> '.$userdata['user_address'].'</li>
	 <li><b>Комментарий:</b> '.$userdata['user_comment'].'</li>
 </ul>
 <p>Информация о заказае:</p>
'.$tbl.'
 <p>Письмо создано автоматически. Пожалуйста, не отвечайте на него, т.к. все ушли на пляж!</p>
</body>
</html>';
				 
// отправляем письмо
$send_ok = smtpmail($to, $subject, $message, $headers);

// Ответ на запрос
$response = [
	'errors' => !$send_ok,
	'message' => $send_ok ? 'Заказ принят в обработку!' : 'Хьюстон! У нас проблемы!'
];
// ! Для версий PHP < 5.4 использовать традиционный синтаксис инициализации массивов:
/*
$response = array (
	'errors' => !$send_ok,
	'message' => $send_ok ? 'Заказ принят в обработку!' : 'Хьюстон! У нас проблемы!'
);
*/
// возрашаем ответ о статусе отправки
exit( json_encode($response) );