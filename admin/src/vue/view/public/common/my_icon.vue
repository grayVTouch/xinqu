<template>
    <i class="run-iconfont" :title="title" :class="{
        ['run-iconfont-' + icon]: true ,
        'padding-left': mode === 'left' || mode === 'both' ,
        'padding-right': mode === 'right' || mode === 'both' ,
        ['f-' + size]: true ,
        gray: color === 'gray' ,
       }"></i>
</template>

<script>
    export default {
        name: "icon" ,

        props: {
            icon: {
                type: String ,
                default: '' ,
            } ,

            mode: {
                type: String ,
                default: '' ,
            } ,

            size: {
                type: [Number,String] ,
                default: 14 ,
            } ,

            title: {
                type: String ,
                default: '' ,
            } ,

            color: {
                type: String ,
                default: 'white' ,
            } ,
        } ,
    }
</script>

<style scoped>
    .run-iconfont {

    }

    .run-iconfont.padding-left {
        padding-left: 5px;
    }

    .run-iconfont.padding-right {
        padding-right: 5px;
    }

    .run-iconfont.gray {
        color: #555;
    }
</style>
