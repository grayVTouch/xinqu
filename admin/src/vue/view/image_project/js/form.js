const form = {
    type: 'misc' ,
    user_id: '' ,
    module_id: '' ,
    category_id: '' ,
    image_subject_id: '' ,
    view_count: 0  ,
    praise_count: 0 ,
    weight: 0 ,
    __tag__: [] ,
    status: 1 ,
    images: [] ,
    tags: [] ,
};

const owner = {
    id: 0 ,
    username: 'unknow' ,
};

const imageSubject = {
    id: 0 ,
    name: 'unknow' ,
};

const table = {
    field: [
        {
            type: 'selection' ,
            width: TopContext.table.checkbox ,
            center: TopContext.table.alignCenter ,
        } ,
        {
            title: 'id fuck' ,
            key: 'id' ,
            minWidth: TopContext.table.id ,
            center: TopContext.table.alignCenter ,
        } ,
        {
            title: '图片' ,
            slot: 'path' ,
            minWidth: TopContext.table.image ,
            center: TopContext.table.alignCenter ,
        } ,
        {
            title: '操作' ,
            minWidth: TopContext.table.action ,
            slot: 'action' ,
        } ,
    ] ,
    data: [] ,
};

export default {
    computed: {
        title () {
            return this.val.mode === 'edit' ? '编辑' : '添加';
        } ,
    } ,

    data () {
        return {
            val: {
                show: false ,
                showImageSubjectSelector: false ,
                tab: 'base' ,
            } ,

            // 用户
            owner: G.copy(owner),

            // 关联主体
            imageSubject: G.copy(imageSubject),

            // 标签
            tags: [] ,

            // 分类
            categories: [] ,

            // 模块
            modules: [] ,

            // 前 10 标签
            topTags: [] ,

            // 元素
            dom: {} ,

            // 实例
            ins: {} ,

            table: G.copy(table) ,

            images: [] ,

            form: G.copy(form) ,

            selection: [] ,
        };
    } ,

    props: {
        id: {
            type: Number ,
            default: 0 ,
        } ,
        mode: {
            type: String ,
            default: 'add' ,
        } ,
    } ,

    mounted () {
        this.initDom();
        this.initIns();

    } ,

    methods: {

        getCategories (moduleId , type , callback) {
            this.pending('getCategories' , true);
            Api.category
                .search({
                    module_id: moduleId ,
                    type: type === 'pro' ? 'image_project' : 'image' ,
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
                }).finally(() => {
                    this.pending('getCategories' , false);
                });
        } ,

        getModules (callback) {
            this.pending('getModules' , true);
            Api.module.all().then((res) => {
                this.pending('getModules' , false);
                if (res.code !== TopContext.code.Success) {
                    this.errorHandle(res.message);
                    G.invoke(callback , null , false);
                    return ;
                }
                this.modules = res.data;
                this.$nextTick(() => {
                    G.invoke(callback , null , true);
                });
            });
        } ,

        getTopTags (moduleId) {
            Api.tag
                .topByModuleId(moduleId)
                .then((res) => {
                    if (res.code !== TopContext.code.Success) {
                        this.errorHandle(res.message);
                        return ;
                    }
                    this.topTags = res.data;
                });
        } ,

        moduleChangedEvent (moduleId) {
            if (!G.isNumeric(moduleId)) {
                return ;
            }
            this.val.error.module_id = '';
            this.form.category_id = '';
            this.form.image_subject_id = '';
            this.form.image_subject_id = '';
            this.form.topTags = [];
            this.form.imageSubjects = G.copy(imageSubjects.current);
            this.getCategories(moduleId , this.form.type);
            this.getTopTags(moduleId);
        } ,

        typeChangedEvent (type) {
            if (type === 'misc') {
                this.form.imageSubjects = G.copy(imageSubjects.current);
                this.form.image_subject_id = '';
            }
        } ,

        initDom () {
            this.dom.tagInput = G(this.$refs['tag-input']);
            this.dom.tagInputOuter = G(this.$refs['tag-input-outer']);
            this.dom.thumb = G(this.$refs.thumb);
            this.dom.images = G(this.$refs.images);
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
                        console.log('图片上传失败' , data);
                        return ;
                    }
                    this.status(file.id , true);
                    self.form.thumb = data.data;
                } ,
                cleared () {
                    self.form.thumb = '';
                } ,
            });

            this.ins.images = new Uploader(this.dom.images.get(0) , {
                api: this.imageApi() ,
                mode: 'append' ,
                multiple: true ,
                clear: true ,
                uploaded (file , data , code) {
                    if (code !== TopContext.code.Success) {
                        this.status(file.id , false);
                        console.log('图片上传失败' , data);
                        return ;
                    }
                    this.status(file.id , true);
                    self.images.push(data.data);
                } ,
                cleared () {
                    self.images = [];
                    self.form.images = '';
                } ,
            });
        } ,

        userChangeEvent (user) {
            this.error({user_id: ''} , false);
            this.form.user_id   = user.id;
            this.owner          = user;
        } ,

        showUserSelector () {
            this.$refs['user-selector'].show();
        } ,

        imageSubjectChangeEvent (res) {
            this.error({image_subject_id: ''} , false);
            this.form.image_subject_id  = res.id;
            this.imageSubject           = res;
        } ,

        showImageSubjectSelector () {
            this.$refs['image-subject-selector'].show();
        } ,

        selectedTagEvent () {

        } ,

        // 获取当前编辑记录详情
        findById (id) {
            this.pending('findById' , true);
            return new Promise((resolve , reject) => {
                Api.imageProject.show(id)
                    .then((res) => {
                        if (res.code !== TopContext.code.Success) {
                            this.errorHandle(res.message);
                            reject();
                            return ;
                        }
                        this.form = res.data;
                        resolve();
                    }).finally(() => {
                        this.pending('findById' , false);
                    });
            });
        } ,

        openFormModal () {
            this.getModules();
            if (this.mode === 'edit') {
                this.findById(this.id).then((res) => {
                    this.getTopTags(this.form.module_id);
                    this.getCategories(this.form.module_id , this.form.type);

                    this.ins.thumb.render(this.form.thumb);
                    this.table.data             = this.form.images;
                    this.owner          = this.form.user ? this.form.user : G.copy(owner);
                    this.imageSubject   = this.form.image_subject ? this.form.image_subject: G.copy(imageSubject);
                });
            }
            this.val.show = true;
        } ,

        closeFormModal () {
            if (this.pending('submitEvent')) {
                this.message('warning' , '请求中...请耐心等待');
                return ;
            }
            // debugger
            this.val.show = false;
            this.ins.thumb.clearAll();
            this.ins.images.clearAll();
            this.form           = G.copy(form);
            this.images         = [];
            this.tags           = [];
            this.topTags        = [];
            this.modules        = [];
            this.categories     = [];
            this.imageSubject  = G.copy(imageSubject);
            this.owner          = G.copy(owner);
            this.table          = G.copy(table);
            this._val('tab' , 'base');
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
                    Api.imageProject
                        .destroyAllImageForImageSubject(ids)
                        .then((res) => {
                            if (res.code !== TopContext.code.Success) {
                                this.errorHandle(res.message);
                                return ;
                            }
                            G.invoke(callback , this , true);
                            this.message('success' , '操作成功');
                            for (let i = 0; i < this.table.data.length; ++i)
                            {
                                const cur = this.table.data[i];
                                if (G.contain(cur.id , ids)) {
                                    this.table.data.splice(i , 1);
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

        selectionChangeEvent (selection) {
            this.selection = selection;
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

        isExistTagByTagId (tagId) {
            for (let i = 0; i < this.form.tags.length; ++i)
            {
                const cur = this.form.tags[i];
                if (tagId === cur.tag_id) {
                    return true;
                }
            }
            for (let i = 0; i < this.tags.length; ++i)
            {
                const cur = this.tags[i];
                if (tagId === cur.id) {
                    return true;
                }
            }
            return false;
        } ,

        isExistTagByName (name) {
            const tags = this.form.tags.concat(this.tags);
            for (let i = 0; i < tags.length; ++i)
            {
                const cur = tags[i];
                if (name === cur.name) {
                    return true;
                }
            }
            return false;
        } ,

        appendTag (v) {
            if (this.isExistTagByTagId(v.id)) {
                this.message('error' , '标签已经存在');
                return ;
            }
            this.tags.push(v);
        } ,

        destroyTag (tagId , direct = true) {
            if (direct) {
                for (let i = 0; i < this.tags.length; ++i)
                {
                    const tag = this.tags[i];
                    if (tag.id === tagId) {
                        this.tags.splice(i , 1);
                        i--;
                    }
                }
                return ;
            }
            // 编辑模式
            const pendingKey = 'destroy_tag_' + tagId;
            this.pending(pendingKey , true);
            Api.imageProject
                .destroyTag({
                    image_project_id: this.form.id ,
                    tag_id: tagId ,
                })
                .then((res) => {
                    if (res.code !== TopContext.code.Success) {
                        this.errorHandle(res.message);
                        return ;
                    }
                    for (let i = 0; i < this.form.tags.length; ++i)
                    {
                        const tag = this.form.tags[i];
                        if (tag.tag_id === tagId) {
                            this.form.tags.splice(i , 1);
                            i--;
                        }
                    }
                }).finally(() => {
                    this.pending(pendingKey , false);
                })
        } ,

        createOrAppendTag () {
            this.val.error.tags = '';
            const name = this.dom.tagInput.text().replace(/\s/g , '');
            this.dom.tagInput.html(name);
            if (!G.isValid(name)) {
                this.message('error' , '请提供标签名称');
                return ;
            }
            if (this.isExistTagByName(name)) {
                this.message('error' , '标签已经存在');
                return ;
            }
            this.dom.tagInput.origin('blur');
            this.dom.tagInputOuter.addClass('disabled');
            Api.tag
                .findOrCreateTag({
                    name ,
                    module_id: this.form.module_id ,
                    user_id: this.form.user_id ,
                })
                .then((res) => {
                    this.dom.tagInputOuter.removeClass('disabled');
                    if (res.code !== TopContext.code.Success) {
                        this.errorHandle(res.message);
                        return ;
                    }
                    this.tags.push(res.data);
                    this.dom.tagInput.html('');
                });
        } ,

        submitEvent () {
            if (this.pending('submitEvent')) {
                this.message('warning' , '请求中...请耐心等待');
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
                    if (keep) {
                        return ;
                    }
                    this.closeFormModal();
                });
            };
            const finalCallback = () => {
                this.pending('submitEvent' , false);
            };
            const form = G.copy(this.form);
            form.images = G.jsonEncode(this.images);
            form.tags = this.tags.map((v) => {
                return v.id;
            });
            form.tags = G.jsonEncode(form.tags);
            this.pending('submitEvent' , true);
            if (this.mode === 'edit') {
                Api.imageProject.update(form.id , form).then(thenCallback).finally(finalCallback);
                return ;
            }
            Api.imageProject.store(form).then(thenCallback).finally(finalCallback);
        } ,

        rowClickEvent (row , index) {
            this.$refs.table.toggleSelect(index);
        } ,

    } ,
};

