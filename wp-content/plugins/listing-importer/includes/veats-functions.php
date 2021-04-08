<?php

/*
 * Functionality supporting import actions
 */

// Add a new menu for Plugin
add_action('admin_menu', 'veats_listing_link');
function veats_listing_link()
{
    add_menu_page(
        'Listing Importer Section', // Title of the page
        'Import Listing', // Text to show on the menu link
        'manage_options', // Capability requirement to see the link
        plugin_dir_path(__FILE__) . 'import-page.php', // The 'slug' - file to display when clicking the link
        '',
        'dashicons-food'
    );
}


//for posting data
/**
 * Handle POST submission
 *
 * @param array $options
 * @return void
 */
function post_listings($options)
{
    $errors = array();
    $errors['error'] = $errors['notice'] = $errors['success'];
    if (!isset($_POST['wp_csv_nonce_field']) || !wp_verify_nonce($_POST['wp_csv_nonce_field'], 'wp_import_csv_action')) {
        $errors['error'][] = 'Invalid attempt';
        print_messages($errors);
        return;
    }

    if (!current_user_can('import')) {
        $errors['error'][] = 'You are not permitted for import data';
        print_messages($errors);
        return;
    }

    if (empty($_POST['page_type'])) {
        $errors['error'][] = 'Please select post type';
        print_messages($errors);
        return;
    }

    if (empty($_FILES['csv_import']['tmp_name'])) {
        $errors['error'][] = 'No file uploaded, aborting.';
        print_messages($errors);
        return;
    }

    if (!current_user_can('publish_pages') || !current_user_can('publish_posts')) {
        $errors['error'][] = 'You don\'t have the permissions to upload th listings. Please contact the administrator.';
        print_messages($errors);
        return;
    }

    $csv_file = $_FILES['csv_import']['tmp_name'];
    //echo $csv_file = sanitize_file_name($csv_file);
    $filename = sanitize_file_name($_FILES['csv_import']['name']);
    $type = strtolower(substr($filename, -3));
    //$type = sanitize_file_name($type);
    echo $type;
    if ($type != 'csv') {
        $errors['error'][] = 'File format is wrong.';
        print_messages($errors);
        return;
    }

    if (!is_file($csv_file)) {
        $errors['error'][] = 'Failed to load file';
        print_messages($errors);
        return;
    }

    $pageType = $_POST['page_type'];
    /**
     *  post type
     * */
    if ($pageType != '') {
        /** Store .csv file value into a array */
        $fldAry = array("listing_name",
            "address",
            "phone",
            "website",
            "classification",
            "site_email",
            "menu",
            "opening_hour",
            "category",
            "currency",
            "description",
            "image_link"
        );
        $arry = csvIndexArray($csv_file, ",", $fldAry, 0);
        $skipped = 0;
        $imported = 0;
        $time_start = microtime(true);
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['baseurl'];
        if ($_POST['page_type'] != 'place') return;
        global $post, $wpdb;
        $updated = false;

        if (count($arry) > 0):
            foreach ($arry as $data) {
                $data = wp_slash($data);
                $current_id = 0;
                wp_reset_postdata();
                $post_title = $data['post_title'];
                $user_id = get_current_user_id();
                if (($data['site_email'] != '') && ($data['site_email'] != 'NA') && ($data['site_email'] != 'N/A')) {
                    $args = array(
                        'post_type' => 'place',
                        'numberposts' => 1,
                        'meta_query' => array(
                            array(
                                'key' => GOLO_METABOX_PREFIX . 'place_email',
                                'value' => trim($data['site_email']),
                                'compare' => '=',
                            )
                        )
                    );
                    $listings = get_posts($args);
                    if( $listings ){
                        foreach ( $listings as $list ){
                            $current_id = $list->ID;
                        }
                    }
                }
                if( !$current_id ){
                    if( ($data['address'] != '') && ($data['address'] != 'N/A')){
                        $post_title = sanitize_title(convert_chars($data['post_title']));
                        $args = array(
                            'post_type' => 'place',
                            'name' => $post_title,
                            'posts_per_page' => 1,
                            'meta_query' => array(
                                array(
                                    'key' => GOLO_METABOX_PREFIX . 'place_address',
                                    'value' => trim($data['address']),
                                    'compare' => '=',
                                )
                            )
                        );
                        $listings = get_posts($args);
                        if( $listings ){
                            foreach ( $listings as $list ){
                                $current_id = $list->ID;
                            }
                        }
                    } else{
                        $current_listing = get_page_by_title(convert_chars($data['listing_name']), OBJECT, 'place');
                        if ($current_listing) {
                            $current_id = $current_listing->ID;
                        }
                    }
                }

                if ($current_id) {
                    $update_post = array(
                        'ID' => (int) $current_id,
                        'post_title' => convert_chars($data['listing_name']),
                        'post_type' => 'place',
                        'post_status' => 'publish',
                        'post_content' => $data['description'],
                        'post_modified' => date('Y-m-d h:m:s'),
                        'post_author' => $user_id
                    );
                    $listing_id = wp_update_post($update_post);
                    if($listing_id){
                        $updated = true;
                    }
                } else {
                    /* create new Listings */
                    $new_post = array(
                        'post_title' => convert_chars($data['listing_name']),
                        'post_type' => 'place',
                        'post_status' => 'publish',
                        'post_content' => $data['description'],
                        'post_modified' => date('Y-m-d h:m:s'),
                        'post_author' => $user_id
                    );
                    // Insert the post into the database
                    $listing_id = wp_insert_post($new_post);
                    if($listing_id){
                        $updated = true;
                    }
                }

                if ($listing_id) {
                    //assigning listing to categories
                    //assign listing to category place-categories ie Category
                    $place_categories = array();
                    if( ($data['category'] != '') && ($data['category'] != 'N/A') ){
                        $categories_raw = explode(",", $data['category']);
                        if( is_array($categories_raw) ){
                            foreach ($categories_raw as $cat){
                                //if(strpos($cat, 'Cuisine:') !== false){
                                    //$cat = explode(":", $cat)[1];
                                //}
                                $all_categories = get_term_by('name', $cat, 'place-categories');
                                if($all_categories){
                                    $place_categories[] = $all_categories->term_id;
                                } else{
                                    $new_term = wp_insert_term($cat,'place-categories' );
                                    if($new_term) $place_categories[] = $new_term->term_id;
                                }
                            }
                        }
                    }

                    //assign listing to category place-amenities ie Classification
                    $classifications = array();
                    if( ($data['classification'] != '') && ($data['classification'] != 'N/A') ){
                        $categories_raw = explode(",", $data['classification']);
                        if( is_array($categories_raw) ){
                            foreach ($categories_raw as $cat){
                                $all_categories = get_term_by('name', $cat, 'place-amenities');
                                if($all_categories){
                                    $classifications[] = $all_categories->term_id;
                                } else{
                                    $new_term = wp_insert_term($cat,'place-amenities' );
                                    if($new_term) $classifications[] = $new_term->term_id;
                                }
                            }
                        }
                    }

                    //assign listing to category place-city ie City Town
                    $city_category = array();
                    if( ($data['address'] != '') && ($data['address'] != 'N/A') ){
                        //get lat and lang from address

                        $address =$data['address']; // Google HQ

                        $apiKey = 'AIzaSyDR1jhRumbcatSVa3JFXid7NL0fzNXtiVM';
                        $prepAddr = str_replace(' ','+',$address);


                        //$geocode=file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false&key='.$apiKey);
                        //$output= json_decode($geocode);
                        //$latitude = $output->results[0]->geometry->location->lat;
                        //$longitude = $output->results[0]->geometry->location->lng;
                        $address_details = get_address_details($address, $apiKey);
                        if( isset($address_details['city']) && !empty($address_details['city']) ){
                            $city = $address_details['city'];
                            $smallCity = strtolower($city);
                            if(strpos($smallCity, 'sydney') != false){
                                $city = 'Sydney';
                            }
                            if(strpos($smallCity, 'melbourne') != false){
                                $city = 'Melbourne';
                            }
                            if(strpos($smallCity, 'adelaide') != false){
                                $city = 'Adelaide';
                            }
                            if(strpos($smallCity, 'perth') != false){
                                $city = 'Perth';
                            }
                            if(strpos($smallCity, 'brisbane') != false){
                                $city = 'Brisbane';
                            }
                            $all_categories = get_term_by('name', $city, 'place-city');
                            if($all_categories){
                                $city_category[] = $all_categories->term_id;
                            } else{
                                $new_term = wp_insert_term($cat,'place-city' );
                                if($new_term) $city_category[] = $new_term->term_id;
                            }
                        }
                        if( isset($address_details['zip']) && ($address_details['zip'] != '') ){
                            update_post_meta($listing_id, GOLO_METABOX_PREFIX . 'place_zip', trim($data['address']));
                        }
                        $formatedAddress = trim($data['address']);
                        if( isset($address_details['formatedAddress']) && ($address_details['formatedAddress'] != '') ){
                            $formatedAddress = $address_details['formatedAddress'];
                        }
                        update_post_meta($listing_id, GOLO_METABOX_PREFIX . 'place_address', $formatedAddress);
                    }
                    if( !empty($city_category) ){
                        //wp_set_post_terms( $listing_id, $city_category, 'place-city' );
                        wp_set_object_terms( $listing_id, $city_category, 'place-city' );
                    }
                    if( !empty($place_categories) ){
                        wp_set_object_terms( $listing_id, $place_categories, 'place-categories' );
                    }
                    if( !empty($classifications) ){
                        wp_set_object_terms( $listing_id, $classifications, 'place-amenities' );
                    }

                    //now save meta fields
                    if( ($data['phone'] != '') && ($data['phone'] != 'N/A') ){
                        update_post_meta($listing_id, GOLO_METABOX_PREFIX . 'place_phone', $data['phone']);
                    }
                    if( ($data['website'] != '') && ($data['website'] != 'N/A') ){
                        update_post_meta( $listing_id, GOLO_METABOX_PREFIX.'place_website', $data['website'] );
                    }
                    if( ($data['site_email'] != '') && ($data['site_email'] != 'N/A') ){
                        update_post_meta( $listing_id, GOLO_METABOX_PREFIX.'place_email', $data['site_email'] );
                    }
                    if( ($data['currency'] != '') && ($data['currency'] != 'N/A') ){
                        update_post_meta( $listing_id, GOLO_METABOX_PREFIX.'place_price_short', $data['currency'] );
                    }
                    update_post_meta($listing_id, GOLO_METABOX_PREFIX . 'opening_sunday', 'Sunday');
                    update_post_meta($listing_id, GOLO_METABOX_PREFIX . 'opening_monday', 'Monday');
                    update_post_meta($listing_id, GOLO_METABOX_PREFIX . 'opening_tuesday', 'Tuesday');
                    update_post_meta($listing_id, GOLO_METABOX_PREFIX . 'opening_wednesday', 'Wednesday');
                    update_post_meta($listing_id, GOLO_METABOX_PREFIX . 'opening_thursday', 'Thursday');
                    update_post_meta($listing_id, GOLO_METABOX_PREFIX . 'opening_friday', 'Friday');
                    update_post_meta($listing_id, GOLO_METABOX_PREFIX . 'opening_saturday', 'Saturday');
                    if( ($data['opening_hour'] != '') && ($data['opening_hour'] != 'N/A') ){
                        //update_post_meta( $listing_id, 'golo-place_phone', $data['phone'] );
                        $sunday = $monday = $tuesday = $wednesday = $thursday = $friday = $saturday = '';
                        $prev = false;
                        $all_days = preg_split('/\r\n|\n|\r/', $data['opening_hour']);
                        if( !empty($all_days) ){
                            $opening_hours = get_opening_hours($all_days);
                            if (isset($opening_hours['monday'])) {
                                update_post_meta($listing_id, GOLO_METABOX_PREFIX . 'opening_monday_time', $opening_hours['monday']);
                            }
                            if (isset($opening_hours['tuesday'])) {
                                update_post_meta($listing_id, GOLO_METABOX_PREFIX . 'opening_tuesday_time', $opening_hours['tuesday']);
                            }
                            if (isset($opening_hours['wednesday'])) {
                                update_post_meta($listing_id, GOLO_METABOX_PREFIX . 'opening_wednesday_time', $opening_hours['wednesday']);
                            }
                            if (isset($opening_hours['thursday'])) {
                                update_post_meta($listing_id, GOLO_METABOX_PREFIX . 'opening_thursday_time', $opening_hours['thursday']);
                            }
                            if (isset($opening_hours['friday'])) {
                                update_post_meta($listing_id, GOLO_METABOX_PREFIX . 'opening_friday_time', $opening_hours['friday']);
                            }
                            if (isset($opening_hours['saturday'])) {
                                update_post_meta($listing_id, GOLO_METABOX_PREFIX . 'opening_saturday_time', $opening_hours['saturday']);
                            }
                            if (isset($opening_hours['sunday'])) {
                                update_post_meta($listing_id, GOLO_METABOX_PREFIX . 'opening_sunday_time', $opening_hours['sunday']);
                            }
                        }
                    }
                    $imported++;
                }//end if $listing

                print_messages($errors);
            }
            if($updated){
                $errors['success'][] = "Listing Updated Successfully";
            }
        endif;
    }
    if (file_exists($csv_file)) {
        @unlink($csv_file);
    }

    $exec_time = microtime(true) - $time_start;

    if ($skipped) {
        $errors['notice'][] = "<b>Skipped {$skipped} posts (most likely due to empty title, body ).</b>";
    }
    $errors['notice'][] = sprintf("<b>Imported {$imported} listings in %.2f seconds.</b>", $exec_time);
    print_messages($errors);
}

