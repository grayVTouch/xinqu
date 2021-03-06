const form = {
    module_id: '' ,
    type: '' ,
    p_id: '' ,
    weight: 0 ,
    status: 1 ,
    is_enabled: 1 ,
};

const owner = {
    id: 0 ,
    username: 'unknow' ,
};

export default {
    name: "my-form" ,

    props: {
        id: {
            type: [Number , String] ,
            required: true ,
        } ,

        mode: {
            type: String ,
            default: 'add' ,
        } ,

        addMode: {
            type: String ,
            // add-添加 add_next-添加下级
            default: 'add' ,
        } ,
    } ,

    computed: {
        title () {
            return this.mode === 'edit' ? '编辑' : '添加';
        } ,
    } ,

    data () {
        return {
            form: G.copy(form) ,

            myValue: {
                show: false ,
                showUserSelector: false ,
                showModuleSelector: false ,
                showTypeSelector: false ,
            } ,

            dom: {} ,

            ins: {} ,

            owner: G.copy(owner) ,

            modules: [] ,

            // 分类
            categories: [] ,

        };
    } ,

    mounted () {
        this.initDom();
        this.initIns();
    } ,

    methods: {

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

        initDom () {

        } ,

        initIns () {

        } ,

        findById (id) {
            this.pending('findById' , true);
            return new Promise((resolve , reject) => {
               Api.category
                   .show(id)
                   .then((res) => {
                       if (res.code !== TopContext.code.Success) {
                           this.errorHandle(res.message);
                           reject();
                           return ;
                       }
                       resolve(res.data);
                   }).finally(() => {
                       this.pending('findById' , false);
                   });
            });
        } ,

        openFormModal () {
            this.getModules();
            if (this.mode === 'add') {
                // 添加
                if (this.addMode === 'add_next') {
                    this.handleForm();
                } else {
                    this.handleStep('module');
                }
            } else {
                this.handleForm();
            }
        } ,

        // 模块处理
        handleModuleStep () {
            this.step = 'module';
            this.myValue.showModuleSelector = true;
        } ,

        // 表单处理
        handleForm () {
            this.myValue.step = 'form';
            this.myValue.show = true;
            if (this.mode === 'edit') {
                this.findById(this.id)
                    .then((res) => {
                        this.form = res;

                        this.getCategories();

                        this.owner = this.form.user ? this.form.user : G.copy(owner);
                        // this.
                    });
            } else {
                if (this.addMode === 'add_next') {
                    this.findById(this.id)
                        .then((res) => {
                            this.form.type = res.type;
                            this.form.module_id = res.module_id;
                            this.form.p_id = res.id;

                            this.getCategories();
                        });
                }
            }
        } ,

        handleStep (step) {
            if (step === 'module') {
                this.form.module_id = '';
                this.form.type = '';
                this.myValue.showModuleSelector = true;
            } else if (step === 'type') {
                if (this.form.module_id  < 1) {
                    this.errorHandle('请选择模块');
                    return ;
                }
                this.myValue.showModuleSelector = false;
                this.myValue.showTypeSelector = true;
            } else if (step === 'form') {
                if (this.form.type  === '') {
                    this.errorHandle('请选择类型');
                    return ;
                }
                this.myValue.showTypeSelector = false;
                this.handleForm();
            } else {
                // 其他相关操作
            }
        } ,

        closeFormModal () {
            if (this.pending('submitEvent')) {
                this.message('warning' , '请求中...请耐心等待');
                return;
            }
            this.myValue.showUserSelector = false;
            this.myValue.showModuleSelector = false;
            this.myValue.show   = false;
            this.modules    = [];
            this.categories = [];
            this.form       = G.copy(form);
            this.owner      = G.copy(owner);
            this.error();
        } ,

        filter (form) {
            const error = {};
            if (G.isEmptyString(form.name)) {
                error.name = '请填写名称';
            }
            if (G.isEmptyString(form.type)) {
                error.type = '请填写类型';
            }
            if (!G.isNumeric(form.user_id)) {
                error.user_id = '请选择用户';
            }
            if (!G.isNumeric(form.module_id)) {
                error.module_id = '请选择模块';
            }
            if (!G.isNumeric(form.p_id)) {
                error.p_id = '请选择上级分类';
            }
            return {
                status: G.isEmptyObject(error) ,
                error ,
            };
        } ,

        submitEvent () {
            const self = this;
            const form = G.copy(this.form);
            const filterRes = this.filter(form);
            if (!filterRes.status) {
                this.error(filterRes.error , true);
                this.errorHandle(G.getObjectFirstKeyMappingValue(filterRes.error));
                return ;
            }
            const thenCallback = (res) => {
                if (res.code !== TopContext.code.Success) {
                    this.errorHandle(res.message);
                    return ;
                }
                this.successModal((keep) => {
                    this.$emit('on-success');
                    if (keep) {
                        return ;
                    }
                    self.closeFormModal();
                });
            };
            const finalCallback = () => {
                this.pending('submitEvent' , false);
                this.error();
            };
            this.pending('submitEvent' , true);
            if (this.mode === 'edit') {
                Api.category.update(form.id , form).then(thenCallback).finally(finalCallback);
                return ;
            }
            Api.category.store(form).then(thenCallback).finally(finalCallback);
        } ,

        userChangeEvent (res) {
            this.error({user_id: ''} , false);
            this.form.user_id   = res.id;
            this.owner          = res;
        } ,

        showUserSelector () {
            this.$refs['user-selector'].show();
        } ,

        getCategoryExcludeSelfAndChildrenByIdAndData (id , data) {
            const selfAndChildren = G.t.childrens(id , data , null , true , false);
            const selfAndChildrenIds = [];
            selfAndChildren.forEach((v) => {
                selfAndChildrenIds.push(v.id);
            });
            const res = [];
            data.forEach((v) => {
                if (G.contain(v.id , selfAndChildrenIds)) {
                    return ;
                }
                res.push(v);
            });
            return res;
        } ,

        getCategories () {
            if (this.form.module_id < 1) {
                return ;
            }
            if (G.isEmptyString(this.form.type)) {
                return ;
            }
            this.pending('getCategories' , true);
            Api.category
                .search({
                    module_id: this.form.module_id ,
                    type: this.form.type ,
                })
                .then((res) => {
                    if (res.code !== TopContext.code.Success) {
                        this.errorHandle(res.message);
                        return ;
                    }
                    this.categories = this.getCategoryExcludeSelfAndChildrenByIdAndData(this.form.id , res.data);
                })
                .finally(() => {
                    this.pending('getCategories' , false);
                });
        } ,

        moduleChangedEvent (moduleId) {
            this.myValue.error.module_id = '';
            if (!G.isNumeric(moduleId)) {
                return ;
            }
            this.form.p_id = '';
            this.getCategories();
        } ,

        typeChangedEvent () {
            this.myValue.error.type = '';
            this.getCategories();
        } ,


        findRecordById (id) {
            for (let i = 0; i < this.table.data.length; ++i)
            {
                const cur = this.table.data[i];
                if (cur.id == id) {
                    return cur;
                }
            }
            throw new Error('未找到 id 对应记录：' + id);
        } ,
    } ,
}
