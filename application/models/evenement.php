<?php

class Evenement extends Storm {

	public function personen()
	{
		return $this->has_and_belongs_to_many('Persoon', 'persoon_evenement');
	}

}