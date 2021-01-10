const form = {
    type: 'misc' ,
    user_id: '' ,
    module_id: '' ,
    category_id: '' ,
    video_project_id: '' ,
    view_count: 0  ,
    play_count: 0  ,
    praise_count: 0 ,
    against_count: 0 ,
    weight: 0 ,
    status: 1 ,
    merge_video_subtitle: 0 ,
    index: 1 ,
};

// 所属用户
const owner         = {id: 0 , username: 'unknow'};
// 所属专题
const videoProject  = {id: 0 , name: 'unknow'};

// 字幕
// const subtitle = {
//     id: 1 ,
//     name: '' ,
//     src: '' ,
//     delete_1: false ,
// };

const videos = {
    field: [
        {
            type: 'selection' ,
            width: TopContext.table.checkbox ,
            center: TopContext.table.alignCenter ,
            fixed: 'left' ,
        } ,
        {
            title: 'id' ,
            key: 'id' ,
            minWidth: TopContext.table.id ,
            center: TopContext.table.alignCenter ,
            fixed: 'left' ,
        } ,
        {
            title: '清晰度' ,
            key: 'definition' ,
            minWidth: TopContext.table.name ,
            center: TopContext.table.alignCenter ,
        } ,
        {
            title: '视频' ,
            slot: 'src' ,
            minWidth: TopContext.table.video ,
        } ,
        {
            title: '操作' ,
            slot: 'action' ,
            minWidth: TopContext.table.action ,
            fixed: 'right' ,
        } ,
    ] ,
    data: [] ,
};

const videoSubtitles = {
    field: [
        {
            type: 'selection' ,
            width: TopContext.table.checkbox ,
            center: TopContext.table.alignCenter ,
            fixed: 'left' ,
        } ,
        {
            title: 'id' ,
            key: 'id' ,
            minWidth: TopContext.table.id ,
            center: TopContext.table.alignCenter ,
            fixed: 'left' ,
        } ,
        {
            title: '名称' ,
            key: 'name' ,
            minWidth: TopContext.table.name ,
            center: TopContext.table.alignCenter ,
        } ,
        {
            title: '字幕源' ,
            minWidth: TopContext.table.src ,
            key: 'src' ,
        } ,
        {
            title: '操作' ,
            slot: 'action' ,
            minWidth: TopContext.table.action ,
            fixed: 'right' ,
        } ,
    ] ,
    data: [] ,
};

