<?php

return array(

	'main' => function($view)
	{
        if(Auth::user()->has_groep(Groep::BESTUUR)){
			$groepen = Groep::order_by('naam')->get();
		} else {
			$groepen = Auth::user()->groepen;
		}
		
		$types = array(
					'commissie' => 'Commissies', 
					'projectgroep' => 'Projectgroepen',
					'dispuut' => 'Disputen'
				);
		
		return $view->bind('groepen', $groepen)->bind('types', $types);
	},
);