function get_opening_hours(array $all_days){
    $days = array();
    $orders = array(
        'monday' => 0,
        'tuesday' => 1,
        'wednesday' => 2,
        'thursday' => 3,
        'friday' => 4,
        'saturday' => 5,
        'sunday' => 6
    );
    if( !empty($all_days) ){
        //print_r($all_days);
        $dataFormat = $cons = 0;
        $start_key = $end_key = '';
        foreach ( $all_days as $day ){
            $day = preg_replace('/[ \t]+/', ' ', preg_replace('/\s*$^\s*/m', "\n", $day));
            if (strpos($day, '-') !== false) {
                if($dataFormat == 4){
                    $days[$dateNow] = $day;
                } else{
                    $substrs = explode('-', $day);
                    if( count($substrs) > 2 ){
                        //eg. Thu - Sun: 12am-3pm //Monday - Tuesday 5:00 pm - 9:00 pm
                        $dayWithTime = explode('-', $substrs[1]);
                        $startDayPosition = strpos($day, '-');
                        //Get the first half of the string
                        $start_key = substr($day, 0, $startDayPosition);

                        //Get the second half of the string
                        $endDateString = substr($day, ($startDayPosition));
                        //Tuesday 5:00 pm - 9:00 pm // Sun: 12am-3pm
                        $endDateString = trim($endDateString);

                        $prefix = '- ';
                        if (substr($endDateString, 0, strlen($prefix)) == $prefix) {
                            $endDateString = substr($endDateString, strlen($prefix));
                        }

                        $endDatePosition = strpos($endDateString, ' ');
                        $end_key = substr($endDateString, 0, $endDatePosition);

                        //Get the first half of the string
                        $end_key = substr($endDateString, 0, $endDatePosition);
                        $opnTime = trim(substr($endDateString, ($endDatePosition)));


                        $dayOnly = true;
                        $dataFormat = 1;
                        $start_key = preg_replace('/[^A-Za-z0-9]/', '', $start_key);
                        $end_key = preg_replace('/[^A-Za-z0-9]/', '', $end_key);
                        $start_key = get_the_day($start_key);
                        $end_key = get_the_day($end_key);

                        foreach ( $orders as $key=>$order ){
                            if( ($order >= $orders[$start_key]) && ($order <= $orders[$end_key]) ){
                                $days[$key] = $opnTime;
                            }
                        }
                    } else{
                        if( preg_match('/\\d/', $substrs[1]) ){
                            //check if string contains number.eg sun 5-11. Sunday 11:00am-10:00pm //12:00 pm - 3:00 pm
                            if( $dataFormat == 3 ){
                                foreach ( $orders as $key=>$order ){
                                    if( ($order >= $orders[$start_key]) && ($order <= $orders[$end_key]) ){
                                        $days[$key] = trim($day);
                                    }
                                }
                                $dataFormat = 0;
                            } else{
                                $day = trim($day);
                                $dayValue = explode(' ', $day);
                                //$dayValue = preg_split('/\s+/', $day);
                                $currentDate = get_the_day(trim(str_replace( ':', '', $dayValue[0])));
                                if( $currentDate != 'not_exist'){
                                    //Get the first occurrence of a character.
                                    $strpos = strpos($day, ' ');
                                    //Get the first half of the string
                                    $stringSplit1 = substr($day, 0, $strpos);
                                    //Get the second half of the string
                                    $stringSplit2 = substr($day, ($strpos));
                                    $days[$currentDate] = $stringSplit2;
                                } else{
                                    if( isset($days[$start_key]) ){
                                        $days[$start_key] = $days[$start_key].' & '.$day;
                                    } else{
                                        $days[$start_key] = trim($day);
                                    }
                                    if( isset($days[$end_key]) ){
                                        $days[$end_key] = $days[$end_key].' & '.$day;
                                    } else{
                                        $days[$end_key] = trim($day);
                                    }
                                }
                                $dataFormat = 2;
                            }
                        } else{ //eg. sun-sat
                            $dayOnly = true;
                            $start_key = get_the_day(str_replace( ':', ' ', $substrs[0]));
                            $end_key = get_the_day(str_replace( ':', ' ', $substrs[1]));
                            $dataFormat = 3;
                        }

                    }
                }
            } else{
                $dateNow = get_the_day(trim($day));
                $dataFormat = 4;
            }
        }
    }
    return $days;
}

