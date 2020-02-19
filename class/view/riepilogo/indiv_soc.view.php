<?php
if (!defined("_BASEDIR_"))
        exit();

class TabellaIndividuali {

        private $unita;
        private $col_peso;
        private $categorie;

        /**
         * @param Gara $gara
         */
        public function __construct($gara) {
                $this->categorie = $gara->getCategorieIndiv();
                if ($gara->usaPeso()) {
	     $this->unita = " Kg";
	     $this->col_peso = Lingua::getParola("peso_iscrizioni");
                } else {
	     $this->unita = " cm";
	     $this->col_peso = Lingua::getParola("altezza_iscrizioni");
                }
        }

        /**
         * @param Atleta[] $atleti
         * @param IscrittoIndividuale[][] $iscritti formato idatleta => IscrittoIndividuale[]
         */
        public function stampa($atleti, $iscritti) {
                if (count($iscritti) == 0)
	     return;
                $lang = Lingua::getParole();
                ?>
                <br>
                <div class="Gare_soc_right"><h1><?php echo Lingua::getParola("atleti"); ?></h1></div>
                <table width="100%" class="atleti" id="atleti" >
                    <tr  class="tr">

                        <th><div class='thAtleti'></div></th>
                        <th><div class='thAtleti'><?php echo $lang["cognome_iscrizioni"] . '/' . $lang["nome_iscrizioni"]; ?></div></th>
                        <th><div class="thAtleti" ><?php echo $lang["sesso_iscrizioni"]; ?></div></th>
                        <th><div class='thAtleti'><?php echo $lang["nascita_iscrizioni"]; ?></div></th>
                        <th><div class='thAtleti'><?php echo $lang["cintura_iscrizioni"]; ?></div></th>

                        <th><div class="thAtleti"><?php echo $lang["tipo_iscrizioni"]; ?></div></th>
                        <th><div class='thAtleti'><?php echo $lang["stile_iscrizioni"]; ?></div></th>
                        <th><div class='thAtleti '><?php echo $this->col_peso; ?></div></th>
                        <th colspan="2"><div class='thAtleti'><?php echo $lang["categoria"]; ?></div></th>
                    </tr>
	 <?php
	 $c = 0;
	 $poolstr = $lang["pool"];
	 foreach ($atleti as $a) {
	         /* @var $a Atleta */
	         $ida = $a->getChiave();
	         if (!isset($iscritti[$ida]))
	                 continue;
	         foreach ($iscritti[$ida] as $i) {
	                 /* @var $i IscrittoIndividuale */
	                 if (($c % 2) == 0)
		      $classe = "riga1";
	                 else
		      $classe = "riga2";
	                 ?>
	                 <tr class="<?php echo $classe; ?>">
	                     <td class="riepilogo_center"><?php echo ($c + 1); ?></td>
	                     <td class="riepilogo_center"><?php echo $a->getCognome() . " " . $a->getNome(); ?></td>
	                     <td class='riepilogo_center'><?php echo $this->getNomeSesso($a); ?></td>
	                     <td class='riepilogo_center'><?php echo $a->getDataNascita()->format("d/m/Y"); ?></td>
	                     <td class='riepilogo_center'><?php echo $this->getNomeCintura($i); ?></td>
	                     <td class='riepilogo_center'><?php echo $this->getNomeTipoGara($i); ?></td>
	                     <td class='riepilogo_center'><?php echo $this->getStile($i); ?></td>
	                     <td class='riepilogo_center'><?php echo $this->getPeso($i); ?></td>
	                     <td class='riepilogo_center'>
		      <?php
		      echo $this->getNomeCategoria($i->getCategoriaFinale());
		      if ($i->isSeparato())
		              echo " - " . str_replace("<NUM>", $i->getPool(), $poolstr);
		      ?></td>
	                     <td class='riepilogo_center'><?php
		      if ($i->isAccorpato()) {
		              $nomeorig = $this->getNomeCategoria($i->getCategoria());
		              echo '<img style="cursor:pointer;" src="';
		              echo _PATH_ROOT_ . "img/spostato.png\" title=\"$nomeorig\" onclick=\"javascript:mostraCatOriginale('$nomeorig')\">";
		      } else if ($i->isSeparato()) {
		              echo '<img src="' . _PATH_ROOT_ . 'img/separa.png" >';
		      }
		      ?></td>

	                 </tr>	
	                 <?php
	                 $c++;
	         } //foreach iscritto
	 } //foreach atleta

	 echo '</table>';
            }

//function stampa

            /**
             * @param Atleta $a
             * @return string
             */
            private function getNomeSesso($a) {
	 return Sesso::toStringBreve($a->getSesso());
            }

            /**
             * @param IscrittoIndividuale $i
             * @return string
             */
            private function getNomeTipoGara($i) {
	 //TODO generalizzare
	 switch ($i->getTipoGara()) {

	         case 0:

	                 return "Kata";

	         case 1:

	                 return "Shobu Sanbon";

	         case 2:

	                 return "Shobu Ippon";
	         case 3:

	                 return "Kata Rengokai";

	         case 4:

	                 return "Shobu Kumite";
	 }
            }

            /**
             * @param IscrittoIndividuale $i
             * @return string
             */
            private function getNomeCintura($i) {
	 return Cintura::getCintura($i->getCintura())->getNome();
            }

            /**
             * @param IscrittoIndividuale $i
             * @return string
             */
            private function getStile($i) {
	 if (is_null($i->getStile()))
	         return "";
	 else
	         return Stile::getStile($i->getStile())->getNome();
            }

            /**
             * @param IscrittoIndividuale $i
             * @return string
             */
            private function getPeso($i) {
	 if (is_null($i->getPeso()))
	         return "";
	 else
	         return $i->getPeso() . $this->unita;
            }

            private function getNomeCategoria($idcat) {
	 return $this->categorie[$idcat]->getNome();
            }

    }
    ?>