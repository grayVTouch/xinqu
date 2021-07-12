import login from '@api/login.js';
import misc from '@api/misc.js';
import user from '@api/user.js';
import adminPermission from '@api/admin_permission.js';
import role from '@api/role.js';
import module from '@api/module.js';
import tag from '@api/tag.js';
import category from '@api/category.js';
import admin from '@api/admin.js';
import position from '@api/position.js';
import nav from '@api/nav.js';
import region from '@api/region.js';
import pannel from '@api/pannel.js';
import disk from '@api/disk.js';
import file from '@api/file.js';
import job from '@api/job.js';
import systemDisk from '@api/system_disk.js';
import systemSettings from '@api/system_settings.js';

/**
 * 图片
 */
import imageSubject from '@api/image_subject.js';
import imageProject from '@api/image_project.js';

/**
 * 视频
 */
import video from '@api/video.js';
import videoProject from '@api/video_project.js';
import videoSeries from '@api/video_series.js';
import videoCompany from '@api/video_company.js';
import videoSubtitle from '@api/video_subtitle.js';

import imageAtPosition from '@api/image_at_position.js';

window.Api = {
    login ,
    misc ,
    user ,
    systemDisk ,
    adminPermission ,
    role ,
    module ,
    tag ,
    category ,
    imageSubject ,
    imageProject ,
    admin ,
    position ,
    imageAtPosition ,
    nav ,
    video ,
    videoProject ,
    videoSeries ,
    region ,
    videoCompany ,
    pannel ,
    disk ,
    file ,
    videoSubtitle ,
    job ,
    systemSettings ,
};
