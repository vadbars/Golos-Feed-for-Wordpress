<?php 
/*
Plugin Name: Golos Feed
#Plugin URI: https://golos.com/golos/@wordpress-tips/golos-for-wordpress-1-display-your-golos-blog-in-your-wordpress-website-with-this-free-plugin
Description: A simple Wordpress plugin that displays a feed of your Golos posts.
Version: 0.1
Author: Vadbars
Author URI: https://vadbars.ru/
License: GPLv3 or later
Text Domain: golos-feed

Copyright 2017  vadbars.
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'MNSFVER', '1.0.1' );

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
//Include admin
include dirname( __FILE__ ) .'/golos-feed-admin.php';

// Add shortcodes
add_shortcode('golos-feed', 'display_golos');

function display_golos($atts, $content = null) {
	
	STATIC $i = 0;
	$i++;
	
    /******************* SHORTCODE OPTIONS ********************/

    $options = get_option('mn_golos_settings');
    
    //Pass in shortcode attributes
    $atts = shortcode_atts(
    array(
        'username' => isset($options[ 'mn_golos_username' ]) ? $options[ 'mn_golos_username' ] : '',
		'ajaxtheme' => isset($options[ 'mn_golos_ajax_theme' ]) ? $options[ 'mn_golos_ajax_theme' ] : '',
		'postscount' => isset($options[ 'mn_golos_posts_count' ]) ? $options[ 'mn_golos_posts_count' ] : '',
		'postimage' => isset($options[ 'mn_golos_post_image' ]) ? $options[ 'mn_golos_post_image' ] : '',
        'posttitle' => isset($options[ 'mn_golos_post_title' ]) ? $options[ 'mn_golos_post_title' ] : '',
        'postcontent' => isset($options[ 'mn_golos_post_content' ]) ? $options[ 'mn_golos_post_content' ] : '',
        'wordlimit' => isset($options[ 'mn_golos_word_limit' ]) ? $options[ 'mn_golos_word_limit' ] : '',
        'postreward' => isset($options[ 'mn_golos_post_reward' ]) ? $options[ 'mn_golos_post_reward' ] : '',
        'postdate' => isset($options[ 'mn_golos_post_date' ]) ? $options[ 'mn_golos_post_date' ] : '',
        'postauthor' => isset($options[ 'mn_golos_post_author' ]) ? $options[ 'mn_golos_post_author' ] : '',
        'posttag' => isset($options[ 'mn_golos_post_tag' ]) ? $options[ 'mn_golos_post_tag' ] : '',
        'postvotes' => isset($options[ 'mn_golos_post_votes' ]) ? $options[ 'mn_golos_post_votes' ] : '',
        'postreplies' => isset($options[ 'mn_golos_post_replies' ]) ? $options[ 'mn_golos_post_replies' ] : ''
    ), $atts);

    /******************* VARS ********************/

    //General
    $mn_golos_username = trim($atts['username']);

    //Post Settings
	$mn_golos_posts_count = $atts['postscount'];

    //Ajax theme
    $mn_golos_ajax_theme = $atts['ajaxtheme'];
    ( $mn_golos_ajax_theme == 'on' || $mn_golos_ajax_theme == 'true' || $mn_golos_ajax_theme == true ) ? $mn_golos_ajax_theme = true : $mn_golos_ajax_theme = false;

    /******************* CONTENT ********************/

    $mn_golos_content = '<div id="mn_steem_feed" class="sfi">';

    //Error messages
    $mn_golos_error = false;
    if( empty($mn_golos_username) || !isset($mn_golos_username) ){
        $mn_golos_content .= '<div class="mn_steem_feed_error"><p>'.__('Please enter a username on the golos Feed plugin Settings page', 'golos-feed' ).'</p></div>';
        $mn_golos_error = true;
    }
    
    $mn_golos_content .= '</div>'; //End #mn_golos
	 	
	 //If using an ajax theme then add the JS to the bottom of the feed
    if($mn_golos_ajax_theme){
        $mn_golos_content .= "<script type='text/javascript' src='".plugins_url( '/js/steem.min.js?ver='.MNSFVER , __FILE__ )."'></script>";
    }
	
	// Add script
	if( isset($mn_golos_username) && $mn_golos_username )
	{
		// golos feed
		$mn_golos_content .= '<div class="steem-feed-'.$i.'"><div class="steem-feed-loader"><i class="fa fa-refresh fa-spin"></i></div></div>';

		$mn_sf_author = $mn_golos_username;
		$mn_sf_datenow = current_time( 'Y-m-d\TH:i:s' );
		$mn_sf_limit = (int)$mn_golos_posts_count;	
		$mn_sf_ajaxurl = admin_url( 'admin-ajax.php' );
		$encoded_atts = json_encode($atts);
	
		$js = "
		<script type='text/javascript'>
			(function ($) {
				$(document).ready(function() 
				{
					// to-do: Localize javascript variables to move javascript in external js file
					var mn_sf_id = '".$i."';
					var mn_sf_author = '".$mn_sf_author."';
					var mn_sf_datenow = '".$mn_sf_datenow."';
					var mn_sf_limit = '".$mn_sf_limit."';
					var mn_sf_ajaxurl = '".$mn_sf_ajaxurl."';
					var encoded_atts = '".$encoded_atts."';
					mn_sf_limit = parseInt(mn_sf_limit, 10);
				
					steem.getDiscussionsByAuthorBeforeDate(mn_sf_author, '', mn_sf_datenow, mn_sf_limit, function(err, response)
					{
						if (typeof response === 'object')
						{
							var data = {
								'action'	: 'render_steem_feed',
								'feed'		: encodeURIComponent(JSON.stringify(response)),
								'atts'		: encodeURIComponent(encoded_atts)
							};
					
							jQuery.post(mn_sf_ajaxurl, data, function(msg) {
								$('.steem-feed-'+mn_sf_id+'').html(msg);
							});
													
						}
					})
				});
			})(jQuery)
		</script>
		";
	
		$mn_golos_content .= $js;	
	}
	 
    //Return our feed HTML to display
    return $mn_golos_content;
}

