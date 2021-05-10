const index = genUrl('image_project');
const store = genUrl('image_project');
const update = genUrl('image_project/{id}');
const show = genUrl('image_project/{id}');
const destroy = genUrl('image_project/{id}');
const destroyAll = genUrl('destroy_all_image_project');
const destroyAllImageForImageSubject = genUrl('destroy_all_image_for_image_project');
const destroyTag = genUrl('destroy_image_project_tag');

export default {
    index (query) {
        return Http.get(index , query);
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
        return Http.delete(destroyAll , null ,{
            ids: G.jsonEncode(ids)
        });
    } ,

    destroyAllImageForImageSubject (ids) {
        return Http.delete(destroyAllImageForImageSubject , null , {
            ids: G.jsonEncode(ids)
        })
    } ,

    destroyTag (data) {
        return Http.delete(destroyTag , null , data)
    } ,


};
