<?php
if (!defined("_BASEDIR_")) exit();

/*

Kata:
stili
eta (solo se etamin = etamax-1)
sesso
no cinture

Sanbon:
cinture
peso
eta
no sesso

Ippon:
eta
cinture
no sesso


 */

class Accorpamenti {
	/**
	 * @param Categoria $cat
	 * @param Categoria[] $lista
	 * @param boolean $forza
	 */
	public static function ordina($cat, $lista, $forza) {
		if($forza)
		{
			unset($lista[$cat->getChiave()]);
			return $lista;
		}
		$acc = array();
		switch ($cat->getTipo()) {
			case 0:
				$val = self::valoriKata();
				break;
			case 1:
				$val = self::valoriSanbon();
				break;
			case 2:
				$val = self::valoriIppon();
				break;
		}
		$id = $cat->getChiave();
		foreach ($lista as $c) {
			/* @var $c Categoria */
			if ($c->getChiave() == $id) continue;
			$car = self::caratt($cat, $c);
			//non valida
			if (is_null($car)) continue;
			$i=0;
			//calcola il valore di differenza
			foreach ($car as $el => $ev) {
				if ($ev != 0) {
					if (isset($val[$el]))
						$i += $val[$el];
					else {
						//non valida
						$i = -1000;
						break;
					}
				}
			}
			if ($i>=0) $acc[$i][] = $c;
		}
		
		ksort($acc);
		$ret = array();
		$i=0;
		foreach ($acc as $cl) {
			foreach ($cl as $c) {
				$ret[$i] = $c;
				$i++;
			}
		}
		
		
		/*//DEBUG
		$debug=array();
		foreach ($acc as $id=>$v) {
			foreach ($v as $id2 =>$c) {
				$debug[$id][$id2] = $c->getNome();
			}
		}
		echo "<pre>".$cat->getNome();
		print_r($debug);
		echo "</pre>";
		//DEBUG*/
		
		return $ret;
	}
	
	/**
	 * Restituisce le caratteristiche comuni alle categorie,
	 * o NULL se le categorie non sono accorpabili
	 * @param Categoria $c1
	 * @param Categoria $c2
	 */
	private static function caratt($c1, $c2) {
		$tipo = $c1->getTipo();
		if ($c2->getTipo() != $tipo) return NULL;
		if ($c1->isHandicap() != $c2->isHandicap())
			return NULL;
		
		//eta
		$car['eta'] = self::pesoIntervallo($c1->getEtaMin(), $c2->getEtaMin(),
				 $c1->getEtaMax(), $c2->getEtaMax());
		if ($car['eta'] == -1) return NULL;
		
		//peso
		if ($tipo == 1) {
			$car['peso'] = self::pesoIntervallo($c1->getPesoMin(), $c2->getPesoMin(),
					 $c1->getPesoMax(), $c2->getPesoMax());
			if ($car['peso'] == -1) return NULL;
		} else $car['peso'] = 0;
		
		//sesso
		if (($c1->getSesso() & $c2->getSesso()) != 0)
			$car['sesso'] = 0;
		else
			$car['sesso'] = 1;
		
		//cinture
		$car['cinture'] = self::pesoInsieme($c1->getCinture(), $c2->getCinture());
		
		//stile
		if ($tipo == 0)
			$car['stili'] = self::pesoInsieme($c1->getStili(), $c2->getStili());
		else $car['stili'] = 0;
		
		return $car;
	}
	
	private static function pesoIntervallo($ep1, $ep2, $eg1, $eg2) {
		if ($ep1 == $ep2 && $eg1 == $eg2)
			return 0;
		if ($ep1 > $eg2+1) return -1;
		if ($ep2 > $eg1+1) return -1;
		return 1;
	}
	
	private static function pesoInsieme($i1, $i2) {
		$inter = count(array_intersect($i1, $i2));
		if ($inter == 0) 
			return -1;
		//intersezione == i1 == i2
		else if ($inter == count($i1) && $inter == count($i2))
			 return 0;
		else 
			return 1;
	}
	
	private static function valoriKata() {
		return array(
				'stili' => 1,
				'eta' => 2
		);
	}
	
	private static function valoriSanbon() {
		return array(
			'cinture' => 1,
			'peso' => 2,
			'eta' => 4
		);
	}
	
	private static function valoriIppon() {
		return array(
			'eta' => 1,
			'cinture' => 2
		);
	}
}
?>