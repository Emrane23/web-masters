/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';
import toastr from 'toastr';
$('#add_comment_form').submit(function (e) {
    e.preventDefault(); // Prevent the default form submission

    var form = $(this);
    var url = form.attr('action');
    var data = form.serialize();

    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        success: function (response) {
            // Handle the success response
            toastr.success('Comment added successfully!','well done',{timeOut: 5000,closeButton: true});
        },
        error: function (xhr, status, error) {
            // Handle the error response
            console.error(xhr.responseText);
        }
    });
});