// Function that handles ajax request (wp_ajax_*action*)
add_action( 'wp_ajax_render_steem_feed', 'mn_render_steem_feed' );
add_action( 'wp_ajax_nopriv_render_steem_feed', 'mn_render_steem_feed' );

function mn_render_steem_feed() {
	
	$html = '';
	$feed = $_POST['feed'];
	$decodefeed = urldecode($feed);
	$decodefeed = str_replace('\\\'', '\\\\\'', $decodefeed);
	$decodejson = json_decode($decodefeed, false);
	
	$atts = $_POST['atts'];
	$decodeatts = urldecode($atts);
	$atts = json_decode($decodeatts, true);

	if (count($decodejson))
	{
		$html .= '<ul class="sf-list">';
			
			foreach ($decodejson as $key => $item)
			{				
				$html .= '<li class="sf-li">';
						 
					$html .= '<article>';
					
						// Metadata
						$metadata = json_decode($item->json_metadata, false);
						
						// Image
						if ($atts['postimage'] === 1 || $atts['postimage'] === '1' || $atts['postimage'] === true || $atts['postimage'] === 'true')
						{
							if (isset($metadata->image))
							{
								$image = $metadata->image;
								if (array_key_exists('0', $image))
								{
									$html .= '<a href="https://golos.com'.$item->url.'" class="sf-image" target="_blank"><img src="https://img1.golos.com/128x256/'.$image[0].'" alt="'.$item->title.'" /></a>';
								}
							}
						}
						
						$html .= '<div class="sf-li-content">';
						
							// Title
							if ($atts['posttitle'] === 1 || $atts['posttitle'] === '1' || $atts['posttitle'] === true || $atts['posttitle'] === 'true')
							{
								$html .= '<a class="sf-li-title" href="https://golos.com'.$item->url.'" target="_blank">'.$item->title.'</a>';
							}
							
							// Body
							if ($atts['postcontent'] === 1 || $atts['postcontent'] === '1' || $atts['postcontent'] === true || $atts['postcontent'] === 'true')
							{
								$itemBody = sf_word_limit($item->body, $atts['wordlimit']);
								$html .= '<div class="sf-li-body">'.$itemBody.'</div>';
							}
							
							// Post footer
							if (($atts['postreward'] === 1 || $atts['postreward'] === '1' || $atts['postreward'] === true || $atts['postreward'] === 'true')
							|| ($atts['postdate'] === 1 || $atts['postdate'] === '1' || $atts['postdate'] === true || $atts['postdate'] === 'true')
							|| ($atts['postauthor'] === 1 || $atts['postauthor'] === '1' || $atts['postauthor'] === true || $atts['postauthor'] === 'true')
							|| ($atts['posttag'] === 1 || $atts['posttag'] === '1' || $atts['posttag'] === true || $atts['posttag'] === 'true')
							|| ($atts['postvotes'] === 1 || $atts['postvotes'] === '1' || $atts['postvotes'] === true || $atts['postvotes'] === 'true')
							|| ($atts['postreplies'] === 1 || $atts['postreplies'] === '1' || $atts['postreplies'] === true || $atts['postreplies'] === 'true'))
							{
							
								$html .= '<div class="sf-li-footer">';
								
									// Reward
									if ($atts['postreward'] === 1 || $atts['postreward'] === '1' || $atts['postreward'] === true || $atts['postreward'] === 'true')
									{
										$total_payout_value = round((float)$item->total_payout_value, 2);
										$total_pending_payout_value = round((float)$item->total_pending_payout_value, 2);
										$total_value = number_format(round(($total_payout_value + $total_pending_payout_value), 2), 2);
										$html .= '<span class="sf-li-reward">';
											$html .= '<span class="sf-li-reward-inner"><span class="sf-li-dollar-sign">&#36;</span>'.$total_value.'</span>';
										$html .= '</span>';
									}
									
									// Date, author, tags
									if (($atts['postdate'] === 1 || $atts['postdate'] === '1' || $atts['postdate'] === true || $atts['postdate'] === 'true')
									|| ($atts['postauthor'] === 1 || $atts['postauthor'] === '1' || $atts['postauthor'] === true || $atts['postauthor'] === 'true')
									|| ($atts['posttag'] === 1 || $atts['posttag'] === '1' || $atts['posttag'] === true || $atts['posttag'] === 'true'))
									{
									
										$html .= '<span class="sf-li-vcard">';
										
											// Date
											if ($atts['postdate'] === 1 || $atts['postdate'] === '1' || $atts['postdate'] === true || $atts['postdate'] === 'true')
											{	
												$html .= sf_time_since($item->created);
											}
												
											// Author
											if ($atts['postauthor'] === 1 || $atts['postauthor'] === '1' || $atts['postauthor'] === true || $atts['postauthor'] === 'true')
											{
												$html .= '<span class="sf-li-author">'; 
													$html .= ' '.__('by', 'golos-feed' ).' <a href="https://golos.com/@'.$item->author.'" target="_blank">'.$item->author.'</a>';
													//$html .= '<span class="sf-li-rep"></span>';
												$html .= '</span>';
											}
											
											// Tags
											if ($atts['posttag'] === 1 || $atts['posttag'] === '1' || $atts['posttag'] === true || $atts['posttag'] === 'true')
											{
												$html .= ' '.__('in', 'golos-feed' ).' <a href="https://golos.com/trending/'.$metadata->tags[0].'" target="_blank">'.$metadata->tags[0].'</a>';
											}
										
										$html .= '</span>';
									
									}
									
									// Votes & replies
									if ($atts['postvotes'] === 1 || $atts['postvotes'] === '1' || $atts['postvotes'] === true || $atts['postvotes'] === 'true')
									{
										$html .= '<span class="sf-li-votes">';
											$html .= '<i class="fa fa-user"></i>&nbsp;';
											$html .= count($item->active_votes);
										$html .= '</span>';
									}
									
									if ($atts['postreplies'] === 1 || $atts['postreplies'] === '1' || $atts['postreplies'] === true || $atts['postreplies'] === 'true')
									{
										$html .= '<span class="sf-li-replies">';
											$html .= '<a href="https://golos.com'.$item->url.'#comments" target="_blank">';
												$html .= '<i class="fa fa-comments"></i>&nbsp;';
												$html .= '<span>'.sf_replies_count($item->author, $item->permlink).'</span>';
											$html .= '</a>';
										$html .= '</span>';
									}
								
								$html .= '</div>';
							
							}
						
						$html .= '</div>';
					
					$html .= '</article>';
					
				$html .= '</li>';
			}
			
		$html .= '</ul>';
	}
	
	echo $html;

	wp_die();
}

