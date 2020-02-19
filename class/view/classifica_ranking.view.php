<?php
if (!defined("_BASEDIR_"))
        exit();

require_once("../../config.inc.php");

include_controller("admin/classifica_result_ranking");
include_esterni("AtletaRanking");

class ClassificaView {

        private $ctrl;
        private $fin;
        private $id_gara = 0;
        private $nome = null;
        private $cognome = null;
        private $sesso = null;
        private $id_kat_elezionato = 0;
        private $id_societa = 0;

        public function __construct() {
                if (isset($_GET['id'])) {
	     $this->id_gara = $_GET['id'];
                }
                $processa = empty($_POST['processa']) ? NULL : $_POST['processa'];
                if (!empty($processa)) {
	     $this->nome = empty($_POST['nome']) ? NULL : $_POST['nome'];
	     $this->cognome = empty($_POST['cognome']) ? NULL : $_POST['cognome'];
	     $this->sesso = empty($_POST['sesso']) ? NULL : $_POST['sesso'];
	     $this->id_kat_elezionato = empty($_POST['id_kat']) ? NULL : $_POST['id_kat'];
	     $this->id_societa = empty($_POST['id_soc']) ? NULL : $_POST['id_soc'];
                }
                $this->ctrl = new ClassificaResultCtrl();
        }

