import myForm from '../form.vue';

const current = {id: 0};

const search = {
    size: TopContext.size ,
};

export default {
    name: "index",

    data () {
        return {
            filter: {
                id: '' ,
            } ,
            dom: {} ,
            ins: {} ,
            myValue: {
                pending: {} ,
                modal: false ,
                error: {} ,
                mode: '' ,

            } ,
            table: {
                field: [
                    {
                        type: 'selection',
                        width: TopContext.table.checkbox ,
                        align: TopContext.table.alignCenter ,
                        fixed: 'left' ,
                    },
                    {
                        title: 'id' ,
                        key: 'id' ,
                        minWidth: TopContext.table.id ,
                        align: TopContext.table.alignCenter ,
                        fixed: 'left' ,
                        sortable: 'custom' ,
                    } ,
                    {
                        title: '名称' ,
                        key: 'name' ,
                        minWidth: TopContext.table.name ,
                        align: TopContext.table.alignCenter ,
                        fixed: 'left' ,
                    } ,
                    {
                        title: '模块【id】',
                        slot: 'module_id',
                        minWidth: TopContext.table.name ,
                        align: TopContext.table.alignCenter,
                        fixed: 'left' ,
                    } ,
                    {
                        title: '类型',
                        key: '__type__',
                        minWidth: TopContext.table.name ,
                        align: TopContext.table.alignCenter,
                    },
                    {
                        title: '用户',
                        slot: 'user_id',
                        minWidth: TopContext.table.name ,
                        align: TopContext.table.alignCenter,
                    },
                    {
                        title: '使用次数' ,
                        key: 'count' ,
                        minWidth: TopContext.table.number ,
                        align: TopContext.table.alignCenter ,
                    } ,
                    {
                        title: '状态',
                        slot: 'status',
                        minWidth: TopContext.table.status ,
                        align: TopContext.table.alignCenter,
                    },
                    {
                        title: '失败原因',
                        key: 'fail_reason',
                        minWidth: TopContext.table.name ,
                        align: TopContext.table.alignCenter,
                    },
                    {
                        title: '权重' ,
                        key: 'weight' ,
                        minWidth: TopContext.table.weight ,
                        align: TopContext.table.alignCenter ,
                    } ,
                    {
                        title: '创建时间' ,
                        key: 'created_at' ,
                        minWidth: TopContext.table.time ,
                        align: TopContext.table.alignCenter ,
                    } ,
                    // {
                    //     title: '操作' ,
                    //     slot: 'action' ,
                    //     minWidth: TopContext.table.action ,
                    //     align: TopContext.table.alignCenter ,
                    //     fixed: 'right' ,
                    // } ,
                ] ,
                total: 0 ,
                size: 0 ,
                sizes: TopContext.sizes ,
                page: 1 ,
                data: [] ,
            } ,

            search: G.copy(search) ,

            modules: [] ,

            selection: [] ,

            current: G.copy(current) ,
        };
    } ,

    mounted () {
        this.initDom();
        this.initIns();
        this.getModules()
            .then(() => {
                this.search.module_id = this.moduleId;
                this.getData();
            });
    } ,

    computed: {
        title () {
            return this.myValue.mode === 'edit' ? '编辑' : '添加';
        } ,

        showBatchBtn () {
            return this.selection.length > 0;
        } ,

        moduleId () {
            return this.modules.length > 0 ? this.modules[0].id : '';
        } ,
    } ,

    components: {
        'my-form': myForm
    } ,

    methods: {

        getModules () {
            return new Promise((resolve , reject) => {
                this.pending('getModules' , true);
                Api.module.all()
                    .then((res) => {
                        if (res.code !== TopContext.code.Success) {
                            this.errorHandle(res.message);
                            reject();
                            return ;
                        }
                        this.modules = res.data;
                        resolve();
                    })
                    .finally(() => {
                        this.pending('getModules' , false);
                    });
            });
        } ,

        initDom () {

        } ,

        initIns () {

        } ,

        getData () {
            this.pending('getData' , true);
            Api.tag
                .index(this.search)
                .then((res) => {
                    if (res.code !== TopContext.code.Success) {
                        this.errorHandle(res.message);
                        return ;
                    }
                    const data = res.data;
                    data.data.forEach((v) => {
                        this.pending(`delete_${v.id}` , false);
                    });
                    this.table.total = data.total;
                    this.table.size = data.per_page;
                    this.table.page = data.current_page;
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
                if (!res) {
                    G.invoke(callback , this , false);
                    return ;
                }
                Api.tag
                    .destroyAll(ids)
                    .then((res) => {
                        if (res.code !== TopContext.code.Success) {
                            G.invoke(callback , this , false);
                            this.errorHandle(res.message);
                            return ;
                        }
                        G.invoke(callback , this , true);
                        this.message('success' , '操作成功');
                        this.getData();
                    });
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

        selectionChangeEvent (selection) {
            this.selection = selection;
        } ,

        searchEvent () {
            this.search.page = 1;
            this.getData();
        } ,

        resetEvent () {
            this.search = G.copy(search);
            this.search.module_id = this.moduleId;

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
    } ,
}
