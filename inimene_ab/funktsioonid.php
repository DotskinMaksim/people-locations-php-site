<?php if (isset($_GET['code'])) {die(highlight_file(__File__, 1)); }?>
<?php
require ('conf.php');
//tabeli Inimene täitmine
function lisaInimene($eesnimi, $perekonnanimi, $maakond_id){
    global $yhendus;
    $paring=$yhendus->prepare("
INSERT INTO inimene(eesnimi, perekonnanimi, maakond_id) VALUES(?,?,?)");
    $paring->bind_param("ssi", $eesnimi, $perekonnanimi, $maakond_id);
    $paring->execute();

}

//tabeli Maakond täitmine
function lisaMaakond($maakond_nimi){
    global $yhendus;
    $paring=$yhendus->prepare("
INSERT INTO maakond(maakond_nimi) VALUES(?)");
    $paring->bind_param("s", $maakond_nimi);
    $paring->execute();

}
// rippLoend tabelist maakonnad
function selectLoend($paring, $nimi,$selected){
    global $yhendus;
    $paring=$yhendus->prepare($paring);
    $paring->bind_result($id, $andmed);
    $paring->execute();
    $tulemus="<select name='$nimi'>";
    while($paring->fetch()){
        if ($andmed==$selected){
            $tulemus .="<option value='$id' selected>$andmed</option>";
        }
        else if ($andmed!=$selected){
            $tulemus .="<option value='$id'>$andmed</option>";
        }

    }
    $tulemus .="</select>";
    return $tulemus;
}

// inimeste näitamine tabelist
function inimesteKuvamine($sort="", $otsisona=""){
    global $yhendus;
    $sort_list = array("eesnimi", "perekonnanimi", "maakond_nimi");
    if(!in_array($sort, $sort_list)) {
        return "Seda tulpa ei saa sorteerida";
    }
    $paring = $yhendus->prepare("SELECT inimene.id, eesnimi, perekonnanimi, maakond.maakond_nimi
    FROM inimene, maakond 
    WHERE inimene.maakond_id = maakond.id
    AND (eesnimi LIKE '%$otsisona%' OR perekonnanimi LIKE '%$otsisona%' OR maakond_nimi LIKE '%$otsisona%')
    ORDER by $sort");
    $paring->bind_result($id, $eesnimi, $perekonnanimi, $maakond_nimi);
    $paring->execute();
    $andmed = array();
    while($paring->fetch()) {
        $inimene = new stdClass();
        $inimene->id = $id;
        $inimene->eesnimi = htmlspecialchars($eesnimi);
        $inimene->perekonnanimi = htmlspecialchars($perekonnanimi);
        $inimene->maakond_nimi = $maakond_nimi;
        array_push($andmed, $inimene);
    }
    return $andmed;
}
//inimeste andmete muutmine tabelis
function muudaInimene($inimene_id, $eesnimi, $perekonnanimi, $maakond_id){
    global $yhendus;
    $paring=$yhendus->prepare("UPDATE inimene SET 
                   eesnimi=?, perekonnanimi=?, maakond_id=?
                   WHERE inimene.id=?");
    $paring->bind_param("ssii", $eesnimi, $perekonnanimi, $maakond_id, $inimene_id);
    $paring->execute();
}



