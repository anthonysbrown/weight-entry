<?php

$sb_admin_members = new sb_admin_members;
add_action('admin_menu', array($sb_admin_members, 'menu'));




add_action( 'wp_ajax_members_send_email', array($sb_admin_members, 'send_email') );


add_action( 'wp_ajax_members_mark_viewed', array($sb_admin_members, 'mark_viewed') );



if ( ! wp_next_scheduled( 'members_daily_email_send' ) ) {
  wp_schedule_event( time(), 'daily', 'members_daily_email_send' );
}

add_action( 'members_daily_email_send', 'members_schedule_daily_email' );

function members_schedule_daily_email() {
	$sb_admin_members = new sb_admin_members;
		
		$sb_admin_members->cron();

}


#add_action('admin_init', 'members_schedule_daily_email');
class sb_admin_members{
	function init(){
		if($_GET['debug'] == 1){

			$this->cron();	
	
}
	}
	function cron(){
		
	global $wpdb;
	$start = date('Y-m-d').' 00:00:00';
	$query = $wpdb->prepare("SELECT  * FROM " . $wpdb->prefix . "weights  WHERE date = %s",	$start );
	
	$r = $wpdb->get_results($query, ARRAY_A);		
	if($r != false){
		
		$h.='<h1>Daily Weight Entry Report</h1><p>Generated from <a href="'.admin_url('admin.php?page=sp-view-members').'"> the weight entry admin page.</a></p><table width="100%" bordercolor="#CCC" border="1" cellpadding="5" cellspacing="0" >
			<tr style="background-color:#333;color#FFF">
			<td><strong style="color:#FFF">Name</strong></td>
			<td><strong style="color:#FFF">Weight</strong></td>
			<td><strong style="color:#FFF">Waist</strong></td>
			<td><strong style="color:#FFF">Hips</strong></td>
			<td><strong style="color:#FFF">Chest</strong></td>
			<td><strong style="color:#FFF">Coach</strong></td>
			</tr>';
			
				for($i=0; $i<count(	$r); $i++){
					
						$user = get_userdata($r[$i]['user_id']);
			
					
					$h .= '<tr class="'.$class.' progress-id-'.$entry[0]['id'].'">
					<td><a href="'.admin_url('admin.php?page=sp-view-members&member_id='.$r[$i]['user_id'].'').'">'.$user->first_name .' '.$user->last_name .'</a></td>
						  <td>'.$r[$i]['weight'].'</td>
						   <td>'.$r[$i]['waist'].'</td>
							<td>'.$r[$i]['hips'].'</td>
							 <td>'.$r[$i]['chest'].'</td>
							  <td>'.$r[$i]['coach'].'</td>
							  </tr>';	
				}
				
				$h .='</table>';
				
			
				
				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				$headers[] = 'From: members@simple-balance.ca';
				$headers[] = 'Cc: anthony@worcesterwideweb.com';

wp_mail( get_option( 'admin_email'),'[simplebalance.ca][Daily Weight Entry Report]['.date('F j, Y').']', $h, $headers);
	}
	}
	
