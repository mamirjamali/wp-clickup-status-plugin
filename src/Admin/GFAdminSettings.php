<?php
//Admin settings for Intigration with Gravity Form
namespace CUSTATUS\ClickUpStatusPlugin\Admin;

class GFAdminSettings {
    public function __construct() {
        // Add an action to initialize the admin settings
        add_action('admin_menu', array($this, 'addAdminMenu'));
        // Add an action to register the settings
        add_action('admin_init', array($this, 'registerSettings'));
    }

    // Add a menu item to the admin dashboard
    public function addAdminMenu() {
        add_menu_page(
            __('Click Up Status Settings', 'clickup-status-plugin'),
            __('Click Up Status Settings', 'clickup-status-plugin'),      
            'manage_options',
            'click_up_status_settings',   
            array($this, 'redirect'), 
            'dashicons-admin-generic'
        );

        // Add submenu "GF Settings" under "ClickUp Settings"
        add_submenu_page(
            'click_up_status_settings',
            __('GF Settings', 'clickup-status-plugin'),
            __('GF Settings', 'clickup-status-plugin'),
            'manage_options',
            'click_up_gf_settings', 
            array($this, 'renderSettingsPage')
        );

    }


    // Redirect function to send to "GF Settings" submenu
    public function redirect() {
        // Use JavaScript to redirect to "GF Settings" submenu
        echo '<script>window.location.href = "' . admin_url('admin.php?page=click_up_gf_settings') . '";</script>';
    }

