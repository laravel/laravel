<script lang="coffee">
module.exports =
    data: {laravue: require './laravue.coffee'}
    components: require './components.coffee'
    ready: () ->
        @laravue.app = this
</script>

<style lang="stylus">
    .container
        text-align: center
        display: table-cell
        vertical-align: middle
    .content
        text-align: center
        display: inline-block
</style>

<template lang="jade">
.container
    .content
        component(is="{{laravue.currentView}}" app="{{@ laravue.app }}")
</template>