<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Registration;
use App\Models\VacationRequest;
use App\Models\AfterHour;
use App\Models\Kid;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DateTime;
use DateInterval;
use DatePeriod;

class GodisnjiController extends Controller
{
   // Računa broj neiskorištenih dana godišnjeg ova godina
  	public static function godisnji($user)   	
	{
		$registration = Registration::where('registrations.employee_id', $user->id)->first();
		
		/* staz prijašnji */
		$stazY = 0;
		$stazM = 0;
		$stazD = 0;
		if($registration->staz) {
			$staz = $registration->staz;
			$staz = explode('-',$registration->staz);
			$stazY = $staz[0];
			$stazM = $staz[1];
			$stazD = $staz[2];
		}
		
		/* staz Duplico */	
		$stazDuplico = 0;
		$datum = new DateTime('now');    /* današnji dan */
		$datum_1 = new DateTime($registration->datum_prijave);  /* datum prijave */
		$stazDuplico = $datum_1->diff($datum);  /* staz u Duplicu*/

		$godina = $stazDuplico->format('%y');  
		$mjeseci = $stazDuplico->format('%m');
		$dana = $stazDuplico->format('%d');
		
		/* staz ukupan */
		$danaUk=0;
		$mjeseciUk=0;
		$godinaUk=0;
		
		if(($dana+$stazD) > 30){
			$danaUk = ($dana+$stazD) -30;
			$mjeseciUk = 1;
		}else {
			$danaUk = ($dana+$stazD);
		}
		
		if(($mjeseci+$stazM) > 12){
			$mjeseciUk += ($mjeseci+$stazM) -12;
			$godinaUk = 1;
		}else {
			$mjeseciUk += ($mjeseci+$stazM);
		}
		$godinaUk += ($godina + $stazY);

	
		/* Godišnji odmor - dani*/
		$ova_godina = date_format($datum,'Y');
		$prosla_godina = date_format($datum,'Y')-1;
		
		$GO = 20;
		$GO += (int)($godinaUk/ 4) ;
		
		If($GO > 25){
			$GO = 25;
		} else {
			$GO = $GO;
		}
		
		/* staz prošle godine */
		$datumPG = new DateTime($prosla_godina .'-12-31');    /* zadnji dan prošle godine */
		$godina_prijave = date_format($datum_1,'Y');  /* godina prijave */
		
		$danaPG = 0;
		$mjeseciPG =0;
		$godinaPG = 0;
		$danaUk_PG=0;
		$mjeseciUk_PG=0;
		$godinaUk_PG=0;
			
		if($godina_prijave < $ova_godina){
			$stazDuplicoPG = $datum_1->diff($datumPG);  /* staz u Duplicu do 31.12*/
			$godinaPG = $stazDuplicoPG->format('%y');  
			$mjeseciPG = $stazDuplicoPG->format('%m');
			$danaPG = $stazDuplicoPG->format('%d');
		}
		
		/* staz ukupan do 31.12.*/
			if(($danaPG+$stazD) > 30){
				$danaUk_PG = ($danaPG+$stazD) -30;
				$mjeseciUk_PG = 1;
			} else {
				$danaUk_PG = ($danaPG+$stazD);
			}
			
			if(($mjeseciPG+$stazM) > 12){
				$mjeseciUk_PG += ($mjeseciPG+$stazM) -12;
				$godinaUk_PG = 1;
			} else {
				$mjeseciUk_PG += ($mjeseciPG+$stazM);
			}
			$godinaUk_PG += ($godinaPG + $stazY);
			$GO_PG = 20;
			$GO_PG += (int)($godinaUk_PG/ 4) ;
		
			If($GO_PG > 25){
				$GO_PG = 25;
			} else {
				$GO_PG = $GO_PG;
			}

		/* Zahtjevi ova godina */		
		$zahtjevi = VacationRequest::where('employee_id',$user->id)->get();
		
		/* ukupno iskorišteno godišnji zaposlenika*/
		$ukupnoGO = 0;
		$ukupnoGO_PG = 0;
		foreach($zahtjevi as $zahtjev){
			if($zahtjev->zahtjev == 'GO' && $zahtjev->odobreno == 'DA' ){
				$begin = new DateTime($zahtjev->start_date);
				$end = new DateTime($zahtjev->end_date);
				$brojDana = date_diff($end, $begin);
				$end->setTime(0,0,1);
				$interval = DateInterval::createFromDateString('1 day');
				$period = new DatePeriod($begin, $interval, $end);
				
				foreach ($period as $dan) {
					if(date_format($dan,'N') < 6 ){
						$ukupnoGO += 1;
						$ukupnoGO_PG += 1;
					}
					if(date_format($dan,'N') < 6 && date_format($dan,'d') == '01' && date_format($dan,'m') == '01' ||
						date_format($dan,'N') < 6 && date_format($dan,'d') == '06' && date_format($dan,'m') == '01' ||
						date_format($dan,'N') < 6 && date_format($dan,'d') == '01' && date_format($dan,'m') == '05' ||
						date_format($dan,'N') < 6 && date_format($dan,'d') == '22' && date_format($dan,'m') == '06' ||
						date_format($dan,'N') < 6 && date_format($dan,'d') == '25' && date_format($dan,'m') == '06' && date_format($dan,'Y') == '2018' ||
						date_format($dan,'N') < 6 && date_format($dan,'d') == '25' && date_format($dan,'m') == '06' && date_format($dan,'Y') == '2019' ||
						date_format($dan,'N') < 6 && date_format($dan,'d') == '15' && date_format($dan,'m') == '08' ||
						date_format($dan,'N') < 6 && date_format($dan,'d') == '05' && date_format($dan,'m') == '08' ||
						date_format($dan,'N') < 6 && date_format($dan,'d') == '08' && date_format($dan,'m') == '10' ||
						date_format($dan,'N') < 6 && date_format($dan,'d') == '01' && date_format($dan,'m') == '11' ||
						date_format($dan,'N') < 6 && date_format($dan,'d') == '25' && date_format($dan,'m') == '12' ||
						date_format($dan,'N') < 6 && date_format($dan,'d') == '26' && date_format($dan,'m') == '12'){
							if(date_format($dan,'Y') == $ova_godina ){
								$ukupnoGO -= 1;
							} elseif(date_format($dan,'Y') == $prosla_godina ){
								$ukupnoGO_PG -= 1;
							}
					}
					if(date_format($dan,'d') == '02' && date_format($dan,'m') == '04' && date_format($dan,'Y') == '2018' ||
						date_format($dan,'d') == '31' && date_format($dan,'m') == '05' && date_format($dan,'Y') == '2018' ||
						date_format($dan,'d') == '22' && date_format($dan,'m') == '04' && date_format($dan,'Y') == '2019' ||
						date_format($dan,'d') == '20' && date_format($dan,'m') == '06' && date_format($dan,'Y') == '2019' ||
						date_format($dan,'d') == '13' && date_format($dan,'m') == '04' && date_format($dan,'Y') == '2020' ||
						date_format($dan,'d') == '11' && date_format($dan,'m') == '06' && date_format($dan,'Y') == '2020'){
						$ukupnoGO -= 1;
					}
				}
			}
		}
		
		return $dani_GO = $GO - $ukupnoGO;
	}

