import Form from '../form.vue';

const current = {id: 0};

const search = {
    size: TopContext.size ,
    user_id: '' ,
    module_id: '' ,
    category_id: '' ,
    video_project_id: '' ,
    status: '' ,
};

export default {
    name: "index",

    components: {
        'my-form': Form ,
    } ,

    data () {
        return {
            dom: {},
            ins: {},
            myValue: {
                pending: {},
                error: {},
                mode: '',
                simpleVideoPreview: '' ,
            },
            table: {
                field: [
                    {
                        type: 'selection',
                        width: TopContext.table.checkbox,
                        align: TopContext.table.alignCenter,
                        fixed: 'left',
                    },
                    {
                        title: 'id',
                        key: 'id',
                        minWidth: TopContext.table.id,
                        align: TopContext.table.alignCenter,
                        fixed: 'left',
                        sortable: 'custom' ,
                    },
                    {
                        title: '名称',
                        key: 'name',
                        width: 250,
                        align: TopContext.table.alignCenter,
                        fixed: 'left',
                        resizable: true,
                        ellipsis: true,
                        // tooltip: true,
                    },
                    {
                        title: '视频索引',
                        key: 'index',
                        minWidth: TopContext.table.number,
                        align: TopContext.table.alignCenter,
                        sortable: 'custom' ,
                    },
                    {
                        title: '封面',
                        slot: 'thumb',
                        minWidth: TopContext.table.name,
                        align: TopContext.table.alignCenter,
                    },
                    {
                        title: '程序截取封面',
                        slot: 'thumb_for_program',
                        minWidth: TopContext.table.name,
                        align: TopContext.table.alignCenter,
                    },
                    {
                        title: '简略预览',
                        slot: 'simple_preview',
                        minWidth: TopContext.table.image,
                        align: TopContext.table.alignCenter,
                    },
                    {
                        title: '完整预览',
                        slot: 'preview',
                        minWidth: TopContext.table.image,
                        align: TopContext.table.alignCenter,
                    },
                    {
                        title: '时长',
                        key: '__duration__',
                        minWidth: TopContext.table.number,
                        align: TopContext.table.alignCenter,
                        sortable: 'custom' ,
                    },
                    {
                        title: '用户【id】',
                        slot: 'user_id',
                        minWidth: TopContext.table.name,
                        align: TopContext.table.alignCenter
                    },
                    {
                        title: '模块【id】',
                        slot: 'module_id',
                        minWidth: TopContext.table.name,
                        align: TopContext.table.alignCenter
                    },
                    {
                        title: '分类【id】',
                        slot: 'category_id',
                        minWidth: TopContext.table.name,
                        align: TopContext.table.alignCenter
                    },
                    {
                        title: '类型',
                        key: '__type__',
                        minWidth: TopContext.table.type,
                        align: TopContext.table.alignCenter
                    },
                    {
                        title: '关联主体【id】',
                        slot: 'video_project_id',
                        minWidth: TopContext.table.name,
                        align: TopContext.table.alignCenter
                    },
                    {
                        title: '视频处理状态',
                        slot: 'video_process_status',
                        minWidth: TopContext.table.status + 30,
                        align: TopContext.table.alignCenter,
                        fixed: 'right',
                    },
                    {
                        title: '文件处理状态',
                        slot: 'file_process_status',
                        minWidth: TopContext.table.status + 30 ,
                        align: TopContext.table.alignCenter,
                        fixed: 'right',
                    },
                    {
                        title: '审核状态',
                        slot: 'status',
                        minWidth: TopContext.table.status,
                        align: TopContext.table.alignCenter,
                        fixed: 'right',
                    },
                    {
                        title: '失败原因',
                        key: 'fail_reason',
                        minWidth: TopContext.table.desc,
                        align: TopContext.table.alignCenter
                    },
                    {
                        title: '浏览次数',
                        key: 'view_count',
                        minWidth: TopContext.table.number,
                        align: TopContext.table.alignCenter
                    },
                    {
                        title: '播放次数',
                        key: 'play_count',
                        minWidth: TopContext.table.number,
                        align: TopContext.table.alignCenter
                    },
                    {
                        title: '获赞次数',
                        key: 'praise_count',
                        minWidth: TopContext.table.number,
                        align: TopContext.table.alignCenter
                    },

                    {
                        title: '反对次数',
                        key: 'against_count',
                        minWidth: TopContext.table.number,
                        align: TopContext.table.alignCenter
                    },
                    {
                        title: '描述',
                        key: 'description',
                        minWidth: TopContext.table.desc,
                        align: TopContext.table.alignCenter,
                        resizable: true,
                        ellipsis: true,
                        tooltip: true,
                    },
                    {
                        title: '权重',
                        key: 'weight',
                        minWidth: TopContext.table.weight,
                        align: TopContext.table.alignCenter,
                    },
                    {
                        title: '创建时间',
                        key: 'created_at',
                        minWidth: TopContext.table.time,
                        align: TopContext.table.alignCenter,
                    },
                    {
                        title: '操作' ,
                        slot: 'action' ,
                        minWidth: TopContext.table.action - 100 ,
                        align: TopContext.table.alignCenter ,
                        fixed: 'right' ,
                    } ,
                ],
                total: 0,
                page: 1,
                size: 0,
                sizes: TopContext.sizes,
                data: [],
            },

            // 搜索
            search: G.copy(search),

            // 模块
            modules: [],

            // 分类
            categories: [],

            current: G.copy(current) ,

            // 当前选中项
            selection: [] ,
        };
    } ,

    mounted () {
        this.initDom();
        this.initIns();
        this.getData();
        this.getModules();
    } ,

    computed: {
        showBatchBtn () {
            return this.selection.length > 0;
        } ,
    } ,

    methods: {

        initDom () {

        } ,



        initIns () {

        } ,

        getModules () {
            this.pending('getModules' , true);
            Api.module
                .all()
                .then((res) => {
                    if (res.code !== TopContext.code.Success) {
                        this.errorHandle(res.message);
                        return ;
                    }
                    this.modules = res.data;
                })
                .finally(() => {
                    this.pending('getModules' , false);
                });
        } ,

        getCategories (moduleId) {
            this.search.category_id = '';
            this.categories         = [];
            if (!G.isNumeric(moduleId)) {
                return ;
            }
            this.pending('getCategories' , true);
            Api.category
                .search({
                    module_id: moduleId
                })
                .then((res) => {
                    if (res.code !== TopContext.code.Success) {
                        this.errorHandle(res.message);
                        return ;
                    }
                    this.categories = res.data;
                })
                .finally(() => {
                    this.pending('getCategories' , false);
                });
        } ,

        getData () {
            this.pending('getData' , true);
            Api.video.index(this.search)
                .then((res) => {
                    if (res.code !== TopContext.code.Success) {
                        this.errorHandle(res.message);
                        return ;
                    }
                    const data = res.data;
                    this.table.total = data.total;
                    this.table.page = data.current_page;
                    this.table.size = data.per_page;
                    this.table.data = data.data;
                })
                .finally(() => {
                    this.pending('getData' , false);
                });
        } ,

        destroy (id , callback) {
            this.destroyAll([id] , callback);
        } ,

        destroyAll (ids , callback) {
            if (ids.length < 1) {
                this.message('warning' ,'请选择待删除的项');
                G.invoke(callback , this , false);
                return ;
            }
            const self = this;
            this.confirmModal('你确定删除吗？'  , (res) => {
                if (res) {
                    Api.video
                        .destroyAll(ids)
                        .then((res) => {
                            if (res.code !== TopContext.code.Success) {
                                G.invoke(callback , this , false);
                                this.errorHandle(res.message);
                                return ;
                            }
                            G.invoke(callback , this , true);
                            this.successHandle();
                            this.getData();
                        })
                        .finally(() => {

                        });
                    return ;
                }
                G.invoke(callback , this , false);
            });
        } ,

        destroyEvent (index , record) {
            const pendingKey = 'delete_' + record.id;
            this.pending(pendingKey , true);
            this.destroy(record.id , () => {
                this.pending(pendingKey , false);

            });
        } ,

        destroyAllEvent () {
            this.pending('destroyAll' , true);
            const ids = this.selection.map((v) => {
                return v.id;
            });
            this.destroyAll(ids , (success) => {
                this.pending('destroyAll' , false);
                if (success) {
                    this.selection = [];
                }
            });
        } ,

        // 重新播放视频
        restartPlayVideo: function(row){
            // const tar = e.currentTarget;
            // tar.currentTime = 0;
            // tar.play();

            // 视频简略预览
            this.myValue.simpleVideoPreview = row.simple_preview;
            // 视频播放
            this.$refs['video-preview'].show();
        } ,

        // 批量：事件：重新处理视频
        retryProcessVideosEvent () {
            const ids = this.selection.map((v) => {
                return v.id;
            });
            this.retryProcessVideos(ids);
        } ,

        // 单个：事件：重新处理视频
        retryProcessVideoEvent (record) {
            const pending = 'retry_' + record.id;
            this.pending(pending , true);
            this.retryProcessVideo(record.id , () => {
                this.pending(pending , false);
            });
        } ,

        // 单个：重新处理视频
        retryProcessVideo (id , callback) {
            this.retryProcessVideos([id] , callback)
        } ,

        // 批量：重新处理视频
        retryProcessVideos (ids , callback) {
            this.pending('retryProcessVideos' , true);
            Api.video
                .retryProcessVideo(ids)
                .then((res) => {
                    if (res.code !== TopContext.code.Success) {
                        G.invoke(callback , null , false);
                        this.errorHandle(res.message);
                        return ;
                    }
                    this.getData();
                    G.invoke(callback , null , true);
                })
                .finally(() => {
                    this.pending('retryProcessVideos' , false);
                });
        } ,

        selectionChangeEvent (selection) {
            this.selection = selection;
        } ,

        searchEvent () {
            this.search.page = 1;
            this.getData();
        } ,

        resetEvent () {
            this.search = G.copy(search);
            this.getData();
        } ,

        pageEvent (page , size) {
            this.search.page = page;
            this.search.size = size;
            this.getData();
        } ,

        sizeEvent (size , page) {
            this.search.page = page;
            this.search.size = size;
            this.getData();
        } ,

        sortChangeEvent (data) {
            if (data.order === TopContext.sort.none) {
                this.search.order = '';
            } else {
                this.search.order = this.generateOrderString(data.key , data.order);
            }
            this.table.page = 1;
            this.getData();
        } ,

        isOnlyOneSelection () {
            return this.selection.length === 1;
        } ,

        isEmptySelection () {
            return this.selection.length === 0;
        } ,

        hasSelection () {
            return this.selection.length > 0;
        } ,

        getFirstSelection () {
            return this.selection[0];
        } ,

        checkOneSelection () {
            if (!this.hasSelection()) {
                this.errorHandle('请选择项');
                return false;
            }
            if (!this.isOnlyOneSelection()) {
                this.errorHandle('请仅选择一项');
                return false;
            }
            return true;
        } ,

        edit (record) {
            this.current = record;
            this.setValue('mode' , 'edit');
            this.$nextTick(() => {
                this.$refs.form.openFormModal();
            });
        } ,

        editEvent (record) {
            this.edit(record);
        } ,

        editEventByButton () {
            if (!this.checkOneSelection()) {
                return ;
            }
            const current = this.getFirstSelection();
            this.edit(current);
        } ,

        addEvent () {
            this.setValue('mode' , 'add');
            this.$nextTick(() => {
                this.$refs.form.openFormModal();
            });
        } ,

        rowClickEvent (row , index) {
            this.$refs.table.toggleSelect(index);
        } ,

        rowDblclickEvent (row , index) {
            this.editEvent(row);
        } ,

        openVideoSelector () {
            if (this.search.module_id <= 0) {
                this.errorHandle('请先选择模块');
                return ;
            }
            this.$refs['video-project-selector'].show();
        } ,

        videoProjectChangedEvent (row) {
            this.search.video_project_id = row.id;
            this.searchEvent();
        } ,

        linkToShowForVideoProjectAtWeb (row) {
            const settings = this.state().settings;
            let url = settings.web_url + settings.show_for_video_project_at_web;
            url = url.replace('{id}' , row.video_project_id);
            this.openWindow(url , '_blank');
        } ,

        linkToShowForVideoAtWeb (row) {
            const settings = this.state().settings;
            let url = settings.web_url + settings.show_for_video_at_web;
            url = url.replace('{id}' , row.id);
            this.openWindow(url , '_blank');
        } ,
    } ,
}
