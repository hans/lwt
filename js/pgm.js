var ol_textfont =
    '"Lucida Grande",Arial,sans-serif,STHeiti,"Arial Unicode MS",MingLiu',
  ol_textsize = 3,
  ol_sticky = 1,
  ol_captionfont =
    '"Lucida Grande",Arial,sans-serif,STHeiti,"Arial Unicode MS",MingLiu',
  ol_captionsize = 3,
  ol_width = 260,
  ol_close = "Close",
  ol_offsety = 30,
  ol_offsetx = 3,
  ol_fgcolor = "#FFFFE8",
  ol_closecolor = "#FFFFFF";
function run_overlib_status_98(a, c, b, d, e, f, h, g, m, n) {
  var k = "",
    k = b.replace(/.*[?&]sl=([a-zA-Z\-]*)(&.*)*$/, "$1");
  k == b && (k = "");
  return overlib(
    "<b>" +
      escape_html_chars_2(d, k, n) +
      "</b><br /> " +
      make_overlib_link_new_word(e, f, g) +
      " | " +
      make_overlib_link_delete_word(e, g) +
      make_overlib_link_new_multiword(e, f, m) +
      " <br /> " +
      make_overlib_link_wb(a, c, b, h, e, f),
    CAPTION,
    "Word"
  );
}
function run_overlib_status_99(a, c, b, d, e, f, h, g, m, n) {
  var k = "",
    k = b.replace(/.*[?&]sl=([a-zA-Z\-]*)(&.*)*$/, "$1");
  k == b && (k = "");
  return overlib(
    "<b>" +
      escape_html_chars_2(d, k, n) +
      "</b><br /> " +
      make_overlib_link_new_word(e, f, g) +
      " | " +
      make_overlib_link_delete_word(e, g) +
      make_overlib_link_new_multiword(e, f, m) +
      " <br /> " +
      make_overlib_link_wb(a, c, b, h, e, f),
    CAPTION,
    "Word"
  );
}
function run_overlib_status_1_to_5(a, c, b, d, e, f, h, g, m, n, k) {
  var l = "",
    l = b.replace(/.*[?&]sl=([a-zA-Z\-]*)(&.*)*$/, "$1");
  l == b && (l = "");
  return overlib(
    "<b>" +
      escape_html_chars_2(d, l, k) +
      "</b><br /> " +
      make_overlib_link_change_status_all(e, f, g, m) +
      " <br /> " +
      make_overlib_link_edit_word(e, f, g) +
      " | " +
      make_overlib_link_delete_word(e, g) +
      make_overlib_link_new_multiword(e, f, n) +
      " <br /> " +
      make_overlib_link_wb(a, c, b, h, e, f),
    CAPTION,
    make_overlib_link_edit_word_title(
      "Word &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;",
      e,
      f,
      g
    )
  );
}
function run_overlib_status_unknown(a, c, b, d, e, f, h, g) {
  var m = "",
    m = b.replace(/.*[?&]sl=([a-zA-Z\-]*)(&.*)*$/, "$1");
  m == b && (m = "");
  return overlib(
    "<b>" +
      escape_html_chars_with_tts(d, m) +
      "</b><br /> " +
      make_overlib_link_wellknown_word(e, f) +
      " <br /> " +
      make_overlib_link_ignore_word(e, f) +
      make_overlib_link_new_multiword(e, f, g) +
      " <br /> " +
      make_overlib_link_wb(a, c, b, h, e, f),
    CAPTION,
    "New Word"
  );
}
function run_overlib_multiword(a, c, b, d, e, f, h, g, m, n, k) {
  var l = "",
    l = b.replace(/.*[?&]sl=([a-zA-Z\-]*)(&.*)*$/, "$1"),
    p = n.trim();
  l == b && (l = "");
  return overlib(
    "<b>" +
      escape_html_chars_2(d, l, k) +
      "</b><br /> " +
      make_overlib_link_change_status_all(e, f, g, m) +
      " <br /> " +
      make_overlib_link_edit_multiword(p, e, f, g) +
      " | " +
      make_overlib_link_delete_multiword(e, g) +
      " <br /> " +
      make_overlib_link_wb(a, c, b, h, e, f),
    CAPTION,
    make_overlib_link_edit_multiword_title(
      p,
      n.trim() + "-Word-Expression",
      e,
      f,
      g
    )
  );
}
function run_overlib_test(a, c, b, d, e, f, h, g, m, n, k) {
  k = parseInt(g, 10);
  var l = k + 1;
  5 < l && (l = 5);
  var p = k - 1;
  1 > p && (p = 1);
  var q = g + " \u25b6 " + l;
  l == k && (q = l);
  l = g + " \u25b6 " + p;
  p == k && (l = p);
  return overlib(
    (1 == n
      ? "<center><hr noshade size=1 /><b>" +
        (1 <= g && 5 >= g
          ? make_overlib_link_change_status_test(
              d,
              1,
              '<img src="icn/thumb-up.png" title="Got it!" alt="Got it!" /> Got it! [' +
                q +
                "]"
            ) +
            "<hr noshade size=1 />" +
            make_overlib_link_change_status_test(
              d,
              -1,
              '<img src="icn/thumb.png" title="Oops!" alt="Oops!" /> Oops! [' +
                l +
                "]"
            ) +
            "<hr noshade size=1 />"
          : "") +
        make_overlib_link_change_status_alltest(d, g) +
        "</b></center><hr noshade size=1 />"
      : "") +
      "<b>" +
      escape_html_chars_with_tts(
        make_tooltip(e, f, h, g),
        b.replace(/.*[?&]sl=([a-zA-Z\-]*)(&.*)*$/, "$1")
      ) +
      '</b><br /> <a href="edit_tword.php?wid=' +
      d +
      '" target="ro">Edit term</a><br />' +
      createTheDictLink(a, e, "Dict1", "Lookup Term: ") +
      createTheDictLink(c, e, "Dict2", "") +
      createTheDictLink(b, e, "GTr", "") +
      createTheDictLink(b, m, "GTr", "<br />Lookup Sentence:"),
    CAPTION,
    "Got it?"
  );
}
function make_overlib_link_new_multiword(a, c, b) {
  a = make_overlib_link_create_edit_multiword_new(a, c, b);
  "<span></span>" != a && (a = " <br />Expr: " + a);
  return a;
}
function make_overlib_link_wb(a, c, b, d, e, f) {
  return (
    createTheDictLink(a, d, "Dict1", "Lookup Term: ") +
    createTheDictLink(c, d, "Dict2", "") +
    createTheDictLink(b, d, "GTr", "") +
    (1 > f || 1 > e
      ? ""
      : "<br />Lookup Sentence: " + createSentLookupLink(f, e, b, "GTr"))
  );
}
function make_overlib_link_wbnl(a, c, b, d, e, f) {
  return (
    createTheDictLink(a, d, "Dict1", "Term: ") +
    createTheDictLink(c, d, "Dict2", "") +
    createTheDictLink(b, d, "GTr", "") +
    (1 > f || 1 > e
      ? ""
      : " | Sentence: " + createSentLookupLink(f, e, b, "GTr"))
  );
}
function make_overlib_link_wbnl2(a, c, b, d, e) {
  return (
    createTheDictLink(a, d, "Dict1", "Term: ") +
    createTheDictLink(c, d, "Dict2", "") +
    createTheDictLink(b, d, "GTr", "") +
    ("" == e ? "" : createTheDictLink(b, e, "GTr", " | Sentence:"))
  );
}
function make_overlib_link_change_status_all(a, c, b, d) {
  for (var e = "St: ", f = 1; 5 >= f; f++)
    e += make_overlib_link_change_status(a, c, b, d, f);
  e += make_overlib_link_change_status(a, c, b, d, 99);
  return (e += make_overlib_link_change_status(a, c, b, d, 98));
}
function make_overlib_link_change_status_alltest(a, c) {
  for (var b = "", d = 1; 5 >= d; d++)
    b += make_overlib_link_change_status_test2(a, c, d);
  b += make_overlib_link_change_status_test2(a, c, 99);
  return (b += make_overlib_link_change_status_test2(a, c, 98));
}
function make_overlib_link_change_status(a, c, b, d, e) {
  return d == e
    ? '<span title="' + getStatusName(d) + '">\u25c6</span>'
    : ' <a href="set_word_status.php?tid=' +
        a +
        "&amp;ord=" +
        c +
        "&amp;wid=" +
        b +
        "&amp;status=" +
        e +
        '" target="ro"><span title="' +
        getStatusName(e) +
        '">[' +
        getStatusAbbr(e) +
        "]</span></a> ";
}
function make_overlib_link_change_status_test2(a, c, b) {
  return c == b
    ? ' <a href="set_test_status.php?wid=' +
        a +
        "&amp;status=" +
        b +
        '" target="ro"><span title="' +
        getStatusName(b) +
        '">[\u25c6]</span></a> '
    : ' <a href="set_test_status.php?wid=' +
        a +
        "&amp;status=" +
        b +
        '" target="ro"><span title="' +
        getStatusName(b) +
        '">[' +
        getStatusAbbr(b) +
        "]</span></a> ";
}
function make_overlib_link_change_status_test(a, c, b) {
  return (
    ' <a href="set_test_status.php?wid=' +
    a +
    "&amp;stchange=" +
    c +
    '" target="ro">' +
    b +
    "</a> "
  );
}
function make_overlib_link_new_word(a, c, b) {
  return (
    ' <a class="edit" href="edit_word.php?tid=' +
    a +
    "&amp;ord=" +
    c +
    "&amp;wid=" +
    b +
    '" target="ro">Learn term</a> '
  );
}
function make_overlib_link_edit_multiword(a, c, b, d) {
  return (
    ' <a class="edit" href="edit_mword.php?tid=' +
    c +
    "&amp;len=" +
    a +
    "&amp;ord=" +
    b +
    "&amp;wid=" +
    d +
    '" target="ro">Edit term</a> '
  );
}
function make_overlib_link_edit_multiword_title(a, c, b, d, e) {
  return (
    '<a style="color:yellow" href="edit_mword.php?tid=' +
    b +
    "&amp;len=" +
    a +
    "&amp;ord=" +
    d +
    "&amp;wid=" +
    e +
    '" target="ro">' +
    c +
    "</a>"
  );
}
function make_overlib_link_create_edit_multiword(a, c, b, d) {
  return (
    ' <a href="edit_mword.php?tid=' +
    c +
    "&amp;ord=" +
    b +
    "&amp;txt=" +
    d +
    "&amp;len=" +
    a +
    '" target="ro">' +
    a +
    ".." +
    escape_html_chars(d.substr(-2).trim()) +
    "</a> "
  );
}
function make_overlib_link_create_edit_multiword_rtl(a, c, b, d) {
  return (
    ' <a dir="rtl" href="edit_mword.php?tid=' +
    c +
    "&amp;ord=" +
    b +
    "&amp;txt=" +
    d +
    "&amp;len=" +
    a +
    '" target="ro">' +
    a +
    ".." +
    escape_html_chars(d.substr(-2).trim()) +
    "</a> "
  );
}
function make_overlib_link_create_edit_multiword_new(a, c, b) {
  var d = $("#ID-" + c + "-1").text(),
    e = 2,
    f = "<span";
  b && (f += ' dir="rtl"');
  var f = f + ">",
    h = parseInt($("#ID-" + c + "-1").attr("data_pos")) + 250;
  $("#ID-" + c + "-1")
    .nextAll('[id$="-1"]')
    .each(function () {
      d += $(this).text();
      if ($(this).attr("data_pos") < h) {
        var b =
          ' <a href="edit_mword.php?tid=' +
          a +
          "&amp;len=" +
          e +
          "&amp;ord=" +
          c +
          "&amp;txt=" +
          d +
          '" target="ro" title="' +
          d +
          '">' +
          e +
          ".." +
          escape_html_chars(d.substr(-2).trim()) +
          "</a> ";
        f += b;
        e += 1;
      }
    });
  return f + "</span>";
}
function refresh_text(a) {
  var c = 0,
    b = 0;
  a.nextAll()
    .addBack()
    .each(function () {
      var a = $(this).attr("id").split("-"),
        e = parseInt(a[2]),
        a = parseInt(a[1]),
        f = parseInt($(this).attr("data_wid"));
      if (a > b && 0 < b) return !1;
      0 < f
        ? ((f = a + 2 * e - 1),
          f > b && (b = f),
          a > c
            ? ((c = f - 1), $(this).removeClass("hide"))
            : $(this).addClass("hide"))
        : a <= c && $(this).addClass("hide");
      a > c && 1 == e && $(this).removeClass("hide");
    });
}
function make_overlib_link_edit_word(a, c, b) {
  return (
    ' <a class="edit" href="edit_word.php?tid=' +
    a +
    "&amp;ord=" +
    c +
    "&amp;wid=" +
    b +
    '" target="ro">Edit term</a> '
  );
}
function make_overlib_link_edit_word_title(a, c, b, d) {
  return (
    '<a style="color:yellow" href="edit_word.php?tid=' +
    c +
    "&amp;ord=" +
    b +
    "&amp;wid=" +
    d +
    '" target="ro">' +
    a +
    "</a>"
  );
}
function make_overlib_link_delete_word(a, c) {
  return (
    ' <a href="delete_word.php?wid=' +
    c +
    "&amp;tid=" +
    a +
    '" target="ro">Delete term</a> '
  );
}
function make_overlib_link_delete_multiword(a, c) {
  return (
    ' <a href="delete_mword.php?wid=' +
    c +
    "&amp;tid=" +
    a +
    '" target="ro">Delete term</a> '
  );
}
function make_overlib_link_wellknown_word(a, c) {
  return (
    ' <a href="insert_word_wellknown.php?tid=' +
    a +
    "&amp;ord=" +
    c +
    '" target="ro">I know this term well</a> '
  );
}
function make_overlib_link_ignore_word(a, c) {
  return (
    ' <a href="insert_word_ignore.php?tid=' +
    a +
    "&amp;ord=" +
    c +
    '" target="ro">Ignore this term</a> '
  );
}
String.prototype.rtrim = function () {
  return this.replace(/\s+$/, "");
};
String.prototype.ltrim = function () {
  return this.replace(/^\s+/, "");
};
String.prototype.trim = function (a) {
  return this.ltrim().rtrim();
};
function getStatusName(a) {
  return STATUSES[a] ? STATUSES[a].name : "Unknown";
}
function getStatusAbbr(a) {
  return STATUSES[a] ? STATUSES[a].abbr : "?";
}
function translateSentence(a, c) {
  if ("undefined" != typeof c && "" != a) {
    var b = c.value;
    "string" == typeof b &&
      b.length &&
      (window.parent.frames.ru.location.href = createTheDictUrl(
        a,
        b.replace(/[{}]/g, "")
      ));
  }
}
function translateSentence2(a, c) {
  if ("undefined" != typeof c && "" != a) {
    var b = c.value;
    "string" == typeof b &&
      b.length &&
      owin(createTheDictUrl(a, b.replace(/[{}]/g, "")));
  }
}
function translateWord(a, c) {
  if ("undefined" != typeof c && "" != a) {
    var b = c.value;
    "string" == typeof b &&
      b.length &&
      (window.parent.frames.ru.location.href = createTheDictUrl(a, b));
  }
}
function translateWord2(a, c) {
  if ("undefined" != typeof c && "" != a) {
    var b = c.value;
    "string" == typeof b && b.length && owin(createTheDictUrl(a, b));
  }
}
function translateWord3(a, c) {
  owin(createTheDictUrl(a, c));
}
function make_tooltip(a, c, b, d) {
  "" != b && ("" != a && (a += "\r"), (a += "\u25b6 " + b));
  "" != c && "*" != c && ("" != a && (a += "\r"), (a += "\u25b6 " + c));
  "" != a && (a += "\r");
  return (a += "\u25b6 " + getStatusName(d) + " [" + getStatusAbbr(d) + "]");
}
function escape_html_chars_2(a, c, b) {
  a = "" != c ? escape_html_chars_with_tts(a, c) : escape_html_chars(a);
  return "" != b && "*" != b
    ? ((c = escape_html_chars(b)),
      (c = new RegExp(
        "(<br />\u25b6[^<]*[" +
          DELIMITER +
          "][ ]{0,1}|<br />\u25b6 )(" +
          c.replace(/[-\/\\^$*+?.()|[\]{}]/g, "\\$&") +
          ")(<|[ ]{0,1}[" +
          DELIMITER +
          "]| \\[)([^<]*[<]*br />\u25b6 [^<]*)$",
        ""
      )),
      a.replace(c, '$1<span style="color:red">$2</span>$3$4'))
    : a;
}
function owin(a) {
  window.open(
    a,
    "dictwin",
    "width=800, height=400, scrollbars=yes, menubar=no, resizable=yes, status=no"
  );
}
function oewin(a) {
  window.open(
    a,
    "editwin",
    "width=800, height=600, scrollbars=yes, menubar=no, resizable=yes, status=no"
  );
}
function createTheDictUrl(a, c) {
  var b = a.trim(),
    d = c.trim();
  return "trans.php?x=2&i=" + escape(b) + "&t=" + d;
}
function createTheDictLink(a, c, b, d) {
  a = a.trim();
  c = c.trim();
  b = b.trim();
  d = d.trim();
  var e = "";
  "" != a &&
    "" != b &&
    (e =
      "*" == a.substr(0, 1)
        ? " " +
          d +
          ' <span class="click" onclick="owin(\'' +
          createTheDictUrl(a.substring(1), escape_apostrophes(c)) +
          "');if($('a.edit')[0]){window.parent.frames['ro'].location.href =$('a.edit').eq(0).attr('href') + '&nodict';}\">" +
          b +
          "</span> "
        : " " +
          d +
          " <a onclick=\"{if($('a.edit')[0]){window.parent.frames['ro'].location.href =$('a.edit').eq(0).attr('href') + '&nodict';}setTimeout(function(){window.parent.frames['ru'].location.href ='" +
          createTheDictUrl(a, c) +
          '\'}, 10);}" href="javascript:{}" target="ru">' +
          b +
          "</a> ");
  return e;
}
function createSentLookupLink(a, c, b, d) {
  b = b.trim();
  d = d.trim();
  var e = "";
  if ("" != b && "" != d)
    if ("*http://" == b.substr(0, 8) || "*https://" == b.substr(0, 9))
      e =
        ' <span class="click" onclick="owin(\'trans.php?x=1&i=' +
        a +
        "&t=" +
        c +
        "');\">" +
        d +
        "</span> ";
    else if (
      "http://" == b.substr(0, 7) ||
      "https://" == b.substr(0, 8) ||
      "ggl.php" == b.substr(0, 7)
    )
      e =
        ' <a href="trans.php?x=1&i=' +
        a +
        "&t=" +
        c +
        '" target="ru">' +
        d +
        "</a> ";
  return e;
}
function escape_html_chars_with_tts(a, c) {
  return (
    '<span id="textToSpeak" style="cursor:pointer" title="Click on expression for pronunciation">' +
    a
      .replace(/&/g, "%AMP%")
      .replace(/</g, "&#060;")
      .replace(/>/g, "&#062;")
      .replace(/"/g, "&#034;")
      .replace(/'/g, "&#039;")
      .replace(/%AMP%/g, "&#038;")
      .replace(/\x0d/g, "<br />")
      .replace(/<br/, "</span><br")
  );
}
function escape_html_chars(a) {
  return a
    .replace(/&/g, "%AMP%")
    .replace(/</g, "&#060;")
    .replace(/>/g, "&#062;")
    .replace(/"/g, "&#034;")
    .replace(/'/g, "&#039;")
    .replace(/%AMP%/g, "&#038;")
    .replace(/\x0d/g, "<br />");
}
function escape_apostrophes(a) {
  return a.replace(/'/g, "\\'");
}
function selectToggle(a, c) {
  for (var b = document.forms[c], d = 0; d < b.length; d++)
    b.elements[d].checked = a ? "checked" : "";
  markClick();
}
function multiActionGo(a, c) {
  if ("undefined" != typeof a && "undefined" != typeof c) {
    var b = c.value,
      d = c.options[c.selectedIndex].text;
    if ("string" == typeof b)
      if ("addtag" == b || "deltag" == b) {
        for (var b = 1, e = ""; b; )
          (e = prompt(
            "*** " +
              d +
              " ***\n\n*** " +
              $("input.markcheck:checked").length +
              " Record(s) will be affected ***\n\nPlease enter one tag (20 char. max., no spaces, no commas -- or leave empty to cancel:",
            e
          )),
            "object" == typeof e && (e = ""),
            0 < e.indexOf(" ") || 0 < e.indexOf(",")
              ? alert("Please no spaces or commas!")
              : 20 < e.length
              ? alert("Please no tags longer than 20 char.!")
              : (b = 0);
        "" != e && ((a.data.value = e), a.submit());
      } else
        "del" == b ||
        "smi1" == b ||
        "spl1" == b ||
        "s1" == b ||
        "s5" == b ||
        "s98" == b ||
        "s99" == b ||
        "today" == b ||
        "delsent" == b ||
        "lower" == b ||
        "cap" == b
          ? (e = confirm(
              "*** " +
                d +
                " ***\n\n*** " +
                $("input.markcheck:checked").length +
                " Record(s) will be affected ***\n\nAre you sure?"
            )) && a.submit()
          : a.submit();
    c.value = "";
  }
}
function allActionGo(a, c, b) {
  if ("undefined" != typeof a && "undefined" != typeof c) {
    var d = c.value,
      e = c.options[c.selectedIndex].text;
    if ("string" == typeof d)
      if ("addtagall" == d || "deltagall" == d) {
        for (var d = 1, f = ""; d; )
          (f = prompt(
            "THIS IS AN ACTION ON ALL RECORDS\nON ALL PAGES OF THE CURRENT QUERY!\n\n*** " +
              e +
              " ***\n\n*** " +
              b +
              " Record(s) will be affected ***\n\nPlease enter one tag (20 char. max., no spaces, no commas -- or leave empty to cancel:",
            f
          )),
            "object" == typeof f && (f = ""),
            0 < f.indexOf(" ") || 0 < f.indexOf(",")
              ? alert("Please no spaces or commas!")
              : 20 < f.length
              ? alert("Please no tags longer than 20 char.!")
              : (d = 0);
        "" != f && ((a.data.value = f), a.submit());
      } else
        "delall" == d ||
        "smi1all" == d ||
        "spl1all" == d ||
        "s1all" == d ||
        "s5all" == d ||
        "s98all" == d ||
        "s99all" == d ||
        "todayall" == d ||
        "delsentall" == d ||
        "capall" == d ||
        "lowerall" == d
          ? (f = confirm(
              "THIS IS AN ACTION ON ALL RECORDS\nON ALL PAGES OF THE CURRENT QUERY!\n\n*** " +
                e +
                " ***\n\n*** " +
                b +
                " Record(s) will be affected ***\n\nARE YOU SURE?"
            )) && a.submit()
          : a.submit();
    c.value = "";
  }
}
function areCookiesEnabled() {
  setCookie("test", "none", "", "/", "", "");
  getCookie("test")
    ? ((cookie_set = !0), deleteCookie("test", "/", ""))
    : (cookie_set = !1);
  return cookie_set;
}
function setLang(a, c) {
  location.href =
    "save_setting_redirect.php?k=currentlanguage&v=" +
    a.options[a.selectedIndex].value +
    "&u=" +
    c;
}
function resetAll(a) {
  location.href = "save_setting_redirect.php?k=currentlanguage&v=&u=" + a;
}
function getCookie(a) {
  for (
    var c = document.cookie.split(";"), b = "", d = "", e = "", f = "", f = 0;
    f < c.length;
    f++
  )
    if (((b = c[f].split("=")), (d = b[0].replace(/^\s+|\s+$/g, "")), d == a))
      return 1 < b.length && (e = unescape(b[1].replace(/^\s+|\s+$/g, ""))), e;
  return null;
}
function setCookie(a, c, b, d, e, f) {
  var h = new Date();
  h.setTime(h.getTime());
  b && (b *= 864e5);
  h = new Date(h.getTime() + b);
  document.cookie =
    a +
    "=" +
    escape(c) +
    (b ? ";expires=" + h.toGMTString() : "") +
    (d ? ";path=" + d : "") +
    (e ? ";domain=" + e : "") +
    (f ? ";secure" : "");
}
function deleteCookie(a, c, b) {
  getCookie(a) &&
    (document.cookie =
      a +
      "=" +
      (c ? ";path=" + c : "") +
      (b ? ";domain=" + b : "") +
      ";expires=Thu, 01-Jan-1970 00:00:01 GMT");
}
function iknowall(a) {
  confirm("Are you sure?") &&
    (top.frames.ro.location.href =
      "all_words_wellknown.php?text=" + a + "&stat=99");
}
function ignoreall(a) {
  confirm("Are you sure?") &&
    (top.frames.ro.location.href = "all_words_wellknown.php?stat=98&text=" + a);
}
function check_table_prefix(a) {
  var c = !1,
    b = /^[_a-zA-Z0-9]*$/;
  20 >= a.length && 0 < a.length && a.match(b) && (c = !0);
  c ||
    alert(
      "Table Set Name (= Table Prefix) must\ncontain 1 to 20 characters (only 0-9, a-z, A-Z and _).\nPlease correct your input."
    );
  return c;
}
