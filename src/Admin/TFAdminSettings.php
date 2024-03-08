<?php
//Admin settings for Tracking Form which enable users to track their requests
namespace CUSTATUS\ClickUpStatusPlugin\Admin;

use CUSTATUS\ClickUpStatusPlugin\Utils\ClickUpApiHelper;


class TFAdminSettings{
    public function __construct() {
        // Add an action to initialize the admin settings
        add_action('admin_menu', array($this, 'addAdminMenu'));
        // Add an action to register the settings
        add_action('admin_init', array($this, 'registerSettings'));
    }
    // Add a menu item to the admin dashboard
    public function addAdminMenu() {

        // Add submenu "GF Settings" under "ClickUp Settings"
        add_submenu_page(
            'click_up_status_settings',
            __('Tracking Form', 'clickup-status-plugin'),
            __('Tracking Form', 'clickup-status-plugin'),
            'manage_options',
            'click_up_tf_settings', 
            array($this, 'renderSettingsPage')
        );

    }


    // Render the settings page
    public function renderSettingsPage() {

        settings_fields('click_up_status_settings');
        do_settings_sections('click_up_status_settings');

        ?>
        <head>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
        </head>
        <?php
        $settings = $this->get_click_up_settings();
        foreach ($settings as $form_id => $form_data) {
            $api_key = isset($form_data['setting']['api_key']) ? $form_data['setting']['api_key']: '';
            $list_id = isset($form_data['setting']['list_id']) ? $form_data['setting']['list_id']: '';
            $statuses = ClickUpApiHelper::get_forms_statuses_detaile($api_key, $list_id);

            ?>
            <div class="wrap">
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
                    </tr>
                </thead>
                <thead>
                    <thead style="padding:25px">
                    <tr>
                        <th scope="col-1" class="manage-column column-title column-primary " >
                            <?php _e('Status', 'clickup-status-plugin'); ?>
                        </th>
                        <th scope="col" class="manage-column column-title column-primary">
                            <?php _e('Description', 'clickup-status-plugin'); ?>  <i class="fas fa-question-circle" data-toggle="tooltip" title="<?php _e('Description to show in the frontend', 'clickup-status-plugin'); ?>"></i>
                        </th>
                        <th scope="col" class="manage-column column-title column-primary">
                            <?php _e('Action', 'clickup-status-plugin'); ?>
                        </th>
                    </tr>
                    </thead>
                </thead>
                <tbody>
                <?php
                foreach($statuses as $status){
                    ?>
                    <tr>
                      <form method="post" action="">
                            <td>
                                    <?php 
                                    $status_index = $status['orderindex'] ?? '';
                                    $status_name = $status['status'] ?? '';
                                    
                                    $status_description = $form_data['statuses'][$status_index][1] ?? '';

                                    if (!empty($status_description)){
                                        // Replace placeholders csp-status-value and csp-assignee-value with actual values
                                            $status_description = str_replace(array('csp-status-value', 'csp-assignee-value' ), array('{{status}}','{{assignee}}'), $status_description);
                                    }

                                    echo $status_name;
                                    ?>
                                    
                            </td>
                            <td>  
                                    <?php
                                    $status_name = sanitize_key(urlencode($status_name));
                                    $editor_id = 'status_description_editor_' . $form_id . '_' . $status_index;
                                    $editor_settings = array(
                                        'textarea_name' => 'status_description_' . $form_id . '_' . $status_index,
                                        'textarea_rows' => 2,
                                    );
                                    ?>
                                    <div id="<?php echo esc_attr($editor_id); ?>-wrapper">
                                        <input type="text" name="status_description_<?php echo esc_attr($form_id . '_' . $status_index); ?>" value="<?php echo esc_attr($status_description); ?>">
                                        <button type="button" class="edit-description-button" data-editor-id="<?php echo esc_attr($editor_id); ?>">Edit</button>
                                    </div>
                                    <div id="<?php echo esc_attr($editor_id); ?>-editor" style="display:none;">
                                        <?php wp_editor($status_description, $editor_id, $editor_settings); ?>
                                        <button type="button" class="save-description-button" data-editor-id="<?php echo esc_attr($editor_id); ?>" data-status-index="<?php echo esc_attr($form_id . '_' . $status_index); ?>">Save</button>
                                    </div>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function () {
                                            var editButton = document.querySelector('.edit-description-button[data-editor-id="<?php echo esc_attr($editor_id); ?>"]');
                                            var saveButton = document.querySelector('.save-description-button[data-editor-id="<?php echo esc_attr($editor_id); ?>"]');
                                            var inputWrapper = document.getElementById('<?php echo esc_attr($editor_id); ?>-wrapper');
                                            var editorDiv = document.getElementById('<?php echo esc_attr($editor_id); ?>-editor');

                                            editButton.addEventListener('click', function () {
                                                inputWrapper.style.display = 'none';
                                                editorDiv.style.display = 'block';
                                            });

                                            saveButton.addEventListener('click', function () {
                                                var editorId = this.getAttribute('data-editor-id');
                                                var statusIndex = this.getAttribute('data-status-index');
                                                var editorContent = tinyMCE.get(editorId).getContent();
                                                document.querySelector('input[name="status_description_' + statusIndex + '"]').value = editorContent;
                                                inputWrapper.style.display = 'flex';
                                                editorDiv.style.display = 'none';
                                            });
                                        });
                                    </script>
                            </td>
                            <td>
                                <input type="hidden" name="form_id" value="<?php echo $form_id; ?>">
                                <input type="hidden" name="status_name" value="<?php echo $status_name; ?>">
                                <input type="hidden" name="status_orderindex" value="<?php echo $status['orderindex']; ?>">
                                <input type="submit" name="update_field_description" value="<?php _e('Update Description', 'clickup-status-plugin'); ?>">
                            </td>
                       </form>  
                    </tr>
                    <?php
                }
                ?>
                </tbody>
                </table>
            </div>
            <?php
        }
        ?>
        <?php
    }


    // Change the name of the method to something unique, e.g., 'trackingFormSectionCallbackPage'
    public function trackingFormSectionCallbackPage() {
        echo '<h2>' . __('Configure your Tracking Form settings below.', 'clickup-status-plugin') . '</h2>';
        echo '<h4>' . __('To edit the Tracking Form you should first add a form.', 'clickup-status-plugin') . '</h4>';
        echo '<h4>' . __('You can define the description to display for each status in the tracking form.', 'clickup-status-plugin') . '</h4>';
        echo '<b><h4>' . __('To make the description dynamic, use the following variables: {{status}} and {{assignee}}. For instance, you can create a dynamic description like this: "Your status is {{status}} and is being investigated by {{assignee}}". These variables will be replaced with actual values when displayed.', 'clickup-status-plugin') . '</h4></b>';
        echo '<b><h4>' . __('#In order to use this form add <b style="color: #8481f5;">[clickup_status_form]</b> shortcode to your desired page.', 'clickup-status-plugin') . '</h4></b>';
    }




    // Retrieve click_up_setting_fields from the database
    private function get_click_up_settings() {
        return get_option('click_up_setting_fields', array());
    }
    

    // Register settings and fields
    public function registerSettings() {
        // Update the 'add_settings_section' call to use the new method name
        add_settings_section(
            'click_up_status_settings',
            __('Tracking Form Settings', 'clickup-status-plugin'),
            array($this, 'trackingFormSectionCallbackPage'),  // Change the method name here
            'click_up_status_settings'
        );
        
        // Process adding a new description
        if (isset($_POST['update_field_description'])) {
            $form_id = sanitize_key($_POST['form_id']);
            $status_name = $_POST['status_name'];
            $status_orderindex = $_POST['status_orderindex'] ?? '';
            $description_id = $form_id . '_' . $status_orderindex;

            // Get the status_description from the editor 
            $status_description = isset($_POST['status_description_' . $description_id]) ? $_POST['status_description_' . $description_id] : '';

            // Replace placeholders {{status}} and {{assignee}} with actual values
            $status_description = str_replace(array('{{status}}', '{{assignee}}'), array('csp-status-value', 'csp-assignee-value'), $status_description);

            // Allow certain HTML tags and attributes
            $allowed_html = array(
                'h4' => array(
                    'style' => array(),
                ),
                'strong' => array(),
            );

            // Save the description after sanitizing HTML
            $status_description = wp_kses_post($status_description);

            if (!empty($form_id) && !empty($status_name)) {

                $settings = $this->get_click_up_settings();

                //First paramter would be the status index and second parameter would be description for the designated status
                $settings[$form_id]['statuses'][$status_orderindex][0] = $status_orderindex; 
                $settings[$form_id]['statuses'][$status_orderindex][1] = $status_description; 
                
                update_option('click_up_setting_fields', $settings);
            }
        }
    }    

}


