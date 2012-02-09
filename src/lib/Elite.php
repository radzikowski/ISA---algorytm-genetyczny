<?php

namespace ISA\lib;

class Elite{
	private $_population = array();
	private $_elite = array();
	private $_eliteKey = array();
	private $_amountOfElite = 0;
	private $_sortedPopulation = array();

	public function __construct($amountOfElite){
		$this->_amountOfElite = $amountOfElite;
	}

	public function setPopulation($population){
		$this->_population = $population;
		$this->_sortedPopulation = $population;
	}
	
	public function getPopulation(){
		return $this->_population;
	}

	public function getSortedPopulation(){
		return $this->_sortedPopulation;
	}

	private function cmp($a, $b){
		if ($a['eval_func'] == $b['eval_func']) {
        	return 0;
	    }
	    return ($a['eval_func'] > $b['eval_func']) ? -1 : 1;
	}

	public function sortByBest(){
		uasort($this->_sortedPopulation, array("ISA\lib\Elite","cmp"));
	}

	public function calcAndSaveElite(){
		$i=0;
		$this->_eliteKey = array();
		$this->_elite = array();
		foreach($this->_sortedPopulation as $key => $element){
			$this->_eliteKey[] = $key;
			if ($i < $this->_amountOfElite) {
				$this->_elite[] = $element;
			}		
			$i++;
		}
	}

	public function getBestOfPopulation(){
		return $this->_sortedPopulation[ $this->_eliteKey[0] ];
	}

	public function getWorstOfPopulation(){
		return $this->_sortedPopulation[ $this->_eliteKey[count($this->_eliteKey)-1] ];
	}

	public function putEliteToPopulation($population){
		$securedKey = array(); //key in array to not rewrite (key with elite)
		$eliteToPush = array();
		$keyToPut = -1;

		for($j=0; $j<$this->_amountOfElite; $j++){
			$key = array_search($this->_elite[$j], $population);
			if ($key){
				$securedKey[] = $key;
			} else {
				$eliteToPush[] = $this->_elite[$j];
			}

		}

		foreach($eliteToPush as $eliteElement){
			$keyToPut = rand(0, count($population)-1);
			if(count($securedKey)) {
				while(array_search($keyToPut, $securedKey)){
					$keyToPut = rand(0, count($population)-1);
				}
			}
			$securedKey[] = $keyToPut;
			$population[ $keyToPut ] = $eliteElement;
		}
		$this->setPopulation($population);

		return $population;
	}




}
