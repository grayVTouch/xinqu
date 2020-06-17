import logo from "../res/logo.jpg";
import notFound from "../res/404.png";
import business from './business.js';
import table from './table.js';
import config from './config.js';

/**
 * ******************
 * 全局上下文环境
 * ******************
 */
window.TopContext = {
    api: 'http://api.xq.test/api/admin_v1' ,
    // 图片上传 api
    fileApi: 'http://api.xq.test/api/admin_v1/upload' ,
    code: {
        Success: 200 ,
        AuthFailed: 401
    } ,
    res: {
        logo ,
        notFound ,
    } ,
    business ,
    table ,
    // 每页显示记录数
    limit: 20 ,
    config ,
};