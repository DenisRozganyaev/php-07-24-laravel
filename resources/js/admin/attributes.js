const selectors = {
    attributes: {
        addBtn: '.add-option',
        removeBtn: '.remove-option',
        list: '.options-list',
        option: {
            wrapper: '.option-wrapper',
            field: '.option-field'
        }
    },
    products: {
        wrapper: '.options-wrapper',
        addOption: '.add-options',
        selectedValues: '#attributes option:selected',
        selectField: '#attributes'
    }
}

const productOptionTemplate = `
<div class="input-group mb-2">
    <span class="input-group-text">_option_name_</span>
    <input type="hidden" name="_id_" min="0" value="_value_" class="form-control">
    <input type="number" name="_qty_name_" min="0" placeholder="Quantity" class="form-control" required />
    <input type="number" name="_price_name_" min="1" placeholder="Single price" class="form-control" required />
</div>
`

const attributeFieldTemplate = `
<div class="input-group mb-2 option-wrapper">
    <input type="text" class="form-control option-field" name="_name_" placeholder="Option name" />
<!--    <a class="btn btn-outline-danger remove-option"><i class="fa-solid fa-trash-can"></i></a>-->
</div>
`

$(document).ready(function() {
    $(selectors.attributes.addBtn).on('click', function(e) {
        e.preventDefault()

        let key = $(selectors.attributes.list).data('key')

        $(selectors.attributes.list).append(attributeFieldTemplate.replace('_name_', `options[${key}][value]`))
        key++
        $(selectors.attributes.list).data('key', key)
    })

    $(selectors.products.addOption).on('click', function(e) {
        e.preventDefault();

        const selected = $(selectors.products.selectedValues)
        let key = $(selectors.products.wrapper).data('key')

        selected.map((_, option) => {
            const $option = $(option)

            const template = productOptionTemplate.replace('_option_name_', $option.text())
                .replace('_qty_name_', `options[${key}][quantity]`)
                .replace('_price_name_', `options[${key}][price]`)
                .replace('_id_', `options[${key}][attribute_option_id]`)
                .replace('_value_', $option.val())

            $(selectors.products.wrapper).data('key', key).append(template)
            key++

            $(selectors.products.selectField).find(option).remove()
        })

        $(selectors.products.wrapper).data('key', key)
    })
})
