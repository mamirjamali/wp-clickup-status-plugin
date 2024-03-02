<?php
namespace CUSTATUS\ClickUpStatusPlugin;

class EmailSender {
    /**
     * Send an email with a hash code and copy button.
     *
     * @param string $email User's email address.
     * @param string $hash Hash code to include in the email.
     */
    public static function sendEmail($email, $hash) {
        // Email Address
        $to = $email;
        $subject = 'کد پیگیری - IBD';

        // Message with a button to copy the hash
        $message = '
            <html>
            <body>
                <p>کد پیگیری شما: </br></br><strong>' . $hash . '</strong></p>
                <button onclick="copyToClipboard(\'' . $hash . '\')">Copy</button>
                <script>
                    function copyToClipboard(text) {
                        var textArea = document.createElement("textarea");
                        textArea.value = text;
                        document.body.appendChild(textArea);
                        textArea.select();
                        document.execCommand("Copy");
                        document.body.removeChild(textArea);
                        alert("Hash code copied to clipboard!");
                    }
                </script>
            </body>
            </html>
        ';

        // Additional headers
        $headers[] = 'Content-Type: text/html; charset=UTF-8';

        // Send the email
        wp_mail($email, $subject, $message, $headers);
    }
}
