<p class="title">
<h1>

<?php  
/* Get page var and query db ; return Title*/
$id = get_query_var('newswire_id', 1 );
$title = "[nwtitle news_id=$id]";
echo $title;
?>

</h1>
</p>

<?php  
/* Get page var and query db ; return Content*/
$id = get_query_var('newswire_id', 1 );
$shortcode = "[nwcontent news_id=$id]";
echo $shortcode;
?>

