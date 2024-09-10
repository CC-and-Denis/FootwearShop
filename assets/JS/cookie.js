// questo andrà legato alla homePage
function updateCookie(title, name) {
    values = getCookie(title)
    values.name += 1
    values = JSON.stringify(values)
    document.cookie = title + "=" + values + "expires = Mer, 1 January 2200 00:00:00 UTC; path=/"
}

function getCookie(title) {
    const decodCookie = decodeURIComponent(document.cookie);
    const arrayCookie = decodCookie.split("; ");

    arrayCookie.forEach(element => {
        if (element.indexOf(title) == 0) {
            result = element.substring(title.lenght() + 1)
            return JSON.parse(result)
        }
    })
}