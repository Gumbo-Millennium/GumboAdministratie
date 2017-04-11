<?php

class Persoon extends Storm {
	
	public static function commit($persoon_id = null)
	{
		// Bepaal of we een nieuwe sectie moeten maken of een
		// bestaande moeten bewerken.
		$persoon = (! is_null($persoon_id)) ? Persoon::find($persoon_id) : new Persoon();
		$validate = static::validate($input = Input::get('persoon'), $persoon);

		// Sla de model op als de validatie succesvol is.
		if ($validate->valid()) {
			
			$log = new Log();
			$log->persoon_id = Auth::user()->id;
			$log->datetime = date('Y-m-d H:i:s');
			$log->type = 'persoon';
				
			if($persoon->exists){
				$log->value = json_encode($persoon);
			} else {
				$log->value = 'added';
			}
			
			if(!isset($input['gumbode']))
				$input['gumbode'] = 0;
			
			if(!isset($input['post']))
				$input['post'] = 0;
			
			if(isset($input['geboortedatum'])){
				$input['geboortedatum'] = Str::reverseDate($input['geboortedatum']);
			}
			
			if(isset($input['lid_sinds'])){
				$input['lid_sinds'] = Str::reverseDate($input['lid_sinds']);
			}
			
			if(isset($input['lid_tot'])){
				$input['lid_tot'] = Str::reverseDate($input['lid_tot']);
			}
			
			if($input['email'] != $persoon->email || $input['gumbode'] != $persoon->gumbode){
				//Utils::debug('Mailchimp relevante wijzigingen');
			}
			
			$persoon->fill($input);
			$persoon->save();		
			
			$log->changed_id = $persoon->id;
			$log->save();
			
			return $persoon;
		}

		return $validate->errors->get();
	}

	private static function validate($input, $persoon)
	{
		$rules = array(
			'email' => 'required',
		);

		// Kijk of er al een sectie in de database bestaat met die naam
		if (! $persoon->exists) {
			$rules['email'] .= '|unique:persoon';
		}

		return new Validator($input, $rules);
	}	
	
	public function volledige_naam()
	{
            if($this->voornaam == ''){
                $this->voornaam = $this->voorletters;
            }
            
            return str_replace('  ', ' ',$this->voornaam.' '.$this->tussenvoegsel.' '.$this->achternaam);
	}
	
	public function groepen()
	{
		return $this->has_and_belongs_to_many('Groep', 'persoon_groep');
	}
	
	public function lidstatussen()
	{
		$lidstatussen = DB::table('persoon_lidstatus')
						->join('lidstatus', 'lidstatus.id', '=', 'persoon_lidstatus.lidstatus_id')
						->where_persoon_id($this->id)
						->order_by('jaar', 'desc')
						->get();
		
		foreach($lidstatussen as $status){
			$status->jaar = $status->jaar.'/'.($status->jaar + 1);
		}
		
		return $lidstatussen;
	}
	
	public function lidstatus()
	{
		$lidstatus = DB::table('persoon_lidstatus')->where_persoon_id($this->id)->order_by('jaar', 'desc')->first();
		return Lidstatus::find($lidstatus->lidstatus_id)->naam;
	}
	
	public function has_groep()
	{
		if (is_null($this->groepen)) {
			$this->groepen = $this->rollen()->get();
		}

		foreach ($this->groepen as $groep) {
			if (in_array($groep->naam, func_get_args()) or in_array($groep->id, func_get_args())) {
				return true;
			}
		}

		return false;
	}
	
	public function has_groeptype($type) {
		
		$result = DB::table('persoon_groep')->join('groep', 'groep.id', '=', 'persoon_groep.groep_id')->where_persoon_id_and_type($this->id, $type)->first();
		
		if(is_object($result) || Auth::user()->has_groep(Groep::BESTUUR)){
			return true;
		}
		
		return false;		
	}
	
	public static function getMorrisLedenopbouw()
	{
		//Huidig jaar bepalen
		$jaar = DB::table('persoon_lidstatus')->order_by('jaar', 'desc')->first(array('jaar'))->jaar;		
		$lidstatussen = DB::table('persoon_lidstatus')
						->join('lidstatus', 'lidstatus.id', '=', 'persoon_lidstatus.lidstatus_id')
						->where_jaar($jaar)
						->where_in('lidstatus_id', array(Lidstatus::LID, Lidstatus::OUDLID, Lidstatus::ERELID, Lidstatus::BEGUNSTIGER, Lidstatus::ALID, Lidstatus::ONBEKEND))
						->get();
		$array = array();
		$array_new = array();
		$count = 0;
		
		foreach($lidstatussen as $status){
			if(array_key_exists($status->naam, $array)){
				$array[$status->naam] += 1;
			} else {
				$array[$status->naam] = 1;
			}
		}
		
		foreach ($array as $status => $aantal){
			$array_new[$count]['label'] = $status;
			$array_new[$count]['value'] = $aantal;
			
			$count++;
		}
		
		return json_encode($array_new);
	}
}