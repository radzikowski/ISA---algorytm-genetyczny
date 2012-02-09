<?php 

namespace ISA;

require_once __DIR__.'/lib/Algorithms.php';
require_once __DIR__.'/lib/Crossings.php';
require_once __DIR__.'/lib/Mutations.php';
require_once __DIR__.'/lib/Selections.php';
require_once __DIR__.'/lib/Elite.php';
require_once __DIR__.'/lib/Calculation.php';

class Laboratorium
{
	
	public function lab1($app){
		$a = $app['request']->get('a-value', -4);
		$b = $app['request']->get('b-value', 12);
		$precision = $app['request']->get('precision-value', 3);
		$populationSize = $app['request']->get('population-value', 10);
	    
		$alg = new lib\Algorithms($a, $b, $precision);
		$alg->calcPopulation($populationSize);
		$results = $alg->getPopulation();
		$resultsWithoutKey = $alg->getPopulationWithoutKey($results, array(
			'xreal',
			'xint',
			'xbin',
			'eval_func'
		));
		
		return array(
			'results' => $results,
			'resultsInJson' => json_encode($resultsWithoutKey),
			'a' => $a,
			'b' => $b,
			'precision' =>  $precision,
			'population' => $populationSize,
		);
	}

	public function lab2($app){
		$a = $app['request']->get('a-value', -4);
		$b = $app['request']->get('b-value', 12);
		$precision = $app['request']->get('precision-value', 3);
		$populationSize = $app['request']->get('population-value', 10);
	    $crossProbability = $app['request']->get('cross-probability-value', 0.5);
	    $mutationProbability = $app['request']->get('mutation-probability-value', 0.01);

		$alg = new lib\Algorithms($a, $b, $precision);
		$alg->calcPopulation($populationSize);
		$minMaxEvalFunc = $alg->findMinMaxFunc();
		$alg->calcMaxFitFunc($minMaxEvalFunc['min']);
		$alg->calcProbability(true);

		$results = $alg->getPopulation();
		$selection = new lib\Selections();
	 	$results = $selection->proportional($results);
	 	$cross = new lib\Crossings($crossProbability, $alg);
	 	$results = $cross->getCanCross($results);
	 	$results = $cross->onePoint($results);
	 	$mutation = new lib\Mutations($mutationProbability, $alg);
	 	$results = $mutation->uniform($results);	
		$resultsWithoutKey = $alg->getPopulationWithoutKey($results, array(
			'xreal',
			'eval_func',
			'fit_func',
			'probability',
			'distribution',
			'rand_selection',
			'x_after_selection',
			'parent_probability',
			'xbin_after_selection',
			'cross_point',
			'xbin_children',
			'x_after_cross',
			'mutation_bits',
			'xbin_after_mutation',
			'xreal_after_mutation',
			'eval_func_after_mutation'
		));
		
	 	return array(
			'results' => $results,
			'resultsInJson' => json_encode($resultsWithoutKey),
			'a' => $a,
			'b' => $b,
			'precision' =>  $precision,
			'population' => $populationSize,
			'cross_probability' => $crossProbability,
			'mutation_probability' => $mutationProbability,
		);
	}

