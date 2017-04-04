<?php

class Log extends Storm {

	public static function getLogItems($aantal = 0)
	{
		$logs = Log::order_by('datetime', 'desc')->get();
		
		foreach($logs as $log){
			
			switch($log->type){
				case 'login':
					$log->message = "heeft ingelogd";
				break;
				case 'persoon':
					if($log->persoon_id == $log->changed_id){
						$log->message = "heeft zijn/haar persoonsgegevens gewijzigd";
					} else {
						if($log->value == 'added'){
							$log->message = "heeft <a class='text-info' href='/personen/".$log->changed_id."/bewerken'>".Persoon::find($log->changed_id)->volledige_naam()."</a> toegevoegd";
						} else {
							$log->message = "heeft de gegevens gewijzigd van <a class='text-info' href='/personen/".$log->changed_id."/bewerken'>".Persoon::find($log->changed_id)->volledige_naam()."</a>";
						}
					}
				break;
			}
		}
		
		return $logs;
	}

}