	// Računa broj dana godišnjeg ova godina
	public static function godisnjiUser($user)
	{
		$stazUkupno = GodisnjiController::stazUkupno($user);
	
		/* Godišnji odmor - dani*/
		$GO = 20;
		$GO += (int)($stazUkupno[0]/ 4) ;
		
		If($GO > 25){
			$GO = 25;
		} else {
			$GO = $GO;
		}

		return $GO;
	}

		/* dani GO zadana godina */
	public static function godisnjiPG($user, $godina)
	{
		/* Računa ukupan staz za prošlu godinu - do 31.12.*/
		$stazPG =  GodisnjiController::stazUkupnoPG($user, $godina);
		
		if($user->datum_prijave) {
			$datum_prijave = $user->datum_prijave;
			$datum_prijave = explode('-', $user->datum_prijave);
			$prijavaGodina = $datum_prijave[0];
			$prijavaMjesec = $datum_prijave[1];
			$prijavaDan = $datum_prijave[2];
		}

		if( $godina >= $prijavaGodina) {
			$dana = $stazPG[2];
			$mjeseci = $stazPG[1];
			$godina = $stazPG[0];
			
			if(($dana) > 30){
				$dana = $dana -30;
				$mjeseci += 1;
			}
			
			if(($mjeseci) > 12){
				$mjeseci += $mjeseci - 12;
				$godina += 1;
			}
				
			/* Godišnji odmor - dani*/

			$GO = 20;
			$GO += (int)($godina/ 4) ;
			
			If($GO > 25){
				$GO = 25;
			}
		} else {
			$GO = 0;
		}

		return $GO;
	}
	
	//računa iskorištene dane godišnjeg odmora ova godina 
	public static function daniZahtjevi($user)
	{
		/* Zahtjevi ova godina */	
		$zahtjevi = VacationRequest::where('employee_id',$user->employee_id)->where('zahtjev','GO')->where('odobreno','DA')->get();
		$datum = new DateTime('now');    /* današnji dan */
		$ova_godina = date_format($datum,'Y');
		
		/* ukupno iskorišteno godišnji zaposlenika*/
		$ukupnoGO = 0;
		
		foreach($zahtjevi as $zahtjev){
			$begin = new DateTime($zahtjev->start_date);
			$end = new DateTime($zahtjev->end_date);
			$end->setTime(0,0,1);
			$interval = DateInterval::createFromDateString('1 day');
			$period = new DatePeriod($begin, $interval, $end);
			foreach ($period as $dan) {
				if(date_format($dan,'N') < 6 ){
					if(date_format($dan,'Y') == $ova_godina ){
						$ukupnoGO += 1;
						
						if(date_format($dan,'d') == '01' && date_format($dan,'m') == '01' ||
							date_format($dan,'d') == '06' && date_format($dan,'m') == '01' ||
							date_format($dan,'d') == '01' && date_format($dan,'m') == '05' ||
							date_format($dan,'d') == '22' && date_format($dan,'m') == '06' ||
							date_format($dan,'d') == '25' && date_format($dan,'m') == '06' && date_format($dan,'Y') == '2018' ||
							date_format($dan,'d') == '25' && date_format($dan,'m') == '06' && date_format($dan,'Y') == '2019' ||
							date_format($dan,'d') == '15' && date_format($dan,'m') == '08' ||
							date_format($dan,'d') == '05' && date_format($dan,'m') == '08' ||
							date_format($dan,'d') == '08' && date_format($dan,'m') == '10' ||
							date_format($dan,'d') == '01' && date_format($dan,'m') == '11' ||
							date_format($dan,'d') == '25' && date_format($dan,'m') == '12' ||
							date_format($dan,'d') == '26' && date_format($dan,'m') == '12' ||
							date_format($dan,'d') == '02' && date_format($dan,'m') == '04' && date_format($dan,'Y') == '2018' ||
							date_format($dan,'d') == '31' && date_format($dan,'m') == '05' && date_format($dan,'Y') == '2018' ||
							date_format($dan,'d') == '22' && date_format($dan,'m') == '04' && date_format($dan,'Y') == '2019' ||
							date_format($dan,'d') == '20' && date_format($dan,'m') == '06' && date_format($dan,'Y') == '2019' ||
							date_format($dan,'d') == '13' && date_format($dan,'m') == '04' && date_format($dan,'Y') == '2020' ||
							date_format($dan,'d') == '11' && date_format($dan,'m') == '06' && date_format($dan,'Y') == '2020'){
								$ukupnoGO -= 1;
						}
					}
				}
			}	
		}
		return $ukupnoGO;
	}

	//računa iskorištene dane godišnjeg PROŠLA ova godina
	public static function daniZahtjeviPG($user)
	{
		/* Zahtjevi ova godina */	
		$zahtjevi = VacationRequest::where('employee_id',$user->employee_id)->where('zahtjev','GO')->where('odobreno','DA')->get();
		
		$datum = new DateTime('now');    /* današnji dan */
		$prosla_godina = date_format($datum,'Y')-1;

		/* ukupno iskorišteno godišnji zaposlenika*/
		$ukupnoGO_PG = 0;
		
		foreach($zahtjevi as $zahtjev){
			$begin = new DateTime($zahtjev->start_date);
			$end = new DateTime($zahtjev->end_date);
			$end->setTime(0,0,1);
			$interval = DateInterval::createFromDateString('1 day');
			$period = new DatePeriod($begin, $interval, $end);
			foreach ($period as $dan) {
				if(date_format($dan,'N') < 6 ){
					if(date_format($dan,'Y') == $prosla_godina ){
						$ukupnoGO_PG += 1;
						
						if(date_format($dan,'d-m') == '01-01' ||
							date_format($dan,'d-m') == '06-01' ||
							date_format($dan,'d-m') == '01-05' ||
							date_format($dan,'d-m') == '22-06' ||
							date_format($dan,'d-m-Y') == '25-06-2018' ||
							date_format($dan,'d-m-Y') == '25-06-2019' ||
							date_format($dan,'d-m') == '15-08' ||
							date_format($dan,'d-m') == '05-08' ||
							date_format($dan,'d-m') == '08-10' ||
							date_format($dan,'d-m') == '01-11' ||
							date_format($dan,'d-m') == '25-12' ||
							date_format($dan,'d-m') == '26-12' ||
							date_format($dan,'d-m-Y') == '02-04-2018' ||
							date_format($dan,'d-m-Y') == '31-05-2018' ||
							date_format($dan,'d-m-Y') == '22-04-2019' ||
							date_format($dan,'d-m-Y') == '20-06-2019' ||
							date_format($dan,'d-m-Y') == '13-04-2020' ||
							date_format($dan,'d-m-Y') == '11-06-2020'){
							$ukupnoGO_PG -= 1;
						}
					}
				}
			}	
		}
		return $ukupnoGO_PG;
	}
	
