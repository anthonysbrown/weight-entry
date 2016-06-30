<?php
/**
 * Plugin Name: Simple Balance Weight Entry
 * Plugin URI: http://www.wpfixr.com
 * Description: Custom plugin for weight management
 */
$sb_weight = new sb_weight;
add_shortcode("sb_weight", array($sb_weight, 'view'));
add_action("wp_enqueue_scripts", array($sb_weight, 'scripts'));
add_action("admin_enqueue_scripts", array($sb_weight, 'scripts'));

define( 'WE_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
include_once'includes/common.php';
include_once'Classes/PHPExcel.php';
include_once'admin/index.php';
include_once'admin/members.php';
class sb_weight{
	
	function __construct(){
	
		
	}
	function trainers(){
	
	$coaches = get_option('sb_get_available_coaches');
	$list = array();
	$list  = explode(",",$coaches);
	
	
	return $list;
	}
	function can_view($user_id){
	$view = 0;
	$groups = get_option('sb_get_available_groups');
	$list = array();
	$list  = explode(",",$groups);
	if(count($list)>0){
		foreach($list as $group){
			
			if ( wc_memberships_is_user_active_member( $user_id ,  $group )  ) {	
			$view = 1;
			}
		}
	}
		return $view;
	}
	
	function path($path){
	return 	$this->path = plugins_url($path, __FILE__);	
		
	}
	function scripts(){
	     wp_register_style('jquery-ui-css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.min.css');
		wp_enqueue_style('jquery-ui-css');
		
	wp_enqueue_style('sp-weight-styles', plugins_url('css/style.css', __FILE__));	
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('	jquery-effects-core');
	wp_enqueue_script('jquery-ui-datepicker');
		
		
		if(is_admin()){
			
			wp_enqueue_script('select2', plugins_url('js/select2.js', __FILE__));	
			wp_enqueue_style('select2-styles', plugins_url('css/select2.css', __FILE__));	
			wp_enqueue_script('simplebalance-scripts', plugins_url('js/scripts.js', __FILE__),array('jquery','select2'));
		}
	}


function get_weights(){
		global $wpdb,$current_user;
		$r = $wpdb->get_results($wpdb->prepare("SELECT  * FROM " . $wpdb->prefix . "_weights where user_id = %d'", $current_user->ID), ARRAY_A);
		
		
		
	
}



function trainers_dropdown($name,$chosen = '',$showall= false){
	$trainers = $this->trainers();
	
	$h .= '<select name="'.$name.'">';
	if($showall == true){
	$h .='<option value="">All</option>';	
	}
	foreach($trainers as $trainer){
		
		if($trainer == $chosen){
		$selected = 'selected="selected"';
			
		}else{
		$selected = '';	
		}
		
		$h .= '<option value="'.$trainer.'" '.$selected.'>'.$trainer.'</option>';
	}
	
	$h .='</select>';
	return $h;
	
}

function daysInWeek($weekNum)
{
    $result = array();
    $datetime = new DateTime();
    $datetime->setISODate((int)$datetime->format('Y'), $weekNum, 1);
    $interval = new DateInterval('P1D');
    $week = new DatePeriod($datetime, $interval, 6);

    foreach($week as $day){
        $result[] = $day->format('F j Y');
    }
    return $result;
}


		function view(){
			global $wpdb,$current_user;
			
			if($current_user->ID == ''){
			$h .= '<div class="sb-error">Please login before entering your weight. <a href="/members/my-account/">Click here to login</a></div>';	
			}else{
			
			if (sb_weight::can_view( $current_user->ID ) == 1) {		
		
					
				
			
			$current_week = $this->daysInWeek(date("W")); 
			
			
		if($_POST['weight'] != ''){	
		
	$error = '';
	
		if(!is_numeric($_POST['weight']) || !is_numeric($_POST['chest']) || !is_numeric($_POST['waist']) || !is_numeric($_POST['hips']) ){
		$error = '<div class="sb-error">Please only enter numbers</div>';	
		}
	if($error== ''){
	$insert['weight'] = $_POST['weight'];
	$insert['chest'] = $_POST['chest'];
	$insert['waist'] = $_POST['waist'];
	$insert['coach'] = $_POST['coach'];
	$insert['hips'] = $_POST['hips'];	
	$insert['user_id'] = $current_user->ID;
	$insert['date'] = date("Y-m-d");
	
	if($_POST['action'] == 'add'){
	$wpdb->insert("".$wpdb->prefix ."weights", $insert);
	$error = '<div class="sb-success">Weight recorded for this week!</div>';	
	}else{
	$where['id'] = $_POST['id'];
	$wpdb->update("".$wpdb->prefix ."weights", $insert,$where);
	$error = '<div class="sb-success">Weight updated! You can update your weight below until the end of the week.</div>';	
	}
	}
	
		}
			
			
			
			
			
	$r = $wpdb->get_results($wpdb->prepare("SELECT  * FROM " . $wpdb->prefix . "weights where user_id = %d  ORDER BY date desc limit 1", $current_user->ID), ARRAY_A);
	
		if(strtotime($r[0]['date']) < strtotime($current_week[0]) ){
		unset($r);	
		}
			  
			
					
			$h .= '<div class="sb-form">
			'.$error.'
			<form class="sb-save-weight" action="" method="post">';
			
			if($r == false){
			$h .='<p><strong>Add Your Measurements for the week of '.$current_week[0].' to '.$current_week[6].'</strong></p>
				<input type="hidden" value="add" name="action">';
			
			
			 $h.='<div class="sb-form-weights">
			 <div class="sb-form-input"><label>Weight</label><input placeholder="Weight" name="weight" type="text" value="'.$r[0]['weight'].'"> </div> 
			<div class="sb-form-input"><label>Waist</label><input placeholder="Waist" name="waist" type="text" value="'.$r[0]['waist'].'">    </div>
			<div class="sb-form-input"><label>Chest</label><input placeholder="Chest" name="chest" type="text"  value="'.$r[0]['chest'].'">  </div> 
			<div class="sb-form-input"><label>Hips</label><input type="text" placeholder="Hips" name="hips"  value="'.$r[0]['hips'].'"> </div>
			  <div class="sb-form-input"><label>Coach</label>'.$this->trainers_dropdown('coach',$r[0]['coach']).'   </div>
			 <div class="sb-form-input"><label>&nbsp;</label> <input type="submit" Name="submit" value="Save" style="padding:15px 10px"> </div>
			 <div style="clear:both"></div>
			   </div>
		
				</form></div>
				';
			}else{
			
		   $h .= '<p><strong>Thank you for adding your measurements for this week!</strong></p>';
					}	
	
			$r = $wpdb->get_results($wpdb->prepare("SELECT  * FROM " . $wpdb->prefix . "weights where user_id = %d  ORDER BY date desc limit 1", $current_user->ID), ARRAY_A);
			
		
			if($r == false){
				
			$h .= ' <div class="sb-current-weight sb-error">You haven\'t entered your weight yet, to get started enter your first weight above.</div>';
			}else{
				$r_past = $wpdb->get_results($wpdb->prepare("SELECT  * FROM " . $wpdb->prefix . "weights where user_id = %d  ORDER BY date desc limit 100 OFFSET 1", $current_user->ID), ARRAY_A);	
				
				if($r_past == false){
				$h .= ' <div class="sb-current-weight sb-error">It looks like you are on your first week! No past recorded weights. Check back here after your first week for your previous week measurements.</div>';	
					
				}else{
				
					
			$h .= '
			<div class="weight-table">
			<h3 style="margin-top:20px">Past Measurements</h3>
			<table width="100%">
			<tr>
			<td><strong>Week</strong></td>
			<td><strong>Weight</strong></td>
			<td><strong>Waist</strong></td>
			<td><strong>Hips</strong></td>
			<td><strong>Chest</strong></td>
			<td><strong>Coach</strong></td>
			</tr>';
			
		for($i=0; $i<count(	$r_past); $i++){
		$week = $this->daysInWeek(date("W",strtotime($r_past[$i]['date']))); 
		
			$h .= '<tr>
			<td>'.$week[0].' - '.$week[6].'</td>
				  <td>'.$r_past[$i]['weight'].'</td>
				   <td>'.$r_past[$i]['waist'].'</td>
				    <td>'.$r_past[$i]['hips'].'</td>
					 <td>'.$r_past[$i]['chest'].'</td>
					  <td>'.$r_past[$i]['coach'].'</td>
					  </tr>';	
			
		}
			
			$h .='</table></div>';	
					
				}
			}
			
				 }else{
				$h .= ' <div class="sb-current-weight sb-error">You do not have a weight loss plan.</div>';	
						 
					 
				 }
			
			
			}
			return $h;
		}
	
	
	
	
}