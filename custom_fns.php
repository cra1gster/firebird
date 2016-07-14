<?php
// References: http://bit.ly/29JEQDB
//-----------------------------------------------
/* Test Function - Lorem */

function lorem_function() {
  return 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec nec nulla vitae lacus mattis volutpat eu at sapien. Nunc interdum congue libero, quis laoreet elit sagittis ut. Pellentesque lacus erat, dictum condimentum pharetra vel, malesuada volutpat risus. Nunc sit amet risus dolor. Etiam posuere tellus nisl. Integer lorem ligula, tempor eu laoreet ac, eleifend quis diam. Proin cursus, nibh eu vehicula varius, lacus elit eleifend elit, eget commodo ante felis at neque. Integer sit amet justo sed elit porta convallis a at metus. Suspendisse molestie turpis pulvinar nisl tincidunt quis fringilla enim lobortis. Curabitur placerat quam ac sem venenatis blandit. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam sed ligula nisl. Nam ullamcorper elit id magna hendrerit sit amet dignissim elit sodales. Aenean accumsan consectetur rutrum.';
}

add_shortcode('lorem', 'lorem_function');
//------------------------------------------------

// Return title of newswire item 

function eg_newstitle($atts) {


     extract(shortcode_atts( array('news_id' => ''), $atts ));

    global $wpdb;
    $rssdb= new wpdb('rss_reader','rss_reader','ttrss','localhost');
    $result = $rssdb->get_var ( "SELECT title FROM ttrss_entries where id=$news_id" );
  
    if(!$result)
      {return "Oops! Article ($news_id) not found in database.";}
    else
      {	return $result;}

}
//--------------------------------------------------
// Return Newswire item content
function eg_newswire($atts) {


     extract(shortcode_atts( array('news_id' => ''), $atts ));

    global $wpdb;
    $rssdb= new wpdb('rss_reader','rss_reader','ttrss','localhost');
    $result = $rssdb->get_var ( "SELECT content FROM ttrss_entries where id=$news_id" );
  
    if(!$result)
      {return "Oops! Article ($news_id) not found in database.";}
    else
      { return $result;}

}
//--------------------------------------------------

add_shortcode('newswire', 'eg_newswire');
add_shortcode('newstitle','eg_newstitle');

// Handle passing of param to WP page

function add_query_vars($aVars) {
$aVars[] = "newswire_id"; // newswire id in  URL
return $aVars;
}
 
// hook add_query_vars function into query_vars
add_filter('query_vars', 'add_query_vars');

//-------------------------------------------------

// Javascript Hooks

function scripts() {
if ( !is_admin() ) {
	 // this if statement will insure the following code only gets added to your wp site and not the admin page cause your code has no business in the admin page right unless that's your intentions
	// jquery
		wp_deregister_script('jquery'); // this deregisters the current jquery included in wordpress
		wp_register_script('jquery', ('http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js'), false); // this registers the replacement jquery
		wp_enqueue_script('jquery'); // you can either let wp insert this for you or just delete this and add it directly to your template
	// your own script
		//wp_register_script('yourscript', ( get_bloginfo('template_url') . '/yourscript.js'), false); //first register your custom script
		//wp_enqueue_script('mootools-core', get_bloginfo('template_url') .'/assets/js/mootools-core.js', false, '1.1.1');
		wp_enqueue_script('swfobject'); // then let wp insert it for you or just delete this and add it directly to your template
        // just in case your also interested
		wp_register_script('yourJqueryScript', ( get_bloginfo('template_url') . '/yourJquery.js'), array('jquery')); // this last part-( array('jquery') )is added in case your script needs to be included after jquery
		wp_enqueue_script('yourJqueryScript'); // then print. it will be added after jquery is added
	}
}
add_action( 'wp_print_scripts', 'scripts'); // now just run the function
?>
