export function copyObject(obj) {
    const copy = Object.assign({}, obj);

    return copy
}

export function objectToFormData (obj, form, namespace) {
    let fd = form || new FormData()

    for(var property in obj) {
        if(obj.hasOwnProperty(property)) {

            let formKey = namespace
                ? namespace + '[' + property + ']'
                : property

            if(obj[property] === Object(obj[property]) && !(obj[property] instanceof File)) {
                objectToFormData(obj[property], fd, formKey);
            } else if(obj[property] instanceof Array) {
                for (var i = 0; i < obj[property].length; i++) {
                    objectToFormData(obj[property][i], fd, `${formKey}[${i}]`);
                }
            } else {
                const value = obj[property] === null ? '' : obj[property]
                fd.append(formKey, value);
            }
        }
    }
    return fd
}
