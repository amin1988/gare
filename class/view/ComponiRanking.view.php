<?phpif (!defined("_BASEDIR_"))    exit();require_once("../../config.inc.php");include_controller("admin/ComponiRanking");class ComponiRankingView{    private $ctrl;    private $fin;    public function __construct()    {        $this->ctrl = new ComponiRankingCtrl();    }    public function stampa()    {        $lang = Lingua::getParole();        ?>        <form enctype="multipart/form-data" method="POST">          <h4>Gara caricata: <select id="gare" name="gare" style="width: 252px;">              <?php              foreach ($this->ctrl->getEventi() as $idg => $g)              {                  $id_gara = $g['id_gara'];                  $ng = $this->ctrl->getNomeGara($id_gara);                  $nome_gara = $ng['nome'];                  echo "<option value=$id_gara>$nome_gara</option>";              }              ?>            </select>          </h4>          <h4>Tipo di evento: <select id="gare" name="tipo_evento" style="width: 252px;">                <?php                 echo "<option value=0></option>";              foreach ($this->ctrl->getCriterioPunteggioRanking() as $i_esimo => $evento)              {                  $nome_evento = $evento['tipologia_gara'];                  $id_evento = $evento['id'];                                   echo "<option value=$id_evento>$nome_evento</option>";              }              ?>                                        </select>            <br><br><br>                       Anno Gara <select  name="anno_gara" style="width: 80px;">                   <?php                             $anno_corrente = date('Y');                            $anno_precedente = date('Y') -1;                   ?>                                <option value="<?php echo $anno_corrente; ?>"selected><?php echo $anno_corrente; ?></option>                                  <option value="<?php echo $anno_precedente; ?>"><?php echo $anno_precedente; ?></option>                   </select>                      <br><br><br>            <input name="processa" type="submit" value="<?php echo $lang["aggiorna_tabele_gara"]; ?>">            </form>            <?php        }    }    