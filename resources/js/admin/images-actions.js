const selectors = {
    wrapper: '#edit-images-wrapper',
    item: '.images-wrapper-item',
    removeBtn: '.images-wrapper-item-remove'
}

$(document).ready(function() {
    $(selectors.removeBtn).on('click', function(e) {
        e.preventDefault()

        const $btn = $(this)
        $btn.addClass('disabled')

        axios.delete($btn.data('url'), {
            responseType: 'json'
        }).then((response) => {
            iziToast.success({
                message: response.data.message,
                position: 'topRight'
            })
            $btn.parent().remove()
        }).catch((error) => {
            iziToast.warning({
                message: error.data.message,
                position: 'topRight'
            })
            $btn.removeClass('disabled')
        })
    })
})
