<script lang="coffee">
module.exports =
    created: () ->
        random = Math.floor(Math.random() * 4)
        @quote = @quotes[random]

    data: () ->
        ret =
            quote: 'hi'
            quotes: require('../data/quotes.json') # You can pull data from anywhere! Here we use a json file.
</script>

<style lang="stylus">
.quote
    font-size: 24px
</style>
<template lang="jade">
.quote {{quote}}
</template>