<?php

return array(
		
    'GET /alv/overzicht' => array('before' => 'auth', function()
    {	
		if(!Auth::user()->has_groep(Groep::BESTUUR)){
			return Redirect::to('/dashboard')->with('errors', array('Je hebt geen toegang tot deze sectie'));
		}
		
		$leden_totaal = DB::table('persoon_lidstatus')->where_jaar(Lidstatus::studiejaar())->where_not_in('lidstatus_id', array(Lidstatus::EXLID, Lidstatus::EXBEGUNSTIGER))->count();
		$leden_stem = DB::table('persoon_lidstatus')->where_jaar(Lidstatus::studiejaar())->where_in('lidstatus_id', array(Lidstatus::LID, Lidstatus::ERELID))->count();
	
        $pagina = View::make('pages/alv/overzicht')->bind('leden_totaal', $leden_totaal)->bind('leden_stem', $leden_stem);
		return View::make('main')->bind('pagina', $pagina);
    }),
			
	'GET /alv/genereeradresstickers' => array('before' => 'auth', function()
    {
		if(!Auth::user()->has_groep(Groep::BESTUUR)){
			return Redirect::to('/dashboard')->with('errors', array('Je hebt geen toegang tot deze sectie'));
		}
		
		$stickers = new Gumbo_stickers;
		return $stickers->createAdresStickers();
    }),
			
	'GET /alv/genereerpresentielijst' => array('before' => 'auth', function()
    {	
		if(!Auth::user()->has_groep(Groep::BESTUUR)){
			return Redirect::to('/dashboard')->with('errors', array('Je hebt geen toegang tot deze sectie'));
		}
		
		$presentie = new Gumbo_presentie;
		return $presentie->createPresentielijst();
    }),
			

);

	
			
