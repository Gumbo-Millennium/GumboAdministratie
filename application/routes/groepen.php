<?php

return array(
			
	'GET /groepen/(:any)/(:num)/bewerken' => array('before' => 'auth', function($type, $groep_id)
    {
		if(!Auth::user()->has_groep(Groep::BESTUUR, $groep_id)){
			return Redirect::to('/dashboard')->with('errors', array('Je bent geen lid van deze groep'));
		}
		
		$groep = Groep::find($groep_id)->with('personen');		
        $pagina = View::make('pages/groepen/formulier')->bind('groep', $groep);
		return View::make('main')->bind('pagina', $pagina);
    }),
			
	'POST /groepen/(:num)/bewerken' => array('before' => 'auth', function($groep_id)
    {   	
		if(!Auth::user()->has_groep($groep_id)){
			return Redirect::to('/dashboard')->with('errors', array('Je bent geen lid van deze groep'));
		}
		
		$result = Groep::commit($groep_id);
		
		if (is_array($result) && count($result) > 0) {
			return Redirect::to('/groepen/'.$persoon_id.'/bewerken')->with('errors', $result);
		}
		
		return Redirect::to('/groepen/'.$persoon_id.'/bewerken')->with('success', array('Groep is aangepast'));
    }),  
);
