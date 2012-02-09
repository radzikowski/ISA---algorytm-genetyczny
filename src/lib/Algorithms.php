<?php

namespace ISA\lib;

class Algorithms{
	private $_countOfChild;
	private $_genLength;
	private $_precision;
	private $_a;
	private $_b;
	private $_population = array();
	private $_fitFuncSum = 0;
	
	public function __construct($a, $b, $precision){ //population size??
		$this->_a = $a;
		$this->_b = $b;
		$this->_precision = pow(10, -1*$precision);
		$this->_countOfChild = ((($this->_b-$this->_a)/$this->_precision) + 1);
		$this->_genLength = ceil(log($this->_countOfChild)/log(2));
	}
	
	public function getPrecision(){
		return $this->_precision;
	}

	public function getGenLength(){
		return $this->_genLength;
	}

	public function random(){
		// php have rand included numbers
		$from = ($this->_a / $this->_precision);
		$to = $this->_countOfChild;
		$x = rand(0, $to)+$from;
		return (floor($x)*$this->_precision);
	}
	
	public function realToInt($xreal){
		return round((1/($this->_b-$this->_a))* ($xreal - $this->_a) * (pow(2, $this->_genLength) - 1));
	}
	
	public function realToBin($xreal){
		$xint = $this->realToInt($xreal);
		return $this->intToBin($xint);
	}
	
	public function intToBin($xint){
		$xBin = '';
		for ($i=0; $i<$this->_genLength; $i++) {
			$xBin = ((($xint%2)) ? '1' : '0').$xBin;
			$xint = floor($xint/2);
		}
		return $xBin;
	}
	
	public function intToReal($xint){
		$xReal = ($this->_b-$this->_a)*($xint) / (pow(2, $this->_genLength) - 1) + $this->_a;
		$x1 = round($xReal / $this->_precision);
		$x2 = $x1 * $this->_precision;
		return $x2;
	}
	
	public function binToInt($xbin){
		$length = strlen($xbin);
		$xint = 0;
		for($i=$length-1; $i>=0; $i--){
			$xint += $xbin[$i]*pow(2, ($length-1) -$i);
		}
		return $xint;
	}

	public function binToReal($xbin){
		$xint = $this->binToInt($xbin);
		return $this->intToReal($xint);
	}
	
	public function evaluateFunc($xreal){
		//    	F(x)= (x MOD 1) * (COS( 20 * π * x) – SIN(x))
		$valueFunc = (fmod($xreal,1)) * cos(20 * M_PI *$xreal) - sin($xreal);
		// get 1 point more of precision to show correct result of function
		$precision = $this->_precision/10;
		$x = round($valueFunc / $precision);
		return $x * $precision;		
	}

	public function fitMinFunc($evaluateFunc, $maxFunc){
		return -($evaluateFunc - $maxFunc) + $this->_precision;
	}

	public function calcMinFitFunc($maxFunc){
		for($i=0; $i<count($this->_population); $i++) {
			$fitFunc = $this->fitMinFunc($this->_population[$i]['eval_func'], $maxFunc);
			$this->_population[$i]['fit_func'] = $fitFunc;
			$this->_fitFuncSum += $fitFunc;
		}
	}
	
	public function fitMaxFunc($evaluateFunc, $minFunc){
		return $evaluateFunc - $minFunc + $this->_precision;
	}

	public function calcMaxFitFunc($minFunc){
		for($i=0; $i<count($this->_population); $i++) {
			$fitFunc = $this->fitMaxFunc($this->_population[$i]['eval_func'], $minFunc);
			$this->_population[$i]['fit_func'] = $fitFunc;
			$this->_fitFuncSum += $fitFunc;
		}
	}

	public function calcProbability($withDistribution){
		//$probabilitySum = 0;
		for($i=0; $i<count($this->_population); $i++) {
			$fitFunc = $this->_population[$i]['fit_func'];
			$this->_population[$i]['probability'] = ($fitFunc/$this->_fitFuncSum);
			//$probabilitySum += $this->_population[$i]['probability'];
			if ($withDistribution){
				$distribution = ($i == 0)? $this->_population[$i]['probability'] : ($this->_population[$i-1]['distribution'] + $this->_population[$i]['probability']);
				$this->_population[$i]['distribution'] = $distribution;
			}
		}	
//		var_dump('suma:'.$probabilitySum);
	}

	public function getPopulation(){
		return $this->_population;
	}

	public function setPopulation($population){
		$this->_population = $population;
	}

	public function getPopulationWithoutKey($population, $keysModel){
		$results = array();
		for($i=0; $i<count($population); $i++) {
			$singleResult = array();
			foreach($keysModel as $key){
				$singleResult[] = $population[$i][$key];
			}
			$results[] = $singleResult;
		}
		return $results;
	}

	public function calcPopulation($size){
		$results = array();
		for($i=0; $i<$size; $i++){
			$x = $this->random();
			$x_int = $this->realToInt($x);
			$x_bin = $this->intToBin($x_int);
			
			$results[$i] = array(
				'xreal' => $x,
				'xint' => $x_int,
				'xbin' => $x_bin,
				'eval_func' => $this->evaluateFunc($x)
			);
		}
		$this->_population = $results;
		return $results;
	}


	
	public function findMinMaxFunc($population = null){
		if (!$population)
			$population = $this->getPopulation();		
		
		$evaluateFuncs = array();
		for($i=0; $i<count($population); $i++){
			$evaluateFuncs[] = $population[$i]['eval_func'];
		}

		return array(
			'min' => min($evaluateFuncs),
			'max' => max($evaluateFuncs)
		);
	}

	public function calcDistribution(){
		for($i=0; $i<count($this->_population); $i++) {
				$distribution = ($i == 0)? 0 : ($this->_population[$i-1]['distribution'] + $this->_population[$i]['probability']);
				$this->_population[$i]['distribution'] = $distribution;
			}
	}
}