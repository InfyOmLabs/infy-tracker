$(function () {
    $('#editAssignTo').select2({
        width: '100%',
        placeholder: "Select Assignee"
    });
    $('#editProjectId').select2({
        width: '100%',
        placeholder: "Select Project"
    });
    $('#editTagIds,#editAssignee').select2({
        width: '100%',
        tags: true
    });

    $('#dueDate,#editDueDate').datetimepicker({
        format: 'YYYY-MM-DD',
        useCurrent: false,
        icons: {
            up: "icon-angle-up",
            down: "icon-angle-down"
        },
        sideBySide: true,
        minDate: new Date()
    });
});

// open edit user model
$(document).on('click', '.edit-btn', function (event) {
    let id = $(event.currentTarget).data('id');
    var loadingButton = jQuery(this);
    loadingButton.button('loading');
    $.ajax({
        url: taskUrl + id + '/edit',
        type: 'GET',
        success: function (result) {
            if (result.success) {
                var task = result.data;
                $('#tagId').val(task.id);
                $('#editTitle').val(task.title);
                $('#editDesc').val(task.description);
                $('#editDueDate').val(task.due_date);
                $('#editProjectId').val(task.project.id).trigger("change");
                if (task.status == 1) {
                    $('#editStatus').prop('checked', true);
                }

                var tagsIds = [];
                var userIds = [];
                $(task.tags).each(function (i, e) {
                    tagsIds.push(e.id);
                });
                $(task.task_assignee).each(function (i, e) {
                    userIds.push(e.id);
                });
                $("#editTagIds").val(tagsIds).trigger('change');

                $("#editAssignee").val(userIds).trigger('change');
                loadingButton.button('reset');
                $('#EditModal').modal('show');
            }
        }
    });
});

$('#editForm').submit(function (event) {
    event.preventDefault();
    var loadingButton = jQuery(this).find("#btnEditSave");
    loadingButton.button('loading');
    var id = $('#tagId').val();
    $.ajax({
        url: taskUrl + id + '/update',
        type: 'post',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                location.reload();
            }
        },
        error: function (result) {
            loadingButton.button('reset');
            printErrorMessage("#editValidationErrorsBox", result);
        }
    });
});

$('#EditModal').on('hidden.bs.modal', function () {
    resetModalForm('#editForm', '#editValidationErrorsBox');
});

// light box image galary
$(document).on('click', '[data-toggle="lightbox"]', function(event) {
    event.preventDefault();
    $(this).ekkoLightbox();
});
function getRandomString(){
    return Math.random().toString(36).substring(2, 8) + Math.random().toString(36).substring(2, 8);
}
//file upload dropzon js
Dropzone.options.dropzone = {
    maxFilesize: 12,
    renameFile: function(file) {
        let dt = new Date();
        let time = dt.getTime();
        let randomString = getRandomString();
        return time+'_'+randomString+file.name;
    },
    thumbnailWidth:125,
    acceptedFiles: 'image/*,.pdf,.doc,.docx,.xls,.xlsx',
    addRemoveLinks: true,
    dictRemoveFile: 'x',
    timeout: 50000,
    init: function() {
        thisDropzone = this;
        $.get(taskUrl+'get-attachments/'+taskId, function(data) {
            $.each(data.data, function(key,value){
                let mockFile = { name: value.name, size: value.size };

                thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                thisDropzone.options.thumbnail.call(thisDropzone, mockFile, value.url);
                thisDropzone.emit("complete", mockFile);
                thisDropzone.emit("thumbnail",mockFile, value.url);
            });
        });
        this.on("thumbnail", function(file, dataUrl) {
            $(file.previewTemplate).find('.dz-details').css('display','none');
            previewFile(file);
            let fileNameExtArr = file.name.split('.');
            let fileName = fileNameExtArr[0];
            let ext = file.name.split('.').pop();
            let previewEle = '';

            if($.inArray( ext, [ "jpg", "jpeg", "png"] ) > -1){
                let fileUrl = attachmentUrl + file.name;
                previewEle = '<a class="'+fileName+'" data-fancybox="gallery" href="'+fileUrl+'" data-toggle="lightbox" data-gallery="example-gallery"></a>';
                $(".previewEle").append(previewEle);
            }

            file.previewElement.addEventListener("click", function() {
                let fileName = file.previewElement.querySelector("[data-dz-name]").innerHTML;
                let fileExt = fileName.split('.').pop();
                if($.inArray(fileExt,['jpg','jpeg','png']) > -1) {
                    let onlyFileName = fileName.split('.')[0];
                    $("." + onlyFileName).trigger('click');
                } else {
                    let fileUrl = attachmentUrl + fileName;
                    window.open(fileUrl, '_blank');
                }
            });
        });
        this.on('addedfile', function(file) {
            previewFile(file);
        });

        function previewFile(file) {
            let ext = file.name.split('.').pop();
            if (ext == "pdf") {
                $(file.previewElement).find(".dz-image img").attr("src", "/assets/img/pdf_icon.png");
            } else if (ext.indexOf("doc") != -1 || ext.indexOf("docx") != -1) {
                $(file.previewElement).find(".dz-image img").attr("src", "/assets/img/doc_icon.png");
            } else if (ext.indexOf("xls") != -1 || ext.indexOf("csv") != -1) {
                $(file.previewElement).find(".dz-image img").attr("src", "/assets/img/xls_icon.png");
            }

            $('.dz-image').last().find('img').attr({width: '100%', height: '100%'});
        }
    },
    removedfile: function(file)
    {
        let fileuploded = file.previewElement.querySelector("[data-dz-name]");
        let name = '';
        if(typeof file.upload != "undefined" ){
            name = fileuploded.innerHTML;
        }else {
            name = file.name;
        }
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            type: 'post',
            url: taskUrl + 'delete-attachment/' + taskId,
            data: {filename: name},
            error: function(e) {
                console.log('error',e);
            }
        });
        let fileRef;
        return (fileRef = file.previewElement) != null ?
            fileRef.parentNode.removeChild(file.previewElement) : void 0;
    },
    success: function(file, response)
    {
        let fileuploded = file.previewElement.querySelector("[data-dz-name]");
        let fileName = response.data.fileName;
        let fileNameExtArr = fileName.split('.');
        let newFileName = fileNameExtArr[0];
        let newFileExt = fileNameExtArr[1];
        let prevFileName = fileuploded.innerHTML.split('.')[0];
        let fileUrl = attachmentUrl + fileName;
        fileuploded.innerHTML = fileName;

        if($.inArray(newFileExt,['jpg','jpge','png']) > -1) {
            $(".previewEle").find('.' + prevFileName).attr('href', fileUrl);
            $(".previewEle").find('.' + prevFileName).attr('class', newFileName);
            // $(file.previewElement).find(".dz-image img").addClass(newFileName);
        }
    },
    error: function(file, response)
    {
        swal("error!", response.message, "error");
        let fileRef;
        return (fileRef = file.previewElement) != null ?
            fileRef.parentNode.removeChild(file.previewElement) : void 0;

        return false;
    },
};