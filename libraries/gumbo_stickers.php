<?php

include_once BASE_PATH.'libraries/tcpdf/tcpdf.php';

/**
 * Description of class
 *
 * @author Stefan
 */
class Gumbo_stickers extends TCPDF {
	
	public function createAdresStickers() {
		
		$pdf = new Gumbo_presentie('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Gumbo Millennium');
		$pdf->SetTitle('titel');
		$pdf->SetSubject('Adresstickers');
		
		// set default header data
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);

		// set margins
		$pdf->SetMargins(0,0,0);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, 0);
		
		// set font
		$pdf->SetFont('helvetica', '', 12);
		
		$leden = DB::table('persoon_lidstatus')
					->join('persoon', 'persoon.id', '=', 'persoon_lidstatus.persoon_id')
					->join('lidstatus', 'lidstatus.id', '=', 'persoon_lidstatus.lidstatus_id')
					->where_in('lidstatus_id', array(1, 2, 3, 4, 5, 8))
					->where('persoon_lidstatus.jaar', '=', Lidstatus::studiejaar())
					->where('adres', "!=", '')
					->where('postcode', "!=", '')
					->where('woonplaats', "!=", '')
					->order_by('persoon.voornaam')
					->get(array('*', 'persoon.id AS id'));
		
		$count = 0;
		
		//Aantal pagina's bepalen
		$aantal_pagina = ceil(count($leden) / 24);
		
		for ($pagina = 0; $pagina < $aantal_pagina; $pagina++) {
			// add a page
			$pdf->AddPage();
			
			for ($rij = 0; $rij < 8; $rij++) {
				
				for($kolom = 0; $kolom < 3; $kolom++){
					
					if((count($leden) - 1) > $count){
						$txt =	"\n\n".str_replace("  ", " ", $leden[$count]->voornaam." ".$leden[$count]->tussenvoegsel." ".$leden[$count]->achternaam)."\n".
								$leden[$count]->adres."\n".
								$leden[$count]->postcode." ".$leden[$count]->woonplaats;
					}
					
					$pdf->	MultiCell(70, 37, $txt, 0, 'C', false, 0, (70 * $kolom), (37 * $rij));
										
					$count++;
				}				
			}
		}
			
		ob_end_clean();
			
		return $pdf->Output(date('Y_m_d').'_Adresstickers.pdf', 'D');
	}
}