	//računa iskorištene dane godišnjeg u zadanoj godini  
	public static function daniZahtjeviGodina($user, $godina)
	{
		/* Zahtjevi ova godina */	
		$zahtjevi = VacationRequest::where('employee_id', $user->employee_id)->where('zahtjev','GO')->where('odobreno','DA')->get();
		
		$datum = new DateTime('now');    /* današnji dan */
		//$prosla_godina = date_format($datum,'Y')-1;

		/* ukupno iskorišteno godišnji zaposlenika*/
		$ukupnoGO_PG = 0;
		
		foreach($zahtjevi as $zahtjev){
			$begin = new DateTime($zahtjev->start_date);
			$end = new DateTime($zahtjev->end_date);
			$end->setTime(0,0,1);
			$interval = DateInterval::createFromDateString('1 day');
			$period = new DatePeriod($begin, $interval, $end);
			foreach ($period as $dan) {
				if(date_format($dan,'Y') == $godina ){
					if(date_format($dan,'N') < 6 ){
						if(date_format($dan,'d') == '01' && date_format($dan,'m') == '01' ||
							date_format($dan,'d') == '06' && date_format($dan,'m') == '01' ||
							date_format($dan,'d') == '01' && date_format($dan,'m') == '05' ||
							date_format($dan,'d') == '22' && date_format($dan,'m') == '06' ||
							date_format($dan,'d') == '25' && date_format($dan,'m') == '06' && date_format($dan,'Y') == '2018' ||
							date_format($dan,'d') == '25' && date_format($dan,'m') == '06' && date_format($dan,'Y') == '2019' ||
							date_format($dan,'d') == '15' && date_format($dan,'m') == '08' ||
							date_format($dan,'d') == '05' && date_format($dan,'m') == '08' ||
							date_format($dan,'d') == '08' && date_format($dan,'m') == '10' ||
							date_format($dan,'d') == '01' && date_format($dan,'m') == '11' ||
							date_format($dan,'d') == '25' && date_format($dan,'m') == '12' ||
							date_format($dan,'d') == '26' && date_format($dan,'m') == '12' ||
							date_format($dan,'d') == '02' && date_format($dan,'m') == '04' && date_format($dan,'Y') == '2018' ||
							date_format($dan,'d') == '31' && date_format($dan,'m') == '05' && date_format($dan,'Y') == '2018' ||
							date_format($dan,'d') == '22' && date_format($dan,'m') == '04' && date_format($dan,'Y') == '2019' ||
							date_format($dan,'d') == '20' && date_format($dan,'m') == '06' && date_format($dan,'Y') == '2019' ||
							date_format($dan,'d') == '13' && date_format($dan,'m') == '04' && date_format($dan,'Y') == '2020' ||
							date_format($dan,'d') == '11' && date_format($dan,'m') == '06' && date_format($dan,'Y') == '2020'){
								//
						} else {
							$ukupnoGO_PG += 1;
						}
					}
				}
			}			
		}
		return $ukupnoGO_PG;
	}

	//računa iskorištene dane godišnjeg odmora za traženi mjesec
	public static function daniZahtjevi_mj($user, $zahtjev, $mjesec, $godina)
	{
	
		/* Zahtjevi ova godina */	
		$zahtjevi = VacationRequest::where('employee_id', $user->employee_id)->where('zahtjev', $zahtjev)->where('odobreno','DA')->get();
		/* ukupno iskorišteno godišnji zaposlenika*/
		$ukupnoGO = 0;
		
		foreach($zahtjevi as $zahtjev){
			$begin = new DateTime($zahtjev->start_date);
			$end = new DateTime($zahtjev->end_date);
			$end->setTime(0,0,1);
			$interval = DateInterval::createFromDateString('1 day');
			$period = new DatePeriod($begin, $interval, $end);
			foreach ($period as $dan) {
				if(date_format($dan,'N') < 6 ){
					
					if(date_format($dan,'Y') == $godina && date_format($dan,'m') == $mjesec){
						$ukupnoGO += 1;
						if(date_format($dan,'d') == '01' && date_format($dan,'m') == '01' ||
							date_format($dan,'d') == '06' && date_format($dan,'m') == '01' ||
							date_format($dan,'d') == '01' && date_format($dan,'m') == '05' ||
							date_format($dan,'d') == '22' && date_format($dan,'m') == '06' ||
							date_format($dan,'d') == '25' && date_format($dan,'m') == '06'  && date_format($dan,'Y') == '2018' ||
							date_format($dan,'d') == '25' && date_format($dan,'m') == '06'  && date_format($dan,'Y') == '2019' ||
							date_format($dan,'d') == '15' && date_format($dan,'m') == '08' ||
							date_format($dan,'d') == '05' && date_format($dan,'m') == '08' ||
							date_format($dan,'d') == '08' && date_format($dan,'m') == '10' ||
							date_format($dan,'d') == '01' && date_format($dan,'m') == '11' ||
							date_format($dan,'d') == '25' && date_format($dan,'m') == '12' ||
							date_format($dan,'d') == '26' && date_format($dan,'m') == '12' ||
							date_format($dan,'d') == '02' && date_format($dan,'m') == '04' && date_format($dan,'Y') == '2018' ||
							date_format($dan,'d') == '31' && date_format($dan,'m') == '05' && date_format($dan,'Y') == '2018' ||
							date_format($dan,'d') == '22' && date_format($dan,'m') == '04' && date_format($dan,'Y') == '2019' ||
							date_format($dan,'d') == '20' && date_format($dan,'m') == '06' && date_format($dan,'Y') == '2019' ||
							date_format($dan,'d') == '13' && date_format($dan,'m') == '04' && date_format($dan,'Y') == '2020' ||
							date_format($dan,'d') == '11' && date_format($dan,'m') == '06' && date_format($dan,'Y') == '2020'){
								$ukupnoGO -= 1;
						}
					}
				}
			}	
		}
		return $ukupnoGO;
	}

	// Računa broj radnih dana između dva datuma
	public static function daniGO($zahtjev)
	{
		$begin = new DateTime($zahtjev['start_date']);
		$end = new DateTime($zahtjev['end_date']);
		$end->setTime(0,0,1);
		$interval = DateInterval::createFromDateString('1 day');
		$period = new DatePeriod($begin, $interval, $end);
		
		$brojDana = 0;
		
		foreach ($period as $dan) {
			if( date_format($dan,'N') < 6 &&
			date_format($dan,'d-m') != '01-01' &&
			date_format($dan,'d-m') != '06-01' &&
			date_format($dan,'d-m') != '01-05' && 
			date_format($dan,'d-m') != '22-06' &&
			date_format($dan,'d-m-Y') != '25-06-2018' && 
			date_format($dan,'d-m-Y') != '25-06-2019' && 
			date_format($dan,'d-m') != '15-08' && 
			date_format($dan,'d-m') != '05-08' && 
			date_format($dan,'d-m') != '08-10' && 
			date_format($dan,'d-m') != '01-11' && 
			date_format($dan,'d-m') != '25-12' &&
			date_format($dan,'d-m') != '26-12' &&
			date_format($dan,'d-m-Y') != '02-04-2018' &&
			date_format($dan,'d-m-Y') != '31-05-2018' &&
			date_format($dan,'d-m-Y') != '22-04-2019' && 
			date_format($dan,'d-m-Y') != '20-06-2019' &&
			date_format($dan,'d-m-Y') != '13-04-2020' &&
			date_format($dan,'d-m-Y') != '11-06-2020' ){
				$brojDana += 1;
			}
		}
		return $brojDana;
	}
	
