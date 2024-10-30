<?php
if(isset($_REQUEST['action'])){
	add_action('init', 'processPost');
	
	function processPost()
	{
		switch($_REQUEST['action']){
			case 'addPoll':
				check_admin_referer( 'savePoll' );
				if(!$id = addNewPoll()){
					wp_die(__( 'Error in adding poll.' ));
				}
				wp_redirect(add_query_arg('updated',$id,admin_url('plugins.php?page=managebsp')));
				exit();
				break;
				
			case 'saveOptions':
				check_admin_referer( 'saveOptions' );
				saveOptions();
				break;
				
			case 'deletePoll':
				check_admin_referer( 'deletePoll' );
				deletePoll();
				wp_redirect(add_query_arg('deleted',$_GET['poll'],admin_url('plugins.php?page=managebsp')));
				break;
				
			case 'bulkDelete':
				check_admin_referer( 'bulk-polls' );
				deletePoll();
				wp_redirect(add_query_arg('deleted',$_GET['poll'],admin_url('plugins.php?page=managebsp')));
				break;
		}
	}
}


function addNewPoll()
{
	global $wpdb;
	if($_POST['id']){
		$wpdb->update($wpdb->prefix.'bsppolls',array('question'=>$_POST['question']),array('poll_id'=>$_POST['id']));
		$id = $_POST['id'];
	} else {
		$wpdb->insert($wpdb->prefix.'bsppolls',array('question'=>$_POST['question']));
		$id = $wpdb->insert_id;
	}
	return $id;
}


function saveOptions()
{
	global $wpdb;
	$opts = array();
	$i = 1;
	foreach($_POST['options'] as $opt){
		$opts[] = array('id'=>$i,'name'=>$opt);
		$i++;
	}
	$numrows = $wpdb->update($wpdb->prefix.'bsppolls',array('options'=>json_encode($opts)),array('poll_id'=>$_POST['id']));
	wp_redirect(add_query_arg('updated',$numrows,admin_url('plugins.php?page=managebsp')));
	exit();
}


function deletePoll()
{
	global $wpdb;
	foreach($_GET['poll'] as $id){
		$wpdb->delete($wpdb->prefix.'bsppollvotes',array('poll_id'=>$id));
		$numrows = $wpdb->delete($wpdb->prefix.'bsppolls',array('poll_id'=>$id));
	}
	return $numrows;
}


function saveVote()
{
	check_ajax_referer('bspPollVoting');
	global $wpdb;
	$wpdb->insert($wpdb->prefix.'bsppollvotes',array('poll_id'=>$_POST['poll_id'],'option_id'=>$_POST['votefor'],'ip'=>$_SERVER['REMOTE_ADDR']));
	echo $wpdb->insert_id;
}