export default {

    computed: {
        title () {
            return this.mode === 'edit' ? '编辑' : '添加';
        } ,
    } ,

    data () {
        return {
            myValue: {
                tab: 'base' ,
                pending: {} ,
                error: {} ,
                show: false ,
            } ,

            owner: G.copy(owner) ,

            // 关联主体
            videoProject: G.copy(videoProject , true),

            // 标签
            tags: [] ,

            // 标签
            dom: {} ,

            // 实例
            ins: {} ,

            uVideoSubtitles: [] ,

            videos: G.copy(videos) ,

            videoSubtitles: G.copy(videoSubtitles) ,

            images: [] ,

            categories: [] ,

            modules: [] ,

            createTime: '' ,

            form: G.copy(form) ,

            videoSelection: [] ,

            videoSubtitleSelection: [] ,
        };
    } ,
    props: {
        id: {
            type: Number ,
            default: 0 ,
        } ,
        mode: {
            type: String ,
            default: '' ,
        } ,
    } ,

    mounted () {
        this.initDom();
        this.initIns();

    } ,

    methods: {

        genVideoSubtitle () {
            const id = G.randomArray(6 , 'letter' , true);
            return {
                id ,
                name: '' ,
                src: '' ,
                delete: false ,
                error: '' ,
                uploaded: false ,
                uploading: false ,
            };
        } ,

        getCategories (moduleId , callback) {
            this.pending('getCategories' , true);
            Api.category
                .search({
                    module_id: this.form.module_id ,
                    type: this.form.type === 'pro' ? 'video_project' : 'video' ,
                })
                .then((res) => {
                    if (res.code !== TopContext.code.Success) {
                        this.errorHandle(res.message);
                        G.invoke(callback , null , false);
                        return ;
                    }
                    this.categories = res.data;
                    this.$nextTick(() => {
                        G.invoke(callback , null , true);
                    });
                })
                .finally(() => {
                    this.pending('getCategories' , false);
                });
        } ,

        getModules (callback) {
            this.pending('getModules' , true);
            Api.module
                .all()
                .then((res) => {
                    if (res.code !== TopContext.code.Success) {
                        this.errorHandle(res.message);
                        G.invoke(callback , null , false);
                        return ;
                    }
                    this.modules = res.data;
                    this.$nextTick(() => {
                        G.invoke(callback , null , true);
                    });
                })
                .finally(() => {
                    this.pending('getModules' , false);
                });
        } ,


        moduleChangedEvent (moduleId) {
            this.error({module_id: ''} , false);
            this.form.category_id = '';
            this.form.video_project_id = '';
            this.videoProject = G.copy(videoProject);
            this.getCategories(moduleId);
        } ,

        typeChangedEvent (type) {
            if (type === 'misc') {
                // 杂项
                this.videoProject  = G.copy(videoProject);
                this.form.video_project_id  = '';
            } else {
                // 专题
                this.form.category_id = 0;
            }
        } ,

        initDom () {
            this.dom.tagInput = G(this.$refs['tag-input']);
            this.dom.tagInputOuter = G(this.$refs['tag-input-outer']);
            this.dom.thumb = G(this.$refs.thumb);
            this.dom.video = G(this.$refs.video);
        } ,

        initIns () {
            const self = this;
            this.ins.thumb = new Uploader(this.dom.thumb.get(0) , {
                api: this.thumbApi(),
                mode: 'override' ,
                clear: true ,
                uploaded (file , data , code) {
                    if (code !== TopContext.code.Success) {
                        this.status(file.id , false);
                        return ;
                    }
                    this.status(file.id , true);
                    self.form.thumb = data.data;
                } ,
                cleared () {
                    self.form.thumb = '';
                } ,
            });

            this.ins.video = new Uploader(this.dom.video.get(0) , {
                api: this.videoApi() ,
                mode: 'override' ,
                multiple: false ,
                clear: true ,
                uploaded (file , data , code) {
                    if (code !== TopContext.code.Success) {
                        this.status(file.id , false);
                        return ;
                    }
                    this.status(file.id , true);
                    self.form.src = data.data;
                } ,
                cleared () {
                    self.form.src = '';
                } ,
            });
        } ,

        // 获取当前编辑记录详情
        findById (id) {
            this.pending('findById' , true);
            return new Promise((resolve , reject) => {
                Api.video
                    .show(id)
                    .then((res) => {
                        if (res.code !== TopContext.code.Success) {
                            this.errorHandle(res.message);
                            reject();
                            return ;
                        }
                        this.form = res.data;
                        resolve();
                    })
                    .finally(() => {
                        this.pending('findById' , false);
                    });
            });
        } ,

        openFormModal () {
            this.myValue.show = true;
            this.getModules();
            // console.log(this.mode , '编辑模式');
            if (this.mode === 'edit') {
                this.findById(this.id).then((res) => {
                    this.ins.thumb.render(this.form.thumb);

                    this.owner                  = this.form.user ? this.form.user : G.copy(owner);
                    this.videoProject           = this.form.video_project ? this.form.video_project : G.copy(videoProject);
                    this.videos.data            = this.form.videos;
                    this.videoSubtitles.data    = this.form.video_subtitles;

                    this.getCategories(this.form.module_id);
                });
            }
        } ,

        closeFormModal () {
            if (this.pending('submitEvent')) {
                this.message('warning' , '请求中...请耐心等待');
                return ;
            }
            this.myValue.show = false;
            this.myValue.tab = 'base';

            this.ins.thumb.clearAll();
            this.ins.video.clearAll();

            this.images             = [];
            this.tags               = [];
            this.uVideoSubtitles    = [];
            this.owner              = G.copy(owner);
            this.videoProject       = G.copy(videoProject);
            this.videos             = G.copy(videos);
            this.videoSubtitles     = G.copy(videoSubtitles);
            this.form               = G.copy(form);
            this.error();
        } ,


        videoSelectionChangeEvent (selection) {
            this.videoSelection = selection;
        } ,

        destroyVideoEvent (index , record) {
            const pendingKey = 'delete_' + record.id;
            this.pending(pendingKey , true);
            this.destroyVideo(record.id , () => {
                this.pending(pendingKey , false);

            });
        } ,

        destroyVideosEvent () {
            this.pending('destroyVideos' , true);
            const ids = this.videoSelection.map((v) => {
                return v.id;
            });
            this.destroyVideos(ids , (success) => {
                this.pending('destroyVideos' , false);
            });
        } ,

        destroyVideo (id , callback) {
            this.destroyVideos([id] , callback);
        } ,

        destroyVideos (ids , callback) {
            if (ids.length < 1) {
                this.message('warning' ,'请选择待删除的项');
                G.invoke(callback , this , false);
                return ;
            }
            const self = this;
            this.confirmModal('你确定删除吗？'  , (res) => {
                if (res) {
                    Api.video
                        .destroyVideos(ids)
                        .then((res) => {
                            if (res.code !== TopContext.code.Success) {
                                G.invoke(callback , this , false);
                                this.errorHandle(res.message);
                                return ;
                            }
                            G.invoke(callback , this , true);
                            this.message('success' , '操作成功');
                            for (let i = 0; i < this.videos.data.length; ++i)
                            {
                                const cur = this.videos.data[i];
                                if (G.contain(cur.id , ids)) {
                                    this.videos.data.splice(i , 1);
                                    i--;
                                }
                            }
                        })
                        .finally(() => {

                        });
                    return ;
                }
                G.invoke(callback , this , false);
            });
        } ,


        videoSubtitleSelectionChangeEvent (selection) {
            this.videoSubtitleSelection = selection;
        } ,

        destroyVideoSubtitle (id , callback) {
            this.destroyVideoSubtitles([id] , callback);
        } ,

        destroyVideoSubtitles (ids , callback) {
            if (ids.length < 1) {
                this.message('warning' ,'请选择待删除的项');
                G.invoke(callback , this , false);
                return ;
            }
            const self = this;
            this.confirmModal('你确定删除吗？'  , (res) => {
                if (res) {
                    Api.videoSubtitle
                        .destroyAll(ids)
                        .then((res) => {
                            if (res.code !== TopContext.code.Success) {
                                G.invoke(callback , this , false);
                                this.errorHandle(res.message);
                                return ;
                            }
                            G.invoke(callback , this , true);
                            this.message('success' , '操作成功');
                            for (let i = 0; i < this.videoSubtitles.data.length; ++i)
                            {
                                const cur = this.videoSubtitles.data[i];
                                if (G.contain(cur.id , ids)) {
                                    this.videoSubtitles.data.splice(i , 1);
                                    i--;
                                }
                            }
                        });
                    return ;
                }
                G.invoke(callback , this , false);
            });
        } ,

        destroyVideoSubtitleEvent (index , record) {
            const pendingKey = 'delete_' + record.id;
            this.pending(pendingKey , true);
            this.destroyVideoSubtitle(record.id , () => {
                this.pending(pendingKey , false);
            });
        } ,

        destroyVideoSubtitlesEvent () {
            this.pending('destroyVideoSubtitles' , true);
            const ids = this.videoSubtitleSelection.map((v) => {
                return v.id;
            });
            this.destroyVideoSubtitles(ids , () => {
                this.pending('destroyVideoSubtitles' , false);
            });
        } ,

        addVideoSubtitleEvent () {
            const videoSubtitle = this.genVideoSubtitle();
            this.uVideoSubtitles.push(videoSubtitle);
        } ,

        videoSubtitleChangeEvent (e , record) {
            const tar   = e.currentTarget;
            const files = tar.files;
            if (files.length < 1) {
                return ;
            }
            record.file = files[0];
            this.uploadVideoSubtitle(record , () => {
                record.uploading = false;
            });
        } ,

        uploadVideoSubtitle (videoSubtitle , callback) {
            if (videoSubtitle.uploaded) {
                // 过滤掉那些上传成功的数据
                return ;
            }
            videoSubtitle.uploaded = false;
            videoSubtitle.uploading = true;
            videoSubtitle.error = '';
            Api.file
                .uploadSubtitle(videoSubtitle.file)
                .then((res) => {
                    if (res.code !== TopContext.code.Success) {
                        videoSubtitle.error = res.message;
                        videoSubtitle.uploaded = false;
                        G.invoke(callback , null , false);
                        return ;
                    }
                    videoSubtitle.uploaded = true;
                    videoSubtitle.src = res.data;
                    G.invoke(callback , null , true);
                })
                .finally(() => {

                });
        } ,

        filter (form) {
            const error = {};
            // if (G.isEmptyString(form.name)) {
            //     error.name = '请填写名称';
            // }
            if (!G.isNumeric(form.user_id)) {
                error.user_id = '请选择用户';
            }
            if (!G.isNumeric(form.module_id)) {
                error.module_id = '请选择模块';
            }
            if (form.type === 'pro' && !G.isNumeric(form.video_project_id)) {
                error.video_project_id = '请选择视频专题';
            }
            if (form.type === 'misc' && !G.isNumeric(form.category_id)) {
                error.category_id = '请选择分类';
            }
            return {
                status: G.isEmptyObject(error) ,
                error ,
            };
        } ,

        submitEvent () {
            if (this.pending('submitEvent')) {
                this.message('warning' , '请求中...请耐心等待');
                return ;
            }
            const form = G.copy(this.form);
            // 字幕上传
            form.video_subtitles = this.uVideoSubtitles.map((v) => {
                return {
                    name: v.name ,
                    src: v.src ,
                };
            });
            form.video_subtitles = G.jsonEncode(form.video_subtitles);
            const filterRes = this.filter(form);
            if (!filterRes.status) {
                this.error(filterRes.error , true);
                this.errorHandle(G.getObjectFirstKeyMappingValue(filterRes.error));
                return ;
            }
            const thenCallback = (res) => {
                this.error();
                if (res.code !== TopContext.code.Success) {
                    this.errorHandle(res.message);
                    return ;
                }
                this.successHandle((keep) => {
                    this.$emit('on-success');
                    if (!keep) {
                        this.closeFormModal();
                    }
                });
            };
            const finalCallback = () => {
                this.pending('submitEvent' , false);
            };
            this.pending('submitEvent' , true);
            if (this.mode === 'edit') {
                Api.video.update(form.id , form).then(thenCallback).finally(finalCallback);
                return ;
            }
            Api.video.store(form).then(thenCallback).finally(finalCallback);
        } ,

        changeEventTest (value) {
            console.log('i-select changed' , value , JSON.stringify(this.modules));
        } ,

        userChangeEvent (res) {
            this.error({user_id: ''} , false);
            this.form.user_id   = res.id;
            this.owner          = res;
        } ,

        showUserSelector () {
            this.$refs['user-selector'].show();
        } ,

        videoProjectChangeEvent (res) {
            this.error({video_project_id: ''} , false);
            this.form.video_project_id      = res.id;
            this.videoProject               = res;
        } ,

        showVideoProjectSelector () {
            this.$refs['video-project-selector'].show();
        } ,
    } ,

    watch: {

    } ,
};

