<?php

namespace IBD\ClickUpStatusPlugin;

class AdminSettings {
    public function __construct() {
        // Add an action to initialize the admin settings
        add_action('admin_menu', array($this, 'addAdminMenu'));
        // Add an action to register the settings
        add_action('admin_init', array($this, 'registerSettings'));
    }

    // Add a menu item to the admin dashboard
    public function addAdminMenu() {
        add_menu_page(
            'ClickUp Settings',
            'ClickUp Settings',
            'manage_options',
            'click_up_settings',
            array($this, 'renderSettingsPage')
        );
    }

    // Render the settings page
    
    public function renderSettingsPage() {
        ?>
        <head>
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
        </head>
        <div class="wrap">
            <form>
                <h2>ClickUp Settings</h2>
                <form method="post" action="">
                <h4>Form Setting</h4>
                    <?php
                    settings_fields('click_up_settings');
                    do_settings_sections('click_up_settings');
                    ?>
                </form> <!-- Close the form for Form Setting -->

                <form method="post" action="">
                    <h4>Add New Form</h4>
                    <input type="text" name="new_form_id" placeholder="Form ID" required>
                    <input type="text" name="new_form_api_key" placeholder="API Key" required>
                    <input type="text" name="new_form_list_id" placeholder="List ID" required>
                    <input type="submit" name="submit" value="Add Form"><i class="fas fa-question-circle" data-toggle="tooltip" title="If the form already exists, adding it again will update the settings in case any changes have been made."></i>
                </form>

                <h4>Fields to Send to ClickUP</h4>
                <form method="post" action="">
                    <input type="text" name="new_field_name" placeholder="Field Name" required>
                    <input type="text" name="new_field_id" placeholder="Field ID" required>
                    <select name="form_id" required onchange="updateTaskNameCheckbox()">
                        <?php
                        $settings = $this->get_click_up_settings();
                        foreach ($settings as $form_id => $form_data) {
                            $taskNameAttribute = isset($form_data['setting']['task_name'])&& !empty($form_data['setting']['task_name']) ? 'task_name="' . $form_data['setting']['task_name'] . '"' : '';
                            echo '<option value="' . $form_id . '" ' . $taskNameAttribute . '>Form ID: ' . $form_id . '</option>';
                        }
                        ?>
                    </select>
                    <input type="checkbox" name="new_form_task_name"> Task Name <i class="fas fa-question-circle" data-toggle="tooltip" title="Selecting this checkbox designates the field to be used as the Task Name in ClickUP. By default, the first field added to the form will be chosen as the Task Name. However, you can change it by selecting a different field when adding a new field. Ensure at least one field is designated as the Task Name."></i>
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
                    <input type="checkbox" name="new_field_type" value="attachment"> Attachment <i class="fas fa-question-circle" data-toggle="tooltip" title="Mark the field as an attachment"></i></br>
                    <input type="submit" name="add_field" value="Add Field">
                </form>

                <h2>Form Configuration Overview and Associated Fields:</h2>
                <form method="post" action="">
                    <?php
                    foreach ($settings as $form_id => $form_data) {
                        ?>

                        <table class="wp-list-table widefat fixed striped table-view-list" style="margin-top:25px">
                           <thead>
                                <tr style ="background: mediumslateblue;">
                                    <th class="manage-column column-title column-primary" style="color: #ffffff;">
                                        Form Congigurations:
                                    </th>
                                    <th class="manage-column column-title column-primary" style="color: #ffffff;">
                                        Form ID: <?php echo $form_id;?>
                                    </th>
                                    <th class="manage-column column-title column-primary" style="color: #ffffff;">
                                        List ID: <?php echo isset($form_data['setting']['list_id']) ? $form_data['setting']['list_id'] : '';?>
                                    </th>
                                    <th class="manage-column column-title column-primary" style="color: #ffffff;">
                                        Task name field: <?php echo isset($form_data['setting']['task_name']) ? $form_data['setting']['task_name'] : '';?>
                                    </th>
                                </tr>
                            </thead>
                           <thead style="padding:25px">
                                <tr>
                                    <th scope="col" class="manage-column column-title column-primary ">
                                        Field Name
                                    </th>
                                    <th scope="col" class="manage-column column-title column-primary">
                                        Field ID
                                    </th>
                                    <th scope="col" class="manage-column column-title column-primary ">
                                        Field Type
                                    </th>
                                    <th scope="col" class="manage-column column-title column-primary ">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                        <?php
                        if (isset($form_data['dynamic_fields'])) {
                            ?>
                            <tbody>
                            <?php
                            foreach ($form_data['dynamic_fields'] as $field_name => $field_data) {
                                $field_id = $field_data[0];
                                $field_type = $field_data[1];
                                ?>
                                <tr>
                                    <td>
                                        <strong><?php echo $field_name;?></strong>
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
                                            <input type="submit" name="delete_field" value="Delete Field">
                                        </form>                      
                                    </td>
                                </tr>
                                <?php
                              }
                            ?>
                            </tbody>
                            <?php
                        }
                    }
                    ?>
                    </table>
                </form>
            </div>
        <?php
    }

    
    // Register settings and fields
    public function registerSettings() {
        // Register the section
        add_settings_section(
            'click_up_section',
            'ClickUp Configuration',
            array($this, 'sectionCallback'),
            'click_up_settings'
        );

     
        // Process adding the settings
        if (isset($_POST['submit'])) {
            $new_form_id = sanitize_key($_POST['new_form_id']);
            $new_form_api_key = $_POST['new_form_api_key'];
            $new_form_list_id = $_POST['new_form_list_id'];

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
                $this->update_click_up_settings($settings);
            }
        }

        // Process adding a new field
        if (isset($_POST['add_field'])) {
            $new_field_id = sanitize_key($_POST['new_field_id']);
            $new_field_name = sanitize_title($_POST['new_field_name']);
            $new_form_task_name = sanitize_text_field($_POST['new_form_task_name']) ? $new_field_id : '';
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

                $settings[$form_id]['dynamic_fields'][$new_field_name] = array($new_field_id, $new_field_type);
                $this->update_click_up_settings($settings);
            }
        }


        // Process deleting dynamic fields
        if (isset($_POST['delete_field'])) {
            $delete_field_name = sanitize_title($_POST['delete_field_name']);
            $delete_field_id = sanitize_key($_POST['delete_field_id']);
            $form_id = sanitize_text_field($_POST['form_id']);

            $settings = $this->get_click_up_settings();

            if (isset($settings[$form_id]['dynamic_fields'][$delete_field_name]) && $settings[$form_id]['dynamic_fields'][$delete_field_name][0] === $delete_field_id) {

                if(isset($settings[$form_id]['setting']['task_name']) && $settings[$form_id]['setting']['task_name'] == $delete_field_id){
                    unset($settings[$form_id]['setting']['task_name']);
                }else{
                    unset($settings[$form_id]['dynamic_fields'][$delete_field_name]);
                }

                if(empty($settings[$form_id]['dynamic_fields'])){
                    unset($settings[$form_id]);
                }
                $this->update_click_up_settings($settings);

            }
        }
    }

    // Section callback
    public function sectionCallback() {
        echo '<p>Configure your ClickUp settings below.</p>';
    }

    // Retrieve click_up_setting_fields from the database
    private function get_click_up_settings() {
        return get_option('click_up_setting_fields', array());
    }

    // Update click_up_setting_fields in the database
    private function update_click_up_settings($settings) {
        update_option('click_up_setting_fields', $settings);
    }
}
