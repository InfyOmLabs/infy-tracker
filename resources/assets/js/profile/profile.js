$('#editProfileForm').submit(function (event) {
    event.preventDefault();
    let loadingButton = jQuery(this).find("#btnEditSave");
    loadingButton.button('loading');
    $.ajax({
        url: usersUrl + 'profile-update',
        type: 'post',
        data: new FormData($(this)[0]),
        processData: false,
        contentType: false,
        success: function (result) {
            if (result.success) {
                $('#EditProfileModal').modal('hide');
                location.reload();
            }
        },
        error: function (result) {
            manageAjaxErrors(result,'editProfileValidationErrorsBox');
        },
        complete: function () {
            loadingButton.button('reset');
        }
    });
});

$('#EditProfileModal').on('hidden.bs.modal', function () {
    resetModalForm('#editProfileForm', '#editProfileValidationErrorsBox');
});

// open edit user profile model
$(document).on('click', '.edit-profile', function (event) {
    let userId = $(event.currentTarget).data('id');
    renderProfileData(usersUrl + userId + '/edit');
});
$(document).on('change', '#pfImage', function () {
    let ext = $(this).val().split('.').pop().toLowerCase();
    if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
        $(this).val('');
        $('#editProfileValidationErrorsBox').html('The profile image must be a file of type: jpeg, jpg, png.').show();
    } else {
        displayPhoto(this, '#edit_preview_photo');
    }
});

window.renderProfileData = function (usersUrl) {
    $.ajax({
        url: usersUrl,
        type: 'GET',
        success: function (result) {
            if (result.success) {
                let user = result.data;
                $('#pfUserId').val(user.id);
                $('#pfName').val(user.name);
                $('#pfEmail').val(user.email);
                $('#pfPhone').val(user.phone);
                $('#edit_preview_photo').attr('src',user.image_path);
                $('#EditProfileModal').modal('show');
            }
        }
    });
}
window.displayPhoto = function (input, selector) {
    let displayPreview = true;
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function (e) {
            let image = new Image();
            image.src = e.target.result;
            image.onload = function () {
                $(selector).attr('src', e.target.result);
                displayPreview = true;

            };
        };
        if (displayPreview) {
            reader.readAsDataURL(input.files[0]);
            $(selector).show();
        }
    }
};