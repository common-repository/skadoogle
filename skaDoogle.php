<?php
/*
Plugin Name: skaDoogle
Plugin URI: http://skadoogle.com/wordpress/skadoogle/
Description: The Affiliate Plugin of the Century! Monetize your Blog with skaDoogle's Contextual AutoLink Sensor, Ads, and Plugins for Clickbank and PayDotCom. Turn your static posts and articles into Cash... 
Author: Tim Brechbill
Author URI: http://skadoogle.com/video/
Version: 1.27
*/
global $skaShow;
$skaShow='';
$skaDoogleMe = new skaDoogle();
$skaDoogleMe->add_skamenu();
extract($_POST);
class skaDoogle {
	function add_skamenu() {
		add_action('admin_menu', array('skaDoogle', 'ska_admin_add_menu'));
		add_action('wp_head', array('skaDoogle', 'ska_front_add_hdr'));
	}
	function ska_admin_add_menu() {
		add_options_page('skaDoogle', 'skaDoogle', 8, 'skaDoogle', array('skaDoogle', 'options'));
	}
	function data_save() {
	  $_SESSION['error']='';
		if(isset($_POST['submitter']))
		{
			$option_name = 'skaDoogle';
			$ska_options['data'][0] = $_POST['skaLinksCode'];
			$ska_options['data'][1] = $_POST['skaUsername'];
			$errSka='';
			if ( get_option($option_name) ) {
				update_option($option_name, $ska_options);
			} else {
				add_option( $option_name, $ska_options );
			}
		}
		if(isset($_POST['shortcoder'])) {
      $errSka="";
      global $skaShow;
      $skaShow="skaElement('skaShortcode').style.display=''; skaWindow();  ";
		  $sc_name=trim($_POST['skaShortName']);
		  $sc_name=str_replace("[","",$sc_name);
		  $sc_name=str_replace("]","",$sc_name);
		  $sc_value=trim($_POST['skaShortValue']);
		  $sc_index=intval(trim($_POST['skaShortIndex']));
		  if (strlen($sc_name)<3) {
        $errSka="skaDoogle Short Name must be at least 3 characters";
		  }
		  if (strlen($sc_value)<3) {
        $errSka="Invalid skaDoogle Short Code Value - must be at least 3 characters";
		  }
		  $sc_option='['.$sc_name.']::'.$sc_value.'::'.$sc_index;
			$option_name = 'skaShortCodes';
			$my_shortcodes=get_option($option_name);
			$my_shortcodes['data'][$sc_index] = $sc_option;
			if (strlen($errSka)) {
			  $_SESSION['error']=$errSka;
			} else {
				 update_option( $option_name, $my_shortcodes);
			   $_SESSION['error']="[$sc_name] has been Updated...";
			}
		}
		if(isset($_POST['deleteshortcode'])) {
      global $skaShow;
      $skaShow="skaElement('skaShortcode').style.display=''; skaWindow(); ";
			$option_name = 'skaShortCodes';
			$my_shortcodes=get_option($option_name);
			$tot=count($my_shortcodes['data']);
		  $sc_index=intval(trim($_POST['skaShortDelete']));
		  if ($tot<2) {
		    delete_option($option_name);
		  } else {
  		  $i=-1;$k=-1;
	  	  foreach ($my_shortcodes['data'] as $keep) {
		      $i++;
		      if ($i==$sc_index) continue;
		      $k++;
		      $new_shortcodes['data'][]=substr($keep,0,strlen($keep)-strlen($i))."$k";
		    }
		    update_option($option_name,$new_shortcodes);
		  }
	    $_SESSION['error']="skaShortCode Deleted...";
		}
	}
	function options()	{
		skaDoogle::data_save();
		$ska_options = get_option('skaDoogle');
		$my_shortcodes = get_option('skaShortCodes');
		$domain_url = trailingslashit(get_bloginfo('url'));
		$blog_url = trailingslashit(get_bloginfo('wpurl'));
		$theme_url = trailingslashit(get_bloginfo('template_url'));
		$plugin_url = trailingslashit(WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)));
		?>