	// Računa broj dana između dva datuma sa vikendima i praznicima
	public static function ukupnoDani($zahtjev)
	{
		$begin = new DateTime($zahtjev['start_date']);
		$end = new DateTime($zahtjev['end_date']);
		$end->setTime(0,0,1);
		$interval = DateInterval::createFromDateString('1 day');
		$period = new DatePeriod($begin, $interval, $end);
		
		$ukupnoDana = 0;
		
		foreach ($period as $dan) {
			$ukupnoDana += 1;
		}
		
		return $ukupnoDana;
	}
	
	// računa broj prekovremenih sati po zahtjevima  /************ RADI!!!!!!! ***************/
	public static function prekovremeni_sati($user)  //user = registration!!!
	{
		$prekovremeniEmpl = AfterHour::where('employee_id', $user->employee_id)->get();
		$razlika = 0;

		foreach($prekovremeniEmpl as $prekovremeni){
			if($prekovremeni->odobreno == 'DA'){
				if($prekovremeni->odobreno_h ) {
					$razlika_vremena = $prekovremeni->odobreno_h;
				} else {
					$vrijeme_1 = new DateTime($prekovremeni->start_time );
					if($prekovremeni->end_time == '00:00:00') {
						$vrijeme_2 = new DateTime('23:59:59');  /* vrijeme do */
					} else {
						$vrijeme_2 = new DateTime($prekovremeni->end_time);  /* vrijeme do */
					}
					
					$razlika_vremena = $vrijeme_2->diff($vrijeme_1);
					$razlika_vremena = $razlika_vremena->format('%H:%I');
					
				}
				// konvert vremena u decimalan broj
				$hm = explode(":", $razlika_vremena);
				$razlika_vremena = $hm[0] + ($hm[1]/60);
				$dan_prekovremeni = new DateTime($prekovremeni->datum);
				if(date_format($dan_prekovremeni,'N') == 6) {
					$razlika_vremena = $razlika_vremena * 1.3;
				} elseif (date_format($dan_prekovremeni,'N') == 7) {
					$razlika_vremena = $razlika_vremena * 1.4;
				} else {
					$razlika_vremena = $razlika_vremena;
				}
				$razlika += $razlika_vremena;

			}
		}

		return $razlika;
	}
	
	// računa broj prekovremenih sati u zadanom mjeseci /************ RADI!!!!!!! ***************/
	public static function prekovremeni_satiMj($user, $mjesec, $godina )  //user = registration!!!
	{
		$prekovremeniEmpl = AfterHour::where('employee_id',$user->employee_id)->whereMonth('after_hours.datum', $mjesec)->whereYear('after_hours.datum', $godina)->get();
		
		$razlika =0;
		
		foreach($prekovremeniEmpl as $prekovremeni){
			if($prekovremeni->odobreno == 'DA'){
				if($prekovremeni->odobreno_h ) {
					$razlika_vremena = $prekovremeni->odobreno_h;
				} else {
					$vrijeme_1 = new DateTime($prekovremeni->start_time );
					if($prekovremeni->end_time == '00:00:00') {
						$vrijeme_2 = new DateTime('23:59:59');  /* vrijeme do */
					} else {
						$vrijeme_2 = new DateTime($prekovremeni->end_time);  /* vrijeme do */
					}
					
					$razlika_vremena = $vrijeme_2->diff($vrijeme_1);
					$razlika_vremena = $razlika_vremena->format('%H:%I');
					
				}
				// konvert vremena u decimalan broj
				$hm = explode(":", $razlika_vremena);
				$razlika_vremena = $hm[0] + ($hm[1]/60);
				$dan_prekovremeni = new DateTime($prekovremeni->datum);
				if(date_format($dan_prekovremeni,'N') == 6) {
					$razlika_vremena = $razlika_vremena * 1.3;
				} elseif (date_format($dan_prekovremeni,'N') == 7) {
					$razlika_vremena = $razlika_vremena * 1.4;
				} else {
					$razlika_vremena = $razlika_vremena;
				}
				$razlika += $razlika_vremena;

			}
		}
		return $razlika;
	}
	
	// računa sate jednog izlaska    
	public static function izlazak($request)  // u requestu vrijeme od i vrijeme do
	{
		$vrijeme_1 = new DateTime($request['od']);  /* vrijeme od */
		$vrijeme_2 = new DateTime($request['do']);  /* vrijeme do */
		$razlika_vremena = $vrijeme_2->diff($vrijeme_1);  /* razlika_vremena*/
		
		$razlika_h = (int)$razlika_vremena->h;
		$razlika_m = (int)$razlika_vremena->i;
		
		if($razlika_m  == 0){
			$razlika_m = '00';
		}
		$razlika = $razlika_h . ':' . $razlika_m ;

		return $razlika;
	}
	
	// računa sve sate izlazaka    
	public static function izlasci_ukupno($user)  //user = registration!!!
	{
		$izlasci = VacationRequest::where('employee_id', $user->employee_id)->where('zahtjev','Izlazak')->where('odobreno','DA')->get();
		
		$razlika_h = 0;
		$razlika_m = 0;
		
		foreach($izlasci as $izlazak){
			$vrijeme_1 = new DateTime($izlazak->start_time);  /* vrijeme od */
			$vrijeme_2 = new DateTime($izlazak->end_time);  /* vrijeme do */
			$razlika_vremena = $vrijeme_2->diff($vrijeme_1);  /* razlika_vremena*/
			
			$razlika_h += (int)$razlika_vremena->h;
			$razlika_m += (int)$razlika_vremena->i;
			if($razlika_m >= 60){
				$razlika_h += round($razlika_m / 60, 0, PHP_ROUND_HALF_DOWN);
				$razlika_m = ($razlika_m - round($razlika_m / 60, 0, PHP_ROUND_HALF_DOWN) *60);
			}
		}
		$razlika = $razlika_h . ':' . $razlika_m ;
		
		return $razlika;
	}
	
	// računa sate izlazaka u zadanom mjeseci    /************ RADI!!!!!!! ***************/
	public static function izlasci_Mj($user, $mjesec, $godina )  //user = registration!!!
	{
		$izlasci = VacationRequest::where('employee_id', $user->employee_id)->where('zahtjev','Izlazak')->where('odobreno','DA')->whereMonth('vacation_requests.start_date', $mjesec)->whereYear('vacation_requests.start_date', $godina)->get();
		
		$razlika_h =0;
		$razlika_m =0;
		
		foreach($izlasci as $izlazak){
			$vrijeme_1 = new DateTime($izlazak->start_time);  /* vrijeme od */
			$vrijeme_2 = new DateTime($izlazak->end_time);  /* vrijeme do */
			$razlika_vremena = $vrijeme_2->diff($vrijeme_1);  /* razlika_vremena*/
			
			
			$razlika_h += (int)$razlika_vremena->h;
			$razlika_m += (int)$razlika_vremena->i;
			if($razlika_m >= 60){
				$razlika_h += round($razlika_m / 60, 0, PHP_ROUND_HALF_DOWN);
				$razlika_m = ($razlika_m - round($razlika_m / 60, 0, PHP_ROUND_HALF_DOWN) *60);
			}
		}
		$razlika = $razlika_h . ':' . $razlika_m ;
		return $razlika;
	}

