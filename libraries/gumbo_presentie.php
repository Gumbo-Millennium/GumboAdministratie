<?php

include_once BASE_PATH.'libraries/tcpdf/tcpdf.php';

/**
 * Description of class
 *
 * @author Stefan
 */
class Gumbo_presentie extends TCPDF {
	
	public function Footer() {
		// output the HTML content
		$this->writeHTMLCell(0, 0, PDF_MARGIN_LEFT, 240, 'Gumbo Millennium ALV presentielijst');
	}
	
	//Factuur genereren
	public function createPresentielijst() {
		
		
		$pdf = new Gumbo_presentie('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Gumbo Millennium');
		$pdf->SetTitle('titel');
		$pdf->SetSubject('Presentielijst');
		
		// set default header data
		$pdf->setPrintHeader(false);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// add a page
		$pdf->AddPage();
		
		// set font
		$pdf->SetFont('helvetica', '', 8);

		// Bedrijfslogo bestaat.
		$pdf->Image(PUBLIC_PATH.'img/gumbo.png', 10, 10, 20, '', 'PNG', '', 'T', true, 300, 'L', false, false, 0, false, false, false);
		
		$leden = DB::table('persoon_lidstatus')
					->join('persoon', 'persoon.id', '=', 'persoon_lidstatus.persoon_id')
					->join('lidstatus', 'lidstatus.id', '=', 'persoon_lidstatus.lidstatus_id')
					->where_jaar(Lidstatus::studiejaar())
					->where_in('lidstatus_id', array(Lidstatus::LID, Lidstatus::OUDLID, Lidstatus::ERELID, Lidstatus::BEGUNSTIGER, Lidstatus::ALID, Lidstatus::ONBEKEND))
					->order_by('persoon.voornaam')
					->get();
		
		$leden_stem = DB::table('persoon_lidstatus')->where_jaar(Lidstatus::studiejaar())->where_in('lidstatus_id', array(Lidstatus::LID, Lidstatus::ERELID))->count();
		$count = 0;
		
		// create some HTML content
		$html = '
				<p align="right">Presentielijst ALV Gumbo Millennium</p>
				<p align="right"><strong>Totaal: '.count($leden).'</p>
				<p align="right"><strong>Stemgerechtigd: '.$leden_stem.'</p>
				
				<p></p>
				<p></p>
				<table border="1" cellpadding="3" width="1000" align="left">
					<tr>
					<th width="50"><strong>Aanwezig</strong></th>
					<th width="50"><strong>Stemrecht</strong></th>
					<th><strong>Heeft gemachtigd</strong></th>
					<th><strong>Voornaam</strong></th>
					<th><strong>Achternaam</strong></th>
					<th><strong>Lidstatus</strong></th>
					</tr>
				';
		
		foreach($leden as $lid){
			
			$html.= '<tr '.($count % 2 == 0 ? 'bgcolor = "lightgrey"' : "").'>
						<td>&nbsp;</td>
						<td>'.(in_array($lid->lidstatus_id, array(Lidstatus::LID, Lidstatus::ERELID)) ? 'Ja' : '').'</td>
						<td>&nbsp;</td>
						<td>'.$lid->voornaam.'</td>
						<td>'.$lid->achternaam.($lid->tussenvoegsel != '' ? ', '.$lid->tussenvoegsel : '').'</td>
						<td>'.$lid->naam.'</td>
					</tr>';			
			
			$count++;
		}
		
		$html .= '</table>';

			// output the HTML content
			$pdf->writeHTML($html, true, false, true, false, '');
			
			ob_end_clean();
			
			return $pdf->Output(date('Y_m_d').'_ALV_Presentielijst.pdf', 'D');
	}
}
