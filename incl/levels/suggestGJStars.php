<?php
//error_reporting(0);
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
require_once "../lib/mainLib.php";
require_once "../lib/webhooks/webhook.php";
$gs = new mainLib();
$uname = $gs->getAccountName($accountID);
$queryNAME = $db->prepare("SELECT levelName, userName FROM levels WHERE levelID = :id");
$queryNAME->execute([':id' => $levelID]);
$res = $queryNAME->fetchAll();
$aLevelName = $res[0]["levelName"];
$aUserName = $res[0]["userName"];

$gjp = ExploitPatch::remove($_POST["gjp"]);
$stars = ExploitPatch::remove($_POST["stars"]);
$feature = ExploitPatch::remove($_POST["feature"]);
$levelID = ExploitPatch::remove($_POST["levelID"]);
$accountID = GJPCheck::getAccountIDOrDie();
$difficulty = $gs->getDiffFromStars($stars);

if($gs->checkPermission($accountID, "actionRateStars")){
	$gs->rateLevel($accountID, $levelID, $stars, $difficulty["diff"], $difficulty["auto"], $difficulty["demon"]);
	$gs->featureLevel($accountID, $levelID, $feature);
	$gs->verifyCoinsLevel($accountID, $levelID, 1);
        if ($feature) {
	$featurestr = "Yes";
	} else {
	$featurestr = "No";
	}
        if ($difficulty == 10) {
        $diff2 = "Easy";
	} elseif ($difficulty == 20) {
        $diff2 = "Normal";
	} elseif ($difficulty == 30) {
        $diff2 = "Hard";
	} elseif ($difficulty == 40) {
        $diff2 = "Harder";
	} elseif ($difficulty == 50) {
        $diff2 = "Insane";
	} elseif ($difficulty == 50) and ($stars == 1)  {
        $diff2 = "Auto";
	} elseif ($difficulty == 50) and ($stars >= 10)  {
        $diff2 = "Demon";
	}
        PostToHook("Command - Rate", "$uname rated $aLevelName by $aUserName ($levelID).\nStars: $stars\nDifficulty: $diff2\nFeatured: $featurestr\nCoins: Yes");
	echo 1;
}else if($gs->checkPermission($accountID, "actionSuggestRating")){
	$gs->suggestLevel($accountID, $levelID, $difficulty["diff"], $stars, $feature, $difficulty["auto"], $difficulty["demon"]);
	echo 1;
}else{
	echo -2;
}
?>
