<?php
$_GET['top_ad'] = '<a href="http://alumni.uwaterloo.ca/alumni/e-newsletter/2012/apr/contest.html" title="Imprint Photography Ad"><img src="http://archive.imprintpub.org/?attachment_id=9754" alt="Imprint Photography Ad"></a>';

function top_ad() {
	echo "<script language=\"javascript\">
<!--
if (window.adgroupid == undefined) {
 window.adgroupid = Math.round(Math.random() * 1000);
}
document.write('<scr'+'ipt language=\"javascript1.1\" src=\"http://adserver.adtechus.com/addyn/3.0/5286.1/-1/0/-1/ADTECH;size=728x90;alias=en_universityofwaterloo_newspaper_imprint-ros-top;kv1=0;kv3=0;kv4=0;kv7=0;kv8=0;target=_blank;loc=100;grp='+window.adgroupid+';misc='+new Date().getTime()+'\"></scri'+'pt>');
//-->
</script>";
}
