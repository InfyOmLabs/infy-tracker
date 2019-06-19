$('#editProfileForm').submit(function (event) {
    event.preventDefault();
    let loadingButton = jQuery(this).find("#btnEditSave");
    loadingButton.button('loading');
    $.ajax({
        url: usersUrl + 'profile-update',
        type: 'post',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                $('#EditProfileModal').modal('hide');
                location.reload();
            }
        },
        error: function (result) {
            printErrorMessage("#editValidationErrorsBox", result);
        },
        complete: function () {
            loadingButton.button('reset');
        }
    });
});

$('#EditProfileModal').on('hidden.bs.modal', function () {
    resetModalForm('#editProfileForm', '#editValidationErrorsBox');
});

// open edit user profile model
$(document).on('click', '.edit-profile', function (event) {
    let userId = $(event.currentTarget).data('id');
    renderProfileData(usersUrl + userId + '/edit');
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
