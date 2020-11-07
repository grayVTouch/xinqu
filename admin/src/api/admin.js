const info = genUrl('info');
const search = genUrl('search_admin');

const index = genUrl('admin');
const store = genUrl('admin');
const update = genUrl('admin/{id}');
const show = genUrl('admin/{id}');
const destroy = genUrl('admin/{id}');
const destroyAll = genUrl('destroy_all_admin');

export default {
    info (success , error) {
        return request(info , 'get' , null , success , error);
    } ,

    search (value , success , error) {
        return request(search , 'get' , {
            value ,
        } , success , error);
    } ,

    index (data , success , error) {
        return request(index , 'get' , data , success , error);
    } ,

    update (id , data , success , error) {
        return request(update.replace('{id}' , id) , 'put' , data , success , error)
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

    destroyAll (idList , success , error) {
        return request(destroyAll , 'delete' , {
            ids: G.jsonEncode(idList)
        } , success , error)
    } ,
};