<script type="text/javascript">
var skaUser='<?php echo $ska_options['data'][1]; ?>'; 
function skaElement(id) {var el=document.getElementById?document.getElementById(id):document.all?document.all[id]:document.layers?document.layers[id]:null; return el; }
function skaLoginHelp() {
  var preview=skaElement('skadoogleLoginHelp').innerHTML;
  var win2=window.open('','skaHelp','width=600,height=210,toolbar=no,top=40,left=450,status=no');
  win2.document.open("text/html","replace");
  win2.document.writeln(preview);
  win2.document.close();  
  win2.focus();  
}
function skaShow(section) {
  if (skaUser.length<3) {
    alert("Please sign-up for skaDoogle (it's Free),\nand enter your skaDoogle Username.");
    return;
  }
  skaElement('skaError').innerHTML='';
  skaElement('skadoogleLoginHelp').style.display="none";
  skaElement('skaLinkSense').style.display="none";
  skaElement('skaPlugins').style.display="none";
  skaElement('skaAds').style.display="none";
  skaElement('skaRSS').style.display="none";
  skaElement('skaShortcode').style.display="none";
  if (skaElement('skaFrame').innerHTML.length<10) {
    skaWindow();
  }
  var iSrc=skaElement('skaIframe').src;
  if (section=='skaLinkSense') { skaSrc='getsense'; }
  if (section=='skaPlugins') { skaSrc='getplugin'; }
  if (section=='skaAds') { skaSrc='getads'; }
  if (section=='skaRSS') { skaSrc='getrss'; }
  if (section=='skaCustom') { skaSrc='getcustom'; }
  if (section=='skaShortcode') {
    // Do not change src
  } else {
   if (iSrc.indexOf(skaSrc)<10) { skaElement('skaIframe').src='http://skadoogle.com/members/navigate.php?f='+skaSrc; }
  }
  skaElement(section).style.display="";
}
function skaExpand(thisId) {
  var expand=skaElement(thisId);
  var instr=skaElement(thisId+"2");
  if (instr.style.display=="none") {
    if (thisId=='skaFrame') {
      instr.style.display='inline';
    } else { 
      instr.style.display='';
    }
  } else {
    instr.style.display='none';
  }
}  
function skaWindow() {
  var f=skaElement('skaFrame2');
  var t=skaElement('skaFrame');
  if (f.style.display=="none") {
    t.innerHTML='<img src="http://skadoogle.s3.amazonaws.com/images/collapse.gif" border=0 /> <b>Hide the skaDoogle Member Area Window Below</b>';
    f.style.display='';
  } else {
    t.innerHTML='<img src="http://skadoogle.s3.amazonaws.com/images/expand.gif" border=0 /> <b>Show the skaDoogle Member Area</b>';
    f.style.display='none';
  }
}  
function skaCurrentShortCodes() {
  var f=skaElement('curSkaShortcodes2');
  var t=skaElement('curSkaShortcodes');
  if (f.style.display=="none") {
    t.innerHTML='<img src="http://skadoogle.s3.amazonaws.com/images/collapse.gif" border=0 /> <b>Hide the Current skaShortCodes Below</b>';
    f.style.display='';
  } else {
    t.innerHTML='<img src="http://skadoogle.s3.amazonaws.com/images/expand.gif" border=0 /> <b>Show All Your Current skaShortCodes</b>';
    f.style.display='none';
  }
}
function skaPreview(myIndex) {
  var skaShort="skaShortValue"+myIndex;
  var preview=skaElement(skaShort).value;
  var win=window.open('','','width=700,height=500');
  win.document.open("text/html","replace");
  if (preview.indexOf("?>")>1) { preview = 'Note: Previews do not work for skaShortCodes with PHP code.<br /><br />'+ preview; }
  win.document.writeln(preview);
  win.document.close();  
}
function skaValidateShortCode() {
  var rc=true; 
  var nlen=skaElement('skaShortName').value.length;
  var vlen=skaElement('skaShortValue').innerHTML.length;
  if (vlen<3) vlen=skaElement('skaShortValue').value.length;
  if (nlen<3) rc=false;
  if (vlen<3) rc=false;
  return rc;
} 
function skaDelete() {
  return confirm('Press "OK" to Delete this record\nPress "Cancel" to ignore.');
}
function skaEdit(indx) {
  var skn='skaShortName'+indx;
  var skv='skaShortValue'+indx;
  var nval=skaElement(skn).value;
  var ival=skaElement(skv).value;
  nval=nval.replace('[',''); nval=nval.replace(']','');
  skaElement('skaShortName').value=nval;
  skaElement('skaShortValue').value=ival;
  skaElement('skaShortIndex').value=indx;
  skaElement('skaShortName').focus();
}
</script>
		<div class="wrap">
			<form method="post" name="skaDoogle_form">
				<div id="icon-options-general" class="icon32"><br /></div>
				<h2>skaDoogle <font style="font-size:11px;"> (Please select a skaDoogle Menu Option below)</font></h2>
				<span style="font-size:14px;"><input type="button" value="Link Sensor" class="button-primary" onclick="skaShow('skaLinkSense');"><input type="button" value="skaShortCodes" class="button-primary" onclick="skaShow('skaShortcode');"><input type="button" value="skaDoogle Ads" class="button-primary" onclick="skaShow('skaAds');"><input type="button" class="button-primary"  onclick="skaShow('skaPlugins');" value="Mall & Plugins"><input type="button" value="skaDoogle RSS" class="button-primary" onclick="skaShow('skaRSS');"></span>
				<p><b>skaDoogle Username</b>: 
				<?php 
				if (strlen($ska_options['data'][1])) {
					echo '<font color="red"><b>'.$ska_options['data'][1].'</b></font>';
					echo '<input type="hidden" name="skaUsername" class="large_text code" value="'.$ska_options['data'][1].'"> ';
				} else {
					?>
					<input type="text" name="skaUsername" class="large_text code" value=""> 
					<input type="submit" name="submitter" value="<?php esc_attr_e('Save Username') ?>" class="button-primary" />	
					<br />Note: You must be a member of skaDoogle.com<br /><a href="http://skadoogle.com/members/signup.php" target="_blank"><b>Signup</b></a> now for a FREE membership or enter your skaDoogle Username above. 
					<?php	
				}
				?>
				<br />
				Visit <a href="http://skaDoogle.com"><b>skaDoogle.com</b></a> for more information on all of the skaDoogle affiliate tools...<br />
				</p>
			  <h4 id="skaError" style="color:red;"><?php echo $_SESSION['error'];?></h4>
				<!-- ======================================================== -->
				<div id="skaLinkSense" style="display:none">
				<!-- ======================================================== -->
			 <div style="width:75%;border:1px solid;align:center;text-align:left;padding:5px;">
			  <div id="skaSenseHide" name="skaSenseHide" style="display:inline;font-size:14px;font-family:verdana;cursor:pointer;" title="Click Here to Expand/Collapse Instructions" onclick="skaExpand('skaSenseHide')"><img src="http://skadoogle.s3.amazonaws.com/images/help.png" border=0 />
			   <b>Instructions for skaDoogle LinkSensor</b></div><br />
			  <div id="skaSenseHide2" name="skaSenseHide2" style="display:none;font-size:12px;font-family:arial;">
				 <ol style="padding-left:20px;">
				  <li>Login to the skaDoogle Member area below. (Problems logging in? <input type="button" class="button-secondary" onclick="skaLoginHelp()" value="Click Here"> for Help)</li>
				  <li>Select the "Tools" Toolbar, then Select "Link Sensor" from the Tools Menu</li>
				  <li>Configure your Link Sensor, then press the "Get Code" Button when finished </li>
				  <li>Copy the Javascript Code and Paste the code into the area below</li>
				  <li>Press the <div class="button-primary" style="display:inline;">Save Changes</div> button</li>
				 </ol>
			  </div>
			 </div>
			<h3>skaDoogle Link Sensor</h3>
				<textarea id="skaLinksCode" name="skaLinksCode" class="large-text code" cols="50" rows="6"><?php echo stripslashes($ska_options['data'][0]); ?></textarea>
				<input type="submit" name="submitter" value="<?php esc_attr_e('Save Changes') ?>" class="button-primary" />		
			</form><br />
			</div>
			<!-- ======================================================== -->
			<div id="skaShortcode" style="display:none">
			<!-- ======================================================== -->
			<div style="width:75%;border:1px solid;align:center;text-align:left;padding:5px;"><div id="skaShortcodeHide" name="skaShortcodeHide" style="display:inline;font-size:14px;font-family:verdana;cursor:pointer;" title="Click Here to Expand/Collapse Instructions" onclick="skaExpand('skaShortcodeHide')"><img src="http://skadoogle.s3.amazonaws.com/images/help.png" border=0 /> <b>Instructions for skaDoogle Short Codes</b></div><br />
			 <div id="skaShortcodeHide2" name="skaShortcodeHide2" style="display:none;font-size:12px;font-family:arial;">
			   Put a skaShortCode on any page or post that you want, at the location you want, and it will be replaced with the skaDoogle RSS Widget, Ad, Plugin, Your Own Custom Niche Ads, or any html/text/javascript/php that you enter as the "<b>Value</b>".<p>
			   For example: Build a skaDoogle Ad or Plugin with the keyword "wordpress", then enter <b>skaWordpress</b> as the skaDoogle ShortCode Name and paste the Code from your Ad or Plugin into the <b>Value</b> textbox. <p>
			   Now, whenever you want to show the Ad or Plugin on any Post, Page, or Sidebar, just add <b>[skaWordpress]</b> and your skaDoogle Ad or Plugin will be inserted! Update your skaShortCode here and it will be propagated everywhere you use it on your blog!<p>
			   Note: You are not required to use skaDoogle Values. You can use any Value you want and it will work the same way.
				<ol style="padding-left:20px;">
				<li>Login to the skaDoogle Member area below. (Problems logging in? <input type="button" class="button-secondary" onclick="skaLoginHelp()" value="Click Here"> for Help)</li>
				<li>Select the "Tools" Toolbar, then Select "skaDoogle Ads", "Mall and Plugins" "My NicheAds", or "RSS" from the Tools Menu</li>
				<li>Configure your Ads, then press the "Get Code" Button when finished </li>
				<li>Copy the <b>Javascript</b>, <b>PHP</b>, or <b>IFRAME</b> Code</li>
				<li>Paste the code into the skaDoogle Short Code Value below</li>
				<li>Enter your skaDoogle ShortCode Name and Press the <div class="button-primary" style="display:inline;">Save</div> button</li>
				</ol>
			 </div>
			</div>
			<br />
  			<form method="post" name="skaDoogle_form">
				   <b>skaDoogle ShortCode Name</b>: <input type="text" id="skaShortName" name="skaShortName" size="15" value="<?=$skaShortName;?>">
				   <br /><b>Value</b>: 
				   <br /><textarea id="skaShortValue" name="skaShortValue" class="large-text code" style="font-size:11px;" cols="100" rows="6"><?=$skaShortValue;?></textarea><input type="hidden" name="skaShortIndex" id="skaShortIndex" value=<?=count($my_shortcodes['data']);?> size=2 >
					<br /><input type="submit" name="shortcoder" value="Save" onclick="var rc=skaValidateShortCode(); if (rc==false) { alert('Invalid ShortCode Name or Value.\n\nMinimum of 3 characters required...')}; return rc;" class="button-primary" />
				</form>
				<br /><br />
				<?php
					  if (count($my_shortcodes['data'])>0) {
					    echo '<h4 id="curSkaShortcodes" style="display:inline;cursor:pointer" title="Click to Show / Hide Current skaShortCodes" onclick="skaCurrentShortCodes()" ><img src="http://skadoogle.s3.amazonaws.com/images/collapse.gif" border=0 /> <b>Hide the Current skaShortCodes Below</b></h4><div id="curSkaShortcodes2">';
					    for ($i=0; $i<count($my_shortcodes['data']); $i++) {
					      $my_sc=$my_shortcodes['data'][$i];
					      $mydata=explode("::",$my_sc);
					      $scName=$mydata[0];
					      $scVal=$mydata[1];
					      $scIndex=intval($mydata[2]);					      
					      echo '<hr width="80%" align="left">
					      <form method="post" name="skaDoogle_form"><input type="text" size="15" style="font-weight:bold" id="skaShortName'.$scIndex.'" name="skaShortName'.$scIndex.'" readonly value="'.$scName. '"> <input type="button" name="editshortcode" value="Edit" class="button-secondary" title="Click here and the ShortCode Name and Value will move to the edit box above where you can edit and save it" onclick="skaEdit('.$scIndex.')"/> <input type="button" name="previewshortcode" title="Please Note: Previews do not work with PHP code..." value="Preview" onclick="skaPreview('.$scIndex.');" class="button-secondary" /> <input type="submit" name="deleteshortcode" onClick="return skaDelete()" title="Permanently remove this skaShortCode" value="Delete" class="button-secondary" /> <input type="hidden" id="skaShortDelete" name="skaShortDelete" value='.$scIndex.'>
					      <br /><textarea cols="140" rows="2" readonly id="skaShortValue'.$scIndex.'" name="skaShortValue'.$scIndex.'" style="background-color:#EEEEEE;font-family:verdana;font-size:10px;">'.stripslashes($scVal).'</textarea>
					      </form>';
					    }
					    echo '</div>';
					  }					  
					?>
			</div>	
			<!-- ======================================================== -->
			<div id="skaAds" style="display:none">
			<!-- ======================================================== -->
			 <div style="width:75%;border:1px solid;align:center;text-align:left;padding:5px;">
			  <div id="skaAdsHide" name="skaAdsHide" style="display:inline;font-size:14px;font-family:verdana;cursor:pointer;" title="Click Here to Expand/Collapse Instructions" onclick="skaExpand('skaAdsHide')"><img src="http://skadoogle.s3.amazonaws.com/images/help.png" border=0 /> <b>Instructions for skaDoogle Ads</b></div><br />
			  <div id="skaAdsHide2" name="skaAdsHide2" style="display:none;font-size:12px;font-family:arial;">
				<ol style="padding-left:20px;">
				<li>Login to the skaDoogle Member area below. (Problems logging in? <input type="button" class="button-secondary" onclick="skaLoginHelp()" value="Click Here"> for Help)</li>
				<li>Select the "Tools" Toolbar, then Select "skaDoogle Ads" or "My NicheAds" from the Tools Menu</li>
				<li>Configure your Ads, then press the "Get Code" Button when finished </li>
				<li>Copy the PHP or Javascript Code</li>
			  <li>Go to the Wordpress Page, Post, Sidebar Widget, or skaShortCode of your choice</li>
			  <li>Paste the code where you want it, and Press the <div class="button-primary" style="display:inline;">Save</div> button</li>
				</ol>
				 <b>Note</b>: I highly recommend you <i>always</i> save your skaDoogle Code to a <b>[skaShortCode]</b>. 
				 <br />skaDoogle skaShortCodes can be used in any Post,  Page, or skaDoogle Sidebar Widget, so you can easily insert the skaShortCode in multiple locations... AND you only have to update the skaShortCode in one place and it will propagate everwhere you use it on your entire blog!<br />Click on the <b>skaShortCodes</b> button above after you copy your code below.
				</div>
			 </div>
			</div>				
			<!-- ======================================================== -->
			<div id="skaPlugins" style="display:none">
			<!-- ======================================================== -->
			 <div style="width:75%;border:1px solid;align:center;text-align:left;padding:5px;">
			  <div id="skaPluginsHide" name="skaPluginsHide" style="display:inline;font-size:14px;font-family:verdana;cursor:pointer;" title="Click Here to Expand/Collapse Instructions" onclick="skaExpand('skaPluginsHide')"><img src="http://skadoogle.s3.amazonaws.com/images/help.png" border=0 /> <b>Instructions for skaDoogle Mall & Plugins</b></div><br />
			  <div id="skaPluginsHide2" name="skaPluginsHide2" style="display:none;font-size:12px;font-family:arial;">
				 <ol style="padding-left:20px;">
				  <li>Login to the skaDoogle Member area below. (Problems logging in? <input type="button" class="button-secondary" onclick="skaLoginHelp()" value="Click Here"> for Help)</li>
				  <li>Select the "Tools" Toolbar, then Select "Mall & Plugins" from the Tools Menu</li>
				  <li>Configure your Plugin, then press the "Get Code" Button when finished </li>
				  <li>Copy the PHP or IFRAME Code</li>
				  <li>Go to the Wordpress Page, Post, Sidebar Widget, or skaShortCode of your choice</li>
				  <li>Paste the code where you want it, and Press the <div class="button-primary" style="display:inline;">Save</div> button</li>
				 </ol>
				 <b>Note</b>: I highly recommend you <i>always</i> save your skaDoogle Code to a <b>[skaShortCode]</b>. 
				 <br />skaDoogle skaShortCodes can be used in any Post,  Page, or skaDoogle Sidebar Widget, so you can easily insert the skaShortCode in multiple locations... AND you only have to update the skaShortCode in one place and it will propagate everwhere you use it on your entire blog!<br />Click on the <b>skaShortCodes</b> button above after you copy your code below.
			  </div>
			 </div>
			</div>
			<!-- ======================================================== -->
			<div id="skaRSS" style="display:none">
			<!-- ======================================================== -->
			 <div style="width:75%;border:1px solid;align:center;text-align:left;padding:5px;">
			  <div id="skaRssHide" name="skaRssHide" style="display:inline;font-size:14px;font-family:verdana;cursor:pointer;" title="Click Here to Expand/Collapse Instructions" onclick="skaExpand('skaRssHide')"><img src="http://skadoogle.s3.amazonaws.com/images/help.png" border=0 /><b>Instructions for skaDoogle RSS</b></div><br />
			  <div id="skaRssHide2" name="skaRssHide2" style="display:none;font-size:12px;font-family:arial;">
				   <ol style="padding-left:20px;">
				   <li>Login to the skaDoogle Member area below. (Problems logging in? <input type="button" class="button-secondary" onclick="skaLoginHelp()" value="Click Here"> for Help)</li>
			   	 <li> Select the "Tools" Toolbar, then Select "RSS" from the Tools Menu</li>
				   <li>Configure your RSS Widget, then follow the "<b>Instructions to Get the Code for This RSS Widget</b>"</li>
			     <li>Go to the Wordpress Page, Post, Sidebar Widget, or skaShortCode of your choice</li>
		    	 <li>Paste the code where you want it, and Press the <div class="button-primary" style="display:inline;">Save</div> button</li>
				   </ol>
				 <b>Note</b>: I highly recommend you <i>always</i> save your skaDoogle Code to a <b>[skaShortCode]</b>. 
				 <br />skaDoogle skaShortCodes can be used in any Post,  Page, or skaDoogle Sidebar Widget, so you can easily insert the skaShortCode in multiple locations... AND you only have to update the skaShortCode in one place and it will propagate everwhere you use it on your entire blog!<br />Click on the <b>skaShortCodes</b> button above after you copy your code below.
				</div>
			 </div>
			</div>				
			<br />
			<!-- ======================================================== -->
			<div id="skadoogleLoginHelp" style="display:none;"><div style="width:95%;border:1px solid;align:center;font-family:tahoma;text-align:left;padding:10px;font-size:11px;background-color:#eeeeee"><b>Login Help Instructions</b> <br />
			  First, make sure the skaDoogle Member Area Window is visible. If you cannot see the skaDoogle Member Area below, Click on <b><img src="http://skadoogle.s3.amazonaws.com/images/expand.gif" border=0 /> Show the skaDoogle Member Area Window</b> located underneath the Instruction box to access the skaDoogle Member Area Window.<br /><br />
			  There is a known issue with some browsers that will not let you Login to the skaDoogle Member Area thru the window below. To correct this, do the following 2 steps:<ol>
