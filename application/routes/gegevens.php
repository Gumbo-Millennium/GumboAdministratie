<?php

return array(
		
    'GET /gegevens' => array('before' => 'auth', function()
    {
		$persoon = Auth::user();		
        $pagina = View::make('pages/gegevens/formulier')->bind('persoon', $persoon);
		return View::make('main')->bind('pagina', $pagina);
    }),
			
    'POST /gegevens' => array('before' => 'auth', function()
    {   		
		$result = Persoon::commit(Auth::user()->id);
		
		if (is_array($result) && count($result) > 0) {
			return Redirect::to('/gegevens')->with('errors', $result);
		}
		
		return Redirect::to('/gegevens')->with('success', true);
    }),  
);
