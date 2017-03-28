<?php

class Log extends Storm {

	public static function getLogItems($aantal = 0)
	{
		$logs = Log::order_by('datetime', 'desc')->get();
		
		//Utils::debug($logs);
	}

}