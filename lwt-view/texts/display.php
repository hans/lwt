<p>
    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?new=1">
        <img src="icn/plus-button.png" title="New" alt="New" /> New Text &hellip;
    </a>
</p>

<?php render('texts/_filter', compact('currentlang', 'currentquery',
                                      'currenttag1', 'currenttag12',
                                      'currenttag2', 'recno', 'currentpage',
                                      'pages', 'currentsort')); ?>

<?php if ( $recno == 0 ): ?>
    <p>No texts found.</p>
<?php else: ?>
    <form name="form2" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <input type="hidden" name="data" value="" />

        <table class="tab1" cellspacing="0" cellpadding="5">
            <tr>
                <th class="th1" colspan="2">
                    Multi Actions&nbsp;
                    <img src="icn/lightning.png" title="Multi Actions" alt="Multi Actions" />
                </th>
            </tr>
            <tr>
                <td class="td1 center">
                    <input type="button" value="Mark All" onclick="selectToggle(true,'form2');" />
                    <input type="button" value="Mark None" onclick="selectToggle(false,'form2');" />
                </td>
                <td class="td1 center">
                    Marked Texts:&nbsp;
                    <select name="markaction" id="markaction" disabled="disabled" onchange="multiActionGo(document.form2, document.form2.markaction);">
                        <?php echo get_multipletextactions_selectoptions(); ?>
                    </select>
                </td>
            </tr>
        </table>

        <table class="sortable tab1" cellspacing="0" cellpadding="5">
            <tr>
                <th class="th1 sorttable_nosort">Mark</th>
                <th class="th1 sorttable_nosort">Read<br />&amp;&nbsp;Test</th>
                <th class="th1 sorttable_nosort">Actions</th>
                <?php if ($currentlang == ''): ?><th class="th1 clickable">Lang.</th><?php endif; ?>
                <th class="th1 clickable">Title [Tags] / Audio?</th>
                <th class="th1 sorttable_numeric clickable">Total<br />Words</th>
                <th class="th1 sorttable_numeric clickable">Saved<br />Wo+Ex</th>
                <th class="th1 sorttable_numeric clickable">Unkn.<br />Words</th>
                <th class="th1 sorttable_numeric clickable">Unkn.<br />%</th>
            </tr>
            <?php foreach ( $records as $record ): ?>
                <tr>
                    <!-- Mark -->
                    <td class="td1 center">
                        <a name="rec<?php echo $record['TxID']; ?>">
                            <input name="marked[]" class="markcheck" type="checkbox" value="<?php echo $record['TxID'] ?>" <?php echo checkTest($record['TxID'], 'marked') ?> />
                        </a>
                    </td>

                    <!-- Read & Test -->
                    <td nowrap="nowrap" class="td1 center">
                        &nbsp;
                        <a href="do_text.php?start=<?php echo $record['TxID']; ?>">
                            <img src="icn/book-open-bookmark.png" title="Read" alt="Read" />
                        </a>
                        &nbsp;&nbsp;
                        <a href="do_test.php?text=<?php echo $record['TxID']; ?>">
                            <img src="icn/question-balloon.png" title="Test" alt="Test" />
                        </a>
                        &nbsp;
                    </td>

                    <!-- Actions -->
                    <td nowrap="nowrap" class="td1 center">
                        &nbsp;
                        <a href="print_text.php?text=<?php echo $record['TxID']; ?>">
                            <img src="icn/printer.png" title="Print" alt="Print" />
                        </a>
                        &nbsp;&nbsp;
                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?arch=<?php echo $record['TxID'] ?>">
                            <img src="icn/inbox-download.png" title="Archive" alt="Archive" />
                        </a>
                        &nbsp;&nbsp;
                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?chg=<?php echo $record['TxID']; ?>">
                            <img src="icn/document--pencil.png" title="Edit" alt="Edit" />
                        </a>
                        &nbsp;&nbsp;
                        <span class="click" onclick="if ( confirm('Are you sure?') ) location.href = '<?php echo $_SERVER['PHP_SELF']; ?>?del=<?php echo $record['TxID']; ?>';">
                            <img src="icn/minus-button.png" title="Delete" alt="Delete" />
                        </span>
                        &nbsp;
                    </td>

                    <!-- Language -->
                    <?php if ( $currentlang == '' ): ?>
                        <td class="td1 center">
                            <?php echo $record['LgName']; ?>
                        </td>
                    <?php endif; ?>

                    <!-- Title, Tags, Audio -->
                    <td class="td1 center">
                        <?php echo $record['TxTitle']; ?>
                        &nbsp;
                        <span class="smallgray2">
                            <?php echo $record['taglist']; ?>
                        </span>
                        &nbsp;
                        <?php if ( $record['audio'] != '' ): ?>
                            <img src="icn/speaker-volume.png" title="With Audio" alt="With Audio" />
                        <?php endif; ?>
                    </td>

                    <?php if ( $showCounts ): ?>
                        <td class="td1 center">
                            <span title="Total"><?php echo $record['total_words']; ?></span>
                        </td>

                        <td class="td1 center">
                            <span title="Saved" class="status4">
                                <?php if ( $record['worked_all'] > 0 ): ?>
                                    <a href="edit_words.php?page=1&amp;query=&amp;status=&amp;tag12=0&amp;tag2=&amp;tag1=&amp;text=<?php echo $record['TxID']; ?>">
                                        <?php echo $record['worked_words'] . '+' . $record['worked_expr']; ?>
                                    </a>
                                <?php else: ?>
                                    0
                                <?php endif; ?>
                            </span>
                        </td>

                        <td class="td1 center">
                            <span title="Unknown" class="status0"><?php echo $record['todo_words']; ?></span>
                        </td>

                        <td class="td1 center">
                            <span title="Unknown (%)"><?php echo $record['percent_unknown']; ?></span>
                        </td>
                    <?php else: ?>
                        <td class="td1 center">
                            <span id="total-<?php echo $record['TxID']; ?>"></span>
                        </td>

                        <td class="td1 center">
                            <span data-id="<?php echo $record['TxID']; ?>" id="saved-<?php echo $record['TxID']; ?>">
                                <span class="click" onclick="do_ajax_word_counts();">
                                    <img src="icn/lightning.png" title="View Word Counts" alt="View Word Counts" />
                                </span>
                            </span>
                        </td>

                        <td class="td1 center">
                            <span id="todo-<?php echo $record['TxID']; ?>"></span>
                        </td>

                        <td class="td1 center">
                            <span id="todop-<?php echo $record['TxID']; ?>"></span>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    </form>

    <?php if ( $pages > 1 ): ?>
        <table class="tab1" cellspacing="0" cellpadding="5">
            <tr>
                <th class="th1" nowrap="nowrap">
                    <?php echo $recno; ?> Text<?php echo ( $recno == 1 ? '' : 's' ); ?>
                </th>
                <th class="th1" nowrap="nowrap">
                    <?php makePager($currentpage, $pages, 'edit_texts.php', 'form1'); ?>
                </th>
            </tr>
        </table>
    <?php endif; ?>
<?php endif; ?>

<p>
    <input type="button" value="Archived Texts" onclick="location.href = 'edit_archivedtexts.php?query=&amp;page=1';" />
</p>
<?php pageend(); ?>