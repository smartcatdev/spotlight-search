jQuery(document).ready(function($) {

    let lastSearch = ''
    $('.spotlight-results').html('')
    $('.spotlight-wrapper input').html('')
    $('.lds-ellipsis').hide()

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
        let spotlight = $('.spotlight-wrapper')
        if ((event.ctrlKey && event.key === '.') || event.metaKey && event.key === '.') {
            if (spotlight.hasClass('spotlight-hidden')) {
                spotlight.removeClass('spotlight-hidden')
                $('input', spotlight).focus()
            } else {
                spotlight.addClass('spotlight-hidden')
            }
        }
    }
    document.addEventListener('keydown', openSpotlight)
    
    
    const handleTextEntry = debounce((ev) => {
        startSearch(ev.target.value)
    }, 500)
    $('.spotlight-form-wrapper input').on('keyup', handleTextEntry)
    
    const startSearch = (input) => {

        $('.lds-ellipsis').show()
        

        if (!input) {
            lastSearch = ''
            // $('.spotlight-results').hide()
            $('.lds-ellipsis').hide()
            return false
        }

        // don't search if same as previous
        if (input === lastSearch) {
            $('.lds-ellipsis').hide()
            return false
        }
        lastSearch = input

        $('.spotlight-results').html('')
        fetchData(input)
        $('.spotlight-results').show()
    }

    const fetchData = (input) => {
        $.ajax({
            url: spotlightSettings.apiUrl + 'search?term=' + input,
            type: 'GET',
        })
        .done((response) => {
            renderHtml(prepareData(response))
            console.log(response)
        })
        .fail((response) => {
            console.log(input, response)
        })
    }

    const prepareData = (data) => {

        if (!Array.isArray(data) || data.length < 1) {
            return `<div>No results found with the search you specified</div>`
        }

        // let html = `<div class="heading">Results</div>`
        let html = ``
        let previousHeader = ''
        for (let post of data) {
            if (previousHeader != post.post_type) {
                html += `<div class="heading">${post.post_type.capitalize()}s</div>`
                previousHeader = post.post_type
            }
            html += `<div>
                        <a href="${post.url}">${post.post_title}</a>
                        <span><i>(Matched by: ${post.type})</i></span>
                        <a href="${post.edit_url}"><span class="dashicons dashicons-edit"></span></a>
                    </div>`
        }
        html += `<hr />`

        return html

    }

    const renderHtml = (html) => {
        $('.lds-ellipsis').hide()
        $('.spotlight-results').html(html)
    }

    Object.defineProperty(String.prototype, 'capitalize', {
        value: function() {
          return this.charAt(0).toUpperCase() + this.slice(1);
        },
        enumerable: false
    });

})
