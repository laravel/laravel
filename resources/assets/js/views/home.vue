<script lang="coffee">
module.exports =
    ready: () -> require '../view-ready.coffee'.call this
    props: ['app']
</script>

<template lang="jade">
title(foo="bar") Laravue 1.0
random-quote
</template>