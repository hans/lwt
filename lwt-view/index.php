<?php pagestart('Home', false); ?>
<?php if ( $langcnt == 0 ): ?>
    <table class="tab3" cellspacing="0" cellpadding="5">
        <tr>
            <th class="th1">
                Hint: The database seems to be empty.<br/>
                <a href="install_demo.php">You may install the LWT demo database, </a><br/>
                or<br/>
                <a href="edit_languages.php?new=1">define the first language you want to learn.</a>
            </th>
        </tr>
    </table>
<?php endif; ?>

<script type="text/javascript">
    if ( !areCookiesEnabled() )
        document.write('<p class="red">*** Cookies are not enabled! Please enable! ***</p>');
</script>

<?php if ( $langcnt > 0 ): ?>
    <ul>
        <li>
            Language:
            <select id="filterlang" onchange="setLang(document.getElementById('filterlang'), 'index.php');"">
                <?php echo get_languages_selectoptions($currentlang, '[Select...]'); ?>
            </select>
        </li>
    </ul>

    <?php if ( $currenttext != '' && isset($txttit) ): ?>
        <ul>
            <li>
                My last Text (in <?php echo $lngname; ?>):<br/>
                <em><?php echo $txttit; ?></em><br/>

                <a href="do_text.php?start=<?php echo $currenttext; ?>">
                    <img src="icn/book-open-bookmark.png" title="Read" alt="Read" />
                    &nbsp;Read
                </a>

                <a href="do_test.php?text=<?php echo $currenttext; ?>">
                    <img src="icn/question-balloon.php" title="Test" alt="Test" />
                    &nbsp;Test
                </a>

                <a href="print_text.php?text=<?php echo $currenttext; ?>">
                    <img src="icn/printer.png" title="Print" alt="Print" />
                    &nbsp;Print
                </a>
            </li>
        </ul>
    <?php endif; ?>
<?php endif; ?>

<ul>
    <li><a href="edit_texts.php">My Texts</a></li>
    <li><a href="edit_archivedtexts.php">My Text Archive</a></li>
    <li><a href="edit_texttags.php">My Text Tags</a><br/><br/></li>
    <li><a href="edit_languages.php">My Languages</a><br /><br /></li>
    <li><a href="edit_words.php">My Terms (Words and Expressions)</a></li>
    <li><a href="edit_tags.php">My Term Tags</a><br /><br /></li>
    <li><a href="statistics.php">My Statistics</a><br /><br /></li>
    <li><a href="check_text.php">Check a Text</a></li>
    <li><a href="upload_words.php">Import Terms</a></li>
    <li><a href="backup_restore.php">Backup/Restore LWT Database</a><br /><br /></li>
    <li><a href="settings.php">Settings/Preferences</a><br /><br /></li>
    <li><a href="info.htm">Help/Information</a></li>
    <li><a href="mobile.php">Mobile LWT (Experimental)</a></li>
</ul>

<p class="smallgray graydotted">&nbsp;</p>

<table>
    <tr>
        <td class="width50px">
            <a target="_blank" href="http://en.wikipedia.org/wiki/Public_domain_software">
                <img alt="Public Domain" src="img/public_domain.png" />
            </a>
        </td>
        <td>
            <p class="smallgray">
                <a href="http://lwt.sourceforge.net/" target="_blank">"Learning with Texts" (LWT)</a> is released into the Public Domain. This applies worldwide.<br />In case this is not legally possible, any entity is granted the right to use this work for any purpose,<br />without any conditions, unless such conditions are required by law.<br />

                This is <b>LWT <?php echo get_version(); ?></b> / Database: <b><?php echo LWT_DB_NAME; ?></b> on <b><?php echo LWT_SERVER; ?></b> / DB-Size: <b><?php echo $mb; ?> MB</b>
            </p>
        </td>
    </tr>
</table>
<?php pageend(); ?>
