<template>
    <div class="text-center">
        <button v-on:click="decrement">-</button>
        {{ count }}
        <button v-on:click="increment">+</button>
    </div>
</template>

<script lang="ts">
    // 定义一个基于类的 Vue 组件
    @Component
    export default class Counter extends Vue {
        count: number = 0;

        increment() {
            this.count++
        }

        decrement() {
            this.count--
        }
    }
</script>