<li> Login to skaDoogle from the "Visit <b>skaDoogle.com</b>" link located under the Menu at the top of the page</li>
<li> Press the "Back" button on your browser (twice) until you return to the skaDoogle Wordpress page.</li></ol>
After completing these steps, you should now be logged into skaDoogle.<br />
			  <input id="skaLoginHelpButton" type="button" class="button-secondary" onclick="window.close()" value="Close"> this Help Window... 
			</div></div>
			<div id="skaFrame" name="skaFrame" style="display:inline;cursor:pointer;" title="Show / Hide the skaDoogle Member Area Window below" onclick="skaWindow()"></div>
			<!-- ======================================================== -->
			<div id="skaFrame2" name="skaFrame2" style="display:none">
			<!-- ======================================================== -->
			<iframe id="skaIframe" src="http://skadoogle.com/members/" width="880" height="1800" marginheight=1 marginwidth=1 frameborder="1" ></iframe>
			</div>
		</div>
				<script type="text/javascript">
				 <?php
				  global $skaShow;
				  echo $skaShow; 
				 ?></script>
		<?php
	}  // end function options()
	function ska_front_add_hdr() {
		$domain_url = trailingslashit(get_bloginfo('url'));
		$blog_url = trailingslashit(get_bloginfo('wpurl'));
		$theme_url = trailingslashit(get_bloginfo('template_url'));
		$plugin_url = trailingslashit(WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)));
		$ska_options = get_option('skaDoogle');
		$ska_output = $ska_options['data'][0];
		$ska_output = str_replace('%domain_url%', $domain_url, $ska_output);
		$ska_output = str_replace('%blog_url%', $blog_url, $ska_output);
		$ska_output = str_replace('%theme_url%', $theme_url, $ska_output);
		$ska_output = str_replace('%plugin_url%', $plugin_url, $ska_output);
		$ska_output = "\n<!-- skaDoogle Header - START -->\n" . $ska_output . "\n<!-- skaDoogle Header - END -->\n";
		echo stripslashes($ska_output);
	}
}
function ska_shortcoder( $text, $case_sensitive=false ) {
		$skaOptions = get_option("skaShortCodes");
		if (is_array($skaOptions)) {
		  foreach($skaOptions['data'] as $shortcode_data) {
			$ska_array=explode("::",$shortcode_data);
		 	$ska_shortcode = stripslashes($ska_array[0]);
			$ska_value = stripslashes($ska_array[1]);
			$text = str_replace($ska_shortcode,$ska_value, $text);
		  }
		}
		ob_start();
		eval("?>$text<?");
		$result = ob_get_contents();
		ob_end_clean();
		return trim($result);
} 
function widget_skadooglephp($args, $widget_args = 1) {
	extract( $args, EXTR_SKIP );
	if ( is_numeric($widget_args) ) $widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract( $widget_args, EXTR_SKIP );
	$options = get_option('widget_skadooglephp');
	if ( !isset($options[$number]) )
		return;
	$title = $options[$number]['title'];
	$text = apply_filters( 'widget_skadooglephp', $options[$number]['text'] );
	echo $before_widget;
	if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } 
	echo '<div class="skadooglephpwidget">'.ska_shortcoder($text).'</div>';
	echo $after_widget;
}
function widget_skadooglephp_control($widget_args) {
	global $wp_registered_widgets;
	static $updated = false;
	if ( is_numeric($widget_args) ) {
		$widget_args = array( 'number' => $widget_args );
	}
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract( $widget_args, EXTR_SKIP );
	$options = get_option('widget_skadooglephp');
	if (!is_array($options)) { $options = array(); }
	if ( !$updated && !empty($_POST['sidebar']) ) {
		$sidebar = (string) $_POST['sidebar'];
		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( isset($sidebars_widgets[$sidebar]) ) {
		  $this_sidebar =& $sidebars_widgets[$sidebar];
		} else {
		  $this_sidebar = array();
		}
		foreach ( $this_sidebar as $_widget_id ) {
			if ( 'widget_skadooglephp' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) ) {
				$widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
				if ( !in_array( "skadooglephp-$widget_number", $_POST['widget-id'] ) ) unset($options[$widget_number]);
			}
		}
		foreach ( (array) $_POST['widget-skadooglephp'] as $widget_number => $widget_text ) {
			$title = strip_tags(stripslashes($widget_text['title']));
			if ( current_user_can('unfiltered_html') ) {
				$text = stripslashes( $widget_text['text'] );
			} else {
				$text = stripslashes(wp_filter_post_kses( $widget_text['text'] ));
			}
			$options[$widget_number] = compact( 'title', 'text' );
		}
		update_option('widget_skadooglephp', $options);
		$updated = true;
	}
	if ( -1 == $number ) {
		$title = '';
		$text = '';
		$number = '%i%';
	} else {
		$title = attribute_escape($options[$number]['title']);
		$text = format_to_edit($options[$number]['text']);
	}
