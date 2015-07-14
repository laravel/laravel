module.exports =
    template: require './random-quote.template.html'
    created: () ->
        random = Math.floor(Math.random() * 6)
        @quote = @quotes[random]

    data: () ->
        ret =
            quote: ''
            quotes: [
                "Notice the small things. The rewards are inversely proportional."
                "Even the largest avalanche is triggered by small things."
                "Great things are done by a series of small things brought together."
                "Great things are not done by impulse, but by a series of small things brought together."
                "Coming together is a beginning; keeping together is progress; working together is success."
                "From small beginnings come great things."
            ]