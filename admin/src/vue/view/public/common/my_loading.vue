<template>
    <i class="run-iconfont run-iconfont-jiazai_dan" :style="'font-size:' + size + 'px; color: ' + color + ';'"></i>
</template>

<script>
    export default {
        name: "loading" ,
        props: {
            size: {
                type: [String,Number] ,
                default: 30
            } ,
            color: {
                type: String ,
                default: '#dbdbdb' ,
            } ,
        } ,
    }
</script>

<style scoped>
    .run-iconfont {
        display: inline-block;
        vertical-align: middle;
        animation: loading 0.6s infinite linear;
        -webkit-animation: loading 0.6s infinite linear;
        -moz-animation: loading 0.6s infinite linear;
        -o-animation: loading 0.6s infinite linear;
    }

    @keyframes loading {
        0% {
            transform: rotateZ(0);
            -webkit-transform: rotateZ(0);
            -moz-transform: rotateZ(0);
            -ms-transform: rotateZ(0);
            -o-transform: rotateZ(0);
        }

        100% {
            transform: rotateZ(360deg);
            -webkit-transform: rotateZ(360deg);
            -moz-transform: rotateZ(360deg);
            -ms-transform: rotateZ(360deg);
            -o-transform: rotateZ(360deg);
        }
    }
</style>