<template>
    <i-button v-ripple :type="type" :size="size" :icon="icon" :shape="shape" :loading="loading" @click.stop="clickEvent">
        <slot></slot>
    </i-button>
</template>

<script>
    export default {
        name: "my-table-button" ,

        props: {
            type: {
                type: String ,
                default: 'primary' ,
            } ,

            size: {
                type: String ,
                default: 'small' ,
            } ,

            loading: {
                type: Boolean ,
                default: false ,
            } ,

            icon: {
                type: String ,
                default: '' ,
            } ,

            shape: {
                type: String ,
            } ,
        } ,

        methods: {
            clickEvent () {
                this.$emit('click');
            } ,
        } ,
    }
</script>

<style scoped>

</style>
