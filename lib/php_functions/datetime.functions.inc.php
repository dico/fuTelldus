<?php
	
	/**
	* 16. Find the name of the day and return it in a spesific language 
	*
	* @param  	String		$language		The language to check for/run
	* @param  	String		$dayName		Name of the day in english (default PHP)
	* @return  	String		-				Name of the day in a language
	*/

	function date_findDayName($dayNumb) {
		if ($dayName == 1) 		return "Mandag";
		elseif ($dayName == 2) 	return "Tirsdag";
		elseif ($dayName == 3) 	return "Onsdag";
		elseif ($dayName == 4) 	return "Torsdag";
		elseif ($dayName == 5) 	return "Fredag";
		elseif ($dayName == 6) 	return "L&oslash;rdag";
		elseif ($dayName == 7) 	return "S&oslash;ndag";
	}
	
	
	function date_findMonthName($monthNumb) {
		if ($monthNumb == 1) return "januar";
		elseif ($monthNumb == 2) return "februar";
		elseif ($monthNumb == 3) return "mars";
		elseif ($monthNumb == 4) return "april";
		elseif ($monthNumb == 5) return "mai";
		elseif ($monthNumb == 6) return "juni";
		elseif ($monthNumb == 7) return "juli";
		elseif ($monthNumb == 8) return "august";
		elseif ($monthNumb == 9) return "september";
		elseif ($monthNumb == 10) return "oktober";
		elseif ($monthNumb == 11) return "november";
		elseif ($monthNumb == 12) return "desember";
	}
	
	function date_stringDate($time) {
		$day = date("d", $time);
		$dayNumb = date("N", $time);
		$month = date("m", $time);
		$year = date("Y", $time);
		
		$dayName = date_findDayName($dayNumb);
		$monthName = date_findMonthName($month);
		
		return "$day. $monthName $year";
	}

	function lastUpdated($time) {

		$day = date("d", $time);
		$dayNumb = date("N", $time);
		$month = date("m", $time);
		$year = date("Y", $time);


		if (date("dmy") == date("dmy", $time)) return date("H:i", $time);
		
		elseif (date("W") == date("W", $time)) {

			$dayName = date_findDayName($dayNumb);
			return substr($dayName, 0, 3) . date("H:i", $time);
		}

		else {
			return date("d.m.y", $time);
		}



	}




	function ago($timestamp) {
		global $lang;

		//type cast, current time, difference in timestamps
		$timestamp      = (int) $timestamp;
		$current_time   = time();
		$diff           = $current_time - $timestamp;
		
		//intervals in seconds
		$intervals      = array (
			'year' => 31556926, 'month' => 2629744, 'week' => 604800, 'day' => 86400, 'hour' => 3600, 'minute'=> 60
		);
		
		//now we just find the difference
		if ($diff == 0)
		{
			return $lang['right now'];
		}    

		if ($diff < 60)
		{
			return $diff == 1 ? $diff . ' ' . $lang['secound'] . ' ' . $lang['since'] : $diff . ' ' . $lang['secounds'] . ' ' . $lang['since'];
		}        

		if ($diff >= 60 && $diff < $intervals['hour'])
		{
			$diff = floor($diff/$intervals['minute']);
			return $diff == 1 ? $diff . ' ' . $lang['minute'] . ' ' . $lang['since'] : $diff . ' ' . $lang['minutes'] . ' ' . $lang['since'];
		}        

		if ($diff >= $intervals['hour'] && $diff < $intervals['day'])
		{
			$diff = floor($diff/$intervals['hour']);
			return $diff == 1 ? $diff . ' ' . $lang['hour'] . ' ' . $lang['since'] : $diff . ' ' . $lang['hours'] . ' ' . $lang['since'];
		}    

		if ($diff >= $intervals['day'] && $diff < $intervals['week'])
		{
			$diff = floor($diff/$intervals['day']);
			return $diff == 1 ? $diff . ' ' . $lang['day'] . ' ' . $lang['since'] : $diff . ' ' . $lang['days'] . ' ' . $lang['since'];
		}    

		if ($diff >= $intervals['week'] && $diff < $intervals['month'])
		{
			$diff = floor($diff/$intervals['week']);
			return $diff == 1 ? $diff . ' ' . $lang['week'] . ' ' . $lang['since'] : $diff . ' ' . $lang['weeks'] . ' ' . $lang['since'];
		}    

		if ($diff >= $intervals['month'] && $diff < $intervals['year'])
		{
			$diff = floor($diff/$intervals['month']);
			return $diff == 1 ? $diff . ' ' . $lang['month'] . ' ' . $lang['since'] : $diff . ' ' . $lang['months'] . ' ' . $lang['since'];
		}    

		if ($diff >= $intervals['year'])
		{
			$diff = floor($diff/$intervals['year']);
			return $diff == 1 ? $diff . ' ' . $lang['year'] . ' ' . $lang['since'] : $diff . ' ' . $lang['years'] . ' ' . $lang['since'];
		}
	}




		/**
	* 19. Converts minutes to string time (180 minutes will return 3 hours)
	*
	* @param  	Int			$min			Minutes
	* @return  	String		$stringTime		String of time
	*/
	
	function min2stringTime($min, $short = false) {
		
		if ($min >= 60) {
			$timeDesimal = ($min / 60);
			list($hour, $min) = explode(".", $timeDesimal);
			$min = (0 . "." . $min) * 60;
			
			$min = round($min);

			if ($short) {
				$stringTimeHour = "$hour t";
			} else {
				if ($hour == 1) $stringTimeHour = "$hour time";
				elseif ($hour > 1) $stringTimeHour = "$hour timer";
			}
		}
		
		if ($short) {
			$stringTimeMin = "$min m";
		} else {
			if ($min == 1) $stringTimeMin = "$min minutt";
			elseif ($min > 1) $stringTimeMin = "$min minutter";
		}


		if ($short) $separator = "";
		else $separator = "og";
		
		if ($hour > 0 && $min > 0) $stringTime = "$stringTimeHour $separator $stringTimeMin";
		elseif ($hour == 0 && $min > 0) $stringTime = "$stringTimeMin";
		elseif ($hour > 0 && $min == 0) $stringTime = "$stringTimeHour";
		
		if (empty($hour) && empty($min)) $stringTime = "Ukjent";
		
		return $stringTime;
	}



	function min2hourMin($min) {
		$timeDesimal = ($min / 60);
		list($hour, $min) = explode(".", $timeDesimal);
		$min = (0 . "." . $min) * 60;
		
		$min = round($min);

		return $hour . ":" . $min;
	}
	
	
	
	
	/**
	* 20. Return decimal time to either hour or minutes (3.5 minutes => 3 min or 0,058 hours)
	*
	* @param  	Int			$min			Minutes
	* @param  	String		$type			hour or min
	* @return  	Int			$time			The converted time
	*/
	
	function min2splitTime($min, $type) {
		$timeDesimal = ($min / 60);
		list($hour, $min) = explode(".", $timeDesimal);
		$min = (0 . "." . $min) * 60;
		
		$min = round($min);

		if ($type == "hour") $time = $hour;
		elseif ($type == "min") $time = $min;
		
		return $time;
	}

?>