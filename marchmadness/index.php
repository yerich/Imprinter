<?php
/**
 * Disclaimer: This project was done on a tight timeline and as a consequence contains some of the worst code that
 * I have ever written. If you're trying to read my code, I apologize in advance.
 */

include("marchmadness.php");

if($_POST && $contest_open) {	//Save a submission
	$data = $_POST;
	$data["time"] = time();	//Add time to submission
	$data["id"] = intval($data['studentid']);	//Convert ID to integer value
	$data["final1"] = intval($data["final1"]);
	$data["final2"] = intval($data["final2"]);
	$error = false;
	if($data["id"] > 99999999 || $data["id"] < 10000000) {	//Validate the ID. Everything else is done via JavaScript and doesn't actually matter
		$error = "You have entered an invalid student ID.";
	}
	
	$data["studentid"] = $data["id"];	//Save original integer ID value
	$data["id"] = substr(md5($data['id']), 0, 16);	//Hash the ID so it's unrecoverable (privacy)
	$file = "saves/".$data["id"].".txt";
	
	if(file_exists($file)) {	//Move old file to the old folder if it exists
		rename($file, "oldsaves/".(time() - 1)."_".$data["id"].".txt");
		chmod("oldsaves/".(time() - 1)."_".$data["id"].".txt", 0777);
	}
	
	if(!$error) {	//Save the file if there are no errors
		$fh = fopen($file, "w");
		$fstr = serialize($data);
		if(strlen($fstr) > 10000)	//Security Feature
			die("Possible Overflow Attack");
		fwrite($fh, $fstr, 10000);
		
		chmod($file, 0777);
		header("Location: view.php?saved=true&id={$data['id']}");
		die();
	}
}

include("header.php");

