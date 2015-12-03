<?php

defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('secsToTime'))
{

	function secsToTime($tmp) {

		if($tmp<0) { $neg='-'; $tmp*=-1; }
		else $neg='';

		$tmp=round($tmp);

		$seconds = $tmp % 60;
		
		$tmp -= $seconds;
		$tmp /= 60;

		$minutes = $tmp % 60;
	
		$tmp -= $minutes;
		$tmp /= 60;

		$hours = $tmp;

		return $neg.$hours.':'.($minutes < 10 ? '0' : '').$minutes.':'.($seconds < 10 ? '0' : '').$seconds;

	}
}


