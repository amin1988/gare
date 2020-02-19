<?php
if (!defined("_BASEDIR_")) exit();

class Info {
	const BOOL = "bool";
	const TEXT = "text";
	const SELECT = "select";
	
	/**
	 * Nome del modulo
	 * @var string
	 */
	private $nome;
	/**
	 * Tipi dei parametri
	 * @var string[] formato: nome => tipo
	 */
	private $params = array();
	/**
	 * Etichette dei parametri
	 * @var string[][] formato: lingua => nome => label
	 */
	private $paramLbl;
	/**
	 * Valori di default dei parametri
	 * @var string[] formato: nome => valore
	 */
	private $paramDef;
	/**
	 * Valori possibili dei parametri
	 * @var array[][] formato: nome => valori[]
	 */
	private $paramVal;
	
	public function __construct($nome) {
		$this->nome = $nome;
	}
	
	public function addParam($nome, $default=false, $val=NULL) {
		$this->paramDef[$nome] = $default;
		if (is_array($val)) {
			$this->paramVal[$nome] = $val;
			$this->params[$nome] = self::SELECT;
		} else {
			unset($this->paramVal[$nome]);
			if (is_bool($default))
				$this->params[$nome] = self::BOOL;
			else
				$this->params[$nome] = self::TEXT;
		}
	}

	public function setLabels($lang, $lbl) {
		$this->paramLbl[$lang] = $lbl;
	}
	
	public function getNome() {
		if (is_array($this->nome))
			return $this->nome[Lingua::getLinguaDefault()];
		else
			return $this->nome;
	}

	/**
	 * @return string[]
	 */
	public function getParams() {
		return $this->params;
	}
	
	public function getLabel($param) {
		return $this->paramLbl[Lingua::getLinguaDefault()][$param];
	}

	public function getDefault($param) {
		return $this->paramDef[$param];
	}
	
	public function getValori($param) {
		if (isset($this->paramVal[$param]))
			return $this->paramVal[$param];
		else
			return NULL;
	}
	
	public function getLabelValore($param, $val) {
		return $this->paramLbl[Lingua::getLinguaDefault()]["$param:$val"];
	}
}