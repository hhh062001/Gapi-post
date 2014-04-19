<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
<?php
$did = 1;

// First check if ID exist with requested ID
$sSql = $wpdb->prepare(
	"SELECT COUNT(*) AS `count` FROM ".WP_GAPI_CONFIG_TABLE."
	WHERE `gapi_id` = %d",
	array($did)
);
$result = '0';
$result = $wpdb->get_var($sSql);

if ($result != '1')
{
	?><div class="error fade"><p><strong><?php _e('Oops, selected details doesnt exist','gapi-posts'); ?></strong></p></div><?php
}
else
{
	$gapi_errors = array();
	$gapi_success = '';
	$gapi_error_found = FALSE;
	
	$sSql = $wpdb->prepare("
		SELECT *
		FROM `".WP_GAPI_CONFIG_TABLE."`
		WHERE `gapi_id` = %d
		LIMIT 1
		",
		array($did)
	);
	$data = array();
	$data = $wpdb->get_row($sSql, ARRAY_A);
	
	// Preset the form fields
	$form = array(
		'ga_email' => $data['ga_email'],
		'ga_password' => $data['ga_password'],
		'ga_profile_id' => $data['ga_profile_id'],
		'ga_weekly' => $data['ga_weekly'],
		'ga_monthly' => $data['ga_monthly'],
		'ga_yearly' => $data['ga_yearly'],
		'max_records' => $data['max_records']
	);
}
// Form submitted, check the data
if (isset($_POST['gapi_form_submit']) && $_POST['gapi_form_submit'] == 'yes')
{
	//	Just security thingy that wordpress offers us
	check_admin_referer('gapi_form_edit');
	
	$form['ga_email'] = isset($_POST['ga_email']) ? $_POST['ga_email'] : '';
	$form['ga_password'] = isset($_POST['ga_password']) ? $_POST['ga_password'] : '';
	$form['ga_profile_id'] = isset($_POST['ga_profile_id']) ? $_POST['ga_profile_id'] : '';
	$form['ga_weekly'] = isset($_POST['ga_weekly']) ? $_POST['ga_weekly'] : '';
	$form['ga_monthly'] = isset($_POST['ga_monthly']) ? $_POST['ga_monthly'] : '';
	$form['ga_yearly'] = isset($_POST['ga_yearly']) ? $_POST['ga_yearly'] : '';
	$form['max_records'] = isset($_POST['max_records']) ? $_POST['max_records'] : '';

	//	No errors found, we can add this Group to the table
	if ($gapi_error_found == FALSE)
	{	
		$sSql = $wpdb->prepare(
				"UPDATE `".WP_GAPI_CONFIG_TABLE."`
				SET `ga_email` = %s,
				`ga_password` = %s,
				`ga_profile_id` = %s,
				`ga_weekly` = %s,
				`ga_monthly` = %s,
				`ga_yearly` = %s,
				`max_records` = %s
				WHERE gapi_id = %d
				LIMIT 1",
				array(trim($form['ga_email']), base64_encode($form['ga_password']), trim($form['ga_profile_id']), $form['ga_weekly'], $form['ga_monthly'], $form['ga_yearly'], $form['max_records'], $did)
			);
		$wpdb->query($sSql);
		
		$gapi_success = __('Details was successfully updated.','gapi-posts');
	}
}

if ($gapi_error_found == TRUE && isset($gapi_errors[0]) == TRUE)
{
	?>
	<div class="error fade">
	<p><strong><?php echo $gapi_errors[0]; ?></strong></p>
	</div>
	<?php
}
if ($gapi_error_found == FALSE && strlen($gapi_success) > 0)
{
	?>
	<div class="updated fade">
	<p><strong><?php echo $gapi_success; ?></strong></p>
	</div>
	<?php
}
?>

<div class="form-wrap">
	<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
	<h2><?php _e('Google Analytic Top Posts','gapi-posts'); ?></h2>
	<form name="gapi_form" method="post" action="#">
      <h3><?php _e('Edit Configuration','gapi-posts'); ?></h3>	  
	 
	  <label for="tag-image"><?php _e('Enter Google Analytic Email','gapi-posts'); ?></label>
      <input name="ga_email" type="text" id="ga_email" value="<?php echo esc_html(stripslashes($form['ga_email'])); ?>" />
	  
	  <label for="tag-image"><?php _e('Enter Google Analytic Password','gapi-posts'); ?></label>
      <input name="ga_password" type="text" id="ga_password" value="<?php echo base64_decode($form['ga_password']); ?>" />
	  
	  <label for="tag-image"><?php _e('Googe profile ID','gapi-posts'); ?></label>
      <input name="ga_profile_id" type="text" id="ga_profile_id" size="20" value="<?php echo $form['ga_profile_id']; ?>" />
      
      <label for="tag-image"><?php _e('Weekly','gapi-posts'); ?>&nbsp;<input name="ga_weekly" type="checkbox" id="ga_weekly" size="20" value="1" <?php if($form['ga_weekly'] == 1){ echo "checked"; }?> /></label>
      
      
      <label for="tag-image"><?php _e('Monthly','gapi-posts'); ?>&nbsp;<input name="ga_monthly" type="checkbox" id="ga_monthly" size="20" value="1" <?php if($form['ga_monthly'] == 1){ echo "checked"; }?> /></label>
      
      
      <label for="tag-image"><?php _e('Yearly','gapi-posts'); ?>&nbsp;<input name="ga_yearly" type="checkbox" id="ga_yearly" size="20" value="1" <?php if($form['ga_yearly'] == 1){ echo "checked"; }?> /></label>
      
      
      <label for="tag-image"><?php _e('Maximum Records Display','gapi-posts'); ?></label>
      <input name="max_records" type="text" id="max_records" size="20" value="<?php echo $form['max_records']; ?>" />	  
	  
      <input name="gapi_id" id="gapi_id" type="hidden" value="">
      <input type="hidden" name="gapi_form_submit" value="yes"/>
      <p class="submit">
        <input name="publish" lang="publish" class="button add-new-h2" value="<?php _e('Update Details','gapi-posts'); ?>" type="submit" />
        <input name="publish" lang="publish" class="button add-new-h2" value="<?php _e('Cancel','gapi-posts'); ?>" type="button" />
      </p>
	  <?php wp_nonce_field('gapi_form_edit'); ?>
    </form>
</div>
</div>