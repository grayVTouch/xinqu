const adminIco = '/resource/preset/ico/admin.png';
const categoryIco = '/resource/preset/ico/category.png';
const imageIco = '/resource/preset/ico/image.png';
const pannelIco = '/resource/preset/ico/pannel.png';
const subjectIco = '/resource/preset/ico/subject.png';
const systemIco = '/resource/preset/ico/system.png';
const tagIco = '/resource/preset/ico/tag.png';
const userIco = '/resource/preset/ico/user.png';
const videoIco = '/resource/preset/ico/video.png';
const moduleIco = '/resource/preset/ico/module.png';
const toolboxIco = '/resource/preset/ico/toolbox.png';

/**
 * 侧边菜单功能
 */
export default [
    {
        key: 'pannel' ,
        cn: '控制台' ,
        en: 'Pannel' ,
        // 路由路径
        path: '/pannel' ,
        hidden: false ,
        view: true ,
        sIco: pannelIco ,
        bIco: pannelIco ,
        children: [] ,
    } ,
    {
        key: 'admin' ,
        cn: '后台用户' ,
        en: 'Admin' ,
        path: '/admin/index' ,
        hidden: false ,
        view: true ,
        sIco: adminIco ,
        bIco: adminIco ,
        children: [] ,
    } ,
    {
        key: 'user' ,
        cn: '用户管理' ,
        en: 'User' ,
        path: '/user/index' ,
        hidden: false ,
        view: true ,
        sIco: userIco ,
        bIco: userIco ,
        children: [] ,
    } ,
    {
        key: 'image_manager' ,
        cn: '图片管理' ,
        en: 'Image' ,
        // 路由路径
        path: '' ,
        hidden: false ,
        view: false ,
        sIco: imageIco ,
        bIco: imageIco ,
        children: [
            {
                cn: '图片主体' ,
                en: 'Image Subject' ,
                // 路由路径
                path: '/image/subject' ,
                hidden: false ,
                view: true ,
                sIco: subjectIco ,
                bIco: subjectIco ,
                children: [] ,
            } ,
            {
                cn: '图片专题' ,
                en: 'Image Project' ,
                path: '/image/project' ,
                hidden: false ,
                view: true ,
                sIco: imageIco ,
                bIco: imageIco ,
                children: [] ,
            } ,
        ] ,
    } ,
    {
        key: 'video_manager' ,
        cn: '视频管理' ,
        en: 'Video' ,
        // 路由路径
        path: '' ,
        hidden: false ,
        view: false ,
        sIco: videoIco ,
        bIco: videoIco ,
        children: [
            {
                key: 'video_series' ,
                cn: '视频系列' ,
                en: 'Video Series' ,
                // 路由路径
                path: '/video/series' ,
                hidden: false ,
                view: true ,
                sIco: videoIco ,
                bIco: videoIco ,
                children: [] ,
            } ,
            {
                key: 'video_company' ,
                cn: '视频制作公司' ,
                en: 'Video Company' ,
                // 路由路径
                path: '/video/company' ,
                hidden: false ,
                view: true ,
                sIco: videoIco ,
                bIco: videoIco ,
                children: [] ,
            } ,
            {
                key: 'video_subject' ,
                cn: '视频主体' ,
                en: 'Video Subject' ,
                // 路由路径
                path: '/video/subject' ,
                hidden: false ,
                view: true ,
                sIco: videoIco ,
                bIco: videoIco ,
                children: [] ,
            } ,
            {
                key: 'video_project' ,
                cn: '视频专题' ,
                en: 'Video Project' ,
                // 路由路径
                path: '/video/project' ,
                hidden: false ,
                view: true ,
                sIco: videoIco ,
                bIco: videoIco ,
                children: [] ,
            } ,
            {
                key: 'video' ,
                cn: '视频列表' ,
                en: 'Video' ,
                // 路由路径
                path: '/video/index' ,
                hidden: false ,
                view: true ,
                sIco: videoIco ,
                bIco: videoIco ,
                children: [] ,
            } ,
        ] ,
    } ,
    {
        key: 'tag' ,
        cn: '个性标签' ,
        en: 'Tag' ,
        // 路由路径
        path: '/tag/index' ,
        hidden: false ,
        view: true ,
        sIco: tagIco ,
        bIco: tagIco ,
        children: [] ,
    } ,
    {
        cn: '分类管理' ,
        en: 'Category' ,
        // 路由路径
        path: '/category/index' ,
        hidden: false ,
        view: true ,
        sIco: categoryIco ,
        bIco: categoryIco ,
        children: [] ,
    } ,
    {

        cn: '模块管理' ,
        en: 'Module' ,
        // 路由路径
        path: '/module/index' ,
        hidden: false ,
        view: true ,
        sIco: moduleIco ,
        bIco: moduleIco ,
        children: [] ,
    } ,
    {

        cn: '工具箱',
        en: 'System',
        // 路由路径
        path: '/toolbox/index',
        hidden: false,
        view: true,
        sIco: toolboxIco,
        bIco: toolboxIco,
        children: []
    } ,
    {
        cn: '系统管理' ,
        en: 'System' ,
        // 路由路径
        path: '' ,
        hidden: false ,
        view: false ,
        sIco: systemIco ,
        bIco: systemIco ,
        children: [
            {

                cn: '通用设置' ,
                en: 'Settings' ,
                // 路由路径
                path: '/system/settings' ,
                hidden: false ,
                view: true ,
                sIco: systemIco ,
                bIco: systemIco ,
                children: [] ,
            } ,
            {

                cn: '存储管理' ,
                en: 'Disk' ,
                // 路由路径
                path: '/system/disk' ,
                hidden: false ,
                view: true ,
                sIco: systemIco ,
                bIco: systemIco ,
                children: [] ,
            } ,
            {
                cn: '导航菜单' ,
                en: 'Navigation' ,
                // 路由路径
                path: '/system/navigation' ,
                hidden: false ,
                view: true ,
                sIco: systemIco ,
                bIco: systemIco ,
                children: [] ,
            } ,
            {
                cn: '定点位置' ,
                en: 'Position' ,
                // 路由路径
                path: '/system/position' ,
                hidden: false ,
                view: true ,
                sIco: systemIco ,
                bIco: systemIco ,
                children: [] ,
            } ,
            {
                cn: '定点图片' ,
                en: 'Image At Position' ,
                // 路由路径
                path: '/system/imageAtPosition' ,
                hidden: false ,
                view: true ,
                sIco: systemIco ,
                bIco: systemIco ,
                children: [] ,
            } ,
        ] ,
    } ,
];