        public function stampa() {
                ?>
                <form enctype="multipart/form-data" method="POST">

                <?php
                $lang = Lingua::getParole();
                ?>
                    <h4>
                        <table>
                            <tr>
                                <td> <?php echo $lang["nome_iscrizioni"]; ?> <input  type="text" name="nome"  style="width: 150px;" id="nome" value="<?php
	 if (isset($this->nome))
	         echo $this->nome;
	 else
	         echo '';
	 ?>">    </td> <td></td><td></td><td></td>
                                <td> <?php echo $lang["cognome_iscrizioni"]; ?> <input  type="text" name="cognome"  style="width: 150px;" id="nome" value="<?php
		  if (isset($this->cognome))
		          echo $this->cognome;
		  else
		          echo '';
		  ?>"></td> <td></td><td></td><td></td>

		  <?php
		  $sel_maschio = "";
		  $sel_femmina = "";
		  if (!empty($this->sesso)) {
		          if ($this->sesso == 1) {
		                  $sel_maschio = " selected ";
		          } else if ($this->sesso == 2) {
		                  $sel_femmina = " selected ";
		          }
		  }
		  ?>
                                <td> Sesso <select  name="sesso" style="width: 80px;">
                	     <option value=""></option>  
                	     <option value="1" <?php echo $sel_maschio; ?>>M</option>  
                	     <option value="2" <?php echo $sel_femmina; ?> >F</option>  
                	 </select>
                                </td>
                            </tr>
                        </table> 
                    </h4>


                    <h4>
                        <table>
                            <tr>
                                <td>
                	 Categoria: 
                	 <select id="gare" name="id_kat" style="width: 252px;">
                <?php
                echo "<option value=0></option>";
                $array_kategorie = $this->ctrl->allCategorie();
                for ($i = 0; $i < count($array_kategorie); $i++) {
	     $kat = $array_kategorie[$i];
	     $nome_kat = $kat['nome'];
	     $id_kat = $kat['id_categoria'];
	     $selected = " ";
	     if ($this->id_kat_elezionato == $id_kat) {
	             $selected = " selected ";
	     }
	     echo "<option value=$id_kat  $selected>$nome_kat</option>";
	     $selected = "";
                }
                ?>
                	 </select>
                                </td>
                                <td></td><td></td><td></td><td></td>

                                <td>
                	 Societa: 
                	 <select id="gare" name="id_soc" style="width: 252px;">
		      <?php
		      echo "<option value=0></option>";
		      $array_societa = $this->ctrl->getAllSocieta();
		      for ($i = 0; $i < count($array_societa); $i++) {
		              $soc = $array_societa[$i];
		              $nome_kat = $soc['nomebreve'];
		              $id_soc = $soc['idsocieta'];
		              $selected = " ";
		              if ($this->id_societa == $id_soc) {
			   $selected = " selected ";
		              }
		              echo "<option value=$id_soc  $selected>$nome_kat</option>";
		              $selected = "";
		      }
		      ?>
                	 </select>
                                </td>
                                <td></td><td></td><td></td><td></td>
                                <td> Anno <select  name="anno" style="width: 80px;">
		      <?php
		      $anno_corrente = date('Y');
		      $anno_precedente = date('Y') - 1;
		      ?>
                	     <option value="<?php echo $anno_corrente; ?>"selected><?php echo $anno_corrente; ?></option>  
                	     <option value="<?php echo $anno_precedente; ?>"><?php echo $anno_precedente; ?></option>  
                	 </select>
                                </td>



                            <tr>
                        </table>
                    </h4>


                    <br>
                    <input class="button" name="processa" type="submit" value="<?php echo "Cerca"; ?>">

                    <br>

                <?php
                // print ' <form enctype="multipart/form-data" method="POST">';



                foreach ($this->ctrl->getClassifica() as $idc => $pools) {

	     if (empty($pools)) {
	             continue;
	     }
	     $classificato = 1;
	     $kat = new Categoria($idc);
	     $nome_categoria = $kat->getNome();

	     print '<table class="atleti" >';

	     echo '<tr class="tr"><th colspan="8"><div class="thSquadra">';
	     echo $nome_categoria;
	     print '</tr>';
	     print '<tr class="tr">';

	     print '<th colspan="2"><div class="thAtleti">' . $lang["cognome_iscrizioni"] . '/' . $lang["nome_iscrizioni"] . "</div></th>";
	     print '<th><div class="thAtleti" >' . ucfirst($lang["societa"]) . "</div></th>";
	     print '<th><div class="thAtleti" >' . $lang["sesso_iscrizioni"] . "</div></th>";
	     print '<th><div class="thAtleti">' . $lang["nascita_iscrizioni"] . "</div></th>";
	     //print '<th><div class="thAtleti">' . $lang["categoria"] . "</div></th>";
	     //print '<th><div class="thAtleti">' . $lang["classificato"] . "</div></th>";
	     print '<th><div class="thAtleti">' . "Punteggio" . "</div></th>";
	     for ($i = 0; $i < count($pools); $i++) {
	             $count = 0;
	             $row_classifica = $pools[$i];

	             if (($count % 2) == 0)
		  $classe = "riga1";
	             else
		  $classe = "riga2";

	             $id_tess = $row_classifica['id_atleta'];
	             $atleta = new AtletaRanking("tesserati", $id_tess);
	             $atleta->carica();
	             $cognome = $atleta->getCognome();
	             $nome = $atleta->getNome();
	             if (empty($nome))
		  continue;
	             if (empty($cognome))
		  continue;

	             $soc = new Societa($atleta->getSocieta());
	             $nome_societa = $atleta->getNomeSoc();
	             $sesso_numerico = $atleta->getSesso();


	             $sesso = ($sesso_numerico == 2) ? "F" : "M";
	             $data_nascita = $atleta->getDataNascita();
	             $data_nascita = date_format(date_create_from_format('Y-m-d', $data_nascita), 'd-m-Y');

	             $array_dati = array('id_atleta' => $id_tess, 'id_societa' => $atleta->getSocieta(), 'kategoria' => $idc);
	             $Json_array_dati = htmlspecialchars(json_encode($array_dati));



	             $ranking = new Ranking();
	             $punteggio = $row_classifica['punteggio_ranking'];

	             $json_popup_dati = serialize($row_classifica);


	             print '<tr class=' . "$classe" . '>';
	             // print '<td class="riepilogo_center">' . ($count + 1) . '</td>';
	             print '<td class="riepilogo_center">' . $cognome . " " . $nome . '</td>';
	             print '<td class="riepilogo_center"> </td>';
	             print '<td class="riepilogo_center">' . $nome_societa . '</td>';
	             print '<td class="riepilogo_center">' . $sesso . '</td>';
	             print '<td class="riepilogo_center">' . $data_nascita . '</td>';
	             //print '<td class="riepilogo_center">' . $nome_categoria . '</td>';

	             //print '<td class="riepilogo_center">' . $classificato . '</td>';
	             $riep_ranking = '<a href="" onclick="javascript:window.location.href=www.google.it">' . $punteggio . ' </a>';

	             $riep_ranking =" <a href="."javascript:void(window.open('ranking_popup_storico_gare.php?cognome=$cognome&nome=$nome&id_atleta=$id_tess&kategoria=".$idc."','mywindowtitle','width=500,height=250'))".">$punteggio</a>";
	             print '<td class="riepilogo_center">' . $riep_ranking . '</td>';
	         

	             //print '<td class="riepilogo_center">' . $punteggio . '</td>';

	              print ' <script>
function myFunction()
{
var videoElement = document.getElementById();

        window.open("ranking_popup_storico_gare.php", "child", "toolbar=no,scrollbars=no,resizable=yes,top=200,left=400,width=400,height=275,location=no, title=no");

}
</script>';

	             
	             print '<input type="hidden"  name="array_dati_eventi[]" value="' . $Json_array_dati . '">';
	             print '</tr>';
	             $count++;
	             $classificato++;
	             print '<br><br><br>';
	     }
	     print '</table>';
                }

                print '<br>';
                print '</form>';
        }

        public function stampaInizioCategoria($c, $pool) {
                $ancora = 'cat' . $c->getChiave();
                $nome = $c->getNome();
                if ($pool > 0) {
	     $ancora .= "p$pool";
	     $nome .= " - " . str_replace('<NUM>', $pool, Lingua::getParola("pool"));
                }
                echo "<a name=\"$ancora\"></a><h1>$nome</h1>\n";
        }

}