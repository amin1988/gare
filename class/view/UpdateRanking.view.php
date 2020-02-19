<?php
if (!defined("_BASEDIR_"))
        exit();

require_once("../../config.inc.php");

include_controller("admin/UpdateRanking");

class UpdateRankingView {

        private $ctrl;
        private $fin;

        public function __construct() {

                $this->ctrl = new UpdateRankingCtrl();

                $this->fin = $this->ctrl->getFinished();
        }

        public function stampa() {

                $lang = Lingua::getParole();

          
                print ' <h4> <strong>Gare caricate: </strong>  <select id="gare" name="gare" style="width: 252px;"> ';
                foreach ($this->ctrl->getRankingEventi() as $index => $gara_inserito) {
	     $id_gara = $gara_inserito['id_gara'];
	     $gara = new Gara($id_gara);
	     $ng_caricata = $gara->getNome();
	     echo "<option value=$index>$ng_caricata</option>";
                }

                print ' </select></h4>';
                
                print '<br>';

                if ($this->fin) {

	     echo "<h4>Risultati inseriti nel ranking</h4>";

	     echo "<a href=\"show.php\">Vai al Ranking</a>";
                } else {
	     ?>

	     <form enctype="multipart/form-data" method="POST">

	         <h4><?php echo $lang["selez_gara"]; ?><strong>Carica Gara</strong>: <select id="gare" name="gare" style="width: 252px;">

	                 <?php
	                 foreach ($this->ctrl->getGare() as $idg => $g) {

		      $ng = $g->getNome();

		      echo "<option value=$idg>$ng</option>";
	                 }
	                 ?>

	             </select></h4>



	         <h4>Stage o gara esterna: <input type="checkbox" name="gara_esterna_o_stage" id="stage" onclick="hideDoc()"></h4>


	         <div id="divDoc"><h4><?php echo $lang["send_file"]; ?>: <input name=<?php echo UpdateRankingCtrl::DOC; ?> type="file"></h4></div>

	         <input name="processa" type="submit" value="<?php echo $lang["carica"]; ?>">

	     </form>

	     <?php
                }//else
        }

}