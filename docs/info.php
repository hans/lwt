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
 		<!--<script type="text/javascript">
		$.ajax(
			{
				type: 'POST',
				url:'inc/ajax_get_theme.php',
				async: false, 
				data: { file:'../css/styles.css' }, 
				success: function (data) {
					if (data.match(/styles.css$/g)) 
						$('style').text( "@import url(" + data + ");" );
			}
		});
		</script>-->
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
				  New  in this Version
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
					<a href="http://www.youtube.com/watch?v=TkcVJ6SpK2Q" target="_blank">01 - Starting with French</a> (created with version 1.0.2)
					<br />
					Finding learning material, importing a text with audio, saving words and expressions, changing the status, printing.
					<br /><br />
					<iframe width="640" height="510" src="http://www.youtube.com/embed/TkcVJ6SpK2Q" frameborder="0" allowfullscreen></iframe>
					<br /><br />
					Mentioned websites in this screencast:
					<ul>
					<li><a href="http://lingq.com" target="_blank">LingQ - Library</a></li>
					<li><a href="http://fluentin3months.com/learning-materials/" target="_blank">Fluent in 3 months - Learning materials</a></li>
					<li><a href="http://ielanguages.com/french1.html" target="_blank">ieLanguages - French I Tutorial</a></li>
					</ul>
					<br />
					
				</li>
				
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
				<ul>
					<li>Texts and vocabulary terms with Unicode characters outside the <a href="https://en.wikipedia.org/wiki/Plane_(Unicode)#Basic_Multilingual_Plane" target="_blank">Basic Multilingual Plane</a> (BMP; U+0000 to U+FFFF), i.e. with Unicode characters U+10000 and higher, are not supported. Therefore, characters for almost all modern languages, and a large number of symbols, are supported; but historic scripts, certain symbols and notations, and	Emojis are not supported. 
					</li>
				</ul>
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
				<ul>
					<li>This section shows some language setups ("RegExp Split Sentences", "RegExp Word Characters", "Make each character a word", "Remove spaces") for different languages. They are only recommendations, and you may change them according to your needs (and texts). See also <a href="#go1">here</a>.
					<br /><br /></li>
					
					<li>If you are unsure, try the "Language Settings Wizard" first. Later you can adjust the settings.
						<br />
						<br />
					</li>

					<li>Please inform yourself about Unicode <a href="http://en.wikipedia.org/wiki/Unicode" target="_blank">here (general information)</a> and <a href="http://unicode.coeurlumiere.com/" target="_blank">here (Table of Unicode characters)</a> and about the characters that occur in the language you learn!
					<br /><br />
					
						<table class="tab3" cellspacing="0" cellpadding="5">
							<tr class="tr1">
								<th class="th1">
									Language
								</th>
								<th class="th1">
									RegExp
									<br />
									Split
									<br />
									Sentences
								</th>
								<th class="th1">
									RegExp
									<br />
									Word
									<br />
									Characters
								</th>
								<th class="th1">
									Make each
									<br />
									character
									<br />
									a word
								</th>
								<th class="th1">
									Remove
									<br />
									spaces
								</th>
							</tr>

							<tr class="tr1">
								<td class="td1">
									Latin and all languages
									<br />
									with a Latin derived alphabet
									<br />
									(English, French, German, etc.)
								</td>
								<td class="td1">
									.!?:;
								</td>
								<td class="td1">
									a-zA-ZÀ-ÖØ-öø-ȳ
								</td>
								<td class="td1">
									No
								</td>
								<td class="td1">
									No
								</td>
							</tr>

							<tr class="tr1">
								<td class="td1">
									Languages with a
									<br />
									Cyrillic-derived alphabet
									<br />
									(Russian, Bulgarian, Ukrainian, etc.)
								</td>
								<td class="td1">
									.!?:;
								</td>
								<td class="td1">
									a-zA-ZÀ-ÖØ-öø-ȳЀ-ӹ
								</td>
								<td class="td1">
									No
								</td>
								<td class="td1">
									No
								</td>
							</tr>

							<tr class="tr1">
								<td class="td1">
									Greek
								</td>
								<td class="td1">
									.!?:;
								</td>
								<td class="td1">
									\x{0370}-\x{03FF}\x{1F00}-\x{1FFF}
								</td>
								<td class="td1">
									No
								</td>
								<td class="td1">
									No
								</td>
							</tr>

							<tr class="tr1">
								<td class="td1">
									Hebrew (Right-To-Left = Yes)
								</td>
								<td class="td1">
									.!?:;
								</td>
								<td class="td1">
									\x{0590}-\x{05FF}
								</td>
								<td class="td1">
									No
								</td>
								<td class="td1">
									No
								</td>
							</tr>
						 
							<tr class="tr1">
								<td class="td1">
									Thai
								</td>
								<td class="td1">
									.!?:;
								</td>
								<td class="td1">
									ก-๛
								</td>
								<td class="td1">
									No
								</td>
								<td class="td1">
									Yes
								</td>
							</tr>

							<tr class="tr1">
								<td class="td1">
									Chinese
								</td>
								<td class="td1">
									.!?:;。！？：；
								</td>
								<td class="td1">
									一-龥
								</td>
								<td class="td1">
									Yes or No
								</td>
								<td class="td1">
									Yes
								</td>
							</tr>

							<tr class="tr1">
								<td class="td1">
									Japanese
								</td>
								<td class="td1">
									.!?:;。！？：；
								</td>
								<td class="td1">
									一-龥ぁ-ヾ
								</td>
								<td class="td1">
									Yes or No
								</td>
								<td class="td1">
									Yes
								</td>
							</tr>

							<tr class="tr1">
								<td class="td1">
									Korean
								</td>
								<td class="td1">
									.!?:;。！？：；
								</td>
								<td class="td1">
									가-힣ᄀ-ᇂ
								</td>
								<td class="td1">
									No
								</td>
								<td class="td1">
									No or Yes
								</td>
							</tr>
						</table>

						<br />
					</li>

					<li>"\'" = Apostrophe, and/or "\-" = Dash, may be added to "RegExp Word Characters", then words like "aujourd'hui" or "non-government-owned" are one word, instead of two or more single words. If you omit "\'" and/or "\-" here, you can still create a multi-word expression "aujourd'hui", etc., later.
						<br />
						<br />
					</li>
					
					<li>":" and ";" may be omitted in "RegExp Split Sentences", but longer example sentences may result from this.
						<br />
						<br />
					</li>

					<li>"Make each character a word" = "Yes" should only be set in Chinese, Japanese, and similar languages. Normally words are split by any non-word character or whitespace. If you choose "Yes", then you do not need to insert spaces to specify word endings. If you choose "No", then you must prepare texts without whitespace by inserting whitespace to specify words. If you are a beginner, "Yes" may be better for you. If you are an advanced learner, and you have a possibility to prepare a text in the above described way, then "No" may be better for you.
						<br />
						<br />
					</li>

					<li>"Remove spaces" = "Yes" should only be set in Chinese, Japanese, and similar languages to remove whitespace that has been automatically or manually inserted to specify words.
					</li>

				</ul>
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
				<ul>
					<li>Important: Before using the keyboard you must set the focus within the frame by clicking once on the frame!<br /><br /></li>
					<li>Key Bindings in the TEXT Frame<br />
					<table class="tab3" cellspacing="0" cellpadding="5">
					<tr class="tr1"><th class="th1">Key(s)</th><th class="th1">Action(s)</th></tr>
					<tr class="tr1"><td class="td1">RETURN</td>
					<td class="td1">The next UNKNOWN (blue) word in the text will be shown for creation</td></tr>
					<tr class="tr1"><td class="td1">RIGHT or SPACE</td>
					<td class="td1">Mark next SAVED (non-blue) term (*)</td></tr>
					<tr class="tr1"><td class="td1">LEFT</td>
					<td class="td1">Mark previous SAVED (non-blue) term (*)</td></tr>
					<tr class="tr1"><td class="td1">HOME</td>
					<td class="td1">Mark first SAVED (non-blue) term (*)</td></tr>
					<tr class="tr1"><td class="td1">END</td>
					<td class="td1">Mark last SAVED (non-blue) term (*)</td></tr>
					<tr class="tr1"><td class="td1">1, 2, 3, 4, 5</td>
					<td class="td1">Set status of marked term to 1, 2, 3, 4, or 5</td></tr>
					<tr class="tr1"><td class="td1">I</td>
					<td class="td1">Set status of marked term to "Ignored"</td></tr>
					<tr class="tr1"><td class="td1">W</td>
					<td class="td1">Set status of marked term to "Well Known"</td></tr>
					<tr class="tr1"><td class="td1">E</td>
					<td class="td1">Edit marked term</td></tr>
					<tr class="tr1"><td class="td1">G</td>
					<td class="td1">Edit marked term and open Google Translate</td></tr>
					<tr class="tr1"><td class="td1">J</td>
					<td class="td1">Edit marked term and open Google Image Search</td></tr>
					<tr class="tr1"><td class="td1">A</td>
					<td class="td1">Set audio position according to position of marked term.</td></tr>
					<tr class="tr1"><td class="td1">T</td>
					<td class="td1">Translate sentence</td></tr>
					<tr class="tr1"><td class="td1">P</td>
					<td class="td1">Pronounce term</td></tr>
					<tr class="tr1"><td class="td1">ESC</td>
					<td class="td1">Reset marked term(s)</td></tr>
					</table>
					(*) Only saved terms with the status(es) defined/filtered in the settings are visited and marked!<br /><br />
					</li>
					<li>Key Bindings in the TEST Frame<br />
					<table class="tab3" cellspacing="0" cellpadding="5">
					<tr class="tr1"><th class="th1">Key(s)</th><th class="th1">Action(s)</th></tr>
					<tr class="tr1"><td class="td1">SPACE</td>
					<td class="td1">Show solution</td></tr>
					<tr class="tr1"><td class="td1">UP</td>
					<td class="td1">Set status of tested term to (old status plus 1)</td></tr>
					<tr class="tr1"><td class="td1">DOWN</td>
					<td class="td1">Set status of tested term to (old status minus 1)</td></tr>
					<tr class="tr1"><td class="td1">ESC</td>
					<td class="td1">Do not change status of tested term</td></tr>
					<tr class="tr1"><td class="td1">1, 2, 3, 4, 5</td>
					<td class="td1">Set status of tested term to 1, 2, 3, 4, or 5</td></tr>
					<tr class="tr1"><td class="td1">I</td>
					<td class="td1">Set status of tested term to "Ignored"</td></tr>
					<tr class="tr1"><td class="td1">W</td>
					<td class="td1">Set status of tested term to "Well Known"</td></tr>
					<tr class="tr1"><td class="td1">E</td>
					<td class="td1">Edit tested term</td></tr>
					</table>
          </li>
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
