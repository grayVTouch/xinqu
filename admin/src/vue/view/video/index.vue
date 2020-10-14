<template>
    <my-base ref="base">
        <div class="mask">

            <div class="line search">
                <div class="run-title">
                    <div class="left">筛选</div>
                    <div class="right"></div>
                </div>

                <div class="filter-option">
                    <form @submit.prevent="searchEvent">
                        <div class="option">
                            <div class="field">id：</div>
                            <div class="value"><input type="text" class="form-text" v-model="search.id"></div>
                        </div>

                        <div class="option">
                            <div class="field">名称：</div>
                            <div class="value"><input type="text" class="form-text" v-model="search.name"></div>
                        </div>

                        <div class="option">
                            <div class="field">用户id：</div>
                            <div class="value"><input type="text" class="form-text" v-model="search.user_id"></div>
                        </div>


                        <div class="option">
                            <div class="field">模块：</div>
                            <div class="value">
                                <my-select :data="modules" v-model="search.module_id" empty="" @change="getCategories"></my-select>
                                <my-loading v-if="val.pending.getModules"></my-loading>
                            </div>
                        </div>

                        <div class="option">
                            <div class="field">分类：</div>
                            <div class="value">
                                <my-deep-select :data="categories" v-model="search.category_id" :has="false" empty=""></my-deep-select>
                                <my-loading v-if="val.pending.getCategories"></my-loading>
                                <span class="msg">请选择模块后操作</span>
                            </div>
                        </div>

                        <div class="option">
                            <div class="field">类型：</div>
                            <div class="value">
                                <RadioGroup v-model="search.type">
                                    <Radio v-for="(v,k) in $store.state.context.business.video.type" :key="k" :label="k">{{ v }}</Radio>
                                </RadioGroup>
                            </div>
                        </div>

                        <div class="option">
                            <div class="field">视频专题：</div>
                            <div class="value">
                                <input type="number" class="form-text" v-model="search.video_project_id">
                            </div>
                        </div>

                        <div class="option">
                            <div class="field">状态：</div>
                            <div class="value">
                                <RadioGroup v-model="search.status">
                                    <Radio v-for="(v,k) in $store.state.context.business.video.status" :key="k" :label="parseInt(k)">{{ v }}</Radio>
                                </RadioGroup>
                            </div>
                        </div>

                        <div class="option">
                            <div class="field"></div>
                            <div class="value">
                                <button type="submit" v-show="false"></button>
                                <Button v-ripple type="primary" :loading="val.pending.getData" @click="searchEvent"><my-icon icon="search" mode="right" />搜索</Button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="line order">
                <div class="run-title">
                    <div class="left">排序</div>
                    <div class="right"></div>
                </div>

                <div class="filter-option">

                    <div class="option">
                        <div class="field">id：</div>
                        <div class="value">
                            <RadioGroup v-model="search.order">
                                <Radio label="id|asc">升序</Radio>
                                <Radio label="id|desc">降序</Radio>
                            </RadioGroup>
                        </div>
                    </div>

                    <div class="option">
                        <div class="field">权重：</div>
                        <div class="value">
                            <RadioGroup v-model="search.order">
                                <Radio label="weight|asc">升序</Radio>
                                <Radio label="weight|desc">降序</Radio>
                            </RadioGroup>
                        </div>
                    </div>

                    <div class="option">
                        <div class="field"></div>
                        <div class="value">
                            <Button v-ripple type="primary" :loading="val.pending.getData" @click="searchEvent"><my-icon icon="search" mode="right" />搜索</Button>
                        </div>
                    </div>

                </div>
            </div>

            <div class="line">
                <div class="run-action-title">
                    <div class="left">
                        <my-table-button @click="addEvent"><my-icon icon="add" />添加</my-table-button>
                        <my-table-button type="error" @click="destroyAllEvent" :loading="val.pending.destroyAll"><my-icon icon="shanchu" />删除选中项 <span v-if="val.selectedIds.length > 0">（{{ val.selectedIds.length }}）</span></my-table-button>
                        <my-table-button @click="retryProcessVideosEvent" :loading="val.pending.retryProcessVideos"><my-icon icon="reset" />重新处理 <span v-if="val.selectedIds.length > 0">（{{ val.selectedIds.length }}）</span></my-table-button>
                    </div>
                    <div class="right">
                        <my-page :total="table.total" :limit="table.limit" :page="table.page" @on-change="pageEvent"></my-page>
                    </div>
                </div>
            </div>

            <div class="line data">

                <div class="run-title">
                    <div class="left">数据列表</div>
                    <div class="right"></div>
                </div>

                <div class="table">

                    <Table border :height="$store.state.context.table.height"  :columns="table.field" :data="table.data" @on-selection-change="selectedEvent" :loading="val.pending.getData" @on-row-dblclick="editEvent">
                        <template v-slot:thumb="{row,index}">
                            <Poptip trigger="hover" placement="right" :transfer="true">
                                <img :src="row.thumb ? row.thumb : $store.state.context.res.notFound" :height="$store.state.context.table.imageH" class="image" @click="link(row.thumb)" alt="">
                                <div slot="content" class="table-preview-image-style">
                                    <img :src="row.thumb ? row.thumb : $store.state.context.res.notFound" class="image" @click="link(row.thumb)" alt="">
                                </div>
                            </Poptip>
                        </template>
                        <template v-slot:thumb_for_program="{row,index}">
                            <Poptip trigger="hover" placement="right" :transfer="true">
                                <img :src="row.thumb_for_program ? row.thumb_for_program : $store.state.context.res.notFound" :height="$store.state.context.table.imageH" class="image" @click="link(row.thumb_for_program)" alt="">
                                <div slot="content" class="table-preview-image-style">
                                    <img :src="row.thumb_for_program ? row.thumb_for_program : $store.state.context.res.notFound" class="image" @click="link(row.thumb_for_program)" alt="">
                                </div>
                            </Poptip>
                        </template>
                        <template v-slot:simple_preview="{row,index}">
                            <Tooltip content="点击播放" placement="right" :transfer="true">
                                <video :src="row.simple_preview" @click="restartPlayVideo" :height="$store.state.context.table.imageH"></video>
                            </Tooltip>
                        </template>
                        <template v-slot:preview="{row,index}">

                            <Poptip trigger="hover" placement="right" :transfer="true">
                                <Button @click="link(row.preview)">悬浮查看</Button>
                                <div slot="content" class="table-preview-image-style">
                                    <img :src="row.preview ? row.preview : $store.state.context.res.notFound" class="image" @click="link(row.preview)" alt="">
                                </div>
                            </Poptip>

                        </template>
                        <template v-slot:user_id="{row,index}">
                            {{ row.user ? `${row.user.username}【${row.user.id}】` : `unknow【${row.user_id}】` }}
                        </template>
                        <template v-slot:module_id="{row,index}">
                            {{ row.module ? `${row.module.name}【${row.module.id}】` : `unknow【${row.module_id}】` }}
                        </template>
                        <template v-slot:category_id="{row,index}">
                            {{ row.category ? `${row.category.name}【${row.category.id}】` : `unknow【${row.category_id}】` }}
                        </template>
                        <template v-slot:video_project_id="{row,index}">
                            {{ row.type === 'pro' ? (row.video_project ? `${row.video_project.name}【${row.video_project.id}】` : `unknow【${row.video_project_id}】`) : null }}
                        </template>

                        <template v-slot:status="{row,index}">
                            <b :class="{'run-red': row.status === -1 , 'run-gray': row.status === 0 , 'run-green': row.status === 1}">{{ row.__status__ }}</b>
                        </template>
                        <template v-slot:process_status="{row,index}">
                            <b :class="{'run-gray': row.process_status === -1 , 'run-red': row.process_status === 0 , 'run-green': row.process_status >= 1}">{{ row.__process_status__ }}</b>
                        </template>

                        <template v-slot:action="{row , index}">
                            <my-table-button @click="editEvent(row)"><my-icon icon="edit" />编辑</my-table-button>
                            <my-table-button @click="retryProcessVideoEvent(row)" :loading="val.pending['retry_' + row.id]" v-if="row.process_status === -1"><my-icon icon="reset" />重新处理</my-table-button>
                            <my-table-button type="error" :loading="val.pending['delete_' + row.id]" @click="destroyEvent(index , row)"><my-icon icon="shanchu" />删除</my-table-button>
                        </template>
                    </Table>

                </div>

            </div>

            <div class="line operation">
                <my-table-button @click="addEvent"><my-icon icon="add" />添加</my-table-button>
                <my-table-button type="error" @click="destroyAllEvent" :loading="val.pending.destroyAll"><my-icon icon="shanchu" />删除选中项 <span v-if="val.selectedIds.length > 0">（{{ val.selectedIds.length }}）</span></my-table-button>
                <my-table-button @click="retryProcessVideosEvent" :loading="val.pending.retryProcessVideos"><my-icon icon="reset" />重新处理 <span v-if="val.selectedIds.length > 0">（{{ val.selectedIds.length }}）</span></my-table-button>
            </div>

            <div class="line page">
<!--                <Page :total="table.total" :page-size="$store.state.context.limit" :current="table.page" :show-total="true" :show-sizer="false" :show-elevator="true"  @on-change="pageEvent" />-->
                <my-page :total="table.total" :limit="table.limit" :page="table.page" @on-change="pageEvent"></my-page>
            </div>

            <my-form
                    ref="form"
                    :data="form"
                    :mode="val.mode"
                    @on-success="getData"
            ></my-form>

        </div>
    </my-base>
</template>

<script src="./js/index.js"></script>

<style scoped>
    .mask {

    }

    .mask > .line {
        margin-bottom: 15px;
    }

    .mask > .line:nth-last-of-type(1) {
        margin-bottom: 0;
    }

    .mask > .data > .table {
        overflow: hidden;
        overflow-x: auto;
        width: 100%;
    }
</style>