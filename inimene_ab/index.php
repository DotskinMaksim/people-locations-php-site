<?php
if (isset($_GET['code'])) {
    die(highlight_file(__FILE__, 1));
}

require('conf.php');
require('funktsioonid.php');
global $yhendus;
$sort = "eesnimi";
$otsisona = "";

if (isset($_REQUEST["sort"])) {
    $sort = $_REQUEST["sort"];
}
if (isset($_REQUEST["otsi_nupp"])) {
    $otsisona = $_REQUEST["otsisona"];
}
if (isset($_REQUEST["inimene_lisamine"])) {
    // ei luba tühja väli ja tühiku sisestamine
    if (!empty(trim($_REQUEST["eesnimi"])) && !empty(trim($_REQUEST["perenimi"]))) {
        lisaInimene($_REQUEST["eesnimi"], $_REQUEST["perenimi"], $_REQUEST["maakond_id"]);
    }
    header("Location: index.php");
    exit();
}

if (isset($_REQUEST["maakonna_lisamine"])) {
    if (!empty(trim($_REQUEST["maakonnanimi"]))) {
        lisaMaakond($_REQUEST["maakonnanimi"]);
    }
    header("Location: index.php");
    exit();
}

if (isset($_REQUEST["salvesta"])) {
    muudaInimene($_REQUEST["muuda_id"],$_REQUEST["eesnimi"], $_REQUEST["perekonnanimi"], $_REQUEST["maakond_id"]);

}
if (isset($_REQUEST["kustuta"])) {
    $paring = $yhendus->prepare("DELETE FROM inimene WHERE id=?");
    $paring->bind_param("i", $_REQUEST["kustuta"]);
    $paring->execute();
    header("Location: index.php");
}



$inimesed = inimesteKuvamine($sort, $otsisona);




?>

<!DOCTYPE html>
<html lang="et">

<head>
    <meta charset="UTF-8">
    <title>Inimesed ja maakonnad</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
</head>

<body>
<h1>Inimesed ja maakonnad</h1>

<form action="index.php" method="GET">
    <input type="text" name="otsisona" placeholder="Otsi..." >
    <input type="submit" value="Otsi" name="otsi_nupp">
</form>

<table border="1" id="tabel">
    <tr>
        <th>id</th>
        <th><a href="index.php?sort=eesnimi">Eesnimi</a></th>
        <th><a href="index.php?sort=perekonnanimi">Perenimi</a></th>
        <th><a href="index.php?sort=maakond_nimi">Maakond</a></th>
        <th colspan="2">Tegevused</th>
    </tr>
    <tr>
        <form action="index.php" method="POST">
            <td></td>
            <td>
                <input type="text" name="eesnimi" placeholder="eesnimi" class="inputText_in_table">
            </td>
            <td>
                <input type="text" name="perenimi" placeholder="perenimi" class="inputText_in_table">
            </td>
            <td>
                <?php
                echo selectLoend("Select id, maakond_nimi from maakond", "maakond_id",null);
                ?>
            </td>
            <td colspan="2">
                <input type="submit" value="Lisa inimene" name="inimene_lisamine" class="inputSubmit_in_table">
            </td>
        </form>
    </tr>

    <?php foreach ($inimesed as $inimene): ?>


        <?php if (isset($_REQUEST["muutmine"]) && $inimene->id == intval($_REQUEST["muutmine"])): ?>
            <tr>
                <form action="index.php" method="POST">
                    <td><input type="text" name="muuda_id" value="<?=$inimene->id?>" readonly></td>

                    <td><input type="text" name="eesnimi" value="<?= $inimene->eesnimi ?>" class="inputText_in_table"></td>
                    <td><input type="text" name="perekonnanimi" value="<?= $inimene->perekonnanimi ?>" class="inputText_in_table"></td>
                    <td>
                        <?php echo selectLoend("Select id, maakond_nimi from maakond", "maakond_id",$inimene->maakond_nimi); ?>
                    </td>
                    <td><input type="submit" name="salvesta" value="Salvesta" class="inputSubmit_in_table"></td>
                    <td><a href="index.php?" class="salvesta_href">Loobu</a></td>

                    
                </form>
            </tr>


        <?php else: ?>
        <tr>
            <td><?= $inimene->id ?></td>
            <td><?= $inimene->eesnimi ?></td>
            <td><?= $inimene->perekonnanimi ?></td>
            <td><?= $inimene->maakond_nimi ?></td>
            <td><a href="index.php?muutmine=<?= $inimene->id ?>"> Muuda</a></td>
            <td><a href="index.php?kustuta=<?= $inimene->id ?>"class="kustuta_href">Kustuta</a></td>

        </tr>
        <?php endif; ?>

    <?php endforeach; ?>
</table>

<br>

<form action="index.php" method="POST">
    <input type="text" name="maakonnanimi" placeholder="maakond">
    <input type="submit" name="maakonna_lisamine" value="lisa maakond">





</form>
</body>

</html>
