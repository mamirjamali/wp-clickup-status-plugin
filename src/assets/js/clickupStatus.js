document.addEventListener("DOMContentLoaded", function () {
    var form = document.getElementById('clickup-status-checker');
    var resultContainer = document.getElementById('result-container');

    if (form && resultContainer) { // Check if elements exist

        form.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent the default form submission

            // Add a nonce field to the form
            var formData = new FormData(form);

            // Include the action in the FormData object
            formData.append('action', 'clickup_status_handle_form_submission');

            // Perform AJAX submission using fetch
            fetch(ajax_object.ajax_url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
            .then(response => response.json())
            .then(result => {

                // Generate HTML based on the response
                const html = generateEventHtml(result.data.status, result.data.assigneeUsername, result.data.orderIndex);

                // Update the content of the result container with the generated HTML
                resultContainer.innerHTML = html;
            })
            .catch(error => console.error('Error:', error));
        });

    } else {
        console.error('Form or result container not found.');
    }

    // Function to generate HTML based on ClickUp status and order index
    function generateEventHtml(status, assignee, orderIndex) {
        // Check if orderIndex is a valid number
        if (Number.isInteger(orderIndex) && orderIndex >= 0) {
            // Return the container HTML if orderIndex is valid
            return `
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                        <ul class="list-vertical">
                                            ${generateStepHtml(0, 'شروع', 'مدارک شما دریافت شد و در حال بررسی می باشد', status, assignee, orderIndex)}
                                            ${generateStepHtml(1, 'مرحله دوم', 'ارسال مدارک به مراجع اداری.', status, assignee, orderIndex)}
                                            ${generateStepHtml(2, 'مرحله سوم', 'در حال مشاوره با تیم حقوقی', status, assignee, orderIndex)}
                                            ${generateStepHtml(3, 'مرحله چهارم ', 'در حال ارزیابی مدارک برای ارسال به بخش بایگانی', status, assignee, orderIndex)}
                                            ${generateStepHtml(4, 'مرحله پنجم ', 'تیم حقوقی مدارک شما را تایید کرد ', status, assignee, orderIndex)}
                                            ${generateStepHtml(5, 'مرحله ششم ', 'کارت شهروندی شما دریافت شد برای دریافت به آدرس شرکت مراجعه فرمایید', status, assignee, orderIndex)}
                                            ${generateStepHtml(6, 'مرحله هفتم ', 'پروسه ثبت شرکت شما با موفقیت به پایان رسید', status, assignee, orderIndex)}
                                        </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            // Return the status if orderIndex is not valid
            return `
            <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="hori-timeline" dir="rtl">
                                <ul class="list-vertical events">
                                        ${status}                             
                                 </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            `
        }
    }

    // Function to generate HTML for a single step with hexagon shape around title
    function generateStepHtml(stepIndex, title, description, status, assignee, orderIndex) {
        var stepClass = (stepIndex === orderIndex) ? 'active' : 'inactive';
        var stepDateClass = (stepIndex === orderIndex) ? 'bg-soft-primary' : 'bg-light';
        var shadowClass = (stepIndex === orderIndex) ? 'box-shadow-active' : '';

        return '<li class="list-inline-item event-list ' + stepClass + '" style="text-align: center; margin: auto;">' +           
            ( (stepIndex === orderIndex) ? 
            '<div class="' + shadowClass + '">'+
                '<div class="step-box">' +
                    '<p style="font-size: 1rem; color: #3498db;">'+title+'</p>' + 
                '</div>' +
                    '<div class="full-width">' + 
                        '<p class="description">' + description + '</p>'+
                        '<p><b>وضعیت: ' + status + '</b></p>' +
                        '<p><b>در حال پیگیری توسط: ' + assignee + '</b></p>' +
                    '</div>' +
             '</div>'
                :
                '<div class="step-box" style="width: 150px;">' +
                `<i class="bi bi-hexagon" style="font-size: 1rem; color: #3498db;">${title}</i>` + 
                '</div>'
                ) +
            '</li>';
    }
});
