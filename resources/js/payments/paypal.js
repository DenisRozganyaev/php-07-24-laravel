import '../bootstrap.js'

const selectors = {
    form: '#checkout-form'
}

const getFields = () => {
    return $(selectors.form).serializeArray()
        .reduce((obj, item) => {
            obj[item.name] = item.value
            return obj
        }, {})
}

const isEmptyFields = () => {
    const fields = getFields()

    Object.keys(fields).map((key) => {
        if (fields[key].length < 1) {
            $(`${selectors.form} input[name="${key}"]`).addClass('is-invalid')
        }
    })

    return Object.values(fields).some((val) => val.length < 1)
}

paypal.Buttons({
    style: {
        color: 'blue',
        shape: 'pill',
        label: 'pay',
        height: 40
    },

    onInit: function (data, actions) {
        actions.disable()

        $(selectors.form).change(() => {
            if (!isEmptyFields()) {
                actions.enable()
                $(selectors.form).find('.is-invalid').removeClass('is-invalid')
            }
        })
    },

    onClick: function (data, actions) {
        if (isEmptyFields()) {
            iziToast.warning({
                title: 'Please fill the form fields',
                position: 'topRight'
            })
            return
        }

        $(selectors.form).find('.is-invalid').removeClass('is-invalid')
    },

    // Call your server to set up the transaction
    createOrder: function (data, actions) {
        console.log('fields', getFields())
        return axios.post('/ajax/paypal/order', getFields())
            .then(function (res) {
                console.log('response', res)
                return res.data.vendor_order_id;
            }).catch((err) => {
                console.error('paypal', err)
            })
    },

    // Call your server to finalize the transaction
    onApprove: function (data, actions) {
        return axios.post(`/ajax/paypal/order/${data.orderID}/capture/`, {})
            .then(function (res) {
                return res.data;
            }).then(function (orderData) {
                console.log('orderData', orderData)
                iziToast.success({
                    title: 'Order was created',
                    position: 'topRight',
                    onClosing: () => {
                        window.location.href = `/orders/${orderData.vendor_order_id}/thank-you`
                    }
                })
            }).catch((err) => {
                let errorDetail = Array.isArray(err.details) && err.details[0];

                if (errorDetail && errorDetail.issue === 'INSTRUMENT_DECLINED') {
                    return actions.restart(); // Recoverable state, per:
                    // https://developer.paypal.com/docs/checkout/integration-features/funding-failure/
                }

                if (errorDetail) {
                    let msg = 'Sorry, your transaction could not be processed.';
                    if (errorDetail.description) msg += '\n\n' + errorDetail.description;
                    if (err.debug_id) msg += ' (' + err.debug_id + ')';
                    return alert(msg); // Show a failure message (try to avoid alerts in production environments)
                }

                // Successful capture! For demo purposes:
                console.log('Capture result', err, JSON.stringify(err, null, 2));
                let transaction = err.purchase_units[0].payments.captures[0];
                alert('Transaction ' + transaction.status + ': ' + transaction.id + '\n\nSee console for all available details');
            })
    }

}).render('#paypal-button-container');