	function menu(){
	add_submenu_page ( 'sp-export-measurements', 'Members','Members', 'manage_options',  'sp-view-members', array($this, 'view') );
			
		
	}
	function mark_viewed(){
		
		
		update_option('wt_weight_row_'.$_POST['id'].'', $_POST['status']);
		
		echo json_encode($_POST);
		
	die();	
	}
	function table($r){
			global $sb_weight,$wpdb,$current_user;
			if(count(	$r)){
			for($i=0; $i<count(	$r); $i++){
			
				
				$grouped[$r[$i]['user_id']][$i] = $r[$i];
				
			}
			
			foreach($grouped as $key =>$value){
			
				 $grouped[$key] = array_values($value);

				
			}
			}
		
		
		
			$h .='<div class="members-filter" style="margin:15px 0px">
			<form action="" method="post">
			<select style="width:200px" name="member_id" class="sb-selects"><option value="">Show user</option>';
		
		
		$r_dropdown = $wpdb->get_results("SELECT  * FROM " . $wpdb->prefix . "weights  group by user_id", ARRAY_A);
		for($i=0; $i<count(	$r_dropdown); $i++){
			
				
				$users_array[] = $r_dropdown[$i]['user_id'];
			}
		
			$args = array(	    
	'include'      => $users_array,
	'orderby'      => 'first_name',
	'order'        => 'ASC',	
 ); 
$dropdown = get_users( $args );
		
		
			foreach ( $dropdown as $user ) {
				
			$h .='<option value="'. $user->ID.'">'.$user->first_name .' '.$user->last_name .'</option>';
			}
			$h .='</select> <input class="button" type="submit" name="show_user" value="Go"></form></div>';
			$h .= '
			
			<div class="weight-table">';
			
			$h.='<table width="100%">
			<tr style="background-color:#333;color#FFF">
			<td><strong style="color:#FFF">Week</strong></td>
			<td><strong style="color:#FFF">Weight</strong></td>
			<td><strong style="color:#FFF">Waist</strong></td>
			<td><strong style="color:#FFF">Hips</strong></td>
			<td><strong style="color:#FFF">Chest</strong></td>
			<td><strong style="color:#FFF">Coach</strong></td>
			</tr>';
			
			foreach($grouped as $user_id =>$entry){
			$user = get_userdata($user_id);
			
			
			if(get_option('wt_weight_row_'.$entry[0]['id'].'',0) == '' || get_option('wt_weight_row_'.$entry[0]['id'].'',0) == '0' ){
			$bg= 'wt-weight-on';	
			$new = '*NEW ';
			}else{
			$bg= 'wt-weight-off';
				$new = ' ';		
			}
			$h .= '
			
			<tr class="'.$bg.' wt-row-click" data-id="'.$entry[0]['id'].'" data-status="'.get_option('wt_weight_row_'.$entry[0]['id'].'',0).'">
			<td  ><div style="font-weight:bold;margin:5px 0px">'.$new.' '.$user->first_name .' '.$user->last_name .'</div>
			<a href="#" class="show-progress-id glyph-button" data-id="'.$entry[0]['id'].'"><span class="dashicons dashicons-plus-alt"></span>View progress</a>
			<a style="margin-left:15px" href="#TB_inline?width=600&height=550&inlineId=email-user-box" class="thickbox glyph-button email-user-id"  data-id="'.$user->ID.'"  data-coach="'.$entry[0]['coach'].'"><span class="dashicons dashicons-email"></span>Email User</a>
			 
			<em style="margin-left:15px">  Last entered: '.date("F j, Y",strtotime($entry[0]['date'])).'</em></td>
			<td>'.$entry[0]['weight'].'</td>
						   <td >'.$entry[0]['waist'].'</td>
							<td >'.$entry[0]['hips'].'</td>
							 <td >'.$entry[0]['chest'].'</td>
							  <td>'.$entry[0]['coach'].'</td>
			</tr>';
			
				for($i=0; $i<count($entry); $i++){
				$week = $sb_weight->daysInWeek(date("W",strtotime($entry[$i]['date']))); 
					if($_REQUEST['member_id'] == ''){
					$class = 'hide-progress';	
						
					}else{
					$class = '';	
					}
					$h .= '<tr class="'.$class.' progress-id-'.$entry[0]['id'].'">
					<td>'.$week[0].' - '.$week[6].'</td>
						  <td>'.$entry[$i]['weight'].'</td>
						   <td>'.$entry[$i]['waist'].'</td>
							<td>'.$entry[$i]['hips'].'</td>
							 <td>'.$entry[$i]['chest'].'</td>
							  <td>'.$entry[$i]['coach'].'</td>
							  </tr>';	
					
				}
				
				
			
				
				$h.= '<tr>
			<td colspan="6"></td>
			
			</tr>';
			}
			$h .='</table></div>';	
			
			return $h;
		}
	function get_signature($user){
		
		
	}
	
	function email_template($body,$email_coach,$coach_name,$coach){
		global $sb_weight;
	$content = we_get_template( 'email.php', array(
					'body' => $body,
					'coach_sig' =>$email_coach,
					'coach' =>$oach,
					'logo' =>$sb_weight->path('images/logo.png'),
				) );
				
	return $content;			
		
	}
	function trainer_sig($trainer){
	global $sb_weight;
	return $sb_weight-> path('images/'.$trainer.'.png');
		
	}
	function send_email(){
		global $wpdb;
		
				$user = get_userdata($_POST['email_user']);
				$email_coach = $_POST['email_coach'];
				$headers[] = 'From: '.get_bloginfo( 'name' ).' <members@simple-balance.ca>';	
				$headers[] = 'Content-type:text/html;charset=UTF-8';
		wp_mail( $user->user_email,$_POST['email_subject'], $this->email_template($_POST['email_body'],$this->trainer_sig($email_coach),$coach),$headers);
		
		echo 'Email sent!';
		
	die();	
	}
	function view(){
		global $wpdb,$current_user;
		add_thickbox(); 
		
		echo '<h1>Members</h1>
		<p>Last viewed: '.date_i18n( get_option( 'date_format' ), get_option('sb_admin_members_last_view_'.$current_user->ID.'' )).' '.date('g:i a',get_option('sb_admin_members_last_view_'.$current_user->ID.'' )).'</p>';
		
		if($_REQUEST['member_id'] != ''){
			
		$and = 'WHERE user_id = '.$_REQUEST['member_id'] .''; 	
		}
		$r = $wpdb->get_results($wpdb->prepare("SELECT  * FROM " . $wpdb->prefix . "weights ".$and." order by date DESC ",$array), ARRAY_A);	

		echo $this->table($r);
		
		
		echo '<div id="email-user-box" style="display:none;">
    <form action="" id="send-email-user"> <input type="hidden" class="email-user" value=""> <input type="hidden"  value="" class="email-coach">
	<table width="100%">
		<tr>
		<td>Subject</td>
		<td><input type="text" style="width:100%" class="email-subject"></td>
		</tr>
		<tr>
		<td>Body</td>
		<td><textarea class="email-body" style="width:100%;height:300px"></textarea></td>
		</tr>
		<tr>
		<td></td>
		<td><input type="submit" class="button" value="Send" name="email_user"></td>
		</tr>
	</table>
	
	
	</form>
</div>';
	update_option('sb_admin_members_last_view_'.$current_user->ID.'', current_time( 'timestamp' ));
		
	}
	
}