<?php

return array(
	
    'GET /personen/overzicht' => array('before' => 'auth', function()
    {
		if(!Auth::user()->has_groep(Groep::BESTUUR)){
			return Redirect::to('/dashboard')->with('errors', array('Je hebt geen toegang tot deze sectie'));
		}
		
		$personen = Persoon::order_by('achternaam')->get();		
		
		foreach($personen as $persoon){
			$persoon->lidstatus_id = DB::table('persoon_lidstatus')->where_persoon_id($persoon->id)->order_by('jaar', 'desc')->first()->lidstatus_id;
		}
		
		$pagina = View::make('pages/personen/overzicht')->bind('personen', $personen);
		return View::make('main')->bind('pagina', $pagina);
    }),
			
	'GET /personen/nieuw' => array('before' => 'auth', function()
    {
		if(!Auth::user()->has_groep(Groep::BESTUUR)){
			return Redirect::to('/dashboard')->with('errors', array('Je hebt geen toegang tot deze sectie'));
		}
		
		$persoon = new Persoon();
        $pagina = View::make('pages/personen/formulier')->bind('persoon', $persoon);
		return View::make('main')->bind('pagina', $pagina);
    }),
			
    'POST /personen/nieuw' => array('before' => 'auth', function()
    {   	
		if(!Auth::user()->has_groep(Groep::BESTUUR)){
			return Redirect::to('/dashboard')->with('errors', array('Je hebt geen toegang tot deze sectie'));
		}
		
		$result = Persoon::commit();
		
		if (is_array($result) && count($result) > 0) {
			return Redirect::to('/personen/nieuw')->with('errors', $result);
		}
		
		DB::table('persoon_lidstatus')->insert(array('persoon_id' => $result->id, 'jaar' => Lidstatus::studiejaar(), 'lidstatus_id' => Lidstatus::ONBEKEND));
		
		return Redirect::to('/personen/overzicht')->with('success', array('Persoon is toegevoegd'));
    }), 
			
    'GET /personen/(:num)/bewerken' => array('before' => 'auth', function($persoon_id)
    {
		if(!Auth::user()->has_groep(Groep::BESTUUR)){
			return Redirect::to('/dashboard')->with('errors', array('Je hebt geen toegang tot deze sectie'));
		}
		
		$persoon = Persoon::find($persoon_id)->with('groepen');		
		$lidstatus = DB::table('persoon_lidstatus')->where_persoon_id($persoon->id)->order_by('jaar', 'desc')->first();
        $pagina = View::make('pages/personen/formulier')->bind('persoon', $persoon)->bind('lidstatus', $lidstatus);
		return View::make('main')->bind('pagina', $pagina);
    }),
			
    'POST /personen/(:num)/bewerken' => array('before' => 'auth', function($persoon_id)
    {   
		if(!Auth::user()->has_groep(Groep::BESTUUR)){
			return Redirect::to('/dashboard')->with('errors', array('Je hebt geen toegang tot deze sectie'));
		}		
		$result = Persoon::commit($persoon_id);
		
		if (is_array($result) && count($result) > 0) {
			return Redirect::to('/personen/'.$persoon_id.'/bewerken')->with('errors', $result);
		}
		
		return Redirect::to('/personen/overzicht')->with('success', array('Persoon is aangepast'));
    }),  
			
	'POST /personen/(:num)/lidmaatschapaanpassen' => array('before' => 'auth', function($persoon_id)
    {   	
		if(!Auth::user()->has_groep(Groep::BESTUUR)){
			return Redirect::to('/dashboard')->with('errors', array('Je hebt geen toegang tot deze sectie'));
		}
		
		$lidmaatschap = DB::table('persoon_lidstatus')->where_persoon_id($persoon_id)->order_by('jaar', 'desc')->first();	
		DB::table('persoon_lidstatus')->where_id($lidmaatschap->id)->update(array('lidstatus_id' => Input::get('lidstatus_id')));

		return Redirect::to('/personen/'.$persoon_id.'/bewerken')->with('success', array('Lidstatus is aangepast'));
    }), 
);
