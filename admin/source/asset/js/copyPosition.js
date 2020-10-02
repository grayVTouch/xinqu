/**
 * 侧边菜单功能
 */

let i                   = 1;
const pannelId          = i++;
const adminId           = i++;
const userId            = i++;
const imageSubjectId    = i++;
const videoId    = i++;
const tagId             = i++;
const categoryId        = i++;
const subjectId         = i++;
const moduleId          = i++;
const permissionId      = i++;
const systemId          = i++;



export default [
    {
        id: pannelId ,
        cn: '控制台' ,
        en: 'Pannel' ,
        // 路由路径
        path: '/pannel' ,
        hidden: false ,
        view: true ,
        parentId: 0 ,
        sIco: TopContext.resUrl + '/preset/ico/pannel.png' ,
        bIco: TopContext.resUrl + '/preset/ico/pannel.png' ,
        children: [] ,
    } ,
    {
        id: adminId ,
        cn: '后台用户' ,
        en: 'Admin' ,
        // 路由路径
        path: '/admin/index' ,
        hidden: false ,
        view: true ,
        parentId: 0 ,
        sIco: TopContext.resUrl + '/preset/ico/admin.png' ,
        bIco: TopContext.resUrl + '/preset/ico/admin.png' ,
        children: [] ,
    } ,
    {
        id: userId ,
        cn: '用户管理' ,
        en: 'User' ,
        // 路由路径
        path: '/user/index' ,
        hidden: false ,
        view: true ,
        parentId: 0 ,
        sIco: TopContext.resUrl + '/preset/ico/user.png' ,
        bIco: TopContext.resUrl + '/preset/ico/user.png' ,
        children: [] ,
    } ,
    {
        id: imageSubjectId ,
        cn: '图片专题' ,
        en: 'Image Subject' ,
        // 路由路径
        path: '/image_subject/index' ,
        hidden: false ,
        view: true ,
        parentId: 0 ,
        sIco: TopContext.resUrl + '/preset/ico/image.png' ,
        bIco: TopContext.resUrl + '/preset/ico/image.png' ,
        children: [] ,
    } ,
    {
        id: videoId ,
        cn: '视频管理' ,
        en: 'Video' ,
        // 路由路径
        path: '' ,
        hidden: false ,
        view: false ,
        parentId: 0 ,
        sIco: TopContext.resUrl + '/preset/ico/video.png' ,
        bIco: TopContext.resUrl + '/preset/ico/video.png' ,
        children: [
            {
                id: ++i ,
                cn: '视频系列' ,
                en: '' ,
                // 路由路径
                path: '/video_series/index' ,
                hidden: false ,
                view: true ,
                parentId: videoId ,
                sIco: '' ,
                bIco: '' ,
                children: [] ,
            } ,
            {
                id: ++i ,
                cn: '视频制作公司' ,
                en: '' ,
                // 路由路径
                path: '/video_company/index' ,
                hidden: false ,
                view: true ,
                parentId: videoId ,
                sIco: '' ,
                bIco: '' ,
                children: [] ,
            } ,
            {
                id: ++i ,
                cn: '视频专题' ,
                en: '' ,
                // 路由路径
                path: '/video_subject/index' ,
                hidden: false ,
                view: true ,
                parentId: videoId ,
                sIco: '' ,
                bIco: '' ,
                children: [] ,
            } ,
            {
                id: ++i ,
                cn: '视频列表' ,
                en: '' ,
                // 路由路径
                path: '/video/index' ,
                hidden: false ,
                view: true ,
                parentId: videoId ,
                sIco: '' ,
                bIco: '' ,
                children: [] ,
            } ,
        ] ,
    } ,
    {
        id: tagId ,
        cn: '个性标签' ,
        en: 'Tag' ,
        // 路由路径
        path: '/tag/index' ,
        hidden: false ,
        view: true ,
        parentId: 0 ,
        sIco: TopContext.resUrl + '/preset/ico/tag.png' ,
        bIco: TopContext.resUrl + '/preset/ico/tag.png' ,
        children: [] ,
    } ,
    {
        id: categoryId ,
        cn: '分类管理' ,
        en: 'Category' ,
        // 路由路径
        path: '/category/index' ,
        hidden: false ,
        view: true ,
        parentId: 0 ,
        sIco: TopContext.resUrl + '/preset/ico/category.png' ,
        bIco: TopContext.resUrl + '/preset/ico/catetory.png' ,
        children: [] ,
    } ,
    {
        id: subjectId ,
        cn: '关联主体' ,
        en: 'Subject' ,
        // 路由路径
        path: '/subject/index' ,
        hidden: false ,
        view: true ,
        parentId: 0 ,
        sIco: TopContext.resUrl + '/preset/ico/subject.png' ,
        bIco: TopContext.resUrl + '/preset/ico/subject.png' ,
        children: [] ,
    } ,
    {
        id: moduleId ,
        cn: '模块管理' ,
        en: 'Module' ,
        // 路由路径
        path: '/module/index' ,
        hidden: false ,
        view: true ,
        parentId: 0 ,
        sIco: TopContext.resUrl + '/preset/ico/module.png' ,
        bIco: TopContext.resUrl + '/preset/ico/module.png' ,
        children: [] ,
    } ,
    {
        id: permissionId ,
        cn: '权限管理' ,
        en: 'Permission' ,
        // 路由路径
        path: '' ,
        hidden: false ,
        view: false ,
        parentId: 0 ,
        sIco: TopContext.resUrl + '/preset/ico/permission.png' ,
        bIco: TopContext.resUrl + '/preset/ico/permission.png' ,
        children: [
            {
                id: ++i ,
                cn: '角色列表' ,
                en: '' ,
                // 路由路径
                path: '/role/index' ,
                hidden: false ,
                view: true ,
                parentId: permissionId ,
                sIco: '' ,
                bIco: '' ,
                children: [] ,
            } ,
            {
                id: ++i ,
                cn: '权限列表' ,
                en: '' ,
                // 路由路径
                path: '/role/index' ,
                hidden: false ,
                view: true ,
                parentId: permissionId ,
                sIco: '' ,
                bIco: '' ,
                children: [] ,
            } ,
        ] ,
    } ,
    {
        id: systemId ,
        cn: '系统管理' ,
        en: 'System' ,
        // 路由路径
        path: '' ,
        hidden: false ,
        view: false ,
        parentId: 0 ,
        sIco: TopContext.resUrl + '/preset/ico/system.png' ,
        bIco: TopContext.resUrl + '/preset/ico/system.png' ,
        children: [
            {
                id: ++i ,
                cn: '存储管理' ,
                en: '' ,
                // 路由路径
                path: '/disk/index' ,
                hidden: false ,
                view: true ,
                parentId: systemId ,
                sIco: '' ,
                bIco: '' ,
                children: [] ,
            } ,
            {
                id: ++i ,
                cn: '导航菜单' ,
                en: '' ,
                // 路由路径
                path: '/nav/index' ,
                hidden: false ,
                view: true ,
                parentId: systemId ,
                sIco: '' ,
                bIco: '' ,
                children: [] ,
            } ,
            {
                id: ++i ,
                cn: '定点位置' ,
                en: '' ,
                // 路由路径
                path: '/position/index' ,
                hidden: false ,
                view: true ,
                parentId: systemId ,
                sIco: '' ,
                bIco: '' ,
                children: [] ,
            } ,
            {
                id: ++i ,
                cn: '定点图片' ,
                en: '' ,
                // 路由路径
                path: '/image_at_position/index' ,
                hidden: false ,
                view: true ,
                parentId: systemId ,
                sIco: '' ,
                bIco: '' ,
                children: [] ,
            } ,
        ] ,
    } ,
];
