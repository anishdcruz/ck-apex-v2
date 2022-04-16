import Vue from 'vue'

Vue.filter('formatMoney', function(value, currency, code = true) {
    if(!currency) {
        return value
    }
    const amount = Number(value)
        .toFixed(currency.decimal_place)
        .replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")

    return code
        ? `${currency.code} ${amount}`
        : amount
})

Vue.filter('trim', (value, max) => {
    if(!value) {
        return value
    }
    const len = value.length
    return len > max
        ? `${value.substring(0, max)}...`
        : value
})
