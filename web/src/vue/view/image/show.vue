<template>
    <div class="view">
        <div class="content">
            <!-- 详情 -->
            <div class="left image-subject" ref="image-subject">
                <div class="subject">
                    <div class="title run-action-title m-b-15">
                        <div class="left">
                            <div class="breadcrumb">
                                <template v-for="(v,k) in state().positions">
                                    <span>{{ v.name}}</span>
                                    <span class="symbol m-r-5" v-if="k < state().positions.length -1 ">&gt;</span>
                                </template>
                            </div>
                        </div>
                        <div class="right">

                            <my-button class="praise" @click="praiseHandle">
                                <my-loading size="16" v-if="val.pending.praiseHandle"></my-loading>
<!--                                <my-icon :class="{'run-red': data.is_praised }" icon="shoucang2" /> 喜欢 {{ data.praise_count }}-->
                                <my-icon :class="{'run-red': data.is_praised }" icon="shoucang2" /> 喜欢
                            </my-button>
                            <my-button class="praise" @click="showFavorites"><my-icon icon="shoucang5" :class="{'run-red': data.is_collected}" /> 收藏</my-button>

                        </div>
                    </div>

                    <div class="tags m-b-15">
                        <div class="left run-tags">
                            <span class="ico p-r-5"><my-icon icon="icontag" /></span>
                            <my-link class="tag" target="_blank" v-for="v in data.tags" :key="v.id" :href="genUrl(`/image/search?tag_id=${v.tag_id}`)">{{ v.name }}</my-link>
                        </div>
                        <div class="right">
                            <span class="number">
                                <span class="praise">
                                    <my-icon icon="shoucang2" class="run-position-relative run-t--1"></my-icon>
                                    {{ data.praise_count }}</span>&nbsp;|&nbsp;
                                <span class="collect">
                                    <my-icon icon="shoucang5" class="run-position-relative run-t--1"></my-icon>
                                    {{ data.collect_count }}</span>&nbsp;|&nbsp
                                <span class="view-count">
                                    <my-icon icon="chakan" class="run-position-relative run-t--1"></my-icon>
                                    {{ data.view_count }}</span>&nbsp;|&nbsp;
                                <span class="create-time">
                                    <my-icon icon="shijian" class="run-position-relative run-t--1"></my-icon>
                                    {{ data.created_at }}
                                </span>
                            </span>
                        </div>
                    </div>

                    <div class="images" ref="images">
                        <img :src="data.src" @click="imageClick(1)" class="image">
                    </div>
                </div>

                <div class="comment"></div>
                <!-- 相关推荐 -->
                <div class="recommend">
                    <div class="inner">
                        <div class="title">相关推荐</div>
                        <div class="list">

                            <my-image-card-box
                                    class="item"
                                    v-for="v in recommend.data"
                                    :key="v.id"
                                    :row="v"
                                    @on-praise="praiseHandle"
                                    :is-praise-pending="val.pending.praiseHandle"
                            ></my-image-card-box>

                            <div class="empty" v-if="!val.pending.getRecommendData && recommend.data.length <= 0">
                                <my-icon icon="empty" size="40"></my-icon>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- 其他 -->
            <div class="right misc" ref="misc">
                <div class="inner" :class="{'fixed-top': val.fixedTop , 'fixed-btm': val.fixedBtm}">

                    <!-- 发布者 -->
                    <a class="user m-b-20" target="_blank" :href="genUrl(`/channel/${data.user.id}`)">
                        <div class="inner">
                            <div class="avatar">
                                <div class="mask">
                                    <img :data-src="data.user ? data.user.avatar : TopContext.res.notFound" v-judge-img-size class="image judge-img-size" alt="">
                                </div>
                            </div>
                            <div class="name">{{ data.user ? data.user.username : '' }}</div>
                            <div class="data">
                                <a class="left" target="_blank" :href="genUrl(`/channel/${data.user_id}/my_focus_user`)">关注 {{ data.user.my_focus_user_count }}</a>
                                <a class="right" target="_blank" :href="genUrl(`/channel/${data.user_id}/focus_me_user`)">粉丝 {{ data.user.focus_me_user_count }}</a>
                            </div>
                            <div class="desc">{{ data.user.description }}</div>
                            <div class="action">
                                <my-button class="focus" @click.prevent="focusHandle">

                                    <template v-if="!data.user.focused"><my-icon icon="add" v-if="!data.user.focused" class="run-position-relative run-t--2" /> 关注</template>
                                    <template v-else>取消关注</template>
                                    <my-loading size="16" v-if="val.pending.focusHandle"></my-loading>
                                </my-button>
                                <my-button class="message" v-if="false" @click.prevent>私信</my-button>
                            </div>
                        </div>
                    </a>

                    <!-- 最新发布 -->
                    <div class="newest" ref="newest">
                        <div class="inner">
                            <div class="title">最新发布</div>
                            <div class="list">

                                <a class="item" v-for="v in newest.data" :Key="v.id" @click.prevent="linkToImage(v)">
                                    <div class="inner">
                                        <img :data-src="v.src ? v.src : TopContext.res.notFound" v-judge-img-size class="image judge-img-size">
                                    </div>
                                </a>

                                <div class="empty" v-if="!val.pending.getNewestData && newest.data.length <= 0">
                                    <my-icon icon="empty" size="40"></my-icon>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- app 下载 -->
                    <div class="mobile"></div>
                </div>
            </div>
        </div>

        <div class="loading" v-if="val.pending.getData">
            <my-loading width="50" height="50"></my-loading>
        </div>

        <!-- 图片预览 -->
        <my-pic-preview ref="pic-preview"></my-pic-preview>

        <!-- 收藏夹 -->
        <my-collection-group
                ref="my-collection-group"
                :relation-id="id"
                relation-type="image"
                @on-change="collectionHandle"
        ></my-collection-group>

    </div>
</template>

<script src="./js/show.js"></script>
<style scoped src="../public/css/base.css"></style>
<style scoped src="./css/show.css"></style>
