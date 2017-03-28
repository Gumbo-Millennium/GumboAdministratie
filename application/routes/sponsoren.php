<?php

return array(
	
    'GET /sponsoren/overzicht' => array('before' => 'auth', function()
    {
		if(!Auth::user()->has_groep(Groep::BESTUUR, Groep::AC)){
			return Redirect::to('/dashboard')->with('errors', array('Je hebt geen toegang tot deze sectie'));
		}
		
		$evenementen = Evenement::order_by('datum', 'desc')->with('personen')->get();
		
		$pagina = View::make('pages/sponsoren/overzicht')->bind('evenementen', $evenementen);
		return View::make('main')->bind('pagina', $pagina);
    }),
			
    'GET /sponsoren/(:num)/details' => array('before' => 'auth', function($evenement_id)
    {
		if(!Auth::user()->has_groep(Groep::BESTUUR, Groep::AC)){
			return Redirect::to('/dashboard')->with('errors', array('Je hebt geen toegang tot deze sectie'));
		}
		
		$evenement = Evenement::find($evenement_id)->with('personen');
		
        $pagina = View::make('pages/sponsoren/details')->bind('evenement', $evenement);
		return View::make('main')->bind('pagina', $pagina);
    }),
			
    'POST /sponsoren/(:num)/bewerken' => array('before' => 'auth', function($persoon_id)
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
);