	/* računa broj slobodnih dana prema prekovremenim satima */   /************ RADI!!!!!!! ***************/
	public static function slobodni_dani($user)
	{
		$prekovremeniEmpl = GodisnjiController::prekovremeni_sati($user);
		
		$razlika = 0;
		
		if($prekovremeniEmpl >= 8){
			$razlika = round($prekovremeniEmpl / 8, 0, PHP_ROUND_HALF_DOWN);
		} 

		return $razlika;
	}
	
	/* oduzima izlaske od prekovremenih sati i računa slobodne dane  */   /************ RADI!!!!!!! ***************/
	public static function prekovremeni_bez_izlazaka($user)
	{
		$prekovremeniEmpl = GodisnjiController::prekovremeni_sati($user);
		$sati_izlazaka = (int) substr(GodisnjiController::izlasci_ukupno($user),0,-2);
		
		$razlika = $prekovremeniEmpl - $sati_izlazaka;
		
		if($razlika >= 8){
			$razlika = round($razlika / 8, 0, PHP_ROUND_HALF_DOWN);
		} else {
			$razlika =0;
		}

		return $razlika;
	}
	
	/* računa iskorištene slobodne dane  - odobrene */          /************ RADI!!!!!!! ***************/
	public static function koristeni_slobodni_dani($user)
	{
		$sl_dani = VacationRequest::where('employee_id',$user->employee_id)->where('zahtjev','SLD')->get();
		
		$SLdan = 0;
		$datum = new DateTime('now');    /* današnji dan */
		$ova_godina = date_format($datum,'Y');
		
		foreach($sl_dani as $sl_dan){
			if($sl_dan->odobreno == 'DA'){
				$begin = new DateTime($sl_dan->start_date);
				$end = new DateTime($sl_dan->end_date);
				$end->setTime(0,0,1);
				$interval = DateInterval::createFromDateString('1 day');
				$period = new DatePeriod($begin, $interval, $end);
				foreach ($period as $dan) {
					if(date_format($dan,'N') < 6 ){
						$SLdan += 1;
					}
					if(date_format($dan,'d-m') == '01-01' ||
						date_format($dan,'d-m') == '06-01' ||
						date_format($dan,'d-m') == '01-05' ||
						date_format($dan,'d-m') == '22-06' ||
						date_format($dan,'d-m-Y') == '25-06-2018' ||
						date_format($dan,'d-m-Y') == '25-06-2019' ||
						date_format($dan,'d-m') == '15-08' ||
						date_format($dan,'d-m') == '05-08' ||
						date_format($dan,'d-m') == '08-10' ||
						date_format($dan,'d-m') == '01-11' ||
						date_format($dan,'d-m') == '25-12' ||
						date_format($dan,'d-m') == '26-12' ||
						date_format($dan,'d-m-Y') == '02-04-2018' ||
						date_format($dan,'d-m-Y') == '31-05-2018' ||
						date_format($dan,'d-m-Y') == '22-04-2019' ||
						date_format($dan,'d-m-Y') == '20-06-2019' ||
						date_format($dan,'d-m-Y') == '13-04-2020' ||
						date_format($dan,'d-m-Y') == '11-06-2020'){
							if(date_format($dan,'Y') == $ova_godina ){
								$SLdan -= 1;
							}
					}
				}
			}
		}
		return $SLdan;
	}
	
	/* Računa dane GO za djecu*/	
	public function djeca($user){
		$kids = Kid::where('employee_id', $user->id)->get();
		$datum = new DateTime('now');    /* današnji dan */
		$danGOdijete = 0;
		
		foreach($kids as $kid){
			$datum_rodjenja = new DateTime($kid->datum_rodjenja);  /* datum rođenja djeteta */
			$godinaDijete = $datum_rodjenja->diff($datum); 
			if((int)$godinaDijete->y < 7){
				$djeca += 1;
			}
			if($djeca >= 2) {
				$danGOdijete = 1;
			}
		}
		return $danGOdijete;
	}
	
	/* Računa trenutan staz u Duplicu */	                /************ RADI!!!!!!! ***************/
	public static function stazDuplico($user)
	{
		$datum = new DateTime('now');    /* današnji dan */
		$stazDuplico = 0;
		$datum_1 = new DateTime($user->datum_prijave);  /* datum prijave - registracija */
		
		$stazDuplico = $datum_1->diff($datum);  /* staz u Duplicu*/
		
		return $stazDuplico;
	}
	
	/* Računa ukupan staž */	
	public static function stazUkupno($user)   /************ RADI!!!!!!! ***************/
	{
		$stazDuplico = GodisnjiController::stazDuplico($user);
		
		$godina = $stazDuplico->format('%y');  
		$mjeseci = $stazDuplico->format('%m');
		$dana = $stazDuplico->format('%d');
		
		$stazY = 0;
		$stazM = 0;
		$stazD = 0;
		if($user->staz) {
			$stazPrijasnji = $user->staz;
			$stazPrijasnji = explode('-',$user->staz);
			$stazY = $stazPrijasnji[0];
			$stazM = $stazPrijasnji[1];
			$stazD = $stazPrijasnji[2];
		} 

		/* Staž ukupan */
		$danaUk = $dana + $stazD;
		$mjeseciUk = $mjeseci + $stazM;
		$godinaUk = $godina + $stazY;
		
		if( $danaUk >= 30){
			$danaUk -= 30;
			$mjeseciUk += 1;
		} 

		if ( $mjeseciUk >= 12 ){
			$mjeseciUk -= 12;
			$godinaUk += 1;
		} 

		$staz = array($godinaUk, $mjeseciUk, $danaUk);
		
		return $staz;
	}
	
	/* Računa staz u Duplicu za prošlu godinu - do 31.12. */
	public static function stazDuplicoPG($user, $godina)      /************ RADI!!!!!!! ***************/
	{
	
		$datumPG = new DateTime($godina . '-12-31');
		
		$stazDuplicoPG = 0;
		$datum_prijave = new DateTime($user->datum_prijave);  /* datum prijave - registracija */
		if( date_format($datum_prijave,'Y') <= $godina) {
			$stazDuplicoPG = $datum_prijave->diff($datumPG);  /* staz u Duplicu PG*/
		}

		return $stazDuplicoPG;
	}
	
	/* Računa ukupan staž za prošlu godinu */		
	public static function stazUkupnoPG($user, $godina)  			 /************ OK  ***************/
	{
		$stazDuplicoPG = GodisnjiController::stazDuplicoPG($user, $godina);
		
		if(!$stazDuplicoPG){
			$godina = 0;
			$mjeseci = 0;
			$dana = 0;
		} else {
			$godina = $stazDuplicoPG->format('%y');  
			$mjeseci = $stazDuplicoPG->format('%m');
			$dana = $stazDuplicoPG->format('%d');
		}
		
		$stazY = 0;
		$stazM = 0;
		$stazD = 0;

		if($user->staz) {
			$stazPrijasnji = $user->staz;
			$stazPrijasnji = explode('-',$user->staz);
			$stazY = $stazPrijasnji[0];
			$stazM = $stazPrijasnji[1];
			$stazD = $stazPrijasnji[2];
		} 
		
		/* Staž ukupan */
		$danaUk=0;
		$mjeseciUk=0;
		$godinaUk=0;
		
		if(($dana+$stazD) > 30){
			$danaUk = ($dana+$stazD) -30;
			$mjeseciUk = 1;
		}else {
			$danaUk = ($dana+$stazD);
		}
		
		if(($mjeseci+$stazM) > 12){
			$mjeseciUk += ($mjeseci+$stazM) -12;
			$godinaUk = 1;
		}else {
			$mjeseciUk += ($mjeseci+$stazM);
		}
		$godinaUk += ($godina + $stazY);
								
		$stazPG = array($godinaUk,$mjeseciUk,$danaUk);
		
		return $stazPG;
	}

