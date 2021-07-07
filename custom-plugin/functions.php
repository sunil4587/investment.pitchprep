<?php
/*
Plugin Name: Formidable form custom handling
Plugin URI: 
Description: This plugin is code for custom requirements.
Author: IDS/SK
Version: 1.0
Author URI:
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class gfCustomCustomisation{

  /**
  * The one, true instance of this object.
  *
  * @static
  * @access private
  * @var null|object
  */
  private static $instance = null;
  private $debug = false;
  private $version = "1.00";
  public $custom_link;
  public $entries_data=[];
  public $pageid ;

   public function debug($var){
    echo "<pre>";
      print_r($var);
    echo "</pre>";
  }
  
   private $formConfiguration = [
     '2' =>  [
       'add' => 'https://investment.pitchprep.co.uk/register-user/',
       'edit' => 'https://investment.pitchprep.co.uk/register-user/?frm_action=edit&entry={id}'
    ],
    '10' => [
       'add' =>'https://investment.pitchprep.co.uk/about-your-business/',
       'edit' =>'https://investment.pitchprep.co.uk/about-your-business?frm_action=edit&entry={id}'
    ],
    '15' =>  [
       'add' => 'https://investment.pitchprep.co.uk/what-you-do/',
       'edit' =>'https://investment.pitchprep.co.uk/what-you-do/?frm_action=edit&entry={id}'
    ],
    '16' =>  [
       'add' => 'https://investment.pitchprep.co.uk/How-it-works_pbd/',
       'edit' => 'https://investment.pitchprep.co.uk/How-it-works_pbd?frm_action=edit&entry={id}'
    ],
    '17' =>  [
       'add' => 'https://investment.pitchprep.co.uk/the-problems-you-solve_pbd/',
       'edit' => 'https://investment.pitchprep.co.uk/the-problems-you-solve_pbd?frm_action=edit&entry={id}'
    ],
     '18' =>  [
       'add' => 'https://investment.pitchprep.co.uk/the-current-opportunity_pbd/',
       'edit' => 'https://investment.pitchprep.co.uk/the-current-opportunity_pbd?frm_action=edit&entry={id}'
    ],
    '19' =>  [
       'add' => 'https://investment.pitchprep.co.uk/your-competative-advantage_pbd/',
       'edit' => 'https://investment.pitchprep.co.uk/your-competative-advantage_pbd?frm_action=edit&entry={id}'
    ],
    '20' =>  [
       'add' => 'https://investment.pitchprep.co.uk/your-development-roadmap_pbd/',
       'edit' => 'https://investment.pitchprep.co.uk/your-development-roadmap_pbd?frm_action=edit&entry={id}'
    ],
    '22' =>  [
       'add' => 'https://investment.pitchprep.co.uk/traction-and-validation_pbd/',
       'edit' => 'https://investment.pitchprep.co.uk/traction-and-validation_pbd?frm_action=edit&entry={id}'
    ],
    '67' =>  [
       'add' => 'https://investment.pitchprep.co.uk/your-revenue-model_pbd/',
       'edit' => 'https://investment.pitchprep.co.uk/your-revenue-model_pbd?frm_action=edit&entry={id}'
    ],
     '25' =>  [
       'add' => 'https://investment.pitchprep.co.uk/your-target-market_pbd/',
       'edit' => 'https://investment.pitchprep.co.uk/your-target-market_pbd?frm_action=edit&entry={id}'
    ],
     '27' =>  [
       'add' => 'https://investment.pitchprep.co.uk/your-market-research_pbd/',
       'edit' => 'https://investment.pitchprep.co.uk/your-market-research_pbd?frm_action=edit&entry={id}'
    ],
    '61' =>  [
       'add' => 'https://investment.pitchprep.co.uk/your-competitors_pbd/',
       'edit' => 'https://investment.pitchprep.co.uk/your-competitors_pbd?frm_action=edit&entry={id}'
    ],
    '62' =>  [
       'add' => 'https://investment.pitchprep.co.uk/customer-acquisition_pbd/',
       'edit' => 'https://investment.pitchprep.co.uk/customer-acquisition_pbd?frm_action=edit&entry={id}'
    ],
    '63' =>  [
       'add' => 'https://investment.pitchprep.co.uk/operations-and-logisitics_pbd/',
       'edit' => 'https://investment.pitchprep.co.uk/operations-and-logisitics_pbd?frm_action=edit&entry={id}'
    ],
    '64' =>  [
       'add' => 'https://investment.pitchprep.co.uk/management-and-organisation_pbd/',
       'edit' => 'https://investment.pitchprep.co.uk/management-and-organisation_pbd?frm_action=edit&entry={id}'
    ],
     '65' =>  [
       'add' => 'https://investment.pitchprep.co.uk/your-financial-plan_pbd/',
       'edit' => 'https://investment.pitchprep.co.uk/your-financial-plan_pbd?frm_action=edit&entry={id}'
    ]
  ];



    public function __construct(){
    
        $this->version = 1;
        
        if( $this->debug === true ){gfCustomCustomisation::
          ini_set('display_errors', 1);
          ini_set('display_startup_errors', 1);
          error_reporting(E_ALL); 
        }
        add_shortcode('ids-show-add-edit-link', [$this, 'showAddEditLink']);
        add_shortcode('ids-show-form-created-on', [$this, 'created_on_date']);
        add_shortcode('ids-show-form-updated-on', [$this, 'updated_on_date']);
        add_shortcode('form-Name', [$this, 'form_title']);
        add_shortcode('updated-success', [$this, 'update_success_msg']);
        add_shortcode('custom_class', [$this, 'custom_class_color']);
        add_shortcode( 'frm-show-description', [$this, 'frm_show_feild_data']);
        add_action( 'user_register', [$this, 'createEntry'], 10, 1 );
        add_action( 'wp_enqueue_scripts', [$this,'ids_custom_js_views'] );
        add_action( 'init', function(){
            add_shortcode( 'Redirecting-to-view', [$this, 'redirecting_view']);
        } );
        add_action( 'template_redirect', [$this, 'checkUserBeforeRender'] );
  }
  
  
    public function checkUserBeforeRender() {
        $this-> pageid = get_queried_object_id();
        $passedUserID = $_GET['userid'];
        $Current_user = wp_get_current_user()->ID;
        $Is_admin = in_array('administrator',  wp_get_current_user()->roles);
        $check = ($Current_user == $passedUserID);
        if ($this->pageid == 786){
            if (!is_user_logged_in() || !($Is_admin) && !($Current_user == $passedUserID)) {
                 global $wp_query;
                $wp_query->set_404();
                status_header( 404 );
                get_template_part( 404 ); 
                exit();
                // // $page_404 = get_permalink( $404_page_id );
                // $this->debug(get_permalink(  ));
                // wp_redirect( $page_404 );
                // exit();
            }
        }
    }

    public function createEntry($userID){
      
        if( class_exists('FrmEntry')){
        
            $userInfo = get_userdata($userID);
                 
            $arg = array(
              'form_id' => 75, //change 5 to your form id
              'item_key' => 'entry', //change entry to a dynamic value if you would like
              'frm_user_id' => $userID, //change $user_ID to the id of the user of your choice (optional)
              'item_meta' => array(
                993 => $userID,
                995 => $userInfo->user_login,
              ),
            );
         
            $response = FrmEntry::create($arg);
            
           // echo '<pre>';
           // var_dump($response);
           // die; 
        }
          
    }
    
    public function ids_custom_js_views(){
        wp_enqueue_script( 'ids_custom_js', plugin_dir_url( __FILE__ ) . 'assests/custom.js', array( 'jquery' ) );
        wp_enqueue_style( 'ids_custom_css_views', plugin_dir_url( __FILE__ ) . 'assests/custom.css');
    }

    public function redirecting_view(){
        $user_ID = wp_get_current_user()->ID;
            if(is_user_logged_in()&&($this->pageid == 2022) ){
            $url = "https://investment.pitchprep.co.uk/admin-view/?userid={$user_ID}";
            wp_redirect( $url );
            exit;
      }
    }

  
    public function update_success_msg(){
            if(isset($_GET['updated'])&&($_GET['updated'])=='successfully'){
            $succsess_msg = '<div class="frm_message_update_ids" id="idsCustomFlashMsg" role="status"><p>Your submission was successfully saved</p></div>';
            return $succsess_msg;
        }
    
    }

    public function form_data_common($args){
         $currentUserID = get_current_user_id();
        if( empty($currentUserID)){
            return;
    }
    
    if(empty($args['form-id'])){
        return;
    }

    $formID = $args['form-id'];
    
    if( empty($this->formConfiguration[$formID])){
        return;
    }
    
    $entries = FrmEntry::getAll( [
        'it.form_id' => $formID,
        'user_id' => $currentUserID
    ], ' ORDER BY it.created_at DESC', 8);
    return ["entries"=>$entries, "FormID"=>$formID];
    
}
     public function custom_class_color($args){ 
         
          $getUserID = $_GET['userid'];
        if( empty($getUserID)){
            return;
        }
        
        if(empty($args['form-id'])){
            return;
        }
    
        $formID = $args['form-id'];
        $entries = FrmEntry::getAll( [
            'it.form_id' => $formID,
            'user_id' => $getUserID
        ], ' ORDER BY it.created_at DESC', 8);
        
        if(!empty($entries)){
            return "custom-fill";
        }else{
            return '';
        }
     }

   public function created_on_date($args){  
        $this->entries_data = $this->form_data_common($args);
        $configurationObject = $this->formConfiguration[$this->entries_data['FormID']];
        $form_entry_id = array_key_first($this->entries_data['entries']);
        $form_entries = $this->entries_data['entries'][$form_entry_id];
        $created_at = $form_entries->created_at;
        if( count($this->entries_data['entries']) ){
          $this->custom_link =  str_replace( '{id}', array_key_first($this->entries_data['entries']), $configurationObject['edit']  );
        }
        if (empty($created_at)) {
          return "<ul><li><u>Not submitted yet</u></li></ul>";
        }else{
        $formatted_date_created = (date("F j, Y, g:i a", strtotime($created_at)));
        return "<ul><li><a href='".$this->custom_link."'>$formatted_date_created</a></li></ul>";
      }
    }
    
    public function updated_on_date($args){
        $form_entry_id = array_key_first($this->entries_data['entries']);
        $form_entries = $this->entries_data['entries'][$form_entry_id];
        $updated_at = $form_entries->updated_at;
        if (empty($updated_at)) {
          return "";
        }else{
        $formatted_date_updated = (date("F j, Y, g:i a", strtotime($updated_at)));
        // return $this->custom_link;
         return "<ul><li><a href='".$this->custom_link."'>$formatted_date_updated</a></li></ul>";
      }
    }

    public function showAddEditLink($args){
      $configurationObject = $this->formConfiguration[$this->entries_data['FormID']];
      # Edit Page
      if( count($this->entries_data['entries']) ){
          return "<a href='$this->custom_link'>Edit</a>";
      }
      return "<a href='{$configurationObject['add']}'>Start</a>";
    }
    
     public function form_title($args){
        $this->entries_data = $this->form_data_common($args);
        // $this->debug($this->entries_data);
        $form_entry_id = array_key_first($this->entries_data['entries']);
        return($this->entries_data['entries'][$form_entry_id]->form_name);
    }
    
    function frm_show_feild_data( $atts ) {
    if ( ! isset( $atts['field_id'] ) || ! is_numeric( $atts['field_id'] ) ) {
    	return '';
    }
    $field_info = FrmField::getOne( $atts['field_id'] );
    return $field_info->description;
    }
    
    
    
 
  ## Plugin Activation Callback
  public function activation() {
    //wp_die( "Plugin has been activated" );
  }

  ## Plugin Deactivation Callback
  public function deactivation() {
    //wp_die( "Plugin has been deactivated" );
  }

  /**
  * Get a unique instance of this object.
  *
  * @return object
  */
  public static function get_instance() {
    if ( null === self::$instance ) {
      self::$instance = new gfCustomCustomisation();
    }
    return self::$instance;
  }

}

function gfCustomCustomisation(){
  return gfCustomCustomisation::get_instance();
}
// if( !empty( $_GET['dev-user'] ) || (!empty( $_GET['custom-dev'] ) && $_GET['custom-dev'] === 'preview')){
//   add_action( 'plugins_loaded', 'gfCustomCustomisation' );
// }

add_action( 'plugins_loaded', 'gfCustomCustomisation' );
add_action('frm_after_create_entry', 'update_or_create_entry');