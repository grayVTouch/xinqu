<template>
    <div class="view">
        <div class="header">
            <div class="thumb">
                <img :src="data.thumb ? data.thumb : TopContext.res.notFound" class="image judge-img-size" v-judge-img-size>
            </div>
            <div class="info">
                <div class="name m-b-10">优秀的图片</div>
                <div class="user">
                    <a :href="genUrl(`/channel/${data.user.id}`)">{{ getUsername(data.user.username , data.user.nickname)}}</a>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="pager top-pager">
                <my-page :page="collections.page" :limit="collections.size" :total="collections.total" @on-change="toPage"></my-page>
            </div>

            <div class="content">
                <div class="title"></div>
                <div class="list">

                    <div class="item card-box" v-for="v in collections.data" :key="v.id">
                        <!-- 封面 -->
                        <div class="thumb">
                            <a class="link" target="_blank" :href="`#/image_project/${v.relation.id}/show`">
                                <img :src="v.relation.thumb ? v.relation.thumb : TopContext.res.notFound" v-judge-img-size class="image judge-img-size">
                                <div class="mask">
                                    <div class="top">
                                        <div class="type" v-if="v.relation.type === 'pro'"><my-icon icon="zhuanyerenzheng" size="35" /></div>
                                        <div class="praise" v-ripple @click.prevent="praiseHandle(v)">
                                            <my-loading size="16" v-if="val.pending.praiseHandle"></my-loading>
                                            <my-icon icon="shoucang2" :class="{'run-red': v.relation.is_praised }" /> 喜欢
                                        </div>
                                    </div>
                                    <div class="btm">
                                        <div class="count">{{ v.relation.images.length }}P</div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="introduction">
                            <!-- 标签 -->
                            <div class="tags">
                                <span class="ico"><my-icon icon="icontag" size="18" /></span>

                                <a class="tag" target="_blank" v-for="tag in v.relation.tags" :href="`#/image_project/search?tag_id=${tag.tag_id}`">{{ tag.name }}</a>
                            </div>
                            <!-- 标题 -->
                            <div class="title"><a target="_blank" :href="`#/image_project/${v.relation.id}/show`">{{ v.relation.name }}</a></div>
                            <!-- 发布者 -->
                            <div class="user">
                                <a class="sender" target="_blank" :href="genUrl(`/channel/${v.relation.user.id}`)">
                                    <span class="avatar-outer"><img :src="v.relation.user.avatar ? v.relation.user.avatar : TopContext.res.avatar" alt="" class="image avatar"></span>
                                    <span class="name">{{ v.relation.user.nickname }}</span>
                                </a>
                                <div class="action"></div>
                            </div>
                            <!-- 统计信息 -->
                            <div class="info">
                                <div class="left"><my-icon icon="shijian" class="ico" mode="right" /> {{ v.relation.created_at }}</div>
                                <div class="right">
                                    <span class="view-count"><my-icon icon="chakan" mode="right" />{{ v.relation.view_count }}</span>
                                    <span class="praise-count"><my-icon icon="shoucang2" mode="right" />{{ v.relation.praise_count }}</span>
                                    <span class="collect-count" v-if="$store.state.user"><my-icon icon="shoucang6" mode="right" />{{ v.relation.collect_count }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 切换标签时的加载层 -->
                    <div class="loading" v-if="val.pending.getCollection">
                        <my-loading width="50" height="50"></my-loading>
                    </div>

                    <div class="empty" v-if="!val.pending.getCollection && collections.data.length === 0">暂无数据</div>

                </div>
            </div>

            <div class="pager btm-pager">
                <my-page :page="collections.page" :limit="collections.size" :total="collections.total" @on-change="toPage"></my-page>
            </div>



        </div>

    </div>
</template>

<script>
    export default {
        name: "index" ,
        data () {
            return {
                val: {
                    pending: {} ,

                } ,
                data: {
                    user: {} ,
                    module: {} ,

                } ,
                images: [] ,
                collections: {
                    page: 1 ,
                    total: 1 ,
                    size: TopContext.size ,
                    data: [] ,
                    relation_type: 'image_project' ,
                } ,
            };
        } ,

        props: ['id'] ,

        mounted () {
            this.initData();
            this.getCollection();
        } ,

        methods: {

            // 图片点赞 | 取消点赞
            praiseHandle (collection) {
                if (this.pending('praiseHandle')) {
                    return ;
                }
                const action = collection.relation.is_praised ? 0 : 1;
                this.pending('praiseHandle' , true);
                Api.user
                    .praiseHandle(null , {
                        relation_type: 'image_project' ,
                        relation_id: collection.relation.id ,
                        action ,
                    })
                    .then((res) => {
                        this.pending('praiseHandle' , false);
                        if (res.code !== TopContext.code.Success) {
                            this.errorHandleAtHomeChildren(res.message , res.code , () => {
                                this.praiseHandle(collection);
                            });
                            return ;
                        }
                        collection.relation.is_praised = action;
                        action ? collection.relation.praise_count++ : collection.relation.praise_count--;
                    })
                    .finally(() => {
                    });
            } ,


            initData () {
                this.pending('initData' , true);
                Api.user
                    .collectionGroupInfo(this.id)
                    .then((res) => {
                        if (res.code !== TopContext.code.Success) {
                            this.errorHandleAtHomeChildren(res.message , res.code);
                            return ;
                        }
                        this.data = res.data;
                    })
                    .finally(() => {
                        this.pending('initData' , false);
                    });
            } ,

            getCollection () {
                this.pending('getCollection' , true);
                Api.user
                    .collections({
                        relation_type: this.collections.relation_type ,
                        collection_group_id: this.id ,
                        size: this.collections.size
                    })
                    .then((res) => {
                        if (res.code !== TopContext.code.Success) {
                            this.errorHandleAtHomeChildren(res.message , res.code);
                            return ;
                        }
                        const data = res.data;
                        data.data.forEach((v) => {
                            v.relation = v.relation ? v.relation : {};
                            this.handleImageProject(v.relation);
                        });
                        this.collections.page = data.current_page;
                        this.collections.total = data.total;
                        this.collections.data = data.data;
                    })
                    .finally(() => {
                        this.pending('getCollection' , false);
                    });
            } ,

            toPage (page) {
                this.collections.page = page;
                this.getCollection();
            } ,
        } ,
    }
</script>

<style scoped src="../public/css/base.css"></style>
<style scoped>

    .view > * {
        margin-top: 20px;
    }

    .view > *:nth-of-type(1) {
        margin-top: 0;
    }

    .view > .header {
        position: relative;
        height: 200px;

    }

    .view .header .thumb {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        z-index: 0;
        height: inherit;
        overflow: hidden;
    }

    .view .header .info {
        position: absolute;
        z-index: 1;
        bottom: 10px;
        left: 20px;
        background-color: rgba(0,0,0,0.3);
        padding: 10px;
    }

    .view .header .info .name {
        color: var(--font-color-white);
    }

    .view .header .info .user {
        color: var(--font-color-yellow);
        font-size: 12px;
    }

    .view > .content > .pager {
        display: flex;
        align-items: center;
    }

    .view > .content > .pager > * {
        margin: 0;
    }

    .view > .content > .top-pager {
        justify-content: flex-end;
    }

    .view > .content > .btm-pager {
        justify-content: center;
    }


    .view > .content > .content {

    }

    .view > .content > .content .list {
        display: flex;
        justify-content: flex-start;
        align-items: flex-start;
        align-content: flex-start;
        flex-wrap: wrap;
        padding: 20px 0;
        position: relative;
    }

    .view > .content > .content .list .item {
        margin: 0;
        flex: 0 0 auto;
        margin-right: 20px;
        margin-top: 20px;
    }

    .view > .content > .content .list .item:nth-of-type(4n) {
        margin-right: 0;
    }

    .view > .content > .content .list .item:nth-of-type(1) ,
    .view > .content > .content .list .item:nth-of-type(2) ,
    .view > .content > .content .list .item:nth-of-type(3) ,
    .view > .content > .content .list .item:nth-of-type(4) {
        margin-top: 0;
    }


    .view > .content .content .list .loading {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 2;
    }

    .view > .content .content .list .empty {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1;
        font-size: 14px;
    }
</style>
