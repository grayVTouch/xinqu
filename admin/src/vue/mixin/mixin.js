
import store from '@vue/vuex';

import route from './route.js';
import api from './api.js';
import message from './message.js';
import value from './value.js';

/**
 * ****************
 * 全局混入
 * ****************
 */
Vue.mixin({

    store ,

    data () {
        return {
            TopContext ,
            myValue: {
                pending: {} ,
                error: {} ,
                select: {} ,
                request: {} ,
            } ,
        };
    } ,

    methods: {
        // 路由
        ...route ,

        ...api ,

        ...value ,

        ...message ,

        isValid (val) {
            return G.isValid(val) && !G.isEmptyString(val);
        } ,

        push (...args) {
            this.$router.push.apply(this.$router , args);
        } ,

        openWindow (url , type = '_blank') {
            window.open(url , type);
        } ,


        handleImageSubject () {

        } ,

        handleVideo () {

        } ,

        getUsername (username , nickname) {
            return username ? username : (nickname ? nickname : '');
        } ,

        // 生成排序字符串
        generateOrderString (key , order) {
            return key + '|' + order;
        } ,

        state () {
            return this.$store.state;
        } ,

        // 快捷方式：获取当前登录用户
        user () {
            return this.state().user;
        } ,

        topContext () {
            return TopContext;
        } ,

        cookie () {
            return G.cookie;
        } ,
    }
});