function get_the_day($day){
    switch ( trim(strtolower($day)) ){
        case 'mon':
        case 'monday':
            return 'monday';
            break;
        case 'tue':
        case 'tuesday':
            return 'tuesday';
            break;
        case 'wed':
        case 'wednesday':
            return 'wednesday';
            break;
        case 'thu':
        case 'thur':
        case 'thursday':
            return 'thursday';
            break;
        case 'fri':
        case 'friday':
            return 'friday';
            break;
        case 'sat':
        case 'saturday':
            return 'saturday';
            break;
        case 'sun':
        case 'sunday':
            return 'sunday';
            break;
        default:
            return 'monday';
            break;
    }
}

/**
 * Determine value of option $name from database, $default value or $params,
 * save it to the db if needed and return it.
 *
 * @param string $name
 * @param mixed $default
 * @param array $params
 * @return string
 */
function process_option($name, $default, $params)
{
    if (array_key_exists($name, $params)) {
        $value = stripslashes($params[$name]);
    } elseif (array_key_exists('_' . $name, $params)) {
        // unchecked checkbox value
        $value = stripslashes($params['_' . $name]);
    } else {
        $value = null;
    }
    $stored_value = get_option($name);
    if ($value == null) {
        if ($stored_value === false) {
            if (is_callable($default) &&
                method_exists($default[0], $default[1])) {
                $value = call_user_func($default);
            } else {
                $value = $default;
            }
            add_option($name, $value);
        } else {
            $value = $stored_value;
        }
    } else {
        if ($stored_value === false) {
            add_option($name, $value);
        } elseif ($stored_value != $value) {
            update_option($name, $value);
        }
    }
    return $value;
}


