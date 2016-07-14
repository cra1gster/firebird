<?php
/* Template Name: ttrssResults */
?>
<?php
/*

Custom template derived from page.php
Website uses the Codilight theme

Pulls db query using dbinfo.php ; allows param for resultset restriction

 */

get_header(); ?>
	<div id="content" class="site-content container <?php echo codilight_sidebar_position(); ?>">
		<div class="content-inside">
			<div id="primary" class="content-area">
				<main id="main" class="site-main" role="main">
				<?php 
					// parse the current uri
					$url = $_SERVER['REQUEST_URI'];
					parse_str(parse_url($url,PHP_URL_QUERY),$array);
					//var_dump($array);
					$param=$array['newswire_id'];
					// NB: php param re-mapping
					$filename=$_SERVER["DOCUMENT_ROOT"] . '/dbinfo.php';
					var_dump($filename);
					include ($filename);
				?>
				
				</main><!-- #main -->
			</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
<?php wp_footer(); ?>
