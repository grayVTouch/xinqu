const index = genUrl('video');
const store = genUrl('video');
const update = genUrl('video/{id}');
const show = genUrl('video/{id}');
const destroy = genUrl('video/{id}');
const destroyAll = genUrl('destroy_all_video');
const destroyVideos = genUrl('destroy_videos');
const retryProcessVideo = genUrl('retry_process_video');

export default {
    index (param) {
        return Http.get(index , param);
    } ,

    localUpdate (id , data) {
        return Http.patch(localUpdate.replace('{id}' , id) , null , data);
    } ,

    update (id , data) {
        return Http.put(update.replace('{id}' , id) , null , data);
    } ,

    store (data) {
        return Http.post(store , null , data);
    } ,

    show (id) {
        return Http.get(show.replace('{id}' , id));
    } ,

    destroy (id) {
        return Http.delete(destroy.replace('{id}' , id));
    } ,

    destroyAll (ids) {
        return Http.delete(destroyAll , null , {
            ids: G.jsonEncode(ids)
        });
    } ,

    destroyVideos (videoSrcIds) {
        return Http.delete(destroyVideos , null , {
            video_src_ids: G.jsonEncode(videoSrcIds)
        })
    } ,

    retryProcessVideo (ids) {
        return Http.post(retryProcessVideo , null , {
            ids: G.jsonEncode(ids) ,
        });
    } ,


};
