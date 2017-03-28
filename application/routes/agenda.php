<?php

return array(
	
	'GET /agenda/overzicht' => array('before' => 'auth', function()
    {	
		if(!Auth::user()->has_groep(Groep::BESTUUR)){
			return Redirect::to('/dashboard')->with('errors', array('Je hebt geen toegang tot deze sectie'));
		}
	
		$items = Agenda::getAgendaItems();
	
		$pagina = View::make('pages/agenda/overzicht')->bind('items', $items);
		return View::make('main')->bind('pagina', $pagina);
    }),
);
