<?php

namespace ISA\lib;

class Crossings{
	private $_from;
	private $_indexsCanCrossElements = array();
	private $_alg;

	public function __construct($from, $alg){
		$this->_from = $from;
		$this->_alg = $alg;
	}

	public function getCanCross($population){
		for($i=0; $i<count($population); $i++){
			$parentProbability = lcg_value();
			$population[$i]['parent_probability'] = $parentProbability;
			if ($parentProbability <= $this->_from){
				$this->_indexsCanCrossElements[]=$i;
			}
				$population[$i]['xbin_after_selection'] = $this->_alg->realToBin($population[$i]['x_after_selection']);
				$population[$i]['cross_point'] = '-';
				$population[$i]['xbin_children'] = '-';
				$population[$i]['x_after_cross'] = $population[$i]['xbin_after_selection'];
		}
		return $population;
	}

	public function cross($bin1, $bin2, $crossPoint){
		$first = substr($bin1, 0, -$crossPoint).substr($bin2, strlen($bin2)-$crossPoint);
		$second = substr($bin2, 0, -$crossPoint).substr($bin1, strlen($bin1)-$crossPoint);
		$result = array(
			'1' => $first,
			'2' => $second,
		);

		return $result;
	}

	public function onePoint($population){
		if (count($this->_indexsCanCrossElements)%2)
			$this->_indexsCanCrossElements[] = $this->_indexsCanCrossElements[0];

		for($i=0; $i<(count($this->_indexsCanCrossElements)-1); $i+=2){
			$crossPoint = rand(1, $this->_alg->getGenLength());
			$crossResult = $this->cross(
				$population[$this->_indexsCanCrossElements[$i]]['xbin_after_selection'],
				$population[$this->_indexsCanCrossElements[$i+1]]['xbin_after_selection'],
				$crossPoint
			);
			$population[$this->_indexsCanCrossElements[$i]]['cross_point'] = $crossPoint-1;
			$population[$this->_indexsCanCrossElements[$i]]['xbin_children'] = $crossResult[1];
			$population[$this->_indexsCanCrossElements[$i]]['x_after_cross'] = $crossResult[1];

			if ($this->_indexsCanCrossElements[$i+1] > $this->_indexsCanCrossElements[$i]){
				$population[$this->_indexsCanCrossElements[$i+1]]['cross_point'] = $crossPoint-1;
				$population[$this->_indexsCanCrossElements[$i+1]]['xbin_children'] = $crossResult[2];
				$population[$this->_indexsCanCrossElements[$i+1]]['x_after_cross'] = $crossResult[2];
			}
			
		}

		return $population;
	}
}
