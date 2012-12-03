<?php
include("marchmadness.php");

if($_GET['studentid']) {
	$_GET['id'] = substr(md5($_GET['studentid']), 0, 16);
}

if(!$_GET['id']) {
	header("Location: ./");
	die();
}
if(!file_exists("saves/".$_GET['id'].".txt")) {
	header("Location: ./?notfound=true");
	die();
}

$data = unserialize(file_get_contents("saves/".$_GET['id'].".txt"));

include("header.php");
?>
		<h2>View Your Results</h2>
		
		<p><a href="./">Back to homepage</a></p>
		
		<?php
		if($_GET['saved'])
			echo "<p>Thanks for submitting your entry into our March Madness contest! This page will be automatically updated with your progress, so be sure to bookmark it! ".
				"If you end up winning something, we'll contact you using the email/phone number that you have provided.</p>";
		?>
		
		<p>Submissions cannot be altered once submitted, you may re-submit and overwrite your previous submission while submissions are still open.</p>
		
		<p>Your predictions are shown below. The games that you predicted correctly are shown <span style="color: green">green</span> , while teams in <span style="color: red">red</span> 
			were not. Team names in	<span style="color: gray">gray</span> means that the game hasn't been played yet.</p>
	</div>
	
	<div id="bracket_wrapper">
		<div id="bracket">
<?php
$points = 0;
function printMatch($id) {
	global $data, $correct, $points;
	
	if(!$correct[$id] || $correct[$id] == "false") {
		if(!$data["match"][$id] || $data["match"][$id] == "false")
			return "<span style='color: red'>N/A</span>";
		return "<span style='color: gray'>{$data["match"][$id]}</span>";
	}
	
	if(!$data["match"][$id] || $data["match"][$id] == "false")
		return "<span style='color: red'>N/A</span>";
	
	if($data["match"][$id] == $correct[$id]) {
		if($id < 32)
			$points += 1;
		else if($id < 48)
			$points += 2;
		else if($id < 56)
			$points += 4;
		else if($id < 60)
			$points += 8;
		else if($id < 62)
			$points += 16;
		else
			$points += 32;
		return "<span style='color: green'>{$data["match"][$id]}</span>";
	}
	else
		return "<span style='color: red'>{$data["match"][$id]}</span>";
}

//First round - 64 teams
for($i = 0; $i < 32; $i++) {
	echo "<div id='roster_$i' class='roster' style='position: absolute; left: 20px; top: ".round(14+20.57*$i)."px;'>".$roster[$i]."</div>\n";
}

for($i = 32; $i < 64; $i++) {
	echo "<div id='roster_$i' class='roster' style='position: absolute; right: 20px; text-align: right; top: ".round(14+20.57*($i-32))."px;'>".$roster[$i]."</div>\n";
}

//Second round - 32 teams remain
for($i = 0; $i < 16; $i++) {
	echo "<div id='match_$i' class='match' style='position: absolute; left: 100px; top: ".round(24+41.12*$i)."px;'>".printMatch($i)."</div>\n";
}
for($i = 16; $i < 32; $i++) {
	echo "<div id='match_$i' class='match' style='position: absolute; right: 100px; text-align: right; top: ".round(24+41.12*($i-16))."px;'>".printMatch($i)."</div>\n";
}

//Third round ("sweet 16") - 16 teams remain
for($i = 32; $i < 40; $i++) {
	echo "<div id='match_$i' class='match' style='position: absolute; left: 175px; top: ".round(45+82.24*($i-32))."px;'>".printMatch($i)."</div>\n";
}
for($i = 40; $i < 48; $i++) {
	echo "<div id='match_$i' class='match' style='position: absolute; right: 175px; text-align: right; top: ".round(45+82.24*($i-40))."px;'>".printMatch($i)."</div>\n";
}

//Fourth round ("elite 8") - 8 teams remain
for($i = 48; $i < 52; $i++) {
	echo "<div id='match_$i' class='match' style='position: absolute; left: 250px; top: ".round(86+164.48*($i-48))."px;'>".printMatch($i)."</div>\n";
}
for($i = 52; $i < 56; $i++) {
	echo "<div id='match_$i' class='match' style='position: absolute; right: 250px; text-align: right; top: ".round(86+164.48*($i-52))."px;'>".printMatch($i)."</div>\n";
}

//Fifth round ("final 4") - 4 teams remain
for($i = 56; $i < 58; $i++) {
	echo "<div id='match_$i' class='match' style='position: absolute; left: 325px; top: ".round(168+328.96*($i-56))."px;'>".printMatch($i)."</div>\n";
}
for($i = 58; $i < 60; $i++) {
	echo "<div id='match_$i' class='match' style='position: absolute; right: 325px; text-align: right; top: ".round(168+328.96*($i-58))."px;'>".printMatch($i)."</div>\n";
}

//Final Match - 2 teams remain
echo "<div id='match_60' class='match' style='position: absolute; left: 405px; top: 241px;'>".printMatch(60)."</div>\n";
echo "<div id='match_61' class='match' style='position: absolute; right: 405px; top: 426px; text-align: right;'>".printMatch(61)."</div>\n";

//Champion - 1 team remains
echo "<div id='match_62' class='match' style='position: absolute; right: 487px; top: 357px; text-align: center;'>".printMatch(62)."</div>\n";
?>
		</div>
	</div>
	<p>Your Final Game Score Prediction: <?php echo $data['final1']." - ".$data['final2']  ?></p>
	<p><b>Total Points: <?php echo getPoints($_GET['id']) ?></b></p>
<?php
include("footer.php");
