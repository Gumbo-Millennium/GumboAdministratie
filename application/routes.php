<?php

return array(

	'GET /, GET /dashboard' => array('before' => 'auth', function()
	{		
		return View::make('main')->bind('pagina', View::make('pages/dashboard/overzicht'));
	}), 
			
	'GET /zoeken' => array('before' => 'auth', function()
	{	
		$q = Input::get('q');
		$count = 0;
		$personen = array();
		
		if(Auth::user()->has_groep(Groep::BESTUUR)){
			$personen = Persoon::where('voornaam', 'LIKE', '%'.$q.'%')->or_where('achternaam', 'LIKE', '%'.$q.'%')->get();
			$count += count($personen);
		}
		
		$pagina = View::make('pages/zoeken/overzicht')->bind('q', $q)->bind('count', $count)->bind('personen', $personen);
		return View::make('main')->bind('pagina', $pagina);
	}), 
			
	'GET /login' => array('name' => 'login', function()
	{
		return View::make('login');
	}),   
			
	'GET /mailchimp/synchronisatie' => array( function()
	{
		if(!Auth::user()->has_groep(Groep::BESTUUR)){
			return Redirect::to('/dashboard')->with('errors', array('Je hebt geen toegang tot deze sectie'));
		}
		
		return View::make('main')->bind('pagina', View::make('pages/mailchimp/overzicht'));
	}),  		
                
	'GET /mailchimp/do-synchronize' => array('before' => 'auth', function()
	{
		if(!Auth::user()->has_groep(Groep::BESTUUR)){
			return Redirect::to('/dashboard')->with('errors', array('Je hebt geen toegang tot deze sectie'));
		}
		
		header('Content-Type: text/event-stream');
		// recommended to prevent caching of event data.
		header('Cache-Control: no-cache'); 

		function send_message($id, $message, $progress) {
			$d = array('message' => $message , 'progress' => $progress);

			echo "id: $id" . PHP_EOL;
			echo "data: " . json_encode($d) . PHP_EOL;
			echo PHP_EOL;

			ob_flush();
			flush();
		}
		
		ini_set('max_execution_time', 0);
		
		$mailchimp = new Mailchimp(Config::get('application.mailchimp_api'));
		$maillist = $mailchimp->get('lists/'.Config::get('application.mailchimp_list').'/members', array('count' => 1000));
		
		$personen = Persoon::where_not_null('email')->get();
		
		$count = 1;
		$total = count($personen);
		
		foreach ($personen as $p) {
			$found = false;
			foreach ($maillist->members as $sub) {
				if ($p->email === $sub->email_address) {
					$found = true;
				}
			}

			if ($found) {		
				try {
					$lidstatus = DB::table('persoon_lidstatus')->where_persoon_id($p->id)->order_by('jaar', 'desc')->first();

					$mailchimp->patch('/lists/'.Config::get('application.mailchimp_list').'/members/'.md5(Str::lower($p->email)), array(
						'merge_fields' => array('FNAME' => $p->voornaam, 'LNAME' => (!is_null($p->tussenvoegsel) ? $p->tussenvoegsel.' ' : '').$p->achternaam,'LIDNUMMER' => $p->id, 'LIDSTATUS' => Lidstatus::find($lidstatus->lidstatus_id)->naam),
					));
					
					send_message($count, $count.'/'.$total.' - '.$p->email.' - '.$p->volledige_naam().' - Gegevens aangepast', (100 / $total * $count));
					
				} catch (Exception $e) {
					$res = json_decode($e->getMessage());
					send_message($count, $count.'/'.$total.' - '.$p->email.' - '.$p->volledige_naam().' - Gegevens konden niet aangepast worden ('.$res->title.')', (100 / $total * $count));
					$count ++;
					continue;
				}
			} else {
				try {

					$lidstatus = DB::table('persoon_lidstatus')->where_persoon_id($p->id)->order_by('jaar', 'desc')->first();

					$mailchimp->post('/lists/'.Config::get('application.mailchimp_list').'/members', array(
						'email_type' => 'html',
						'status' => 'subscribed',
						'merge_fields' => array('FNAME' => $p->voornaam, 'LNAME' => (!is_null($p->tussenvoegsel) ? $p->tussenvoegsel.' ' : '').$p->achternaam,'LIDNUMMER' => $p->id, 'LIDSTATUS' => Lidstatus::find($lidstatus->lidstatus_id)->naam),
						'email_address' => $p->email,
					));
					
					send_message($count, $count.'/'.$total.' - '.$p->email.' - '.$p->volledige_naam().' - Gegevens toegevoegd', (100 / $total * $count));
				} catch (Exception $e) {
					$res = json_decode($e->getMessage());
					send_message($count, $count.'/'.$total.' - '.$p->email.' - '.$p->volledige_naam().' - Gegevens konden niet toegevoegd worden ('.$res->title.')', (100 / $total * $count));
					$count ++;
					continue;
				}
			}
			
			$count ++;
		}
		
		send_message('CLOSE', 'Synchronisatie voltooid', 100);
	}), 
			
	'POST /login' => function()
	{
		if (Auth::login(Input::get('email'), Input::get('password'))) {
			DB::table('log')->insert(array('persoon_id' => Auth::user()->id, 'datetime' => date('Y-m-d H:i:s'), 'type' => 'login', 'value' => 'success'));
			return Redirect::to('/');
		}

		return Redirect::to('/login')->with('error', true);
	},
			
	'GET /lostpass' => function()
	{
		return View::make('lostpass');
	}, 
			
	'POST /lostpass' => function()
	{
		$persoon = Persoon::where_email(Input::get('email'))->first();

		if (is_null($persoon)) {
			return Redirect::to('/lostpass')->with('error', true);
		}

		Token::create($persoon->id, $persoon->email, 3600);
		DB::table('log')->insert(array('persoon_id' => $persoon->id, 'datetime' => date('Y-m-d H:i:s'), 'type' => 'login', 'value' => 'requestpassword'));

		return Redirect::to('/login')->with('message', 'E-mail verzonden');
	},
			
	'GET /newpass/token/(:any)' => array('name' => 'resetpassword', 'do' => function($token)
	{
		if (is_null(DB::table('token')->where_token($token)->first())) {
			return Redirect::to('/login')->with('message', '<b>Let op:</b> U kunt de link in de mail maar één keer gebruiken. Indien nodig kunt u nieuwe link aanvragen door op "Wachtwoord vergeten" te klikken.');
		}

		return View::make('newpass');
	}),

	'POST /newpass/token/(:any)' => function($token)
	{
		if (Input::get('password') != Input::get('confirm_password') || strlen(Input::get('password')) < 7) {
			return Redirect::to_resetpassword(array($token))->with('error', 'Kapot');
		}

		Token::resetPassword($token, Input::get('password'));
		return Redirect::to('/login')->with('message', 'Wachtwoord is opnieuw ingesteld. U kunt nu inloggen met het nieuwe wachtwoord.');
	},

	'GET /logout' => function()
	{
		Auth::logout();
		return Redirect::to('/login');
	}
);