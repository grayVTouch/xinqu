<template>
    <div class="view">

        <div class="content">

            <!-- 筛选 -->
            <div class="filter">
                <div class="condition">
                    <div class="filter-selector horizontal" ref="filter-selector-in-horizontal">

                        <div class="action item" @click.stop="showVideoSeriesSelector" v-ripple>
                            <div class="inner">
                                <my-icon icon="guanlian" /> 视频系列
                                <span class="number" v-if="videoSeries.selected.length > 0">
                                <template v-if="videoSeries.selected.length < 10">{{ videoSeries.selected.length }}</template>
                                <template v-else>9+</template>
                            </span>
                            </div>
                        </div>

                        <div class="action item" @click.stop="showVideoCompanySelector" v-ripple>
                            <div class="inner">
                                <my-icon icon="gongsi" /> 制作公司
                                <span class="number" v-if="videoCompanies.selected.length > 0">
                                <template v-if="videoCompanies.selected.length < 10">{{ videoCompanies.selected.length }}</template>
                                <template v-else>9+</template>
                            </span>
                            </div>
                        </div>

                        <div class="action item" @click.stop="showCategorySelector" v-ripple>
                            <div class="inner">
                                <my-icon icon="leimupinleifenleileibie" /> 分类
                                <span class="number" v-if="categories.selected.length > 0">
                                <template v-if="categories.selected.length < 10">{{ categories.selected.length }}</template>
                                <template v-else>9+</template>
                            </span>
                            </div>
                        </div>

                        <div class="action item" @click.stop="showTagSelector" v-ripple>
                            <div class="inner">
                                <my-icon icon="icontag" /> 标签

                                <span class="number" v-if="tags.selected.length > 0">
                                <template v-if="tags.selected.length < 10">{{ tags.selected.length }}</template>
                                <template v-else>9+</template>
                            </span>

                            </div>
                        </div>

                        <div class="action item order" ref="order" @click="showOrderSelectorInHorizontal" v-ripple>
                            <div class="inner">
                                <my-icon icon="paixu" /> 排序
                                <span class="number" v-if="videoProjects.order">1</span>
                            </div>
                            <div class="order-selector hide" ref="order-selector-in-horizontal" @click.stop @mousedown.stop>
                                <div class="background" @click.stop="closeOrderSelectorInHorizontal"></div>
                                <div class="list">
                                    <div class="item" v-ripple v-for="v in orders" :key="v.key" :data-id="v.key" :class="{cur: videoProjects.order === v.key}" @click.stop="orderInImageProject(v)">{{ v.name }}</div>
                                    <!--                            <div class="item cur">测试项</div>-->
                                </div>
                            </div>
                        </div>

                        <div class="action item" @click="resetFilter" v-ripple>
                            <div class="inner">
                                <my-icon icon="reset" /> 重置
                                <!--                            <span class="number">9+</span>-->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="operation"></div>
            </div>

            <div class="images">
                <!-- 切换标签时的加载层 -->
                <div class="loading" v-if="val.pending.getData">
                    <my-loading width="50" height="50"></my-loading>
                </div>

                <div class="empty" v-if="!val.pending.getData && videoProjects.data.length === 0">暂无数据</div>

                <div class="list">


                    <div class="item card-box" v-for="v in videoProjects.data" :key="v.id">
                        <!-- 封面 -->
                        <div class="thumb">
                            <a class="link" target="_blank" :href="genUrl(`/video_project/${v.id}/show`)">
                                <img :src="v.thumb ? v.thumb : TopContext.res.notFound" v-judge-img-size class="image judge-img-size">
                                <div class="mask">
                                    <div class="top">
                                        <div class="type"><my-icon icon="zhuanyerenzheng" size="35" /></div>
                                        <div class="praise" v-ripple @click.prevent="praiseHandle(v)">
                                            <my-loading size="16" v-if="val.pending.praiseHandle"></my-loading>
                                            <my-icon icon="shoucang2" :class="{'run-red': v.is_praised }" /> 喜欢
                                        </div>
                                    </div>
                                    <div class="btm">
                                        <div class="count">{{ v.count }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="introduction">
                            <!-- 标签 -->
                            <div class="tags">
                                <span class="ico"><my-icon icon="icontag" size="18" /></span>
                                <a class="tag" target="_blank" v-for="tag in v.tags" :href="genUrl(`/video_project/search?tag_id=${tag.tag_id}`)">{{ tag.name }}</a>
                            </div>
                            <!-- 标题 -->
                            <div class="title"><a target="_blank" :href="genUrl(`/video_project/${v.id}/show`)">{{ v.name }}</a></div>
                            <!-- 统计信息 -->
                            <div class="info">
                                <div class="left"><my-icon icon="shijian" class="ico" mode="right" /> {{ v.created_at }}</div>
                                <div class="right">
                                    <span class="view-count"><my-icon icon="chakan" mode="right" />{{ v.view_count }}</span>
                                    <span class="praise-count"><my-icon icon="shoucang2" mode="right" />{{ v.praise_count }}</span>
                                    <span class="collect-count" v-if="$store.state.user"><my-icon icon="shoucang6" mode="right" />{{ v.collect_count }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="pager">
                <my-page :total="videoProjects.total" :limit="videoProjects.size" :page="videoProjects.page" @on-change="toPageInImageProject"></my-page>
            </div>

        </div>

        <!-- 水平菜单 -->
        <div class="filter-fixed-in-slidebar hide" ref="filter-fiexed-in-slidebar">

            <div class="filter-selector vertical">

                <div class="action item" @click.stop="showVideoSeriesSelector" v-ripple>
                    <div class="inner">
                        <my-icon icon="guanlian" /> 系列
                        <span class="number" v-if="videoSeries.selected.length > 0">
                                <template v-if="videoSeries.selected.length < 10">{{ videoSeries.selected.length }}</template>
                                <template v-else>9+</template>
                            </span>
                    </div>
                </div>

                <div class="action item" @click.stop="showVideoCompanySelector" v-ripple>
                    <div class="inner">
                        <my-icon icon="gongsi" /> 公司
                        <span class="number" v-if="videoCompanies.selected.length > 0">
                                <template v-if="videoCompanies.selected.length < 10">{{ videoCompanies.selected.length }}</template>
                                <template v-else>9+</template>
                            </span>
                    </div>
                </div>

                <div class="action item" @click.stop="showCategorySelector" v-ripple>
                    <div class="inner">
                        <my-icon icon="leimupinleifenleileibie" /> 分类
                        <span class="number" v-if="categories.selected.length > 0">
                        <template v-if="categories.selected.length < 10">{{ categories.selected.length }}</template>
                        <template v-else>9+</template>
                    </span>
                    </div>
                </div>

                <div class="action item" @click.stop="showTagSelector" v-ripple>
                    <div class="inner">
                        <my-icon icon="icontag" /> 标签
                        <span class="number" v-if="tags.selected.length > 0">
                        <template v-if="tags.selected.length < 10">{{ tags.selected.length }}</template>
                        <template v-else>9+</template>
                    </span>
                    </div>
                </div>

                <div class="action item order" @click.stop="showOrderSelectorInVertical" v-ripple>
                    <div class="inner">
                        <my-icon icon="paixu" /> 排序
                        <span class="number" v-if="videoProjects.order">1</span>
                    </div>
                    <div class="order-selector hide" ref="order-selector-in-vertical" @click.stop @mousedown.stop>
                        <div class="list">
                            <div class="item" v-ripple v-for="v in orders" :key="v.key" :data-id="v.key" :class="{cur: videoProjects.order === v.key}" @click="orderInImageProject(v)">{{ v.name }}</div>
                        </div>
                    </div>
                </div>

                <div class="action item" @click="resetFilter" v-ripple>
                    <div class="inner">
                        <my-icon icon="reset" /> 重置
                    </div>
                </div>

            </div>

        </div>


        <!-- 分类选择器 -->
        <div class="category-selector hide" ref="category-selector" @click.stop>
            <div class="background" ref="category-background" @click="closeCategorySelector"></div>

            <div class="content">
                <div class="title">
                    <div class="name">请选择分类</div>
                    <div class="action">
                        <div class="item reset" v-ripple @click.stop="resetCategoryFilter"><my-icon icon="reset" size="13" /></div>
                        <div class="item close" v-ripple @click.stop="closeCategorySelector"><my-icon icon="close" size="13" /></div>
                    </div>
                </div>
                <div class="selected">
                    <div class="title">当前选中项</div>
                    <div class="list">
                        <div class="item" v-for="v in categories.selected" :key="v.id" @click="delCategory(v)">{{ v.name }}</div>
                    </div>
                </div>
                <div class="categories">
                    <div class="loading" v-if="val.pending.getCategories"><my-loading size="30"></my-loading></div>
                    <div class="item" v-ripple v-for="v in categories.data" :class="{cur: categories.selectedIds.indexOf(v.id) >= 0}" @click="filterByCategory(v)">{{ '&nbsp;'.repeat((v.floor - 1) * 8) + v.name }}</div>
                </div>
            </div>

        </div>

        <!-- 视频系列 -->
        <div class="pager-selector video-series-selector hide" ref="video-series-selector" @click.stop>
            <div class="background" ref="subject-background" @click="closeVideoSeriesSelector"></div>
            <div class="content">
                <div class="title">
                    <div class="name">请选择视频系列</div>
                    <div class="action">
                        <div class="item reset" v-ripple @click.stop="resetVideoSeriesFilter"><my-icon icon="reset" size="13" /></div>
                        <div class="item close" v-ripple @click.stop="closeVideoSeriesSelector"><my-icon icon="close" size="13" /></div>
                    </div>
                </div>
                <div class="selected">
                    <div class="title">当前选中项</div>
                    <div class="list">
                        <div class="item"  v-for="v in videoSeries.selected" @click="delVideoSeries(v)">
                            <div class="thumb" v-if="false"><img :src="v.thumb ? v.thumb : TopContext.res.notFound" class="image" alt=""></div>
                            <div class="name">{{ v.name }}</div>
                        </div>
                    </div>
                </div>
                <div class="filter">
                    <div class="title">数据列表</div>
                    <div class="search">
                        <div class="inner">
                            <div class="ico"><my-icon icon="search" /></div>
                            <div class="input"><input type="text" class="form-text" v-model="videoSeries.value" @keyup.enter="searchVideoSeries" placeholder="请搜索关联主体"></div>
                        </div>
                    </div>

                    <div class="list">
                        <div class="loading" v-if="val.pending.getVideoSeries"><my-loading size="30"></my-loading></div>
                        <div class="empty" v-if="!val.pending.getVideoSeries && videoSeries.data.length === 0">暂无相关数据</div>
                        <div class="item" v-ripple v-for="v in videoSeries.data" :class="{cur: videoSeries.selectedIds.indexOf(v.id) >= 0}" @click="filterByVideoSeries(v)">
                            <div class="thumb" v-if="false"><img :src="v.thumb ? v.thumb : TopContext.res.notFound" class="image" alt=""></div>
                            <div class="name">{{ v.name }}</div>
                        </div>
                    </div>
                    <div class="pager">
                        <my-page :total="videoSeries.total" :limit="videoSeries.size" :page="videoSeries.page" @on-change="toPageInVideoSeries"></my-page>
                    </div>
                </div>
            </div>

        </div>

        <!-- 视频公司 -->
        <div class="pager-selector video-company-selector hide" ref="video-company-selector" @click.stop>
            <div class="background" ref="subject-background" @click="closeVideoCompanySelector"></div>
            <div class="content">
                <div class="title">
                    <div class="name">请选择制作公司</div>
                    <div class="action">
                        <div class="item reset" v-ripple @click.stop="resetVideoCompanyFilter"><my-icon icon="reset" size="13" /></div>
                        <div class="item close" v-ripple @click.stop="closeVideoCompanySelector"><my-icon icon="close" size="13" /></div>
                    </div>
                </div>
                <div class="selected">
                    <div class="title">当前选中项</div>
                    <div class="list">
                        <div class="item"  v-for="v in videoCompanies.selected" @click="delVideoCompany(v)">
                            <div class="thumb"><img :src="v.thumb ? v.thumb : TopContext.res.notFound" class="image" alt=""></div>
                            <div class="name">{{ v.name }}</div>
                        </div>
                    </div>
                </div>
                <div class="filter">
                    <div class="title">数据列表</div>
                    <div class="search">
                        <div class="inner">
                            <div class="ico"><my-icon icon="search" /></div>
                            <div class="input"><input type="text" class="form-text" v-model="videoCompanies.value" @keyup.enter="searchVideoCompany" placeholder="请搜索关联主体"></div>
                        </div>
                    </div>

                    <div class="list">
                        <div class="loading" v-if="val.pending.getVideoCompany"><my-loading size="30"></my-loading></div>
                        <div class="empty" v-if="!val.pending.getVideoCompany && videoCompanies.data.length === 0">暂无相关数据</div>
                        <div class="item" v-ripple v-for="v in videoCompanies.data" :class="{cur: videoCompanies.selectedIds.indexOf(v.id) >= 0}" @click="filterByVideoCompany(v)">
                            <div class="thumb"><img :src="v.thumb ? v.thumb : TopContext.res.notFound" class="image" alt=""></div>
                            <div class="name">{{ v.name }}</div>
                        </div>
                    </div>
                    <div class="pager">
                        <my-page :total="videoCompanies.total" :limit="videoCompanies.size" :page="videoCompanies.page" @on-change="toPageInVideoCompany"></my-page>
                    </div>
                </div>
            </div>

        </div>

        <!-- 标签选择器 -->
        <div class="tag-selector hide" ref="tag-selector" @click="closeTagSelector">

            <div class="inner" @click.stop>
                <div class="title">
                    <div class="close" @click.stop="closeTagSelector">
                        <button class="close-btn"><i class="run-iconfont run-iconfont-guanbi"></i></button>
                    </div>
                    <div class="text">标签列表</div>
                    <div class="operation" @click="resetTagFilter" v-ripple>重置</div>
                </div>
                <div class="content">
                    <!-- 当前选中的标签 -->
                    <div class="line" v-if="tags.selected.length > 0">
                        <div class="title m-b-15 f-14 weight">当前选择标签</div>
                        <div class="run-tags horizontal">
                            <my-button class="tag" v-for="v in tags.selected" :key="v.id" @click="delTag(v.tag_id)">{{ v.name }}</my-button>
                        </div>
                    </div>
                    <div class="line mode-swith">
                        <div class="left">
                            <p class="title m-b-15 f-14 weight">宽松匹配</p>
                            <p class="desc f-12">
                                <template v-if="tags.mode === 'strict'">严格匹配所有选中标签才认为满足要求</template>
                                <template v-if="tags.mode === 'loose'">只要匹配中其中单个标签即认为满足要求</template>
                            </p>
                        </div>
                        <div class="right">
                            <my-switch v-model="tags.mode" trueValue="loose" falseValue="strict" @on-change="filterModeChangeEvent"></my-switch>
                        </div>
                    </div>
                    <div class="line tags">
                        <div class="title f-14 weight">请选择过滤标签</div>
                        <div class="search">
                            <div class="inner">
                                <div class="ico"><my-icon icon="search" /></div>
                                <div class="input"><input type="text" class="form-text" v-model="tags.value" @keyup.enter="searchTag" placeholder="请搜索标签"></div>
                            </div>
                        </div>
                        <div class="list run-tags horizontal" :class="{loading: val.pending.getTags}">
                            <div class="mask" v-if="val.pending.getTags"><my-loading></my-loading></div>
                            <div class="empty" v-if="!val.pending.getTags && tags.total <= 0">暂无相关记录</div>
                            <my-button class="tag" v-for="v in tags.data" :class="{selected: tags.selectedIds.indexOf(v.tag_id) >= 0}" :key="v.id" @click="filterByTag(v)">{{ v.name }}</my-button>
                        </div>
                        <div class="pager" v-if="tags.total > 0">
                            <my-page :total="tags.total" :limit="tags.size" :page="tags.page" @on-change="toPageInTag"></my-page>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</template>

<script src="./js/search.js"></script>

<style scoped src="../public/css/base.css"></style>
<style scoped src="./css/search.css"></style>
<style scoped>

</style>
