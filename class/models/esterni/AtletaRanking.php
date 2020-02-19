<?php

if (!defined("_BASEDIR_"))
    exit();
include_model("Atleta");
include_esterni("AnnoSportivoFiam", "AffiliazioneFiam");

/**
 * @access public
 * @package models
 */
class AtletaRanking extends Atleta
{

    const CINTURA_NERA = 7;
    const TIPO_ATL_FIAM = 1;

    private $verificato = NULL;

    public function __construct($nome_tabella = "", $id = NULL)
    {
        parent::__construct($nome_tabella, "idtesserato", $id, $GLOBALS["connest"]);
    }

    public function carica($row = NULL)
    {
        if (!is_null($row))
        {
            parent::carica($row);
            return;
        }
        $conn = $GLOBALS["connest"];

        $conn->connetti();
        if (is_null($this->getChiave()))
            return;
        $id = $this->getChiave();
        $sql = "SELECT a.idtesserato, cognome, nome, sesso, data_nascita,  a.idsocieta "
                . " FROM tesserati a INNER JOIN tipi_tesserati t USING(idtesserato) "
                . " INNER JOIN pagamenti_correnti p USING(idtesserato,idtipo) WHERE a.idtesserato = '$id'";

        // print $sql. " <br> <br> ";
        $res = $conn->query($sql);
        if ($res)
        {
            $count_row = $res->num_rows;
            while ($row = $res->fetch_assoc())
            {
                if (!is_null($row))
                {
	      $idsoc = $row['idsocieta'];
	      $query = "SELECT nomebreve FROM societa WHERE idsocieta = $idsoc";
	      $res_soc = $conn->query($query);
	      $riga_soc = $res_soc->fetch_assoc();
	      $row['nomebreve'] = $riga_soc['nomebreve'];
	      
                    parent::carica($row);
                }
            }
        }
    }

    /**
     * @access public
     * @return string
     */
    public function getNome()
    {
        return $this->get("nome");
    }

    /**
     * @access public
     * @return string
     */
    public function getCognome()
    {
        return $this->get("cognome");
    }

    /**
     * @access public
     * @return int
     */
    public function getSesso()
    {
        return $this->get("sesso");
    }

    /**
     * @access public
     * @return DateTime
     */
    public function getDataNascita()
    {
        return $this->get("data_nascita");
    }
    
    public function getEta($data) {
            parent::getEta($data);
    }

    /**
     * @access public
     * @return int
     */
    public function getCintura()
    {
        return self::convertiCintura($this->get("idgrado"));
    }

    /**
     * @access public
     * @return int
     */
    public function getSocieta()
    {
        return $this->get("idsocieta");
    }
    
    public function getNomeSoc()
    {
            return $this->get("nomebreve");
    }

    public function isVerificato()
    {
        //TODO prendere giorno gara
        if ($this->verificato === NULL)
        {
            $conn = $GLOBALS["connest"];
            $conn->connetti();
            $anno = AnnoSportivoFiam::get();

            //verifica pagamento
            $id = $this->getChiave();
            $mr = $conn->select('pagamenti_correnti', "idtesserato='$id' AND YEAR(scadenza) >= $anno AND idtipo=" . self::TIPO_ATL_FIAM);
            if ($mr->fetch_row() === NULL)
            {
                $this->verificato = false;
            } else
            {
                //verifica assicurazione
                $mr = $conn->select('assicurazioni_correnti', "idtesserato='$id' AND YEAR(valido_da) <= $anno AND YEAR(valido_a) >= $anno");
                $this->verificato = ($mr->fetch_row() !== NULL);
            }
        }
        return $this->verificato;
    }

    public function getUrlDettagli()
    {
        
    }

}

?>