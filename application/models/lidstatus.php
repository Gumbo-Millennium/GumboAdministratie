<?php

class Lidstatus extends Storm 
{
	const LID = 1;
	const OUDLID = 2;
	const ERELID = 3;
	const BEGUNSTIGER = 4;
	const ALID = 5;
	const EXLID = 6;
	const EXBEGUNSTIGER = 7;
	const ONBEKEND = 8;
	
	public static function studiejaar(){
		if(date('m') <= 8){
			$jaar = date('Y') - 1;
		} else { 
			$jaar = date('Y');
		}
		
		return $jaar;
	}	
}