?>
		<p>
			<input class="widefat" id="skadooglephp-title-<?php echo $number; ?>" name="widget-skadooglephp[<?php echo $number; ?>][title]" type="text" value="<?php echo $title; ?>" />
			<p>PHP Code (MUST be enclosed in &lt;?php and ?&gt; tags!):</p>
			<textarea class="widefat" rows="16" cols="20" id="skadooglephp-text-<?php echo $number; ?>" name="widget-skadooglephp[<?php echo $number; ?>][text]"><?php echo $text; ?></textarea>
			<input type="hidden" id="skadooglephp-submit-<?php echo $number; ?>" name="skadooglephp-submit-<?php echo $number; ?>" value="1" />
		</p>
<?php
}
function widget_skadooglephp_register() {
	if ( !function_exists('wp_register_sidebar_widget') || !function_exists('wp_register_widget_control') )
		return;
	if ( !$options = get_option('widget_skadooglephp') )
		$options = array();
	$widget_ops = array('classname' => 'widget_skadooglephp', 'description' => __('skaDoogle Widget- Text, Javascript, HTML, PHP, or [skaShortCode]'));
	$control_ops = array('width' => 460, 'height' => 350, 'id_base' => 'skadooglephp');
	$name = __('skaDoogle');
	$id = false;
	foreach ( array_keys($options) as $o ) {
		if ( !isset($options[$o]['title']) || !isset($options[$o]['text']) )
			continue;
		$id = "skadooglephp-$o"; 
		wp_register_sidebar_widget($id, $name, 'widget_skadooglephp', $widget_ops, array( 'number' => $o ));
		wp_register_widget_control($id, $name, 'widget_skadooglephp_control', $control_ops, array( 'number' => $o ));
	}
	if ( !$id ) {
		wp_register_sidebar_widget( 'skadooglephp-1', $name, 'widget_skadooglephp', $widget_ops, array( 'number' => -1 ) );
		wp_register_widget_control( 'skadooglephp-1', $name, 'widget_skadooglephp_control', $control_ops, array( 'number' => -1 ) );
	}
}
add_action( 'widgets_init', 'widget_skadooglephp_register' );
add_filter('the_content', 'ska_shortcoder', 9);
?>