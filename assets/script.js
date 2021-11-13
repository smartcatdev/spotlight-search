let searching = false

const debounce = (callback, wait) => {
    let timeoutId = null;
    return (...args) => {
        window.clearTimeout(timeoutId);
        timeoutId = window.setTimeout(() => {
            callback.apply(null, args);
        }, wait);
    };
}

const openSpotlight = (event) => {
    let spotlight = document.querySelector('.spotlight-wrapper')
    if ((event.ctrlKey && event.key === '.') || event.metaKey && event.key === '.') {
        if (spotlight.style.display === 'none') {
            spotlight.style.display = 'block'
            spotlight.querySelector('input').focus()
        } else {
            spotlight.style.display = 'none'
        }
    }
}
document.addEventListener('keydown', openSpotlight)


const handleTextEntry = debounce((ev) => {
    startSearch
}, 500)

const startSearch = () => {
    searching = true
    document.querySelector('.spotlight wrapper .searching').style.display = 'block'
}