	/*  računa razmjeran GO na traženi datum */
	public static function razmjeranGO_date($user, $request)    /************ RADI!!!!!!! ***************/
	{	
		//početak zahtjeva
		$godina_pocetak_zahtjeva = date('Y', strtotime($request['start_date']));
		$mjesec_pocetak_zahtjeva = date('Y', strtotime($request['start_date']));
		$dan_pocetak_zahtjeva = date('Y', strtotime($request['start_date']));
		//kraj zahtjeva
		$ova_godina = date('Y', strtotime($request['end_date']));
		$ovaj_mjesec = date('m', strtotime($request['end_date']));
		$ovaj_dan = date('d', strtotime($request['end_date']));

		$date = new DateTime($request['end_date']);

		$dan_pola_mjeseca = round(cal_days_in_month(CAL_GREGORIAN, $ovaj_mjesec, $ova_godina )/2,0,PHP_ROUND_HALF_UP);   // 15 dana ili 14 ili 16

		if($ovaj_dan < $dan_pola_mjeseca){
			$ovaj_mjesec -=1;
		} 
		
		$GO  = GodisnjiController::godisnjiUser($user);

		if($user->datum_prijave) {
			$datum_prijave = $user->datum_prijave;
			$datum_prijave = explode('-',$user->datum_prijave);
			
			$prijavaGodina = $datum_prijave[0];
			$prijava = new DateTime($user->datum_prijave);
			$staz = $prijava->diff($date);   /* staz u Duplicu*/
			$mjesec = $staz->format('%m');
			$dan = $staz->format('%d');
			if($dan >= $dan_pola_mjeseca) {
				$mjesec +=1;
			}
			
			if($godina_pocetak_zahtjeva < $ova_godina ) {
				if($prijavaGodina < $godina_pocetak_zahtjeva){
					$razmjeranGO = round($GO/12 * $mjesec_pocetak_zahtjeva, 0, PHP_ROUND_HALF_UP);
				} else {
					if($user->prekidStaza == 'DA' || $user->prvoZaposlenje == 'DA'){
						if($mjesec >= 6){
							$razmjeranGO = $GO;
						} else {
							$razmjeranGO = round($GO/12 * $mjesec, 0, PHP_ROUND_HALF_UP);
						}
					} else {
						$razmjeranGO = round($GO/12 * $mjesec, 0, PHP_ROUND_HALF_UP);
					}
				}				
			//	$razmjeranGO += 2;

			} else {
				if($prijavaGodina < $ova_godina){
					$razmjeranGO = round($GO/12 * $ovaj_mjesec, 0, PHP_ROUND_HALF_UP);
				} else {
					if($user->prekidStaza == 'DA' || $user->prvoZaposlenje == 'DA'){
						if($mjesec >= 6){
							$razmjeranGO = $GO;
						} else {
							$razmjeranGO = round($GO/12 * $mjesec, 0, PHP_ROUND_HALF_UP);
						}
					} else {
						$razmjeranGO = round($GO/12 * $mjesec, 0, PHP_ROUND_HALF_UP);
					}
				}
			}
			
		} else {
			$razmjeranGO = 0;
		}
		
		if($razmjeranGO > 25){
			$razmjeranGO = 25;
		}
		if($razmjeranGO > $GO){
			$razmjeranGO = $GO;
		}
			
		return $razmjeranGO;
	}

	/*  računa razmjeran GO za tekuću godinu */
	public static function razmjeranGO($user)    
	{
		$datum = new DateTime('now');    /* današnji dan */
		$ova_godina = date_format($datum,'Y');
		$ovaj_mjesec = date_format($datum,'m');
		$ovaj_dan = date_format($datum,'d');
		
		if( $ovaj_mjesec == 1 ||
			$ovaj_mjesec == 3 ||
			$ovaj_mjesec == 5 ||
			$ovaj_mjesec == 7 ||
			$ovaj_mjesec == 8 ||
			$ovaj_mjesec == 10 ||
			$ovaj_mjesec == 12 ) {
				$dan_pola_mjeseca = 16;
			} else if($ovaj_mjesec == 2) {
				$dan_pola_mjeseca = 14;
			} else {
				$dan_pola_mjeseca = 15;
			}
		/*  $dan_pola_mjeseca = round( cal_days_in_month(CAL_GREGORIAN, intval($ovaj_mjesec), intval($ova_godina) )/2,0,PHP_ROUND_HALF_UP); */   // 15 dana ili 14 ili 16  NE RADI NA SERVERU!!!!!

		
		if($ovaj_dan < $dan_pola_mjeseca){
			$ovaj_mjesec -=1;
		} 
		
		$GO  = GodisnjiController::godisnjiUser($user);

		if($user->datum_prijave) {
			$datum_prijave = $user->datum_prijave;
			$datum_prijave = explode('-',$user->datum_prijave);
			
			$prijavaGodina = $datum_prijave[0];
			$prijava = new DateTime($user->datum_prijave);
			$staz = $prijava->diff($datum);   /* staz u Duplicu*/
			$mjesec = $staz->format('%m');
			$dan = $staz->format('%d');
			if($dan >= $dan_pola_mjeseca){
				$mjesec +=1;
			}
			if($prijavaGodina < $ova_godina){
				$razmjeranGO = round($GO/12 * $ovaj_mjesec, 0, PHP_ROUND_HALF_UP);
			} else {
				if($user->prekidStaza == 'DA' || $user->prvoZaposlenje == 'DA'){
					if($mjesec >= 6){
						$razmjeranGO = $GO;
					} else {
						$razmjeranGO = round($GO/12 * $mjesec, 0, PHP_ROUND_HALF_UP);
					}
				} else {
					$razmjeranGO = round($GO/12 * $mjesec, 0, PHP_ROUND_HALF_UP);
				}
			}
			
		} else {
			$razmjeranGO = 0;
		}
		
		if($razmjeranGO > 25){
			$razmjeranGO = 25;
		}
		if($razmjeranGO > $GO){
			$razmjeranGO = $GO;
		}
			
		return $razmjeranGO;
	}
	
