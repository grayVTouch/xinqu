
let id              = 1;
const indexId         = id++;
const videoSubjectId  = id++;
const imageSubjectId  = id++;
const userId          = id++;
const channelId       = id++;

export default [
    {
        id: indexId ,
        name: '首页' ,
        route: '/index' ,
        hidden: false ,
        isBuiltIn: true ,
        parentId: 0 ,
        children: [] ,
    } ,
    {
        id: videoSubjectId ,
        name: '视频专题' ,
        route: '/video_subject' ,
        hidden: false ,
        isBuiltIn: true ,
        parentId: 0 ,
        children: [
            {
                id: id++ ,
                name: '视频专题详情' ,
                route: '/video_subject/:id/show' ,
                hidden: true ,
                isBuiltIn: true ,
                parentId: videoSubjectId ,
                children: [] ,
            } ,
            {
                id: id++ ,
                name: '搜索' ,
                route: '/video_subject/search' ,
                hidden: true ,
                isBuiltIn: true ,
                parentId: imageSubjectId ,
                children: [] ,
            } ,
        ] ,
    } ,
    {
        id: imageSubjectId ,
        name: '图片专题' ,
        route: '/image_subject' ,
        hidden: false ,
        isBuiltIn: true ,
        parentId: 0 ,
        children: [
            {
                id: id++ ,
                name: '图片专题详情' ,
                route: '/image_subject/:id/show' ,
                hidden: true ,
                isBuiltIn: true ,
                parentId: imageSubjectId ,
                children: [] ,
            } ,
            {
                id: id++ ,
                name: '搜索' ,
                route: '/image_subject/search' ,
                hidden: true ,
                isBuiltIn: true ,
                parentId: imageSubjectId ,
                children: [] ,
            } ,
        ] ,
    } ,
    {
        id: userId ,
        name: '用户中心' ,
        route: '/user' ,
        hidden: false ,
        isBuiltIn: true ,
        parentId: 0 ,
        children: [
            {
                id: id++ ,
                name: '用户信息' ,
                route: '/user/info' ,
                hidden: true ,
                isBuiltIn: true ,
                parentId: userId ,
                children: [] ,
            } ,
            {
                id: id++ ,
                name: '修改密码' ,
                route: '/user/password' ,
                hidden: true ,
                isBuiltIn: true ,
                parentId: userId ,
                children: [] ,
            } ,
            {
                id: id++ ,
                name: '历史记录' ,
                route: '/user/history' ,
                hidden: true ,
                isBuiltIn: true ,
                parentId: userId ,
                children: [] ,
            } ,
            {
                id: id++ ,
                name: '我的收藏' ,
                route: '/user/favorites' ,
                hidden: true ,
                isBuiltIn: true ,
                parentId: userId ,
                children: [] ,
            } ,
        ] ,
    } ,
    {
        id: channelId ,
        name: '频道' ,
        route: '/channel' ,
        hidden: true ,
        isBuiltIn: true ,
        parentId: 0 ,
        children: [
            {
                id: id++ ,
                name: '图片专题' ,
                route: '/channel/:id/image' ,
                hidden: true ,
                isBuiltIn: true ,
                parentId: channelId ,
                children: [] ,
            } ,
            {
                id: id++ ,
                name: '关注我的人' ,
                route: '/channel/:id/focus_me_user' ,
                hidden: true ,
                isBuiltIn: true ,
                parentId: channelId ,
                children: [] ,
            } ,
            {
                id: id++ ,
                name: '我关注的人' ,
                route: '/channel/:id/my_focus_user' ,
                hidden: true ,
                isBuiltIn: true ,
                parentId: channelId ,
                children: [] ,
            } ,
        ] ,
    } ,
];