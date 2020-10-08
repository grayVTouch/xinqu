const index = genUrl('video');
const store = genUrl('video');
const update = genUrl('video/{id}');
const show = genUrl('video/{id}');
const destroy = genUrl('video/{id}');
const destroyAll = genUrl('destroy_all_video');
const destroyVideos = genUrl('destroy_videos');
const retryProcessVideo = genUrl('retry_process_video');

export default {
    index (data , success , error) {
        return request(index , 'get' , data , success , error);
    } ,

    localUpdate (id , data , success , error) {
        const url = localUpdate.replace('{id}' , id);
        return request(url , 'patch' , data , success , error)
    } ,

    update (id , data , success , error) {
        const url = update.replace('{id}' , id);
        return request(url , 'put' , data , success , error)
    } ,

    store (data , success , error) {
        return request(store , 'post' , data , success , error)
    } ,

    show (id , success , error) {
        return request(show.replace('{id}' , id) , 'get' , null , success , error)
    } ,

    destroy (id , success , error) {
        const url = destroy.replace('{id}' , id);
        return request(url , 'delete' , null , success , error)
    } ,

    destroyAll (ids , success , error) {
        return request(destroyAll , 'delete' , {
            ids: G.jsonEncode(ids)
        } , success , error)
    } ,

    destroyVideos (videoSrcIds , success , error) {
        return request(destroyVideos , 'delete' , {
            video_src_ids: G.jsonEncode(videoSrcIds)
        } , success , error)
    } ,

    retryProcessVideo (ids , success , error) {
        return request(retryProcessVideo , 'post' , {
            ids: G.jsonEncode(ids) ,
        } , success , error);
    } ,


};
