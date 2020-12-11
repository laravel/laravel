<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Example of using Vue.extend(...)</div>

                    <div class="panel-body" v-html="description"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    export default Vue.extend({
        data() {
            let description = [
                'This is a Vue component written in TypeScript.',
                'To let TypeScript properly infer types inside Vue component options, ',
                'you need to define components with <code>Vue.component(...)</code> or <code>Vue.extend(...)</code>',
                '',
                '<i><a href="https://vuejs.org/v2/guide/typescript.html">Click Detail</a> for more information</i>'
            ];
            return {
                description
            }
        },
        mounted(): void {
            this.description = _.join(this.description, '<br>');        // Test global lodash
        }
    })
</script>
