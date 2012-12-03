<?php
$_REQLOGIN = true;
include("marchmadness.php");

if($_POST['roster'] && $_SESSION['logged_in']) {
	$data = $_POST;
	$data["time"] = time();
	
	$file = "saves/master.txt";
	
	$fh = fopen($file, "w");
	fwrite($fh, serialize($data), 10000);
	
	//We now need to refresh the page.
	header("Location: ?saved=true");
	die();
}

if($_GET['clear'] && $_GET['clear'] == $_SESSION['cleartime'] && $_SESSION['logged_in']) {
	rename("saves", "cleared/".time());
	rename("oldsaves", "cleared/".time()."_old");
	mkdir("saves");
	mkdir("oldsaves");
	
	copy("cleared/".time()."/master.txt", "saves/master.txt");
}
$_SESSION['cleartime'] = time();

include("header.php");
?>

	<script type="text/javascript">
		function get_selected(id) {
			var html = $("#match_"+id).html();
			if(html == "--")
				return null;
			val = $("#match_"+id+" select").val();
			if(val && val != "false") return val;
			return null;
		}
	
		function redraw() {
			$(".match").each(function(e) {
				if($("select", this).length == 0 && $(this).html() != "--")
					return;
				
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
				
				if(option1 != false && option1 && option2 && option1 != "--" && option2 != "--") {
					newhtml = "<select name='match["+id+"]'><option value='false'>--</option><option value='"+option1+"'>"+option1+"</option><option value='"+option2+"'>"+option2+"</option></select>";
				}
				else
					newhtml = "--";
				
				
				var curroptions = [];
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
			
			$(".roster").click(function() {
				if($("input", this).length != 0) return;
				tempcontent = $(this).html();
				$(this).html("<input type='text' id='roster_edit' value='"+tempcontent+"'/>");
				$("input", this).focus();
				
				$(".roster input").blur(function() {
					if(!$(this).val())
						$(this).val("NONAME");
					$(this).parent().html($(this).val());
					redraw();
				})
			});
		
		})
	</script>
	
		<h2>Administration Panel</h2>
		
		<noscript><b>Javascript is required to participate in this contest.</b></noscript>
		
		<p>Click on a team to edit it. <a href="?logout=true">Logout</a></p>
		
		<?php if($_GET['saved'] == true) echo "<p>The results have been saved.</p>"?>
	</div>
	
		<form id="marchmadness" action="./admin.php" method="post" />
		<div id="bracket_wrapper">
			<div id="bracket">
<?php
function  printMatch($id) {
	global $data, $roster, $correct, $points;
	
	if(!$correct[$id] || $correct[$id] == "false" || $correct[$id] == "--") {
		return "--";
	}
	
	else {
		if($id < 32) {
			$option1 = $roster[2 * $id];
			$option2 = $roster[2*$id + 1];
		}
		else if($id < 48) {
			$option1 = $correct[($id-32) * 2];
			$option2 = $correct[($id-32) * 2 + 1];
		}
		else if($id < 56) {
			$option1 = $correct[32 + (($id-48) * 2)];
			$option2 = $correct[32 + (($id-48) * 2) + 1];
		}
		else if($id < 60) {
			$option1 = $correct[48 + (($id-56) * 2)];
			$option2 = $correct[48 + (($id-56) * 2) + 1];
		}
		else if($id < 62) {
			$option1 = $correct[56 + (($id-60) * 2)];
			$option2 = $correct[(56 + (($id-60) * 2) + 1)];
		}
		else if($id == 62) {
			$option1 = $correct[60];
			$option2 = $correct[61];
		}
		
		if($option1 && $option2 && $option1 != "false" && $option2 != "false" && $option1 != "--" && $option2 != "--") {
			if($correct[$id] == $option1) {
				$option1 = "<option value='$option1' selected='selected'>$option1</option>";
			}
			else {
				$option1 = "<option value='$option1'>$option1</option>";
			}
			
			if($correct[$id] == $option2) {
				$option2 = "<option value='$option2' selected='selected'>$option2</option>";
			}
			else {
				$option2 = "<option value='$option2'>$option2</option>";
			}
			
			$select = "<select name='match[".$id."]'><option value='false'>--</option>$option1 $option2</select>";
			return $select;
		}
		
		return $correct[$id];
	}
}

//First round - 64 teams
for($i = 0; $i < 64; $i++) {
	if(!$roster[$i])
		$roster[$i] = "--";
}

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
</div></div>

		<noscript>Error: Javascript is required to make a submission.</noscript><br /><br />
		
		<input type="checkbox" name="contest_open" value="true" <?php if($contest_open == true) echo "checked='checked'"?> /> Contest is Open (uncheck to block new entrants)
		<br />
		<input type="button" value="Save" id="submit_button" />
		
		<div id="roster_values"></div>
		
		</form>
		
	
	<h2>View Submissions</h2>
	
	<p>(Click on a column to sort it)</p>
	
	<table class="sortable" style="width: 100%;">
		<tr>
			<th style="width: 200px;">Student ID</th>
			<th style="width: 70px;">Points</th>
			<th style="width: 200px;">Name</th>
			<th>Email</th>
			<th style="width: 200px;">Phone</th>
			<th style="width: 80px;">View</th>
		</tr>
	
	<?php
		$files = getDirectoryList("saves");
		
		foreach($files as $value) {
			if($value == "master.txt")
				continue;
			$data = unserialize(file_get_contents("saves/".$value));
			$points = getPoints("saves/".$value, $data);
			
			echo("<tr><td>".$data['studentid']."</td><td>".$points ."</td><td>".$data['name']."</td><td>".$data['email']."</td><td>".$data['phone']."</td>".
				"<td><a href='view.php?id={$data['id']}'>View</a></td></tr>");
		}
	?>
	</table>
	
	<br /><br /><a href="?clear=<?php echo $_SESSION['cleartime'] ?>">Clear all Submissions</a>. This will clear the submissions list. A backup of the submissions will be
	stored in the <code>cleared/</code> folder. The game data will be preserved.
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$("#submit_button").click(function() {
			//We have to pass the roster to the server -- thus we need to generate a whole bunch of hidden input fields with updated roster values
			i = 0;
			$(".roster").each(function() {
				$("#roster_values").append("<input type='hidden' name='roster["+i+"]' value='"+$(this).html()+"'/>")
				i++;
				
				
			})
			$("#marchmadness").submit();
		});
	});
</script>

<?php include("footer.php");
