<?php

class Groep extends Storm {
	
	// Constanten voor de verschillende rollen.
	const BESTUUR = 1;
	const AC = 3;
	
	public function personen()
	{
		return $this->has_and_belongs_to_many('Persoon', 'persoon_groep');
	}

}