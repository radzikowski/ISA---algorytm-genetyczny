<?php

namespace ISA\lib;

class Mutations{
	private $_probability;
	private $_alg;

	public function __construct($probability, $alg){
		$this->_probability = $probability;
		$this->_alg = $alg;
	}

	public function uniformSingle($single){
		$binReturn = '';
		$binMutation = array();
		for($i=0; $i<strlen($single); $i++){
			$probability = lcg_value();
			if ($probability <= $this->_probability){
				$binReturn = $binReturn.($single[$i] ? 0: 1);
				$binMutation[] = $i;
			}else{
				$binReturn = $binReturn.($single[$i]);
			}
		}


		return array(
			'xbin' => $binReturn,
			'mutationBits' => (count($binMutation) ? implode(',', $binMutation) : '-')
		);
	}

	public function uniform($population){
		for($i=0; $i<count($population); $i++){
			$resultMutation = $this->uniformSingle($population[$i]['x_after_cross']);
			$population[$i]['mutation_bits'] = $resultMutation['mutationBits'];
			$population[$i]['xbin_after_mutation'] = $resultMutation['xbin'];
			$population[$i]['xreal_after_mutation'] = $this->_alg->binToReal($resultMutation['xbin']);
			$population[$i]['eval_func_after_mutation'] = $this->_alg->evaluateFunc($population[$i]['xreal_after_mutation']);
		}
		return $population;

	}

	
}
