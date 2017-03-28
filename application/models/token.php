<?php

class Token extends Storm
{
	public static function create($persoon_id, $email, $lifetime = 0, $code = 'default')
	{
		$token = new Token();
		$token->token = Str::random(40);
		$token->created_at = time();
		$token->lifetime = $lifetime;
		$token->code = $code;
		$token->persoon_id = $persoon_id;
		$token->email = $email;
		$token->save();

		$mailer = new Gumbo_Mailer();
		$mailer->sendNewPass($token->token, $email);

		return true;
	}

	public static function forget($persoon_id, $email)
	{
		Token::where('persoon_id', '=', $persoon_id)->delete();
	}

	public static function resetPassword($token, $password)
	{
		$token_data = Token::where('token', '=', $token)->first();

		$persoon = Persoon::find($token_data->persoon_id);

		$persoon->wachtwoord = Hash::make($password);
		$persoon->save();

		DB::table('log')->insert(array('persoon_id' => $persoon->id, 'datetime' => date('Y-m-d H:i:s'), 'type' => 'login', 'value' => 'newpassword'));
		Token::find($token_data->id)->delete();
	}
}