
<tr>
<td class="td1 right">YouTube Video Id:</td>
<td class="td1">
  <input type='text' id='ytVideoId'>
  <input type='button' value='Fetch Text from Youtube' onclick='getYtTextData()'>
  <input type='text' id='ytApiKey' value='<?php echo $YT_API_KEY ?>' style='visibility:collapse' aria='hidden'>
  <p id='ytDataStatus'></p>
</td>
</tr>
<script>
var API_KEY = document.getElementById('ytApiKey').value;
function setYtDataStatus(msg) {
  var el = document.getElementById('ytDataStatus');
  el.textContent = msg;
}

function getYtTextData() {
  setYtDataStatus('Fetching YouTube data...');
  var ytVideoId = document.getElementById('ytVideoId').value;
  var url = `https://www.googleapis.com/youtube/v3/videos?part=snippet&id=${ytVideoId}&key=${API_KEY}`;
  console.log('fetching', url);
  var req = new XMLHttpRequest();
  req.onload = function(e) {
    var res = JSON.parse(req.responseText);
    if (res.items.length == 0) {
      setYtDataStatus('No videos found.');
    } else {
      setYtDataStatus('Success!');
      var snippet = res.items[0].snippet;
      $('[name=TxTitle]')[0].value = snippet.title;
      $('[name=TxText]')[0].value = snippet.description;
      $('[name=TxSourceURI]')[0].value = `https://youtube.com/watch?v=${ytVideoId}`;
    }
  };
  req.open('GET', url);
  req.send();
}
</script>
