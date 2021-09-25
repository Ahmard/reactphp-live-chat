let viewList;
let editList;
let deleteList;
let updateList;
let addList;
let findList;
let moveList;
let doMoveList;
let listData = [];

let templateAddList = $('#template-add-list').html();
let templateEditList = Handlebars.compile($('#template-edit-list').html());
let templateListItem = Handlebars.compile($('#template-list-item').html());
let templateListsBlock = Handlebars.compile($('#template-list-index').html());
let templateMoveList = Handlebars.compile($('#template-move-list').html());
let templateMoveListItem = Handlebars.compile($('#template-move-list-item').html());

const categories = new App.Category({
    endpoint: 'list-categories/',
    last_opened_cat_key: 'last_opened_note_cat',
    viewData: null,
    initData: function (category, lists) {
        listData = lists;

        $elRoot.html(templateListsBlock({
            category: category
        }));

        lists.forEach(function (list) {
            let listItem = Object.assign({}, list);
            listItem.content = formatLink(listItem.content);
            $('#lists').append(templateListItem({
                list: listItem
            }));
        });
    }
});


findList = function (listId, returnCopy = true) {
    for (let i = 0; i < listData.length; i++) {
        if (listData[i] && listId === listData[i].id) {
            return {
                list: (returnCopy
                    ? Object.assign({}, listData[i])
                    : listData[i]),
                key: i
            }
        }
    }

    return undefined;
}

addList = function () {
    $modal.find('.modal-title').html('Add list item');
    $modal.find('.modal-footer').hide();
    $modal.find('.modal-body').html(templateAddList);
    $modal.one('shown.bs.modal', function () {
        let $formAddList = $('#form-add-list');

        $formAddList.find('input[name="list-content"]').focus();

        $formAddList.one('submit', function (event) {
            event.preventDefault();

            $formAddList.find('button[type="submit"]')
                .text('Adding...')
                .attr('disabled', 'disabled');

            let listItem = {
                category_id: currentCategory.id,
                content: $formAddList.find('input[name="list-content"]').val(),
            };

            $.ajax({
                url: '/api/lists' + '/' + TOKEN,
                method: 'POST',
                data: listItem,
                error: function (error) {
                    alert('Error occurred');
                    console.log(error);
                }
            }).then(function (response) {
                let list = response.data;
                list.content = formatLink(list.content);
                //Add to list lists
                listData.push(list);
                //Append to lists
                $('#lists').append(templateListItem({
                    list: list
                }));

                $modal.modal('hide');
            });


        })
    });
    $modal.modal('show');
};

moveList = function (listId) {
    let list = findList(listId).list;

    $modal.find('.modal-title').html('Move list');
    $modal.find('.modal-body').html(templateMoveList({
        list: list
    }));

    let $elItemDest = $('#move-list-cats');

    $elItemDest.html('');

    categoryLists.forEach(function (category) {
        if (category.id !== currentCategory.id) {
            $elItemDest.append(templateMoveListItem({
                list: list,
                category: category
            }));
        }
    });

    $modal.find('.modal-footer').hide();
    $modal.modal('show');
};

doMoveList = function (categoryId, listId) {
    $.ajax({
        url: '/api/lists/' + listId + '/move/' + categoryId + '/' + TOKEN,
        method: 'GET',
        error: function (error) {
            alert('Error occurred');
            console.log(error);
        }
    }).then(function (response) {
        $modal.find('.modal-body').html('<div class="fa-2x text-success">List Moved Successfully</div>');

        $('#list-item-' + listId).remove();

        let timeout = setTimeout(function () {
            $modal.modal('hide');
        }, 5000);

        $modal.one('hidden.bs.modal', function () {
            clearTimeout(timeout);
        });
    });
};

deleteList = function (listId) {
    if (confirm('DO you really want to delete this list item?')) {
        $.ajax({
            url: '/api/lists/' + listId + '/' + TOKEN,
            method: 'DELETE',
            error: function (error) {
                alert('Error occurred');
                console.log(error);
            }
        }).then(function (response) {
            let list = response.data;
            //Remove list from list
            delete listData[findList(listId).key];
            //Append to lists
            let $listItem = $('#list-item-' + listId);
            $listItem.addClass('border-danger').fadeOut(200, function () {
                $listItem.remove();
            });
            $modal.modal('hide');
        });
    }
};

editList = function (listId) {
    console.log(findList(listId))
    $modal.find('.modal-title').html('Update list');
    $modal.find('.modal-footer').hide();
    $modal.find('.modal-body').html(templateEditList({
        list: findList(listId).list
    }));
    $modal.modal('show');
};

updateList = function (listId, button) {
    let content = $('input[name="list-content"]').val();

    $(button).addClass('disabled')
        .attr('disabled', 'disabled')
        .html('Saving...');

    $.ajax({
        url: '/api/lists/' + listId + '/' + TOKEN,
        method: 'PUT',
        data: {content},
        error: function (error) {
            alert('Error occurred');
            console.log(error);
        }
    }).then(function (response) {
        let list = findList(listId, false);

        listData[list.key].content = content;

        //Add to list lists
        $('#list-item-' + listId)
            .find('.list-content')
            .html(formatLink(findList(listId).list.content));

        $modal.modal('hide');
    });
}