function print_messages($response)
{
    if (!empty($response)) {

        // messages HTML {{{
        ?>
        <div class="wrap">
            <?php if (!empty($response['error'])): ?>
                <div class="error">
                    <?php foreach ($response['error'] as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($response['notice'])): ?>
                <div class="updated fade">
                    <?php foreach ($response['notice'] as $notice): ?>
                        <p><?php echo $notice; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($response['success'])): ?>
                <div class="updated fade">
                    <?php foreach ($response['success'] as $success): ?>
                        <p><?php echo $success; ?></p>
                    <?php endforeach; ?>
                </div>

            <?php endif; ?>
        </div><!-- end wrap -->
        <?php
        // end messages HTML }}}

        $response = array();
    }
}


/** Reterive data from csv file to array format */
function csvIndexArray($filePath = '', $delimiter = '|', $header = null, $skipLines = -1)
{
    $lineNumber = 0;
    $dataList = array();
    //$headerItems = array();
    if (($handle = fopen($filePath, 'r')) != FALSE) {

        while (($items = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if ($lineNumber == 0) {
                //$header = $items;
                $lineNumber++;
                continue;
            }

            $record = array();
            for ($index = 0, $m = count($header); $index < $m; $index++) {
                //If column exist then and then added in data with header name
                if (isset($items[$index])) {
                    $itmcont = trim(mb_convert_encoding(str_replace('"', '', $items[$index]), "utf-8", "HTML-ENTITIES"));
                    $record[$header[$index]] = str_replace('#', ',', $itmcont);
                }
            }
            $dataList[] = $record;


        }
        fclose($handle);
    }
    return $dataList;
}


//get address details
function get_address_details($address, $apiKey){
    //$geolocation = $latitude.','.$longitude;
    $request = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false&key='.$apiKey;
    $file_contents = file_get_contents($request);
    $json_decode = json_decode($file_contents);
    $address = array();
    if(isset($json_decode->results[0])) {
        $response = array();
        foreach($json_decode->results[0]->address_components as $addressComponet) {
            if(in_array('political', $addressComponet->types)) {
                $response[] = $addressComponet->short_name;
            }
        }
        if(isset($json_decode->results[0]->address_components[7]) && ($json_decode->results[0]->address_components[7] != '')){
            $address['zip'] = $json_decode->results[0]->address_components[7]->short_name;
        }
        if(isset($json_decode->results[0]->formatted_address) && ($json_decode->results[0]->formatted_address != '')){
            $address['formatedAddress'] = $json_decode->results[0]->formatted_address;
        }

        if(isset($response[0])){ $first  =  $response[0];  } else { $first  = 'null'; }
        if(isset($response[1])){ $second =  $response[1];  } else { $second = 'null'; }
        if(isset($response[2])){ $third  =  $response[2];  } else { $third  = 'null'; }
        if(isset($response[3])){ $fourth =  $response[3];  } else { $fourth = 'null'; }
        if(isset($response[4])){ $fifth  =  $response[4];  } else { $fifth  = 'null'; }

        if( $first != 'null' && $second != 'null' && $third != 'null' && $fourth != 'null' && $fifth != 'null' ) {
            $address['city'] = $second;
            $address['state'] = $fourth;
            $address['country'] = $fifth;
        }
        else if ( $first != 'null' && $second != 'null' && $third != 'null' && $fourth != 'null' && $fifth == 'null'  ) {
            $address['city'] = $second;
            $address['state'] = $third;
            $address['country'] = $fourth;
        }
        else if ( $first != 'null' && $second != 'null' && $third != 'null' && $fourth == 'null' && $fifth == 'null' ) {
            $address['city'] = $first;
            $address['state'] = $second;
            $address['country'] = $third;
        }
        else if ( $first != 'null' && $second != 'null' && $third == 'null' && $fourth == 'null' && $fifth == 'null'  ) {
            $address['city'] = $first;
            $address['state'] = $first;
            $address['country'] = $second;
        }
        else if ( $first != 'null' && $second == 'null' && $third == 'null' && $fourth == 'null' && $fifth == 'null'  ) {
            $address['country'] = $first;
        }
    }
    return $address;
}





//admins styles and scripts
if (isset($_GET['page']) && $_GET['page'] == 'import-page') {
    add_action('admin_footer', 'veats_importer_admin_scripts');
}
if (!function_exists('veats_importer_admin_scripts')):
    function veats_importer_admin_scripts()
    {
// wp_register_style( 'wp_importer_admin_style', plugins_url( 'css/admin-min.css',__FILE__ ) );
// wp_enqueue_style( 'wp_importer_admin_style' );
        echo $script = '<script type="text/javascript">
	/* Wp Importer js for admin */
	jQuery(document).ready(function(){
		jQuery(".wpimporter-tab").hide();
		jQuery("#div-wpimporter-general").show();
	    jQuery(".wpimporter-tab-links").click(function(){
		var divid=jQuery(this).attr("id");
		jQuery(".wpimporter-tab-links").removeClass("active");
		jQuery(".wpimporter-tab").hide();
		jQuery("#"+divid).addClass("active");
		jQuery("#div-"+divid).fadeIn();
		});
	jQuery(".button-primary").click(function(){
	 if(confirm("Click OK to continue?")){
      }
	})
	}); 
	</script>';
    }
endif;


function print_out($arr, $bool = false)
{
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
    if ($bool) die('die');
}