<?php
include 'SpellCorrector.php';
// make sure browsers see this page as utf-8 encoded HTML
header('Content-Type: text/html; charset=utf-8');
$div=false;
$correct = "";
$correct1="";
$output = "";
$limit = 10;
$query = isset($_REQUEST['q']) ? $_REQUEST['q'] : false;
$results = false;

if ($query)
{
$choice = isset($_REQUEST['sort'])? $_REQUEST['sort'] : "default";

  // The Apache Solr Client library should be on the include path
  // which is usually most easily accomplished by placing in the
  // same directory as this script ( . or current directory is a default
  // php include path entry in the php.ini)
  require_once('Apache/Solr/Service.php');

  // create a new solr service instance - host, port, and webapp
  // path (all defaults in this example)
  $solr = new Apache_Solr_Service('localhost', 8983, '/solr/crawldata');

  // if magic quotes is enabled then stripslashes will be needed
  if (get_magic_quotes_gpc() == 1)
  {
    $query = stripslashes($query);
  }
  // in production code you'll always want to use a try /catch for any
  // possible exceptions emitted  by searching (i.e. connection
  // problems or a query parsing error)
  try
  {
	if($choice == "default")
		 $additionalParameters=array('sort' => ''/*,'qt' => '/suggest'*/);
	  else{
    $additionalParameters=array('sort' => 'pageRankFile desc'/*,'qt' => '/suggest'*/);
}
$word = explode(" ",$query);
$spell = $word[$word.length-1];
for($i=0;$i<sizeOf($word);$i++){
$che = SpellCorrector::correct($word[$i]) ;
if($correct!="")
$correct = $correct."+".trim($che);
else
$correct = trim($che);
$correct1 = $correct1." ".trim($che);
}
  $correct1 = str_replace("+"," ",$correct);
$div=false;
if($query==$correct1){
	  $results = $solr->search($query, 0, $limit, $additionalParameters);
}
else {
$div =true;
$results = $solr->search($query, 0, $limit, $additionalParameters);
$link = "http://localhost/search.php?q=$correct&sort=$choice";
$output = "Did you mean: <a href='$link'>$correct1</a>";
//console();
}
  }
  catch (Exception $e)
  {
    // in production you'd probably log or email this error to an admin
    // and then show a special message to the user but for this example
    // we're going to show the full exception
    die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
  }
}

?>
<html>
  <head>
    <title>Homework 4</title>
  <link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="http://code.jquery.com/jquery-1.10.2.js"></script>
  <script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
 <script src = "/Porter-Stemmer/PorterStemmer1980.js"></script>
  </head>
  <body>
    <form  accept-charset="utf-8" method="get">
      <label for="q">Search:</label>
      <input id="q" name="q" type="text" value="<?php $input = htmlspecialchars($query, ENT_QUOTES, 'utf-8');echo $input; ?>"/>
      <input type="submit" value="Submit"/>
<br/>

		<input type="radio" name="sort" value="pagerank" <?php if(isset($_REQUEST['sort']) && $choice == "pagerank") { echo 'checked="checked"';} ?>>Page Rank
		<input type="radio" name="sort" value="default" <?php if(isset($_REQUEST['sort']) && $choice == "default") { echo 'checked="checked"';} else {echo 'checked="checked"';}?>>Default
    </form>
<script>
 $(function() {
 var URL_PREFIX = "http://localhost:8983/solr/crawldata/suggest?q=";
 var URL_SUFFIX = "&wt=json&indent=true";
var count=0;
var tags = [];
$("#q").autocomplete({
 source : function(request, response) {
 var correct="",before="";
var query = $("#q").val();
var space =  query.lastIndexOf(' ');
if(query.length-1>space && space!=-1){
correct=query.substr(space+1);
before = query.substr(0,space);
}
else{
correct=query.substr(0); 
}
var URL = URL_PREFIX + correct+ URL_SUFFIX;
 $.ajax({
 url : URL,
 success : function(data) {
var js =data.suggest.suggest;
 var docs = JSON.stringify(js);
 var jsonData = JSON.parse(docs);
var result =jsonData[correct].suggestions;
var j=0;
var stem =[];
for(var i=0;i<5 && j<result.length;i++,j++){

if(result[j].term==correct)
{
i--;
continue;
}

for(var k=0;k<i && i>0;k++){
//console.log(tags[k]+" "+s);console.log(i+" "+k);
if(tags[k].indexOf(result[j].term) >=0){
console.log("in "+tags[k]);
i--;
continue;
}
}
if(result[j].term.indexOf('.')>=0 || result[j].term.indexOf('_')>=0)
{
i--;
continue;
}
var s =/* stemmer*/(result[j].term);
if(stem.length == 5)
break;
if(stem.indexOf(s) == -1)
{
//console.log(s);
stem.push(s);
if(before==""){
tags[i]=s;
}
else
{
tags[i] = before+" ";
tags[i]+=s;
}
}
}
console.log(tags);
 response(tags);
 },
 dataType : 'jsonp',
 jsonp : 'json.wrf'
 });
 },
 minLength : 1
 })
 });
</script>
<?php
if($div){
echo $output;
}
$count =0;
$prev="";
// display results
if ($results)
{

  $total = (int) $results->response->numFound;
  $start = min(1, $total);
  $end = min($limit, $total);

echo "  <div>Results $start -  $end of $total :</div> <ol>";
  // iterate result documents
foreach ($results->response->docs as $doc)
  {  $id = $doc->id;
    $title = $doc->title;
   $size = $doc->stream_size;
   $date = $doc->created;
   $author = $doc->producer;
$size = round($size/1000);
   if($date=="" ||$date==null)
	   $date="N/A";
   if($author=="" ||$author==null)
	   $author="N/A";
   if($title=="" ||$title==null){
	   $title = $doc->dc_title;
	   if($title=="" ||$title==null)
		   $title="N/A";
   }
   $id = str_replace("home/soumyara/IR/download/","",$id);
   $id = str_replace("http@@","",$id);
   $id = str_replace("@","/",$id);
   $id = str_replace("%",".",$id);
   $id = str_replace(".html","",$id);
   $id = str_replace("#","=",$id);
   $id = str_replace(";","?",$id);
if($count==0)
$prev = $id;
else{
$count++;
	if($prev==$id)
		continue;
else
$prev = $id;

}
    echo "  <li>    <p><a href='http://$id' target='_blank'>Webpage</a> $title</p>
	<br/> Size: $size KB Author: $author Date Created: $date</li>";
}
  echo "</ol>";
}
?>

  </body>
</html>
