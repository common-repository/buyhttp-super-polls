<?php
function bspPollPages()
{
	add_plugins_page('Manage BSP','Manage BSP','manage_options','managebsp','manageBsp');
}


function manageBsp()
{
	//Create an instance of our package class...
    $bspListTable = new Bsp_List_Table();
    //Fetch, prepare, sort, and filter our data...
    $bspListTable->prepare_items();
	?>
	<div class="wrap">
		<h2><?php echo __('Manage BuyHTTP Super Polls','bsppolls'); ?> <a class="editPoll add-new-h2"><?php echo __('Add New','bsppolls'); ?></a></h2>
		<form id="poll-filter" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <input type="hidden" name="noheader" value="true" />
            <!-- Now we can render the completed list table -->
            <?php $bspListTable->display() ?>
        </form>
	</div>
	
	<div id="addNewPoll">
		<h3 id="newPollHeader"><?php echo __('Add A New Poll','bsppolls'); ?></h3>
		<form method="post" action="<?php echo admin_url('plugins.php?page=managebsp&noheader=true'); ?>">
			<p><input type="text" name="question" id="pollQuestion" placeholder="Question" size="60"></p>
			<p><input type="submit" value="Save Poll" class="button button-primary"></p>
			<input type="hidden" name="action" value="addPoll">
			<input type="hidden" name="id" value="" class="pollId">
			<?php wp_nonce_field( 'savePoll' ); ?>
		</form>
	</div>
	
	<div id="optionsModal">
		<h3><?php echo __('Options for','bsppolls'); ?> <span id="optionsSpan"></span></h3>
		<form method="post" action="<?php echo admin_url('plugins.php?page=managebsp&noheader=true'); ?>" id="optionsForm">
			<p class="optionsP">
				<label for="option1"><?php echo __('Option','bsppolls'); ?> <span class="optionNumber">1</span></label>
				<input type="text" name="options[]" id="option1" size="60">
			</p>
			<p><a class="button button-secondary addOptionButton"><?php echo __('Add Option','bsppolls'); ?></a> <input type="submit" value="Save Options" class="button button-primary"></p>
			<input type="hidden" name="action" value="saveOptions">
			<input type="hidden" name="id" value="" class="pollId">
			<?php wp_nonce_field( 'saveOptions' ); ?>
		</form>
	</div>
	<?php
}


if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


class Bsp_List_Table extends WP_List_Table {
	function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'poll', 
            'plural'    => 'polls',
            'ajax'      => false 
        ) );
        
    }
    
    function column_default($item){
	    switch($column_name){
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }
    
    function column_question($item){
        $actions = array(
            'edit'		=> sprintf('<a data-id="%d" data-question="%s" class="editPoll">Edit</a>',$item->poll_id,stripslashes($item->question)),
            'options'	=> sprintf('<a data-id="%d" data-options=%s data-question="%s" class="editOptions">Options</a>',$item->poll_id,($item->options=='' ? '""' : $item->options),stripslashes($item->question)),
            'delete'	=> sprintf('<a href="'.admin_url('plugins.php?page=managebsp').'&_wpnonce=%s&action=deletePoll&poll[]=%d">Delete</a>',wp_create_nonce( 'deletePoll' ),$item->poll_id),
        );
        
        return sprintf('%1$s %2$s',
            stripslashes($item->question),
            $this->row_actions($actions)
        );
    }
    
    function column_numvotes($item){
	    return $item->numvotes;
    }
    
    function column_options($item){
	    return count(json_decode($item->options));
    }
    
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],
            /*$2%s*/ $item->poll_id
        );
    }
    
    function get_columns(){
        $columns = array(
            'cb'		=> '<input type="checkbox" />',
            'question'	=> 'Question',
            'options'	=> '# of Options',
            'numvotes'  => '# of Votes'
        );
        return $columns;
    }
    
    function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }
    
    function process_bulk_action() {

	    //Detect when a bulk action is being triggered...
	    if( 'delete'===$this->current_action() ) {
	    	check_admin_referer('bulk-polls');
	        $this->deletePoll();
			wp_redirect(add_query_arg('deleted',$_GET['poll'],admin_url('plugins.php?page=managebsp')));
	    }
	
	}
	
	function deletePoll()
	{
		global $wpdb;
		foreach($_GET['poll'] as $id){
			$wpdb->delete($wpdb->prefix.'bsppollvotes',array('poll_id'=>$id));
			$numrows = $wpdb->delete($wpdb->prefix.'bsppolls',array('poll_id'=>$id));
		}
		return true;
	}
    
    function prepare_items() {
        global $wpdb;

        $per_page = 25;
        
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->process_bulk_action();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $data = $wpdb->get_results(
			"SELECT p.*, (SELECT COUNT(vote_id) FROM ".$wpdb->prefix."bsppollvotes WHERE poll_id=p.poll_id) AS numvotes
			FROM ".$wpdb->prefix."bsppolls AS p
			ORDER BY p.poll_id DESC"
		);
                
        $current_page = $this->get_pagenum();
        
        $total_items = count($data);
        
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        
        $this->items = $data;
        
        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items/$per_page)
        ) );
    }

}