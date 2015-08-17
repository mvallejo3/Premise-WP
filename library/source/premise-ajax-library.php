<?php 
/**
 * Premise Ajax Library
 *
 * All Ajaxa related functions go here
 *
 * @package Premise WP
 * @subpackage Library
 *
 * @since  1.2 
 */



/**
 * Output ajax dialog markup
 * 
 * @return string html for premise ajax dialog
 */
function premise_load_ajax_markup() {
	$ajax_overlay = '<div id="premise-ajax-overlay" style="
		display:none;
		position:fixed;
		top:0;
		left:0;
		width:100%;
		height:100%;
		background-color:#FFFFFF;
		opacity:.6;
		z-index:9990;
		"></div>';

	$ajax_icon = '<div id="premise-ajax-loading" 
		class="absolute center" style="
		display:none;
		position:fixed;
		width:60px;
		top:40%;
		left:50%;
		margin-left:-30px;
		z-index:9991;
		"><i class="fa fa-3x fa-spinner fa-spin"></i></div>';

	$ajax_dialog = '<div id="premise-ajax-dialog" style="
		display:none;
		position:fixed;
		top:10%;
		left:10%;
		width:80%;
		height:80%;
		background-color:#FFFFFF;
		z-index:9992;
		overflow:auto;
		box-shadow: 0 0 5px #333333;
		-webkit-box-shadow: 0 0 5px #333333;
		-moz-box-shadow: 0 0 5px #333333;
		-ms-box-shadow: 0 0 5px #333333;
		-o-box-shadow: 0 0 5px #333333;
		padding:20px;
		" class="round-corners25"></div>';

	$ajax_control = '<a id="premise-ajax-close" style="
		display:none;
		position: fixed;
		padding: 2px 12px;
		top: 60px;
		right: 40px;
		background: #FFFFFF;
		z-index: 9995;
		line-height: 150%;
		font-size: 20px;
		color: #AAAAAA;
		border-radius: 24px;
		-webkit-border-radius: 24px;
		-moz-border-radius: 24px;
		-ms-border-radius: 24px;
		-o-border-radius: 24px;
		box-shadow: 0 0 5px #333333;
		-webkit-box-shadow: 0 0 5px #333333;
		-moz-box-shadow: 0 0 5px #333333;
		-ms-box-shadow: 0 0 5px #333333;
		-o-box-shadow: 0 0 5px #333333;
		" class="row" href="javascript:;" onclick="premiseAjaxClose();">x</a>';

	echo $ajax_overlay, $ajax_icon, $ajax_dialog, $ajax_control;
}



?>