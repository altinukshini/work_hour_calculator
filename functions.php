<?php

define('DAY_WORK', 32400); // 9 hours, from 08:00:00 to 17:00:00 (((( 3600 = 9 * 60 * 60 ))))
define('HOUR_START_DAY', '08:00:00');
define('HOUR_END_DAY', '17:00:00');

// Main function that calculates working hours and returns the calculated time in seconds
function calculateWorkHours($dateStart, $dateEnd){ //returns seconds || parameter format required 'Y-m-d H:i:s'

    // keep the initial dates for later use
    $d1 = new DateTime($dateStart);
    $d2 = new DateTime($dateEnd);

    // Date formats
    $d1Format = $d1->format('Y-m-d H:i:s');
    $d2Format = $d2->format('Y-m-d H:i:s');
    $EndOfDayFormat = $d1->format('Y-m-d '.HOUR_END_DAY);
    $StartOfDayFormat = $d1->format('Y-m-d '.HOUR_START_DAY);

    $StartOfDayObj = new DateTime($StartOfDayFormat);
    $EndOfDayObj = new DateTime($EndOfDayFormat);

    $interval = new DateInterval('P1D');
    $d3 = clone $d2;
    $d3 = $d3->modify( '+1 day' ); // to get the last day below
    $period = new DatePeriod($d1, $interval, $d3);

    $workedTime = 0;
    $nb = 0;

    if ($d1->format('Y-m-d') == $d2->format('Y-m-d')) {

    	$difference = $d2->diff($d1)->format('%H:%I:%S');
    	$difference = split(':', $difference);
    	$difference = $difference[0]*3600 + $difference[1]*60 + $difference[2];
    	$workedTime += $difference;

    } else {
	    
	    foreach($period as $date) { // for every work day, add the hours you want

	    	$week_day = $date->format('w'); // 0 (for Sunday) through 6 (for Saturday)

			if (!in_array($week_day,array(0, 6))) {

				if ($date->format('Y-m-d') == $d1->format('Y-m-d')) { // if first day

					$HourEndDayInt = split(':', HOUR_END_DAY);
					$HourEndDayInt = $HourEndDayInt[0];

					if ($d1->format("H") < $HourEndDayInt) {
						$difference = $EndOfDayObj->diff($d1)->format("%H:%I:%S");
					    $difference = split(':', $difference);
					    $difference = $difference[0]*3600 + $difference[1]*60 + $difference[2];
					    $workedTime += $difference;
					}

				} elseif ($date->format('Y-m-d') == $d2->format('Y-m-d')) { // if last day

				    $difference = $StartOfDayObj->diff($d2)->format('%H:%I:%S');
				    $difference = split(':', $difference);
				    $difference = $difference[0]*3600 + $difference[1]*60 + $difference[2];
				    $workedTime += $difference;
				
				} else {
				    // full day
				    $workedTime += DAY_WORK;
				}
			}

			if ($nb> 10) {
				die("die ".$nb);
			}

	    }
	}

    return $workedTime;
}


// Different functions to convert seconds to Hour, Minute, Seconds format

function format_time($t,$f=':') // t = seconds, f = separator 
{
  return sprintf("%02d hours %s %02d minutes %s %02d seconds", floor($t/3600), $f, ($t/60)%60, $f, $t%60);
}

function secondsToTime($seconds) 
{
    $dtF = new DateTime("@0");
    $dtT = new DateTime("@$seconds");
    return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes and %s seconds');
}

function formatWorkingHours($seconds)
{

    $working_hours_timestamp = gmdate("H:i:s", $seconds);
    $poz = split(":", $working_hours_timestamp);

    $result['hours'] = $poz[0];
    $result['minutes'] = $poz[1];
    $result['seconds'] = $poz[2];

    return $result;
}



