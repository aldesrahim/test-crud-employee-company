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
    if (!obj[prop]) return obj

    const {[prop]: _, ...rest} = obj
    return rest
}
