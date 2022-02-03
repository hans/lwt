<?php
/**
 * \file
 * \brief LWT Information / Help
 *
 * @package Lwt
 * @author  LWT Project <lwt-project@hotmail.com>
 * @license Unlicense <http://unlicense.org/>
 * @link    https://hugofara.github.io/lwt/docs/html/info_8php.html
 * @since   1.0.3
 */

require_once __DIR__ . '/../src/php/markdown_converter.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />

		<meta http-equiv="content-language" content="en-US" />
		<meta http-equiv="cache-control" content="no-cache" />
		<meta http-equiv="pragma" content="no-cache" />
		<meta http-equiv="expires" content="0" />
		<meta name="keywords" content="Language Learning Texts LWT Software Freeware LingQ Alternative AJATT Khatzumoto MCD MCDs Massive Context Cloze Deletion Cards Tool Stephen Krashen Second Language Acquisition Steve Kaufmann" />
		<meta name="description" content="Learning with Texts (LWT) is a tool for Language Learning, inspired by Stephen Krashen's principles in Second Language Acquisition, Steve Kaufmann's LingQ System and ideas (e. g. Massive-Context Cloze Deletion Cards = MCDs) from Khatzumoto, published at AJATT - All Japanese All The Time. It is an Alternative to LingQ, 100 % free, Open Source, and in the Public Domain." />
		<meta name="revisit-after" content="2 days" />
		<meta name="viewport" content="width=1280, user-scalable=yes" />
		<link rel="apple-touch-icon" href="../img/apple-touch-icon-57x57.png" />
		<link rel="apple-touch-icon" sizes="72x72" href="../img/apple-touch-icon-72x72.png" />
		<link rel="apple-touch-icon" sizes="114x114" href="../img/apple-touch-icon-114x114.png" />
		<link rel="apple-touch-startup-image" href="../img/apple-touch-startup.png" />
		<style type="text/css">
		@import url(../css/styles.css);
		</style>
		<script type="text/javascript" src="../js/jquery.js"></script>  
		<script type="text/javascript" src="../js/floating.js"></script>
 		<script type="text/javascript">
		$.ajax(
			{
				type: 'POST',
				url: '../inc/ajax_get_theme.php',
				async: false, 
				data: { file: '../css/styles.css' }, 
				success: function (data) {
					console.log("theme path loaded");
					console.log(data);
					if (data.match(/styles.css$/g)) 
						$('style').text( "@import url(" + data + ");" );
			}
		});
		</script>
    <title>
      Learning with Texts :: Help/Information
    </title>
  </head>
  <body>
	<div style="position:fixed; width:auto; height:auto; top:10px; right:10px; 
	padding:5px; z-index:2; font-size: 10pt; text-align:center;">
		<a href="#">↑ TOP ↑</a><br /><br />
		<a href="#preface">Preface</a><br />
		<a href="#current">Curr. Version </a><br />
		<a href="#links">Links</a><br />
		<a href="#abstract">Abstract</a><br />
		<a href="#features">Features</a><br />
		<a href="#new_features">New Features</a><br />
		<a href="#screencasts">Screencasts</a><br />
		<a href="#restrictions">Restrictions</a><br />
		<a href="#license">(Un-) License</a><br />
		<a href="#thirdpartylicenses">Third Party</a><br />
		<a href="#install">Installation</a><br />
		<a href="#learn">How to learn</a><br />
		<a href="#howto">How to use</a><br />
		<a href="#faq">Q &amp; A</a><br /><br />
		<a href="#ipad">Setup Tablets</a><br /> 
		<a href="#langsetup">Lang. Setup</a><br /> 
		<a href="#termscores">Term Scores</a><br />
		<a href="#keybind">Key Bindings</a><br />
		<a href="#wordpress">WordPress Integration</a><br />
		<a href="#database">Database</a><br />
		<a href="#history">Changelog</a>
	</div>	

		<div style="margin-right:100px;">

		<h4>
			<a href="../index.php" target="_top"><img src="../img/lwt_icon_big.png" class="lwtlogoright" alt="Logo" />Learning with Texts</a>
			<br />
			<br />
			<span class="bigger">Help/Information</span>
		</h4>

		<p class="inline">
			Jump to topic:
			<select id="topicjump" onchange="{var qm = document.getElementById('topicjump'); var val=qm.options[qm.selectedIndex].value; qm.selectedIndex=0; if (val != '') { location.href = '#' + val;}}">
				<option value="" selected="selected">
					[Select...]
				</option>
				<option value="preface">
					Preface
				</option>
				<option value="current">
					Current Version
				</option>
				<option value="links">
					Links
				</option>
				<option value="abstract">
					Abstract
				</option>
				<option value="features">
					Features
				</option>
				<option value="new_features">
				  New in this Version
				</option>
				<option value="screencasts">
				  Screencasts
				</option>
				<option value="restrictions">
					Restrictions
				</option>
				<option value="license">
					(Un-) License
				</option>
				<option value="install">
					Installation
				</option>
				<option value="learn">
					How to learn
				</option>
				<option value="howto">
					How to use
				</option>
				<option value="faq">
					Questions and Answers
				</option>
				<option value="ipad">
					Setup for Tablets
				</option>
				<option value="langsetup">
					Language Setup
				</option>
				<option value="termscores">
					Term Scores
				</option>
				<option value="keybind">
					Key Bindings
				</option>
				<option value="wordpress">
				  WordPress Integration
				</option>
				<option value="database">
				  Database Structure
				</option>
				<option value="history">
					Changelog
				</option>
			</select>
		</p>

		<dl>
			
			<!-- ================================================================ -->
			
			<dt>
				▶ <b><a name="preface" id="preface">Preface</a></b> - <a href="#">[↑]</a>
			</dt>

			<dd>
				<?php echo markdown_converter(__DIR__ . "/preface.md"); ?>
			</dd>
			
			<!-- ================================================================ -->

			<dt>
				▶ <b><a name="current" id="current">Current Version</a></b> - <a href="#">[↑]</a>
			</dt>

			<dd>
				<ul>
					<li>The current version is <?php 
					require __DIR__ . '/../inc/kernel_utility.php'; 
					echo get_version(); 
					?>.
					</li>

					<li>
						<a href="#history">View the Changelog.</a>
					</li>
				</ul>
			</dd>
			
			<!-- ================================================================ -->

			<dt>
				▶ <b><a name="links" id="links">Important Links</a></b> - <a href="#">[↑]</a>
			</dt>

			<dd>
				<?php echo markdown_converter(__DIR__ . "/links.md"); ?>
			</dd>
			
			<!-- ================================================================ -->

			<dt>
				▶ <b><a name="abstract" id="abstract">Abstract</a></b> - <a href="#">[↑]</a>
			</dt>

			<dd>
				<?php echo markdown_converter(__DIR__ . "/abstract.md"); ?>
			</dd>
			
			<!-- ================================================================ -->

			<dt>
				▶ <b><a name="features" id="features">Features</a></b> - <a href="#">[↑]</a>
			</dt>

			<dd>
				<?php echo markdown_converter(__DIR__ . "/features.md"); ?>
			</dd>

			<!-- ================================================================ -->

			<dt>
				▶ <b><a name="new_features" id="new_features">New in this Version (not available in the OFFICIAL LWT)</a></b> - <a href="#">[↑]</a>
			  </dt>

			<dd>
				<?php echo markdown_converter(__DIR__ . "/newfeatures.md"); ?>
			</dd>
		 
		 
			<dt>
				▶ <b><a name="screencasts" id="screencasts">Screencasts/Videos</a></b> - <a href="#">[↑]</a>
			</dt>
		
			<dd>
				<ul>
				
				<li>
					A <a target="_blank" href="http://www.youtube.com/watch?v=QSLPOATWAU4">video</a> from <a target="_blank" href="http://www.youtube.com/user/FluentCzech">FluentCzech</a>:
					<br /><br />
					<iframe width="640" height="360" src="http://www.youtube.com/embed/QSLPOATWAU4" frameborder="0" allowfullscreen></iframe>
					<br /><br />
					Please have a look at the other great videos of <a target="_blank" href="http://www.youtube.com/user/FluentCzech">FluentCzech</a> that contain many good ideas for language learning! <br />
					<a target="_blank" href="http://www.anthonylauder.com">Website of FluentCzech (anthonylauder.com)</a>
					<br /><br />
				</li>
		
				<li>
					A <a target="_blank" href="http://www.youtube.com/watch?v=QnGG-_urLKk">video</a> from <a target="_blank" href="http://www.youtube.com/user/irishpolyglot">Benny the Irish polyglot</a>:
					<br /><br />
					<iframe width="640" height="360" src="http://www.youtube.com/embed/QnGG-_urLKk" frameborder="0" allowfullscreen></iframe>
					<br /><br />
					<a href="http://www.fluentin3months.com/learning-with-texts/" target="_blank">Fluent In 3 Months: Introducing LWT</a>, with <a target="_blank" href="http://lwtfi3m.co/">Benny's own (free) version of LWT</a>.<br />
				</li>
				</ul>
			</dd>
			
			<!-- ================================================================ -->

			<dt>
				▶ <b><a name="restrictions" id="restrictions">Restrictions</a></b> - <a href="#">[↑]</a>
			</dt>

			<dd>
				<?php echo markdown_converter(__DIR__ . "/restrictions.md"); ?>
			</dd>
			
			<!-- ================================================================ -->

			<dt>
				▶ <b><a name="license" id="license">(Un-) License</a></b> - <a href="#">[↑]</a>
			</dt>

			<dd>
				<?php echo markdown_converter(__DIR__ . "/../UNLICENSE.md" ) ?>
			</dd>
			<dt>
				▶ <b><a name="thirdpartylicenses" id="thirdpartylicenses">Third party licenses</a></b> - <a href="#">[↑]</a>
			</dt>
			<dd>
				<?php echo markdown_converter(__DIR__ . "/thirdpartylicenses.md" ) ?>
			</dd>
			
			<!-- ================================================================ -->

			<dt>
				▶ <b><a name="install" id="install">Installation on MS Windows, macOS, Linux</a></b> - <a href="#">[↑]</a>
			</dt>

			<dd>
				<!--<ul>
					<li><a target="_blank" href="http://learning-with-texts.sourceforge.io/LWT_INSTALLATION.txt">Please follow the up-to-date instructions <b><u><bigger>HERE</bigger></u></b> (you must be online!).</a><br /></li>
				</ul>-->
				<?php echo markdown_converter(__DIR__ . "/installation.md"); ?>
			</dd>
					 
			<!-- ================================================================ -->

			<dt>
				▶ <b><a name="learn" id="learn">How to learn with LWT</a></b> - <a href="#">[↑]</a>
			</dt>

			<dd>
				<?php echo markdown_converter(__DIR__ . "/learn.md"); ?>
			</dd>


			<dt>
				▶ <b><a name="howto" id="howto">How to use</a></b> - <a href="#">[↑]</a>
			</dt>

			<dd>
		  		<?php echo markdown_converter(__DIR__ . "/howto.md"); ?>
			</dd>
			
			<!-- ================================================================ -->

			<dt>
				▶ <b><a name="faq" id="faq">Questions and Answers</a></b> - <a href="#">[↑]</a>
			</dt>

			<dd>
				<?php echo markdown_converter(__DIR__ . "/faq.md"); ?>
			</dd>
			
			<!-- ================================================================ -->

			<dt>
				▶ <b><a name="ipad" id="ipad">Setup for Tablets</a></b> - <a href="#">[↑]</a>
			</dt>

			<dd>
				<?php echo markdown_converter(__DIR__ . "/ipad.md"); ?>
			</dd>
				
			<!-- ================================================================ -->
		
			<dt>
				▶ <b><a name="langsetup" id="langsetup">Language Setup</a></b> - <a href="#">[↑]</a>
			</dt>

			<dd>
				<?php echo markdown_converter(__DIR__ . '/langsetup.md'); ?>
			</dd>
			
			<!-- ================================================================ -->

			<dt>
				▶ <b><a name="termscores" id="termscores">Term Scores</a></b> - <a href="#">[↑]</a>
			</dt>

			<dd>
				<?php echo markdown_converter(__DIR__ . "/termscores.md"); ?>
			</dd>
			
			<!-- ================================================================ -->

			<dt>
				▶ <b><a name="keybind" id="keybind">Key Bindings</a></b> - <a href="#">[↑]</a>
			</dt>

			<dd>
				<?php echo markdown_converter(__DIR__ . "/keybind.md"); ?>
			</dd>
			
			<dt>
				▶ <b><a name="wordpress" id="wordpress">WordPress Integration</a></b> - <a href="#">[↑]</a>
			</dt>

			<dd>
				<?php echo markdown_converter(__DIR__ . "/wordpress.md"); ?>
			</dd>
			
			<dt>
				▶ <b><a name="database" id="database">Database Structure</a></b> - <a href="#">[↑]</a>
			</dt>

			<dd>
				<?php echo markdown_converter(__DIR__ . "/database.md"); ?>
			</dd>

			<dt>
				▶ <b><a name="history" id="history">Changelog</a></b> - <a href="#">[↑]</a>
			</dt>

			<dd>
				<?php echo markdown_converter(__DIR__ . "/CHANGELOG.md"); ?>
			</dd>
		</dl>

        <p class="smallgray graydotted">
            &nbsp;
        </p>

        <table>
            <tr>
                <td class="width50px">
                    <a target="_blank" href="http://en.wikipedia.org/wiki/Public_domain_software"><img src="../img/public_domain.png" alt="Public Domain" /></a>
                </td>
                <td>
                    <p class="smallgray">
                        <a href="http://sourceforge.net/projects/learning-with-texts/" target="_blank">"Learning with Texts" (LWT)</a> is released into the Public Domain. This applies worldwide.
                        <br />
                        In case this is not legally possible, any entity is granted the right to use this work for any purpose,
                        <br />
                        without any conditions, unless such conditions are required by law.

                    </p>
                </td>
            </tr>
        </table>
        </div>
    </body>
</html>