function sf_is_json($string) 
{
	json_decode($string);
	
	return (json_last_error() == JSON_ERROR_NONE);
}

function sf_word_limit($str, $limit = 20, $end_char = '&#8230;') 
{
	if (trim($str) == '')
		return $str;
		
	// always strip tags for text
	$str = strip_tags($str);
	$find = array("/\r|\n/u", "/\t/u", "/\s\s+/u");
	$replace = array(" ", " ", " ");
	$str = preg_replace($find, $replace, $str);
	
	// strip urls
	$str = preg_replace('/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '', $str);  
	preg_match('/\s*(?:\S*\s*){'.(int)$limit.'}/u', $str, $matches);
	
	if (strlen($matches[0]) == strlen($str))
		$end_char = '';
		
	return rtrim($matches[0]).$end_char;
}

function sf_time_since($date) 
{
	$date = strtotime($date);
	$now = current_time( 'mysql' );
	$now = strtotime($now);
	$since = $now - $date;
	
	$chunks = array(
		array(60 * 60 * 24 * 365 , __('year ago', 'golos-feed'), __('years ago', 'golos-feed')),
		array(60 * 60 * 24 * 30 , __('month ago', 'golos-feed'), __('months ago', 'golos-feed')),
		array(60 * 60 * 24 * 7, __('week ago', 'golos-feed'), __('weeks ago', 'golos-feed')),
		array(60 * 60 * 24 , __('day ago', 'golos-feed'), __('days ago', 'golos-feed')),
		array(60 * 60 , __('hour ago', 'golos-feed'), __('hours ago', 'golos-feed')),
		array(60 , __('minute ago', 'golos-feed'), __('minutes ago', 'golos-feed')),
		array(1 , __('second ago', 'golos-feed'), __('seconds ago', 'golos-feed'))
	);

	for ($i = 0, $j = count($chunks); $i < $j; $i++) {
		$seconds = $chunks[$i][0];
		$name_1 = $chunks[$i][1];
		$name_n = $chunks[$i][2];
		if (($count = floor($since / $seconds)) != 0) {
			break;
		}
	}

	$print = ($count == 1) ? '1 '.$name_1 : "$count {$name_n}";
	return $print;
}

function sf_replies_count($author, $permlink)
{
	$replies = file_get_contents('https://api.steemjs.com/getContentReplies?parent='.$author.'&parentPermlink='.$permlink);
	$isjson = sf_is_json($replies);
	
	if ($isjson)
	{
		$replies = json_decode($replies, false);
		$childrenCount = 0;
		
		// Get children replies
		foreach ($replies as $reply)
		{
			$childrenCount += $reply->children;
		}
		
		$repliesCount = count($replies) + $childrenCount;
		
		return $repliesCount;	
	}
	else
	{
		return false;
	}
}

#############################

//Allows shortcodes in theme
add_filter('widget_text', 'do_shortcode');

//Enqueue stylesheet
add_action( 'wp_enqueue_scripts', 'mn_golos_styles_enqueue' );
function mn_golos_styles_enqueue() {
    wp_register_style( 'mn_golos_styles', plugins_url('css/mn-golos-style.css', __FILE__), array(), MNSFVER );
    wp_enqueue_style( 'mn_golos_styles' );

    $options = get_option('mn_golos_settings');
    if(isset($options['mn_golos_disable_awesome'])){
        if( !$options['mn_golos_disable_awesome'] || !isset($options['mn_golos_disable_awesome']) ) wp_enqueue_style( 'mn_golos_icons', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css', array(), '4.6.3' );
    }
    
}

//Enqueue scripts
add_action( 'wp_enqueue_scripts', 'mn_golos_scripts_enqueue' );
function mn_golos_scripts_enqueue() {
    //Register the script to make it available
    wp_register_script( 'mn_golos_scripts', plugins_url( '/js/steem.min.js' , __FILE__ ), array('jquery'), MNSFVER, true );

    //Options to pass to JS file
    $mn_golos_settings = get_option('mn_golos_settings');

    isset($mn_golos_settings[ 'mn_golos_ajax_theme' ]) ? $mn_golos_ajax_theme = trim($mn_golos_settings['mn_golos_ajax_theme']) : $mn_golos_ajax_theme = '';
    ( $mn_golos_ajax_theme == 'on' || $mn_golos_ajax_theme == 'true' || $mn_golos_ajax_theme == true ) ? $mn_golos_ajax_theme = true : $mn_golos_ajax_theme = false;

    //Enqueue it to load it onto the page
    if( !$mn_golos_ajax_theme ) wp_enqueue_script('mn_golos_scripts');

    //Pass option to JS file
    wp_localize_script('mn_golos_scripts', 'mn_golos_js_options', $data);
}

//Run function on plugin activate
function mn_golos_activate() {
    $options = get_option('mn_golos_settings');
    update_option( 'mn_golos_settings', $options );
}
register_activation_hook( __FILE__, 'mn_golos_activate' );

//Uninstall
function mn_golos_uninstall()
{
    if ( ! current_user_can( 'activate_plugins' ) )
        return;

    //Settings
    delete_option( 'mn_golos_settings' );
}
register_uninstall_hook( __FILE__, 'mn_golos_uninstall' );

?>
