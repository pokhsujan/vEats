<?php //require_once plugin_dir_path(__FILE__) . 'veats-functions.php'; ?>
<?php 
	$opt_draft = process_option('csv_importer_import_as_draft', 'publish', $_POST);
    $opt_cat = process_option('csv_importer_cat', 0, $_POST);

    if ('POST' == $_SERVER['REQUEST_METHOD']) {
        post_listings(compact('opt_draft', 'opt_cat'));
        //print_r(compact('opt_draft', 'opt_cat'));
    } ?>
<div class="wrap">
<div id="veats-settings"> 
    <h1> Importer Settings</h1><hr />
    <form class="add:the-list: validate" method="post" enctype="multipart/form-data">
	<div style="width: 80%; padding: 10px; margin: 10px;"> 
	<div id="listing-import-tab-menu"><a id="listing-import-general" class="listing-import-tab-links active" >Import</a> <a  id="listing-import-support" class="listing-import-tab-links">File</a> </div>
	<div class="listing-import-setting">
	<!-- General Setting -->	
	<div class="first listing-import-tab" id="div-listing-import-general">
		
		<table border="5" cellpadding="20" width="80%">
			<tr>
			<td valign="top">		
			<h2>General Settings</h2>
			<?php wp_nonce_field( 'wp_import_csv_action', 'wp_csv_nonce_field' ); ?>
				<!-- Parent category -->
		        <p><label for="page_type">Choose Post Type:</label> 
		        <select name="page_type" id="page_type">
						<option value="">Select post type</option>
						<?php 
						$args = array(
						   'public'   => true,
						   '_builtin' => false
						);

						$output = 'names'; // names or objects, note names is the default
						$operator = 'and'; // 'and' or 'or'

						$post_types = get_post_types( $args, $output, $operator );
						//array_push($post_types,'post');array_push($post_types,'page');
							
						foreach ( $post_types  as $post_type ) {
                            $selected='';
						    if( $post_type =='place' ) $selected =' selected ="selected"';
							echo '<option value="'.$post_type.'"'.$selected.'>'.$post_type.'</option>';
						}

						?>
				</select>
				<?php //print_r($post_types); ?>
				</p>
			</td></tr>
			<tr><td>
		        <p><label for="csv_import">Upload file:</label><input name="csv_import" id="csv_import" type="file" value="" aria-required="true" /></p>
		        <p class="submit"><?php submit_button("Import Now"); ?></p>
		        </td>
	        </tr>
	    </table>
    </div>
    	<?php
//        $address = '48-50 Alfred St South, Milson\'s Point, Sydney, New South Wales,'; // Google HQ
//        $prepAddr = str_replace(' ','+',$address);
//        $apiKey = 'AIzaSyDR1jhRumbcatSVa3JFXid7NL0fzNXtiVM';
//
//        $geocode=file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false&key='.$apiKey);
//
//        //print_r($geocode);
//
//        $output= json_decode($geocode);
//        $latitude = $output->results[0]->geometry->location->lat;
//        $longitude = $output->results[0]->geometry->location->lng;
//        print_out($output->results[0]);
//        echo $latitude;
//        echo $longitude;
//        //$allAdd = get_address_details($latitude, $longitude, $apiKey);
//        $allAdd = get_address_details($address, $apiKey);
//        echo 'oee hoee';
//        echo "<br>";
//        print_out($allAdd);
        ?>
    </div>
</div>