if($contest_open) {	//Contest is open
?>

	<script type="text/javascript">
		function get_selected(id) {	//Get the string value of the team which is selected for a given match id
			var html = $("#match_"+id).html();
			if(html == "--")
				return null;
			val = $("#match_"+id+" select").val();
			if(val && val != "false") return val;
			return null;
		}
	
		function redraw() {	//Redraws the roster and match list
			$(".match").each(function(e) {
				var id = Number($(this).attr('id').substring(6));
				var newhtml = "--";
				var option1 = null;
				var option2 = null;
				
				if(id < 32) {
					option1 = $("#roster_"+(2*id)).html();
					option2 = $("#roster_"+(2*id + 1)).html();
				}
				else if(id < 48) {
					option1 = get_selected((id-32) * 2);
					option2 = get_selected((id-32) * 2 + 1);
				}
				else if(id < 56) {
					option1 = get_selected(32 + ((id-48) * 2));
					option2 = get_selected(32 + ((id-48) * 2) + 1);
				}
				else if(id < 60) {
					option1 = get_selected(48 + ((id-56) * 2));
					option2 = get_selected(48 + ((id-56) * 2) + 1);
				}
				else if(id < 62) {
					option1 = get_selected(56 + ((id-60) * 2));
					option2 = get_selected(56 + ((id-60) * 2) + 1);
				}
				else if(id == 62) {
					option1 = get_selected(60);
					option2 = get_selected(61);
				}
				
				if(option1 != false && option1 && option2 && option1 != "--" && option2 != "--") {	//If there are two valid options, generate select box
					newhtml = "<select name='match["+id+"]'><option value='false'>--</option><option value='"+option1+"'>"+option1+
					"</option><option value='"+option2+"'>"+option2+"</option></select>";
				}
				else
					newhtml = "--";
				
				
				var curroptions = [];	//Select box drop-down options
				$('option', this).each(function() {
					curroptions.push($(this).html());
				});
				if(option1 == false) {
					option1 = null;
				}
				
				if(option1 == false || newhtml == "--" || (curroptions[1] != option1 || curroptions[2] != option2)) {
					$(this).html(newhtml);
				}
				
				$("#bracket_wrapper select").unbind("change");
				$("#bracket_wrapper select").change(function() {
					redraw();
				});
			});
		}
		
		$(document).ready(function() {
			redraw();
			$("#bracket_wrapper select").change(function() {
				redraw();
			});
		})
	</script>
		<p>
			Imprint Publications is proud to present our 3rd Annual March Madness contest! The contest is free to enter for UW Undergraduate students, and the winner will recieve a $50 gift card from Retail Services. If you have a love for basketball, this is the contest for you!
		</p>
			<p>
			Entries will be open until the 17th of March (before the start of the third round). To enter, fill out the form below with the choices for your bracket. You may enter more than once, but only the most recent submission will be counted.
			</p>
		
		<h2>Enter the Contest</h2>
		<form id="marchmadness" action="./" method="post" />
		<div id="bracket_wrapper">
			<div id="bracket">
<?php
//Print out the rosters and the matches
//First round - 64 teams
for($i = 0; $i < 32; $i++) {
	echo "<div id='roster_$i' class='roster' style='position: absolute; left: 20px; top: ".round(14+20.57*$i)."px;'>".$roster[$i]."</div>\n";
}

for($i = 32; $i < 64; $i++) {
	echo "<div id='roster_$i' class='roster' style='position: absolute; right: 20px; text-align: right; top: ".round(14+20.57*($i-32))."px;'>".$roster[$i]."</div>\n";
}

//Second round - 32 teams remain
for($i = 0; $i < 16; $i++) {
	echo "<div id='match_$i' class='match' style='position: absolute; left: 100px; top: ".round(24+41.12*$i)."px;'>Match $i</div>\n";
}
for($i = 16; $i < 32; $i++) {
	echo "<div id='match_$i' class='match' style='position: absolute; right: 100px; text-align: right; top: ".round(24+41.12*($i-16))."px;'>Match $i</div>\n";
}

//Third round ("sweet 16") - 16 teams remain
for($i = 32; $i < 40; $i++) {
	echo "<div id='match_$i' class='match' style='position: absolute; left: 175px; top: ".round(45+82.24*($i-32))."px;'>Match $i</div>\n";
}
for($i = 40; $i < 48; $i++) {
	echo "<div id='match_$i' class='match' style='position: absolute; right: 175px; text-align: right; top: ".round(45+82.24*($i-40))."px;'>Match $i</div>\n";
}

//Fourth round ("elite 8") - 8 teams remain
for($i = 48; $i < 52; $i++) {
	echo "<div id='match_$i' class='match' style='position: absolute; left: 250px; top: ".round(86+164.48*($i-48))."px;'>Match $i</div>\n";
}
for($i = 52; $i < 56; $i++) {
	echo "<div id='match_$i' class='match' style='position: absolute; right: 250px; text-align: right; top: ".round(86+164.48*($i-52))."px;'>Match $i</div>\n";
}

//Fifth round ("final 4") - 4 teams remain
for($i = 56; $i < 58; $i++) {
	echo "<div id='match_$i' class='match' style='position: absolute; left: 325px; top: ".round(168+328.96*($i-56))."px;'>Match $i</div>\n";
}
for($i = 58; $i < 60; $i++) {
	echo "<div id='match_$i' class='match' style='position: absolute; right: 325px; text-align: right; top: ".round(168+328.96*($i-58))."px;'>Match $i</div>\n";
}

//Final Match - 2 teams remain
echo "<div id='match_60' class='match' style='position: absolute; left: 405px; top: 241px;'>Match 60</div>\n";
echo "<div id='match_61' class='match' style='position: absolute; right: 405px; top: 426px; text-align: right;'>Match 61</div>\n";

//Champion - 1 team remains
echo "<div id='match_62' class='match' style='position: absolute; right: 487px; top: 357px; text-align: center;'>Match 62</div>\n";
?>
	</div>
		<div id="bracket_final">Final Game Score: <br />
<input type="text" size="4" name="final1" /> - <input type="text" size="4" name="final2" /></div>
</div>
<br />

<p><b>Contact Information</b> (Will not be shared)</p>

<table>
	<tr>
		<td style="width: 300px;">Name</td>
		<td><input type="text" size="40" name="name" id="form_name" /></td>
	</tr>
	<tr>
		<td>Email Address</td>
		<td><input type="text" size="40" name="email" id="form_email" /></td>
	</tr>
	<tr>
		<td>UW Student ID (for verification)</td>
		<td><input type="text" size="40" name="studentid" id="studentid" /></td>
	</tr>
	<tr>
		<td>Phone Number (optional)</td>
		<td><input type="text" size="40" name="phone" /></td>
	</tr>
</table>

		<noscript>Error: Javascript is required to make a submission.</noscript>
		
		<input type="button" value="Submit Entry" id="submit_button" /><br /><br />
		
		<span style="font-size: 10pt">Contest Rules:
			One submission per UW ID, limit one submission per person. If multiple submissions are recieved by the same WatCard or person, then the most recent will be used.
			Winners of prizes will be required to present proof of eligibility before being eligile to recieve their prize. <em>Imprint</em> reserves the right to decline to
			give out prizes if cheating, hacking or otherwise unfair play is suspected. <a href='javascript:void(0)' onclick="$('#full_rules').slideToggle()">View Full Rules</a></span>
		
		<span style="font-size: 10pt; display: none; color: #999;" id="full_rules">
<p>Official Rules for Imprint Publication's March Madness Bracket Contest</p>
<p>THE FOLLOWING CONTEST IS INTENDED FOR PLAY BY UNDERGRADUATE STUDENTS OF THE UNIVERSITY OF WATERLOO (COLLECTIVELY, THE "ELIGIBLE PARTICIPANTS"). DO NOT ENTER THIS CONTEST IF YOU ARE NOT A UNDERGRADUATE STUDENT OF THE UNIVERSITY OF WATERLOO.</p>
<p>NO PURCHASE IS NECESSARY TO ENTER OR WIN. A PURCHASE DOES NOT IMPROVE YOUR CHANCES OF WINNING.</p>
<p>ENTRY IN THIS CONTEST CONSTITUTES YOUR ACCEPTANCE OF THESE OFFICIAL RULES.</p>
<p>1.	About the Contest: The Imprint Publication's March Madness Bracket Contest (hereby referred to as "Contest") is a free game being offered in connection with NCAA Division 1 Men's College Basketball Tournament. Participants are invited to create a free tournament bracket in which they select the winners of the 63 games (excluding the "play-in games") comprising the 2012 NCAA Division I Men's College Basketball Tournament (collectively, the "2012 Tournament"). In this Contest, the eligible participants who score the highest bracket in the Contest will win prizes.</p>
<p>2.	Eligibility: To participate in the Contest, you must be a University of Waterloo undergraduate student.</p>
<p>3.	How to Enter: To enter the Contest, you must create and submit your tournament bracket either through the Imprint website (located at www.imprint.uwaterloo.ca) or submit your bracket on the provided official bracket sheet from an Imprint newspaper issue. To participate in the Contest you must have a valid undergraduate student ID number. For each Game Bracket, you will also be asked to guess what the final score of the championship game of the 2012 Tournament (the "Championship Game") will be (the "Tiebreaker Question"). The Tiebreaker Question will be used in the event there is a tie between two or more players at the conclusion of the Contest; refer to Rule 6 below for more information. You may submit only one bracket in the Contest. You have until just prior to the published tip-off time of the first 2012 Tournament game on Thursday, March 17, 2012 (the "Entry Deadline") to complete and/or make revisions to your Game Bracket(s). The exact Entry Deadline will be posted on the Contest Website when it is officially announced. The period between 12:01 AM EST on March 15, 2012 and the Entry Deadline on Thursday, March 20, 2012 is referred to in these Official Rules as the "Registration Period." All Entry Information, Game Bracket and answer to the Tiebreaker Question must be received by the close of the Registration Period. A user who has submitted the foregoing materials in keeping with the deadlines described in these Official Rules, and who is otherwise in full compliance with the eligibility restrictions and all other provisions of these Official Rules, is referred to as an "Entrant".</p> 
<p>LIMIT OF ONLY ONE (1) GAME BRACKET PER PERSON. Although subsequent attempts to enter may be received, only the last Game Bracket received from a particular individual will count; prior attempts to enter will be disqualified. Any attempt by any Entrant to submit more than the permitted number of Game Brackets and/or entries will void all of that Entrant's entries. The submission of an entry is solely the responsibility of the Entrant. Entries may only be made according to the method described above. Automated entries (including but not limited to entries submitted using any bot, script, macro, or Contest submission service), copies, third party entries, facsimiles and/or mechanical reproductions are not permitted and will be disqualified. Only eligible entries actually received by Contest Entities before the end of the specified entry period will be considered for a prize in this Contest. Unintelligible, incomplete or garbled entries will be disqualified. All entries become the property of Imprint, and none will be acknowledged or returned.</p>
<p>4.	Conduct: By entering the Contest, Entrants agree to comply with and be bound by these Official Rules. The Official Rules will be posted at the Contest Website throughout the Contest. Failure to comply with these Official Rules may result in disqualification from the Contest. Entrants further agree to comply with and be bound by the decisions of the judges, which will be final and binding in all respects. Imprint reserves the right at its sole discretion to disqualify any individual. CAUTION: ANY ATTEMPT BY AN ENTRANT OR ANY OTHER INDIVIDUAL TO DELIBERATELY DAMAGE ANY WEBSITE OR UNDERMINE THE LEGITIMATE OPERATION OF THE CONTEST MAY BE A VIOLATION OF CRIMINAL AND CIVIL LAWS. SHOULD SUCH AN ATTEMPT BE MADE, IMPRINT RESERVES THE RIGHT TO SEEK DAMAGES FROM ANY SUCH PERSON TO THE FULLEST EXTENT PERMITTED BY LAW.</p>
<p>5.	Prizes: Sixty-three games are scheduled to be played in the 2012 Tournament (the "play-in" game is not counted for purposes of the Contest). Prizes will be handed out to the top 20 predicted brackets, based on scoring outlined by Imprint guidelines. The prize structure is as follows: </p>
<p>[PRIZING TO BE UPDATED].</p>
<p>6.	Tiebreakers: In the event that more than one verified Entrant correctly predicts the outcome of all 63 games played in the 2012 Tournament on a single Game Bracket, or with respect to the Best Bracket Prize, if more than one verified Entrant has a Game Bracket with the highest score (using Yahoo!'s Default Scoring formula) among the Game Brackets of all verified Entrants participating in this Contest, Imprint will attempt to break the tie by awarding the Prize, among those tied:</p> 
<p>1.	to the Entrant whose answer to the Tiebreaker Question on the tied Game Bracket is closest to the actual total score of the Championship Game (i.e. Total number of points scored by Team A plus Total number of points scored by Team B);</p>
<p>2.	in the event that the tie is still not broken, to the Entrant (among those still tied) whose answer to the Tiebreaker Question on the tied Game Bracket is closest to the actual score of the winning team in the Championship Game; and</p>
<p>3.	in the event that the tie is still not broken, to the Entrant (among those still tied) whose answer to the Tiebreaker Question on the tied Game Bracket is closest to the actual score of the losing team in the Championship Game.</p>
<p>In the event that the tie is still not broken after applying the tiebreakers above, the affected Prize will be divided evenly among all such still-tied, verified Entrants, and each prize awarded will be payable according to the same time schedule as above.</p>
<p>7.	Odds; Winner Notification: The maximum retail value of all prizes awarded in this game is five hundred dollars ($500); exact value depends on actual value of donated prizes. Odds of winning Prizes depend on the outcome of the 2012 Tournament games and the number of eligible entries received. Only listed Prizes will be awarded, as applicable, and no substitutions, cash equivalents or redemptions will be made.</p> 
<p>8.	PLEASE NOTE THAT EVEN IF YOUR GAME BRACKET IS LISTED AS A POTENTIAL WINNER AT THE CLOSE OF THIS CONTEST, YOU HAVE NOT YET WON A PRIZE. POTENTIAL WINNERS ARE SUBJECT TO VERIFICATION BY CONTEST ENTITIES, AND MUST MEET ALL ELIGIBILITY REQUIREMENTS BEFORE AN ENTRANT WILL BE CONFIRMED AS A WINNER AND A PRIZE WILL BE AWARDED. Potential winner(s) will be notified within seven days after selection date via email or telephone number.</p>
<p>NCAA, FINAL FOUR, SWEET SIXTEEN, and ELITE EIGHT are the trademarks of the National Collegiate Athletic Association. MARCH MADNESS is the trademark of the March Madness Athletic Association. Imprint Publication is not endorsed, sponsored, or affiliated with the National Collegiate Athletic Association or the March Madness Athletic Association.</p>


		</span>
		
		
		</div>
		</form>
	
<h2>Retrieve Your Submission</h2>
<p>Want to look up what you've already entered? Type in your Student ID to access your submission.</p>

<form action="view.php" method="get">
	Type in your Student ID: <input name="studentid" type="text" size="30" />
	<input type="submit" value="Submit" />
	<?php if($_GET['notfound'] == "true") echo "<br /><span style='color: red'>Student ID Not Found</span>"?>
</form>

<script type="text/javascript">
	$(document).ready(function() {
		$("#submit_button").click(function() {	//Form validation
			id = Number($("#studentid").val());
			error = false;
			
			if(!id || id < 10000000 || id > 99999999) {
				alert("Please enter a valid UW Student ID.");
				error = true;
			}
			if(!$("#form_name").val()) {
				alert("Please enter your name.");
				error = true;
			}
			if(!$("#form_email").val()) {
				alert("Please enter an email address so we can contact you if you've won.");
				error = true;
			}
			
			if(error == false) {
				$("#marchmadness").submit();
			}
		});
	});
</script>

<?php } else { ?>

<h2>Sorry &mdash; submissions are closed.</h2>

<p>Sorry, but submissions are currently closed. If you've already entered, enter your student ID and your email address below to see how you're doing.
	The results and winners of the contest will be announced in <em>Imprint</em>.</p>

<form action="view.php" method="get">
	Type in your Student ID: <input name="studentid" type="text" size="30" />
	<input type="submit" value="Submit" />
	<?php if($_GET['notfound'] == "true") echo "<br /><span style='color: red'>Student ID Not Found</span>"?>
</form>

<h2>The Results so Far</h2>

		<div id="bracket_wrapper">
			<div id="bracket">
<?php
function printMatch($id) {
	global $correct;
	
	if(!$correct[$id] || $correct[$id] == "false") {
		return "<span style='color: gray'>--</span>";
	}
	
	return $correct[$id];
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



<?php } include("footer.php") ?>