	/*  razmjeran GO PROŠLA godina*/
	public static function razmjeranGO_PG($user, $godina)     /***************** OK  *****************/
	{		
		$datum = new DateTime('now');    /* današnji dan */
		$ova_godina = date_format($datum,'Y');
		$dan_pola_mjeseca = 15;

		$datumPG = new DateTime($godina . '-12-31');
		
		if ($godina == $ova_godina) {
			$razmjeranGO_PG = GodisnjiController::razmjeranGO($user); 		/* dani GO ova godina */
		} else {
			$GO  = GodisnjiController::godisnjiPG($user, $godina); 		/* dani GO zadana godina */
			$razmjeranGO_PG = 0;

			if($user->datum_prijave) {
				$datum_prijave = $user->datum_prijave;
				$datum_prijave = explode('-', $user->datum_prijave);
				$prijavaGodina = $datum_prijave[0];
				$prijava = new DateTime($user->datum_prijave);
				if($prijavaGodina <= $godina) {
					$staz = $datumPG->diff($prijava);   					/* staz u Duplicu do 31.12. prošla godina*/
					$mjesec = $staz->format('%m');
					$dan = $staz->format('%d');
					$godinaU = $staz->format('Y');
					
					if($mjesec > 0 && $godinaU > 0)
						$dan_pola_mjeseca = round(cal_days_in_month(CAL_GREGORIAN, intval($mjesec), intval($godinaU) )/2,0,PHP_ROUND_HALF_UP);
					if($prijavaGodina < $godina){
						$razmjeranGO_PG = $GO; 
					} elseif ($prijavaGodina == $godina) {
						
						if($user->prekidStaza == 'DA' || $user->prvoZaposlenje == 'DA'){
							if($mjesec >= 6){
								$razmjeranGO_PG = $GO;
							} else {
								if($dan >= $dan_pola_mjeseca){
									$mjesec +=1;
								}
								$razmjeranGO_PG = round($GO/12 * $mjesec, 0, PHP_ROUND_HALF_UP);
							}
						} else {
							if($dan >= $dan_pola_mjeseca){
								$mjesec +=1;
							}
							$razmjeranGO_PG = round($GO/12 * $mjesec, 0, PHP_ROUND_HALF_UP);
						}
					
					} elseif ($prijavaGodina == $ova_godina) {
						$razmjeranGO_PG = 0;
					}
				}	else {
					$razmjeranGO_PG = 0;
				}
			}
		}		
		
		return $razmjeranGO_PG;
	}
	
	/*  razmjeran GO sve godine*/
	public static function razmjeranGO_years($user)
	{
		$godine = GodisnjiController::godineZahtjeva(); // sve godine zahtjeva
		$dani = array();
		foreach ($godine as $godina) {
			$GO_dani = GodisnjiController::razmjeranGO_PG($user, $godina); // razmjerni dani za godinu
			$dani[$godina] = $GO_dani;

		}
		return $dani;
	}

	public static function zahtjevi_novo ($registration)   // nije dobro!!!!!!!
	{
		$datum = new DateTime('now');    /* današnji dan */
		$ova_godina = date_format($datum,'Y');
		$prosla_godina = date_format($datum,'Y') - 1;
		$mjesec_danas = date_format($datum,'m');
		
		$GO_razmjeran = GodisnjiController::razmjeranGO($registration); // razmjerni dani ova godina
		$GO_PG = GodisnjiController::razmjeranGO_PG($registration, $prosla_godina); // razmjerni dani prošla godina
		$zahtjevi = VacationRequest::where('employee_id',$registration->employee_id)->where('zahtjev','GO')->where('odobreno','DA')->get();
		
		$preostalo_PG = $GO_PG;
		$preostalo_OG = $GO_razmjeran;
		$zahtjevi_Dani_OG = 0;
		$zahtjevi_Dani_PG = 0;
			
		foreach($zahtjevi as $zahtjev){
			$begin = new DateTime($zahtjev->start_date);
			$end = new DateTime($zahtjev->end_date);
			$end->setTime(0,0,1);
			$interval = DateInterval::createFromDateString('1 day');
			$period = new DatePeriod($begin, $interval, $end);
			foreach ($period as $dan) {
				if(date_format($dan,'N') < 6 ){
					if(date_format($dan,'d') == '01' && date_format($dan,'m') == '01' ||
						date_format($dan,'d') == '06' && date_format($dan,'m') == '01' ||
						date_format($dan,'d') == '01' && date_format($dan,'m') == '05' ||
						date_format($dan,'d') == '22' && date_format($dan,'m') == '06' ||
						date_format($dan,'d') == '25' && date_format($dan,'m') == '06' && date_format($dan,'Y') == '2018' ||
						date_format($dan,'d') == '25' && date_format($dan,'m') == '06' && date_format($dan,'Y') == '2019' ||
						date_format($dan,'d') == '15' && date_format($dan,'m') == '08' ||
						date_format($dan,'d') == '05' && date_format($dan,'m') == '08' ||
						date_format($dan,'d') == '08' && date_format($dan,'m') == '10' ||
						date_format($dan,'d') == '01' && date_format($dan,'m') == '11' ||
						date_format($dan,'d') == '25' && date_format($dan,'m') == '12' ||
						date_format($dan,'d') == '26' && date_format($dan,'m') == '12' ||
						date_format($dan,'d') == '02' && date_format($dan,'m') == '04' && date_format($dan,'Y') == '2018' ||
						date_format($dan,'d') == '31' && date_format($dan,'m') == '05' && date_format($dan,'Y') == '2018' ||
						date_format($dan,'d') == '22' && date_format($dan,'m') == '04' && date_format($dan,'Y') == '2019' ||
						date_format($dan,'d') == '20' && date_format($dan,'m') == '06' && date_format($dan,'Y') == '2019' ||
						date_format($dan,'d') == '13' && date_format($dan,'m') == '04' && date_format($dan,'Y') == '2020' ||
						date_format($dan,'d') == '11' && date_format($dan,'m') == '06' && date_format($dan,'Y') == '2020'){
							//
					} else {
							if($preostalo_PG > 0) {
								if(date_format($dan,'Y') < $ova_godina) { // sve prijašnje godine
									$preostalo_PG -= 1;
									$zahtjevi_Dani_PG += 1;
								} elseif(date_format($dan,'m') < '07' && date_format($dan,'Y') == $ova_godina) {
									$preostalo_PG -= 1;
									$zahtjevi_Dani_PG += 1;
								} else {
									$preostalo_OG -= 1;
									$zahtjevi_Dani_OG += 1;
								}
							} else {
								$preostalo_OG -= 1;
								$zahtjevi_Dani_OG += 1;
							}
					}
				}
			}	
		}
		if ($mjesec_danas >= 7 ) {
			$preostalo_PG = 0;
		}


		return ['preostalo_PG' => $preostalo_PG, 'preostalo_OG' => $preostalo_OG, 'preostalo_ukupno' => $preostalo_OG + $preostalo_PG, 'zahtjevi_Dani_PG' => $zahtjevi_Dani_PG, 'zahtjevi_Dani_OG' => $zahtjevi_Dani_OG];
	}

	public static function godineZahtjeva () 
	{
		$datum = new DateTime('now');    /* današnji dan */
		$ova_godina = date_format($datum,'Y');
		$zahtjevi = VacationRequest::where('zahtjev','GO')->where('odobreno','DA')->get();
		$godine = array();
		if($zahtjevi->first()) {
			$prva_godina = date('Y',strtotime($zahtjevi->first()->start_date));
		}

		while ($prva_godina <= $ova_godina) {
			array_push($godine, strval($prva_godina));
			$prva_godina++;
		}

		return $godine;
	}

