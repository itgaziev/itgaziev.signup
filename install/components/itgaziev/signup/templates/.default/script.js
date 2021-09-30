$(document).on('click', '.initialBtn', function(){
    $('.form-signin').attr('data-initial', $(this).attr('data-toggle-initial'))
})

$(document).on('submit', 'form.signin', function(e){
    e.preventDefault()

    let ajax = $(this).attr('action');
    let formData = new FormData($(this)[0]);
    let _this = $(this);
    $.ajax({
        url: ajax,
        type: 'POST',
        data: formData,
        async: false,
        cache: false,
        contentType: false,
        enctype: 'multipart/form-data',
        processData: false,
        success: function (response) {
            console.log(response);
            let success = true;
            if(response.errors !== undefined) {
                if(response.errors.length) {
                    response.errors.map(value => {
                        $(_this).find('input[name="' + value.field + '"]').addClass('is-invalid')
                        $(_this).find('input[name="' + value.field + '"]').parent().find('.invalid-feedback').html(`${value.text}`)
                    })
                    success = false;
                }
            }

            if(response.throw !== undefined && response.throw) {
                $(_this).find('.note-form').html(`<div class="error-form">${response.throw}</div>`)
                success = false;
            }

            if(success && response.back_url !== undefined) {
                location.href = response.back_url
            } else if(success && response.message !== undefined) {
                $(_this).find('.note-form').html(`<div class="success-form">${response.message}</div>`)
            }
        }
    });
})