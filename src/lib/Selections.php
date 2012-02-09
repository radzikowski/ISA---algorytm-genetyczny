<?php

namespace ISA\lib;

class Selections{

	public function __construct(){
	}

	public function proportional($population){

		for($i=0; $i<count($population); $i++) {
		//random float
			$randomR1 = lcg_value();
			$selectionIndex = 0;
			while ( ($selectionIndex < count($population)-2)
					&& $population[$selectionIndex]['distribution'] <= $randomR1 ) {
				$selectionIndex++;
        	}

        	$population[$i]['rand_selection'] = $randomR1;
        	$population[$i]['x_after_selection'] = $population[$selectionIndex]['xreal'];
		}
		return $population;
	}



}
