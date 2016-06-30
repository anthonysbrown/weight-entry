<?php

$sb_admin = new sb_admin;
add_action('admin_menu', array($sb_admin, 'menu'));
add_action('admin_init', array($sb_admin, 'export_csv'));
add_action('admin_init', array($sb_admin, 'export_html'));
class sb_admin{
	
	
		function menu(){
		
		
		 add_menu_page('Weight Entry', 'Weight Entry', 'manage_options', 'sp-export-measurements', array($this,'view'),'dashicons-universal-access-alt',5);
		add_submenu_page ( 'sp-export-measurements', 'Settings','Settings', 'manage_options',  'sp-export-measurements-settings', array($this, 'settings') );
		
			
		}
		
		function export_html(){
			global $sb_weight,$wpdb;
				if($_GET['sb_export_html'] == 1){
			$start_date = 	$_GET['start_date'];
			$end_date = 	$_GET['end_date'] ;	
				if($_GET['coach'] != ''){
	$coach = ' AND coach = %s   ';	
	$coach_filename= ''.$_GET['coach'].' ';
	}
			
			$r = $wpdb->get_results($wpdb->prepare("SELECT  * FROM " . $wpdb->prefix . "weights where date BETWEEN %s AND %s ".$coach ." ",date("Y-m-d", strtotime($start_date)),date("Y-m-d", strtotime($end_date)), $_GET['coach']), ARRAY_A);	

					
			flush();
			ob_start();
			echo $this->table($r,$start_date,$end_date,$_GET['coach']);
			exit();
			}
			
		}
		function export_csv_edit(){
		global $sb_weight,$wpdb;
			if($_GET['sb_export_xls'] == 1){
			$start_date = 	$_GET['start_date'];
			$end_date = 	$_GET['end_date'] ;	
				if($_GET['coach'] != ''){
	$coach = ' AND coach = %s   ';	
	$coach_filename= ''.$_GET['coach'].' ';
	}
			
			$r = $wpdb->get_results($wpdb->prepare("SELECT  * FROM " . $wpdb->prefix . "weights where date BETWEEN %s AND %s ".$coach ." ",date("Y-m-d", strtotime($start_date)),date("Y-m-d", strtotime($end_date)), $_GET['coach']), ARRAY_A);	

		
					
		$inputFileType = 'HTML';
		$inputFileName = $temp ;
		$outputFileType = 'Excel2007';
		$outputFileName = './myExcelFile.xlsx';
		$upload_dir = wp_upload_dir();
		
		$file = $upload_dir['basedir'].'/temp.html';
		echo $file;   
		$write = file_put_contents($file, $this->table($r,$start_date,$end_date,$_GET['coach']));
		$objPHPExcelReader = PHPExcel_IOFactory::createReader($file);
		$objPHPExcel = $objPHPExcelReader->load($file);
		
		$objPHPExcelWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,$outputFileType);
		$objPHPExcel = $objPHPExcelWriter->save('php://output');
					
			
	
			exit();
			}
			
		}
		
		function export_csv(){
		global $sb_weight,$wpdb;
			if($_GET['sb_export_xls'] == 1){
			$start_date = 	$_GET['start_date'];
			$end_date = 	$_GET['end_date'] ;	
				if($_GET['coach'] != ''){
	$coach = ' AND coach = %s   ';	
	$coach_filename= ''.$_GET['coach'].' ';
	}
			
			$r = $wpdb->get_results($wpdb->prepare("SELECT  * FROM " . $wpdb->prefix . "weights where date BETWEEN %s AND %s ".$coach ." ",date("Y-m-d", strtotime($start_date)),date("Y-m-d", strtotime($end_date)), $_GET['coach']), ARRAY_A);	

			
			flush();
			ob_start();
			header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
			header("Content-Disposition: attachment; filename=".''.$coach_filename.''.$start_date.' - '.$end_date.''.".xls");  //File name extension was wrong
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			echo trim(strip_tags($this->table($r,$start_date,$end_date,$_GET['coach']),'<table><tr><td><strong>'));
			exit();
			}
			
		}
		function table($r,$start_date,$end_date,$coach){
			global $sb_weight,$wpdb;
			if(count(	$r)){
			for($i=0; $i<count(	$r); $i++){
			
				
				$grouped[$r[$i]['user_id']][$i] = $r[$i];
				
			}
			
			foreach($grouped as $key =>$value){
			
				 $grouped[$key] = array_values($value);

				
			}
			}
		
			$h .= '
			
			<div class="weight-table">';
			if($_GET['sb_export_xls'] != 1){
			$h.='<h3 style="margin-top:20px">Client Measurements for '.$start_date.' - '.$end_date.'</h3>';
			
			$h.='<div style="margin:10px;font-size:1.3em"><a href="admin.php?page=sp-export-measurements&start_date='.$start_date.'&end_date='.$end_date.'&coach='.$coach.'&sb_export_xls=1" class="button"><img style="width:16px" src="'.$sb_weight->path('images/excel-file.png').'">Download Excel File</a></div>';
			}
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
			$h .= '
			
			<tr>
			<td colspan="6" style="background-color:#CCC;color:#000"><strong><a href="'.admin_url('admin.php?page=sp-view-members&member_id='.$user_id.'').'">'.$user->display_name.'</a></strong></td>
			
			</tr>';
			
				for($i=0; $i<count($entry); $i++){
				$week = $sb_weight->daysInWeek(date("W",strtotime($entry[$i]['date']))); 
				
					$h .= '<tr>
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
		
		function settings(){
				global $sb_weight,$wpdb;
			if($_POST['sb-save-settings'] != ''){
				
			update_option('sb_get_available_groups', $_POST['sb_get_available_groups']);
			update_option('sb_get_available_coaches', $_POST['sb_get_available_coaches']);
			echo ' <div class="notice notice-success is-dismissible">
        <p>Saved settings!</p>
    </div>';
			}
			
			echo '<h2>Settings</h2>
			<form action="" method="post">
			<table  style="width:100%;background-color:#FFF">
			<tr><td style="width:300px"><strong>Memberships that can view the weight entry system.</strong> <em> Use the <strong>Slug</strong> and comma seperate for more then one grou</em>p</td>
			<td><input type="input" style="width:100%" name="sb_get_available_groups" value="'.get_option('sb_get_available_groups').'"></td>
			</tr>
			<tr><td style="width:300px"><strong>Coaches</strong> <em>Comma seperate names.</em></td>
			<td><input type="input" style="width:100%" name="sb_get_available_coaches" value="'.get_option('sb_get_available_coaches').'"></td>
			</tr>
				<tr><td >
			<td><input type="submit" name="sb-save-settings" value="Save Settings"></td>
			</tr>
			</table>
			</form>';
				
			
			
		}
		function view(){
			global $sb_weight,$wpdb;
			$current_week = $sb_weight->daysInWeek(date("W")); 
			if($_POST['start_date'] == '' && $_POST['end_date'] == ''){
			$start_date = 	date("m/d/Y", strtotime($current_week[0]));
			$end_date = 	date("m/d/Y", strtotime($current_week[6]));
			}else{
			$start_date = 	$_POST['start_date'];
			$end_date = 	$_POST['end_date'] ;	
			}
			
		
			
			
			echo '<h2>Measurements Exporter</h2>
			<script>
  jQuery(function($) {
    $( "#from" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      onClose: function( selectedDate ) {
        $( "#to" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#to" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      onClose: function( selectedDate ) {
        $( "#from" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
  });
  </script>
			<div class="sb-admin-form"><form action="" method="post">
			<label for="from">From</label>
<input type="text" id="from" name="start_date" value="'.$start_date.'">
<label for="to">to</label>
<input type="text" id="to" name="end_date"  value="'.$end_date.'">
<label>Coach</label>
'.$sb_weight->trainers_dropdown('coach',$_POST['coach'], true).' <input type="submit" name="save" value="Export" class="button"></form>		
			</div>
			';
	if($_POST['coach'] != ''){
	$coach = ' AND coach = %s   ';	
	}
$r = $wpdb->get_results($wpdb->prepare("SELECT  * FROM " . $wpdb->prefix . "weights where date BETWEEN %s AND %s ".$coach ." ",date("Y-m-d", strtotime($start_date)),date("Y-m-d", strtotime($end_date)), $_POST['coach']), ARRAY_A);	

	echo $this->table($r,$start_date,$end_date,$_POST['coach']);

		}
	
}