    // Render the settings page
    public function renderSettingsPage() {
        ?>
        <head>
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
        </head>
        <div class="wrap">
            <form>
                <form method="post" action="">
                    <?php
                    settings_fields('click_up_settings');
                    do_settings_sections('click_up_settings');
                    ?>
                </form> <!-- Close the form for Form Setting -->

                <form method="post" action="">
                  <h4><?php _e('Add New Form', 'clickup-status-plugin'); ?></h4>
                    <input type="text" name="new_form_id" placeholder="<?php _e('Form ID', 'clickup-status-plugin'); ?>" required> <i class="fas fa-question-circle" data-toggle="tooltip" title="<?php _e('Add Gravity Form ID', 'clickup-status-plugin'); ?>"></i>
                    <input type="text" name="new_form_api_key" placeholder="<?php _e('API Key', 'clickup-status-plugin'); ?>" required> <i class="fas fa-question-circle" data-toggle="tooltip" title="<?php _e('Add Your ClickUP Personal API Key', 'clickup-status-plugin'); ?>"></i>
                    <input type="text" name="new_form_list_id" placeholder="<?php _e('List ID', 'clickup-status-plugin'); ?>" required> <i class="fas fa-question-circle" data-toggle="tooltip" title="<?php _e('Add ClickUP List ID to send the form data to', 'clickup-status-plugin'); ?>"></i>
                    <input type="checkbox" name="new_form_custom_fields" value="1" > <?php _e('Custom fields', 'clickup-status-plugin'); ?> <i class="fas fa-question-circle" data-toggle="tooltip" title="<?php _e('If your ClickUP account supports custom fields, you can enable this feature. Ensure that your field names match the custom fields, or they will be added to the description section.', 'clickup-status-plugin'); ?>"></i>
                    <input type="checkbox" name="new_form_tracking_status" value="tracking" onchange="enableTrackingField()"> <?php _e('Send tracking code', 'clickup-status-plugin'); ?> <i class="fas fa-question-circle" data-toggle="tooltip" title="<?php _e('Enable sending email to the user to track the status of their request for this form', 'clickup-status-plugin'); ?>"></i></br>
                    <input style="display:none" type="text" name="new_form_tracking_field" placeholder="<?php _e('Email Field ID', 'clickup-status-plugin'); ?>" disabled>
                    <input style="display:none; width:250px" type="text" name="new_form_gf_tracking_code_field" placeholder="<?php _e('GF Tracking Code Field (optional)', 'clickup-status-plugin'); ?>" disabled> <i  id="tracking-code-icon" style="display:none" class="fas fa-question-circle" data-toggle="tooltip" title="<?php _e('To include the tracking code with form entries for display in the form entries settings, consider adding a hidden field to your form and placing the ID there.', 'clickup-status-plugin'); ?>"></i>
                    <input style="margin-top:10px" type="submit" name="submit" value="<?php _e('Add Form', 'clickup-status-plugin'); ?>"> <i class="fas fa-question-circle" data-toggle="tooltip" title="<?php _e('If the form already exists, adding it again will update the settings in case any changes have been made.', 'clickup-status-plugin'); ?>"></i>
                    <script>
                        function enableTrackingField() {
                                var trackingField = document.querySelector('input[name="new_form_tracking_field"]');
                                var trackingCodeField = document.querySelector('input[name="new_form_gf_tracking_code_field"]');
                                var trackingCodeIcon = document.getElementById('tracking-code-icon');
                                trackingField.disabled = !trackingField.disabled;
                                trackingField.required = !trackingField.disabled;
                                trackingField.style.display = trackingField.disabled ? 'none' : '';
                                trackingCodeField.disabled = trackingField.disabled;
                                trackingCodeField.style.display = trackingField.style.display;
                                trackingCodeIcon.style.display = trackingCodeField.style.display;
                            }
                    </script>
                </form>

                <h4 style="margin-top:50px" ><?php _e('Fields to send to ClickUP for the added forms', 'clickup-status-plugin'); ?></h4>
                <h5><?php _e('Note: Please be aware that Attachment fields and Custom fields must be sent individually, necessitating one request for each file and each field. This is due to ClickUP not supporting arrays for these types.', 'clickup-status-plugin'); ?></h5>
                <form method="post" action="">
                <input type="text" name="new_field_name" placeholder="<?php _e('Field Name', 'clickup-status-plugin'); ?>" required> <i class="fas fa-question-circle" data-toggle="tooltip" title="<?php _e('If you intend to save this field into Click UP custom fields, make sure to first enable the Custom Field option for the form, and second, enter the field name exactly matching the Custom Field name.', 'clickup-status-plugin'); ?>"></i>
                <input type="text" name="new_field_id" placeholder="<?php _e('Field ID', 'clickup-status-plugin'); ?>" required> <i class="fas fa-question-circle" data-toggle="tooltip" title="<?php _e('Enter the Field ID for the form where you want to save its data into Click UP', 'clickup-status-plugin'); ?>"></i>
                    <select name="form_id" required onchange="updateTaskNameCheckbox()">
                        <?php
                        $settings = $this->get_click_up_settings();
                        foreach ($settings as $form_id => $form_data) {
                            $taskNameAttribute = isset($form_data['setting']['task_name']) && !empty($form_data['setting']['task_name']) ? 'task_name="' . esc_attr($form_data['setting']['task_name']) . '"' : '';
                            echo '<option value="' . esc_attr($form_id) . '" ' . $taskNameAttribute . '>' . sprintf(__('Form ID: %s', 'clickup-status-plugin'), esc_html($form_id)) . '</option>';
                        }
                        ?>
                        </select> <i class="fas fa-question-circle" data-toggle="tooltip" title="<?php _e('Select the form from the ones you have added here.', 'clickup-status-plugin'); ?>"></i>
                        <input type="checkbox" name="new_form_task_name"> <?php _e('Task Name', 'clickup-status-plugin'); ?> <i class="fas fa-question-circle" data-toggle="tooltip" title="<?php _e('Selecting this checkbox designates the field to be used as the Task Name in ClickUP. By default, the first field added to the form will be chosen as the Task Name. However, you can change it by selecting a different field when adding a new field. Ensure at least one field is designated as the Task Name.', 'clickup-status-plugin'); ?>"></i>
                        <script>
                        function updateTaskNameCheckbox() {
                            var selectElement = document.querySelector('select[name="form_id"]');
                            var selectedOption = selectElement?.options[selectElement.selectedIndex];
                            var formId = selectedOption?.value;

                            var taskName = selectedOption?.getAttribute('task_name');

                            var taskNameCheckbox = document.querySelector('input[name="new_form_task_name"]');
                            taskNameCheckbox.checked = taskName === null ? 'true' : '';
                            taskNameCheckbox.required = taskName === null ? 'true' : '';

                        }

                        // Call the function on page load to initialize checkbox state
                        updateTaskNameCheckbox();
                        </script>
                        <input type="checkbox" name="new_field_type" value="attachment"> <?php _e('Attachment', 'clickup-status-plugin'); ?> <i class="fas fa-question-circle" data-toggle="tooltip" title="<?php _e('Mark the field as an attachment field', 'clickup-status-plugin'); ?>"></i></br>
                        <input style="margin-top:10px" type="submit" name="add_field" value="<?php _e('Add Field', 'clickup-status-plugin'); ?>"> <i class="fas fa-question-circle" data-toggle="tooltip" title="<?php _e('If the field already exists (add existing Field Name), adding it again will update the fields\' data in case any changes have been made.', 'clickup-status-plugin'); ?>"></i>
                </form>
                <?php
                    if (!empty($settings)) {
                        ?>
                        <h2 style="margin-top:50px"><?php _e('Forms configuration overview and associated fields:', 'clickup-status-plugin'); ?></h2>
                        <?php
                    }
                ?>
                <form method="post" action="">
                    <?php
                    foreach ($settings as $form_id => $form_data) {
                        ?>

                        <table class="wp-list-table widefat fixed striped table-view-list" style="margin-top:25px">
                        <thead>
                            <tr style ="background: mediumslateblue;">
                                <th class="manage-column column-title column-primary" style="color: #ffffff;">
                                    <?php _e('Form Configurations =>', 'clickup-status-plugin'); ?>
                                </th>
                                <th class="manage-column column-title column-primary" style="color: #ffffff;">
                                    <?php _e('Form ID:', 'clickup-status-plugin'); ?> <?php echo $form_id;?>
                                </th>
                                <th class="manage-column column-title column-primary" style="color: #ffffff;">
                                    <?php _e('List ID:', 'clickup-status-plugin'); ?> <?php echo isset($form_data['setting']['list_id']) ? $form_data['setting']['list_id'] : '';?>
                                </th>
                                <th class="manage-column column-title column-primary" style="color: #ffffff;">
                                    <?php _e('Task Name Field:', 'clickup-status-plugin'); ?> <?php echo isset($form_data['setting']['task_name']) && !empty($form_data['setting']['task_name']) ? $form_data['setting']['task_name'] : 'none';?>
                                </th>
                                <th class="manage-column column-title column-primary" style="color: #ffffff;">
                                    <?php _e('Email Field:', 'clickup-status-plugin'); ?> <?php echo isset($form_data['setting']['email_field_id']) && !empty($form_data['setting']['email_field_id']) ? $form_data['setting']['email_field_id'] : 'none';?>
                                </th>
                            </tr>
                        </thead>
                           <thead style="padding:25px">
                           <tr>
                            <th scope="col-1" class="manage-column column-title column-primary " >
                                <?php _e('Field Name', 'clickup-status-plugin'); ?>
                            </th>
                            <th scope="col" class="manage-column column-title column-primary">
                                <?php _e('Field ID', 'clickup-status-plugin'); ?>
                            </th>
                            <th scope="col" class="manage-column column-title column-primary ">
                                <?php _e('Field Type', 'clickup-status-plugin'); ?> <i class="fas fa-question-circle" data-toggle="tooltip" title="<?php _e('Files type are either Attachment or Regular', 'clickup-status-plugin'); ?>"></i>
                            </th>
                            <th scope="col" class="manage-column column-title column-primary ">
                                <?php _e('Action', 'clickup-status-plugin'); ?>
                            </th>
                            <th scope="col" class="manage-column column-title column-primary ">
                            </th>
                            </tr>
                            </thead>
                        <?php
                        if (isset($form_data['dynamic_fields'])) {
                            ?>
                            <tbody>
                            <?php
                            foreach ($form_data['dynamic_fields'] as $field_name => $field_data) {
                                $field_id = isset($field_data[0]) ? $field_data[0]: '';
                                $field_type = isset($field_data[1]) ? $field_data[1]: '';
                                $custom_field = isset($field_data[2]) ? 'cuatom_field': false;
                                ?>
                                <tr>
                                    <td>
                                        <b><strong><?php echo $field_name;?></strong></b>
                                    </td>
                                    <td class="id column-id">
                                        <strong><?php echo $field_id;?></strong>
                                    </td>
                                    <td>
                                        <strong><?php echo $field_type;?></strong>
                                    </td>
                                    <td>
                                    <form method="post" action="">
                                        <input type="hidden" name="delete_field_name" value="<?php echo $field_name; ?>">
                                        <input type="hidden" name="delete_field_id" value="<?php echo $field_id; ?>">
                                        <input type="hidden" name="form_id" value="<?php echo $form_id; ?>">
                                        <input type="submit" name="delete_field" value="<?php _e('Delete Field', 'clickup-status-plugin'); ?>">
                                    </form>                      
                                    </td>
                                    <td>
                                    <?php
                                    $custom_field = $custom_field ? sprintf(__('%s <i class="fas fa-question-circle" data-toggle="tooltip" title="This field name is present in your ClickUP custom fields list with the defined Name."></i>', 'clickup-status-plugin'), esc_html($custom_field)) : '';
                                    echo $custom_field;
                                    ?>

                                    </td>
                                </tr>
                                <?php
                              }
                            ?>
                            </tbody>
                            <?php
                        }
                        ?>
                        </table>
                        <?php
                    }
                    ?>
                </form>
            </div>
        <?php
    }

    
    // Register settings and fields
    public function registerSettings() {
        // Register the section
        add_settings_section(
            'click_up_section',
            __('ClickUp Configuration', 'clickup-status-plugin'),
            array($this, 'sectionCallback'),
            'click_up_settings'
        );

     
        // Process adding the settings
        if (isset($_POST['submit'])) {
            $new_form_id = sanitize_key($_POST['new_form_id']);
            $new_form_api_key = $_POST['new_form_api_key'];
            $new_form_list_id = $_POST['new_form_list_id'];
            $new_form_email_field_status = isset($_POST['new_form_tracking_status']);
            $new_form_email_field_id = isset($_POST['new_form_tracking_field']) && $new_form_email_field_status ? $_POST['new_form_tracking_field'] : '';
            $new_form_gf_tracking_code_field = isset($_POST['new_form_gf_tracking_code_field']) && $new_form_email_field_status ? $_POST['new_form_gf_tracking_code_field'] : '';
            $new_form_custom_fields =isset($_POST['new_form_custom_fields'])? $_POST['new_form_custom_fields']: '';

            if (!empty($new_form_id) && !empty($new_form_api_key) && !empty($new_form_list_id)) {
                $settings = $this->get_click_up_settings();
                
                if (!isset($settings[$new_form_id])) {
                    $settings[$new_form_id] = array();
                }
                
                if (!isset($settings[$new_form_id]['setting'])) {
                    $settings[$new_form_id]['setting'] = array();
                }

                $settings[$new_form_id]['setting']['api_key'] = $new_form_api_key;
                $settings[$new_form_id]['setting']['list_id'] = $new_form_list_id;
                $settings[$new_form_id]['setting']['email_field_id'] = $new_form_email_field_id;
                $settings[$new_form_id]['setting']['custom_fields'] = $new_form_custom_fields;
                $settings[$new_form_id]['setting']['gf_tracking_code_field'] = $new_form_gf_tracking_code_field;
                $this->update_click_up_settings($settings);
            }
        }

        // Process adding a new field
        if (isset($_POST['add_field'])) {
            $new_field_id = sanitize_key($_POST['new_field_id']);
            $new_field_name = $_POST['new_field_name'];
            $new_form_task_name = sanitize_text_field(isset($_POST['new_form_task_name']) ? $new_field_id : '');
            $new_field_type = isset($_POST['new_field_type']) ? 'attachment' : 'regular';
            $form_id = sanitize_text_field($_POST['form_id']);

            if (!empty($new_field_id) && !empty($new_field_name) && !empty($form_id)) {
                $settings = $this->get_click_up_settings();
                
                if (!isset($settings[$form_id])) 
                    $settings[$form_id] = array();

                
                if (!isset($settings[$form_id]['dynamic_fields'])) 
                    $settings[$form_id]['dynamic_fields'] = array();
                

                if(!empty($new_form_task_name)) 
                    $settings[$form_id]['setting']['task_name'] = $new_form_task_name ;

                
                $custom_field = isset($settings[$form_id]['setting']['custom_fields'])? $settings[$form_id]['setting']['custom_fields']: '';
                
                if($custom_field){
                
                    $api_key = isset($settings[$form_id]['setting']['api_key']) ? $settings[$form_id]['setting']['api_key'] : false ;
                    $list_id = isset($settings[$form_id]['setting']['list_id']) ? $settings[$form_id]['setting']['list_id'] : false ;

                    $json_data = $this->get_click_up_custom_fields($api_key, $list_id);
                    $decoded_data = json_decode($json_data, true);
                    $fields = $decoded_data['fields'];

                    $custom_field_id = $this->match_custom_fields($fields, $new_field_name);
                    
                    $settings[$form_id]['dynamic_fields'][$new_field_name] = array($new_field_id, $new_field_type, $custom_field_id);

                }else{
                    $settings[$form_id]['dynamic_fields'][$new_field_name] = array($new_field_id, $new_field_type);
                }
    
                $this->update_click_up_settings($settings);
            }
        }


        // Process deleting dynamic fields
        if (isset($_POST['delete_field'])) {
            $delete_field_name = $_POST['delete_field_name'];
            $delete_field_id = sanitize_key($_POST['delete_field_id']);
            $form_id = sanitize_text_field($_POST['form_id']);

            $settings = $this->get_click_up_settings();
            if (isset($settings[$form_id]['dynamic_fields'][$delete_field_name]) && $settings[$form_id]['dynamic_fields'][$delete_field_name][0] === $delete_field_id) {

                if (isset($settings[$form_id]['setting']['task_name']) && $settings[$form_id]['setting']['task_name'] == $delete_field_id) {
                    unset($settings[$form_id]['setting']['task_name']);
                    unset($settings[$form_id]['dynamic_fields'][$delete_field_name]);
            
                    if (!empty($settings[$form_id]['dynamic_fields'])) {
                        $first_dynamic_field = reset($settings[$form_id]['dynamic_fields']);
                        $settings[$form_id]['setting']['task_name'] = $first_dynamic_field[0];

                    }
                } else {
                    unset($settings[$form_id]['dynamic_fields'][$delete_field_name]);
                }
            
                if (empty($settings[$form_id]['dynamic_fields'])) {
                    unset($settings[$form_id]);
                }
                $this->update_click_up_settings($settings);
            }            
        }

    }


