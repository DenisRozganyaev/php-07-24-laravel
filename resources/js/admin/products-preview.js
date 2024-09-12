const selectors = {
    thumbnail: {
        input: '#thumbnail',
        preview: '#thumbnail-preview'
    }
};

$(document).ready(function() {
    if (window.FileReader) {
        $(selectors.thumbnail.input).on('change', function() {
           const reader = new FileReader();

           reader.onloadend = (e) => {
               $(selectors.thumbnail.preview).attr('src', e.target.result).show();
           }
           reader.readAsDataURL(this.files[0]);
        });
    }
});
