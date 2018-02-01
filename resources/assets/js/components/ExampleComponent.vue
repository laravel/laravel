<template>
    <!--<div class="flex flex-col">-->
        <!--<div class="h-16 flex bg-blue-lightest"> &lt;!&ndash; top section &ndash;&gt;-->
            <!--<div class="w-64 flex items-center justify-between"> &lt;!&ndash; top left &ndash;&gt;-->
                <!--<logo class="m-4"></logo>-->
                <!--<i class="fa fa-angle-right mr-2"></i>-->
                <!--<div class="text-xs flex-grow truncate mr-2">{{ campaignName }}</div>-->
                <!--<i class="fa fa-cog text-xs cursor-pointer"></i>-->
                <!--<div class="w-px bg-grey-lightest h-8 ml-2"></div>-->
            <!--</div>-->
            <!--<div class="bg-yellow"></div>-->
        <!--</div>-->
        <!--<div class="bg-orange flex-grow flex"> &lt;!&ndash; main section &ndash;&gt;-->
            <!--<div class="bg-grey-dark w-64"> &lt;!&ndash; left &ndash;&gt;-->

            <!--</div>-->
        <!--</div>-->
    <!--</div>-->

    <div class="flex flex-col">
        <div class="bg-yellow h-16 flex">
            <div class="bg-blue-lightest w-64 flex justify-between items-center">
                <logo class="mx-2"></logo>
                <i class="fa fa-angle-right mx-2"></i>
                <div class="flex-grow">{{ campaignName }}</div>
                <i class="fa fa-cog mr-2"></i>
            </div>
            <div class="bg-blue-dark flex-grow"></div>
        </div>
        <div class="bg-pink flex-grow">2</div>
    </div>
</template>

<script>
    import Logo from './Logo.vue';

    export default {
        data() {
            return {
//                campaignName: 'Vue.js demo that has quite a long name',
                campaignName: 'Vue.js demo',
            }
        },

        mounted() {
            console.log('Component mounted.')
        },

        components: {
            Logo
        }
    }
</script>