    // Section callback
    public function sectionCallback() {
        echo '<p>' . __('Configure your ClickUp settings below.', 'clickup-status-plugin') . '</p>';
    }

    // Retrieve click_up_setting_fields from the database
    private function get_click_up_settings() {
        return get_option('click_up_setting_fields', array());
    }

    // Update click_up_setting_fields in the database
    private function update_click_up_settings($settings) {
        update_option('click_up_setting_fields', $settings);
    }

    //Get ClickUP accessiable custom fields
    private function get_click_up_custom_fields($api_key, $list_id){

        $curl = curl_init();
        
        curl_setopt_array($curl, [
        CURLOPT_HTTPHEADER => [
            "Authorization: ". $api_key,
            "Content-Type: application/json"
        ],
        CURLOPT_URL => "https://api.clickup.com/api/v2/list/" . $list_id . "/field",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "GET",
        ]);
        
        $response = curl_exec($curl);
        $error = curl_error($curl);
        
        curl_close($curl);
        
        if ($error) {
            error_log("ClickUp API Error: Unexpected response - " . $response);
            return "ClickUp API Error: Unexpected response";
        } else {
            return $response;
        }

    }

    private function match_custom_fields($fields, $new_field_name){
        //Get response of available custom_fields from clickup 
        foreach($fields as $field){
            if (isset($field["name"]) && $field["name"]==$new_field_name){
                return $field["id"];
            }
        }

    }
}

