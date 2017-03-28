<?php
include BASE_PATH.'libraries/phpmailer/class.phpmailer.php';

class Gumbo_Mailer extends PHPMailer
{
	public function sendNewPass($token, $email)
	{
 		$html = '
		<body>
			<p>Je hebt een nieuw wachtwoord aangevraagd. Via de onderstaande link kan je een nieuw wachtwoord instellen:</p>
			<p><a href="http://mijn.gumbo-millennium.nl/newpass/token/'.$token.'">Wachtwoord instellen</a></p>
			<p>Met vriendelijke groet,</p>
			<p>Het Gumbo administratiesysteem</p></p>
		</body>';

		$this->addAddress($email);
		$this->Subject = 'Nieuw wachtwoord';
		$this->setFrom('no-reply@gumbo-millennium.nl', 'Administratie Gumbo');
		$this->Body = $html;
		$this->AltBody = strip_tags($html, '<a>');
		$this->isHTML(true);
		$this->send();

		$this->clearAllRecipients();
	}
}