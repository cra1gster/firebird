<?php
/* Template Name: ttrssResults */
?>
<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Codilight_Lite
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
