<?php
namespace CUSTATUS\ClickUpStatusPlugin;

class ClickUpStatusForm {

    private static $bootstrap_classes_found;

    public static function clickup_status_form_shortcode() {

        // Check if Bootstrap classes are already found
        if (self::$bootstrap_classes_found === null) {
            self::$bootstrap_classes_found = self::check_bootstrap_classes();
        }

        // Start output buffering
        ob_start();
        ?>
        <?php if (!self::$bootstrap_classes_found) : ?>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.17.0/font/bootstrap-icons.css">
            <style>
               /* Add custom CSS for button alignment */
                #clickup-status-checker .col-auto {
                    display: flex;
                    align-items: baseline;
                }

                #clickup-status-checker button {
                    margin-left: 8px; /* Adjust the margin as needed */
                }
            </style>
        <?php endif; ?>

        <link rel="stylesheet" href="<?php echo plugins_url('/assets/css/clickupStatus.css', __FILE__); ?>">
        <script type="text/javascript">
            var ajax_object = <?php echo json_encode(array('ajax_url' => admin_url('admin-ajax.php'))); ?>;
        </script>
        <script type="text/javascript" src="<?php echo plugins_url('/assets/js/clickupStatus.js', __FILE__); ?>"></script>

        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <form id="clickup-status-checker" method="post" action="">
                        <div class="form-row align-items-center">
                             <input type="hidden" name="form_identifier" value="clickup_client_status">

                            <div class="col-auto">
                                <label class="sr-only" for="user_email">ایمیل:</label>
                                <input type="email" name="user_email" class="form-control mb-2" id="user_email" placeholder="ایمیل" required>
                            </div>
                            <div class="col-auto">
                                <label class="sr-only" for="hash_code">کد پیگیری:</label>
                                <input type="text" name="hash_code" class="form-control mb-2" id="hash_code" placeholder="کد پیگیری" required>
                            </div>
                            <div class="col-auto">
                                <button type="submit" name="submit" class="btn btn-primary mb-2 btn-align"><?php _e('Submit', 'clickup-status-plugin'); ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="result-container"></div>       
        
        <?php
        return ob_get_clean(); // Return the buffered content
    }

    // Function to check if Bootstrap classes are present
    private static function check_bootstrap_classes() {
        // Retrieve the value from the option
        $option_value = get_option('csp_bootstrap_classes_not_found', null);

        // If the option is set, return its value
        if ($option_value !== null) {
            return $option_value;
        }

        $stylesheets = wp_styles();
        $bootstrap_classes = array(
            'container',
            'row',
            'col-',
        );

        // Initialize a flag to check if Bootstrap classes are found
        $bootstrap_found = false;

        foreach ($stylesheets->registered as $stylesheet) {
            // Check if the stylesheet has a valid source
            if (!empty($stylesheet->src)) {
                $file_content = @file_get_contents($stylesheet->src);

                if ($file_content !== false) {
                    foreach ($bootstrap_classes as $class) {
                        if (strpos($file_content, $class) !== false) {
                            // Set the flag and break out of the inner loop
                            $bootstrap_found = true;
                            break;
                        }
                    }

                    // Break out of the outer loop if Bootstrap classes are found
                    if ($bootstrap_found) {
                        break;
                    }
                }
            }
        }

        // Update the option based on whether Bootstrap classes are found
        update_option('csp_bootstrap_classes_not_found', !$bootstrap_found);

        return !$bootstrap_found;
    }

}