	public function lab3($app){
		$a = $app['request']->get('a-value', -4);
		$b = $app['request']->get('b-value', 12);
		$precision = $app['request']->get('precision-value', 3);
		$populationSize = $app['request']->get('population-value', 20);
	    $crossProbability = $app['request']->get('cross-probability-value', 0.5);
	    $mutationProbability = $app['request']->get('mutation-probability-value', 0.01);
	    $amountPopulation = $app['request']->get('amount-population-value', 10);
		$amountElite = $app['request']->get('amount-elite-value', 1);

		$alg = new lib\Algorithms($a, $b, $precision);
		$selection = new lib\Selections();
	 	$cross = new lib\Crossings($crossProbability, $alg);
	 	$mutation = new lib\Mutations($mutationProbability, $alg);
	 	$calc = new lib\Calculation();
	 	$elite = new lib\Elite($amountElite);

		$populationArray = array();
		$results = array();
		for($i=0; $i<$amountPopulation; $i++){
			if ($i == 0) {
				$alg->calcPopulation($populationSize);
				$results = $alg->getPopulation();
			} else {
				$elite->setPopulation($results);
				$elite->sortByBest();
				$elite->calcAndSaveElite();

				$minMaxEvalFunc = $alg->findMinMaxFunc();
				$alg->calcMaxFitFunc($minMaxEvalFunc['min']);
				$alg->calcProbability(true);

				$results = $alg->getPopulation();
				$results = $selection->proportional($results);
				$results = $cross->getCanCross($results);
		 	 	$results = $cross->onePoint($results);
		 	 	$results = $mutation->uniform($results);	
			 	
		 	 	$newPopulation = $calc->getNewPopulationFrom($results, $alg);
		 	 	$newPopulation = $elite->putEliteToPopulation($newPopulation);
		 	 	$alg->setPopulation($newPopulation);
				$results = $newPopulation;	
			}			
			
			$elite->setPopulation($results);
			$elite->sortByBest();
			$elite->calcAndSaveElite();
			$results = $elite->putEliteToPopulation($results);

		 	$best = $elite->getBestOfPopulation();
		 	$worst = $elite->getWorstOfPopulation();
			$percentInPopulation = $calc->getPercentInPopulation($results, $best['xreal']);
		 	
		 	$populationArray[$i] = array(
		 		'i' => $i,
			 	'population' => $results,
			 	'best_xreal' => $best['xreal'],
			 	'best_eval_func' => $best['eval_func'],
			 	'best_percent_in_population' => $percentInPopulation,
			 	'worst_xreal' => $worst['xreal'],
			 	'worst_eval_func' => $worst['eval_func'],
			 	'avg_eval_func' => ($best['eval_func']+$worst['eval_func'])/2
			);
		}

		$resultsWithoutKey = $calc->getPopulationWithoutKey($populationArray, array(
			'i',
			'best_xreal',
			'best_eval_func',
			'best_percent_in_population',
			'population'
		));

		$resultsForChartWithoutKey = $calc->getPopulationWithoutKey($populationArray, array(
			'i',
			'worst_eval_func',
			'avg_eval_func',
			'best_eval_func'
		));

			$fp = fopen(__DIR__."/../tests/fileChar-".date("m.d.G.i.s").".csv", "w");

			if ($fp){
					fputcsv($fp, array('Paramerty: a:'.$a.' b:'.$b.' dokladnosc:'.$precision.' rozmiar populacji:'.$populationSize.' pk:'.$crossProbability.' pm:'.$mutationProbability.' liczba populacji(T): '.$amountPopulation.' elita:'.$amountElite));
					fputcsv($fp, array('lp', 'fmin', 'favg', 'fmax') , ';', '"');
				foreach ($resultsForChartWithoutKey as $fields) {
			    	fputcsv($fp, $fields, ';', '"');
				}
				fclose($fp);
			}

			$fp = fopen(__DIR__."/../tests/results-".date("m.d.G.i.s").".csv", "w");

			if ($fp){
					fputcsv($fp, array('Paramerty: a:'.$a.' b:'.$b.' dokladnosc:'.$precision.' rozmiar populacji:'.$populationSize.' pk:'.$crossProbability.' pm:'.$mutationProbability.' liczba populacji(T): '.$amountPopulation.' elita:'.$amountElite));
					fputcsv($fp, array('lp', 'fmin', 'favg', 'fmax') , ';', '"');
				foreach ($resultsForChartWithoutKey as $fields) {
			    	fputcsv($fp, $fields, ';', '"');
				}
				fclose($fp);
			}

	 	return array(
			'results' => $results,
			'resultsInJson' => json_encode($resultsWithoutKey),
			'resultsForChartInJson' => json_encode($resultsForChartWithoutKey),
			'a' => $a,
			'b' => $b,
			'precision' =>  $precision,
			'population' => $populationSize,
			'cross_probability' => $crossProbability,
			'mutation_probability' => $mutationProbability,
			'amount_population' => $amountPopulation,
			'amount_elite' => $amountElite
		);
	}

	public function lab5($app){
		$a = $app['request']->get('a-value', -4);
		$b = $app['request']->get('b-value', 12);
		$precision = $app['request']->get('precision-value', 3);
		$populationSize = $app['request']->get('population-value', 20);
			    
		$alg = new lib\Algorithms($a, $b, $precision);
		$alg->calcPopulation($populationSize);
		$results = $alg->getPopulation();

		$highClimpResults = array();		
		$fbest = $results[0]; // przypisanie pierwszego jako najlepszego do rozpatrywania
		foreach ($results as $key => $value) {
			$local = false;
				$localBest = $value;
				for ($i=0; $i < strlen($value['xbin']) && ($local == false) ; $i++){
					$localBest['id'] = 'x'.($key+1);
					$localBest['best'] = array(
						'xreal' => $fbest['xreal'],
						'eval_func' => $fbest['eval_func'],
					);
					$highClimpResults[] = $localBest;
					$xNext = array();
					$xNext['xbin'] = substr_replace($value['xbin'], (($value['xbin'][$i]) ? '0' : '1')  , $i, 1);
					$xNext['xreal'] = $alg->binToReal($xNext['xbin']);
					$xNext['eval_func'] = $alg->evaluateFunc( $xNext['xreal'] );
					if ($localBest['eval_func'] < $xNext['eval_func']){
						$localBest = $xNext;
					} else{
						$local = true;	
					}
				}
			if ($localBest['eval_func'] > $fbest['eval_func'])
				$fbest = $localBest;
		}

		return array(
			'a' => $a,
			'b' => $b,
			'precision' =>  $precision,
			'population' => $populationSize,
			'results' => $results,
			'highClimpResults' => $highClimpResults,
			'fbest' => $fbest,
		);
	}	

}



?>