	public static function zahtjeviSveGodine ($registration) 
	{
		$zahtjeviArray = array();
		$zahtjevi_godina_SG = array();
		$zahtjevi_godina_PG = array();
	
		$godine = GodisnjiController::godineZahtjeva(); // sve godine zahtjeva
		$zahtjevi = VacationRequest::where('employee_id',$registration->employee_id)->where('zahtjev','GO')->where('odobreno','DA')->orderBy('start_date','ASC')->get(); // svi zahtjevi djelatnika
	
		foreach ($godine as $godina) {
			$zahtjeviArray[$godina] = array();
			$GO_dani = GodisnjiController::razmjeranGO_PG($registration, $godina); // razmjerni dani za godinu
			/* $razmjeranGO_years = GodisnjiController::razmjeranGO_years($registration); */ // razmjerni dani sve  godine
			
			$zahtjevi_godina_PG = array_unique(array_merge($zahtjevi_godina_PG, $zahtjevi_godina_SG));  // zahtjevi_PG - zahtjevi iz slijedeće godine do 30.6. ako ima razmjernih dana slobodnih + prebačeni zahtjevi iz prošle godine ako nije bilo dana - maknuti dupli datumi
			$zahtjevi_godina = array();
			
			foreach($zahtjevi as $zahtjev){
				$begin = new DateTime($zahtjev->start_date);
				$end = new DateTime($zahtjev->end_date);
				$end->setTime(0,0,1);
				$interval = DateInterval::createFromDateString('1 day');
				$period = new DatePeriod($begin, $interval, $end);

				foreach ($period as $dan) {
					if(date_format($dan,'N') < 6 ){
						if(date_format($dan,'d') == '01' && date_format($dan,'m') == '01' ||
							date_format($dan,'d') == '06' && date_format($dan,'m') == '01' ||
							date_format($dan,'d') == '01' && date_format($dan,'m') == '05' ||
							date_format($dan,'d') == '22' && date_format($dan,'m') == '06' ||
							date_format($dan,'d') == '25' && date_format($dan,'m') == '06'  && date_format($dan,'Y') == '2018'  ||
							date_format($dan,'d') == '25' && date_format($dan,'m') == '06'  && date_format($dan,'Y') == '2019'  ||
							date_format($dan,'d') == '15' && date_format($dan,'m') == '08' ||
							date_format($dan,'d') == '05' && date_format($dan,'m') == '08' ||
							date_format($dan,'d') == '08' && date_format($dan,'m') == '10' ||
							date_format($dan,'d') == '01' && date_format($dan,'m') == '11' ||
							date_format($dan,'d') == '25' && date_format($dan,'m') == '12' ||
							date_format($dan,'d') == '26' && date_format($dan,'m') == '12' ||
							date_format($dan,'d') == '02' && date_format($dan,'m') == '04' && date_format($dan,'Y') == '2018' ||
							date_format($dan,'d') == '31' && date_format($dan,'m') == '05' && date_format($dan,'Y') == '2018' ||
							date_format($dan,'d') == '22' && date_format($dan,'m') == '04' && date_format($dan,'Y') == '2019' ||
							date_format($dan,'d') == '20' && date_format($dan,'m') == '06' && date_format($dan,'Y') == '2019' ||
							date_format($dan,'d') == '13' && date_format($dan,'m') == '04' && date_format($dan,'Y') == '2020' ||
							date_format($dan,'d') == '11' && date_format($dan,'m') == '06' && date_format($dan,'Y') == '2020'){
								//
						} else {
							if($GO_dani > 0  ) {
								if(date_format($dan,'Y') == $godina) {
									if(isset( $zahtjeviArray[$godina-1] )) { 
										if( ! in_array( date_format($dan,'Y-m-d'), $zahtjeviArray[$godina-1])) {
											array_push($zahtjevi_godina, date_format($dan,'Y-m-d'));
											$GO_dani--;	

										}
									} else if (! in_array( date_format($dan,'Y-m-d'), $zahtjeviArray[$godina])){
										array_push($zahtjevi_godina, date_format($dan,'Y-m-d'));
										$GO_dani--;	
									}
														
								} elseif(date_format($dan,'m') < '07' && date_format($dan,'Y') == $godina+1 ) {
									array_push($zahtjevi_godina, date_format($dan,'Y-m-d'));
									$GO_dani--;	
									array_push($zahtjevi_godina_PG, date_format($dan,'Y-m-d'));
								}	
							} else {   // ako nema slobodnih dana u godini prijenos zahtjeva u drugu godinu
								if( $godina == date('Y') ) {
									array_push($zahtjevi_godina, date_format($dan,'Y-m-d'));
									$GO_dani--;	
								} else if(date_format($dan,'Y') == $godina) {
									array_push($zahtjevi_godina_SG, date_format($dan,'Y-m-d'));
									$GO_dani--;	
								}
							} 
						}
					}
				}	
			}
		

			$zahtjeviArray[$godina] = (array_merge( $zahtjevi_godina));	
			
		}
		return $zahtjeviArray;
	}

	public static function go_na_dan ($employee_id, $date)
	{
		$zahtjev = VacationRequest::where('employee_id',$employee_id)->where('start_date',$date)->where('odobreno','<>','NE')->first(); // svi zahtjevi djelatnika

		if($zahtjev) {
			return false;
		} else {
			return true;
		}		
	} 

	public static function go_for_request ($employee_id, $date_start, $date_end)
	{
		$zahtjevi = VacationRequest::where('employee_id', $employee_id)->where('odobreno','<>','NE')->get(); // svi zahtjevi djelatnika
		$response =  true;
		if($date_start == $date_end) {
			$response = GodisnjiController::go_na_dan($employee_id, $date_start);
		} else {
			foreach($zahtjevi as $zahtjev){		
				$begin = new DateTime($zahtjev->start_date);
				$end = new DateTime($zahtjev->end_date);
				$end->setTime(0,0,1);
				$interval = DateInterval::createFromDateString('1 day');
				$period = new DatePeriod($begin, $interval, $end);
	
				$begin_request = new DateTime($date_start);
				$end_request = new DateTime($date_end);
				$period_request = new DatePeriod($begin_request, $interval, $end_request);
	
				foreach ($period as $dan) {					
					foreach ($period_request as $dan_zahtjev) {
						if(date_format($dan,'Y-m-d') == date_format($dan_zahtjev,'Y-m-d') ){
							$response =  false;
						}
					}				
				}
			}
		}
			
		return $response;
	} 

	public static function go_na_dan_odobreni ($employee_id, $date)
	{
		$zahtjevi = VacationRequest::where('employee_id',$employee_id)->where('zahtjev', '<>', 'Izlazak')->where('odobreno', 'DA')->get(); // svi zahtjevi djelatnika
		
		foreach($zahtjevi as $zahtjev){
			$begin = new DateTime($zahtjev->start_date);
			$end = new DateTime($zahtjev->end_date);
			$end->setTime(0,0,1);
			$interval = DateInterval::createFromDateString('1 day');
			$period = new DatePeriod($begin, $interval, $end);

			foreach ($period as $dan) {
				if(date_format($dan,'Y-m-d') == $date){
					return false;
				}
			}
		}

		return true;
	} 

	public static function afterHour_for_request ($employee_id, $date, $time1, $time2)
	{
		$zahtjevi = AfterHour::where('employee_id',$employee_id)->get(); // svi zahtjevi djelatnika
		$response =  true;
		foreach($zahtjevi as $zahtjev){
			
			if($zahtjev->datum == $date && date('H:i',strtotime($zahtjev->start_time)) == $time1) {
				$response =  false;
			}
		}
		return $response;
	}
	
}