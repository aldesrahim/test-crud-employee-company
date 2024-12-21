export function debounce(fn, delay) {
    let timeoutID = null;

    return function () {
        clearTimeout(timeoutID)

        const args = arguments;
        const that = this;

        timeoutID = setTimeout(function () {
            fn.apply(that, args)
        }, delay)
    }
}

export function removeProp(obj, prop) {
    if (prop instanceof Array) {
        let _rest;

        prop.forEach(_prop => {
            _rest = removeProp(obj, _prop)
        })

        return _rest
    }

    if (!obj || !obj[prop]) return obj

    const {[prop]: _, ...rest} = obj
    return rest
}
