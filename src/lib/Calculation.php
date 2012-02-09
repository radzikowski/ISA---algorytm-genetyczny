<?php

namespace ISA\lib;

class Calculation{
	
	public function __construct(){ }
	
	public function getPopulationWithoutKey($population, $keysModel){
		$results = array();
		for($i=0; $i<count($population); $i++) {
			$singleResult = array();
			foreach($keysModel as $key){
				if (count($population[$i][$key]))
					$singleResult[]	= json_encode($population[$i][$key]);
				else
					$singleResult[] = $population[$i][$key];
			}
			$results[] = $singleResult;
		}
		return $results;
	}

	public function getPercentInPopulation($population, $object){
		$results = 0;
		for($i=0; $i<count($population); $i++) {
			if ($object == $population[$i]['xreal'])
				$results++;
		}

		return ($results/count($population)*100);
	}

	public function getNewPopulationFrom($population, $alg){
		$newPopulation = array();
		for($i=0; $i<count($population); $i++){

			$newPopulation[$i] = array(
				'xreal' => $population[$i]['xreal_after_mutation'],
				'xbin' => $population[$i]['xbin_after_mutation'],
				'xint' => $alg->realToInt($population[$i]['xreal_after_mutation']),
				'eval_func' => $population[$i]['eval_func_after_mutation'],
			);
		}
		return $newPopulation;
	}

	public function printArray($array){
		echo "<pre>";
		print_r($array);
		echo "</pre>";
	}
	
	public function saveToFile($fileName, $header, $content){
		$fp = fopen($fileName, "w");

		if ($fp){
				fputcsv($fp, $header , ';', '"');
			foreach ($content as $fields) {
		    	fputcsv($fp, $fields, ';', '"');
			}
			fclose($fp);
		}

	}
}