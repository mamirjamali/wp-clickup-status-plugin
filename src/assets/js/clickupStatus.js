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
            // In the next version this will be retrive from Admin dashboard (The number of steps and the details for each step)
            return `
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                        <ul class="list-vertical">
                                            ${generateStepHtml(0, 'Step1', 'Details to show for each step.', status, assignee, orderIndex)}
                                            ${generateStepHtml(1, 'Step2', 'Details to show for each step.', status, assignee, orderIndex)}
                                            ${generateStepHtml(2, 'Step3', 'Details to show for each step.', status, assignee, orderIndex)}

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
                        '<p><b>Status: ' + status + '</b></p>' +
                        '<p><b>Assigne: ' + assignee + '</b></p>' +
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
