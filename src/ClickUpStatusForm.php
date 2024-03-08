<?php
namespace CUSTATUS\ClickUpStatusPlugin;

class ClickUpStatusForm {
    public static function clickup_status_form_shortcode() {
        ob_start(); // Start output buffering
        ?>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.17.0/font/bootstrap-icons.css">
        <link rel="stylesheet" href="<?php echo plugins_url('/assets/css/clickupStatus.css', __FILE__); ?>">
        <script type="text/javascript">
            var ajax_object = <?php echo json_encode(array('ajax_url' => admin_url('admin-ajax.php'))); ?>;
        </script>
        <script type="text/javascript" src="<?php echo plugins_url('/assets/js/clickupStatus.js', __FILE__); ?>"></script>

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

        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
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
}
