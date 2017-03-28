<?php

class Agenda extends Storm {
	
	public static function getAgendaItems(){
		
		$verjaardagen = static::getVerjaardagen();
		
		return json_encode($verjaardagen);
		
	}

	private static function getVerjaardagen()
	{
		$personen = Persoon::get();

		$verjaardag_array = array();
		$count = 0;

		foreach ($personen as $persoon) {
			
			$lidstatus = DB::table('persoon_lidstatus')->where_persoon_id($persoon->id)->order_by('jaar', 'desc')->first();
			
			if(in_array($lidstatus->lidstatus_id, array(1, 2, 3, 4, 5, 8))){
				$verjaardag = date('Y').'-'.substr($persoon->geboortedatum, 5);

				$verjaardag_array[$count]['id'] = 'p-'.$persoon->id;
				$verjaardag_array[$count]['title'] = 'Verjaardag '.$persoon->volledige_naam();
				$verjaardag_array[$count]['allDay'] = true;	
				$verjaardag_array[$count]['start'] = $verjaardag;
				$verjaardag_array[$count]['url'] = '#';
				$verjaardag_array[$count]['color'] = '#006b00';

				$count++;
			}
		}

	   return $verjaardag_array;
	}
}