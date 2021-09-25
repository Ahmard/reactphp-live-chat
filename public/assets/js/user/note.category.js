window.App = window.App || {};

let categoryLists = [];


App.Category = (function (settings) {
    const _this = this;
    const ENDPOINT = settings.endpoint;
    const LAST_OPENED_CAT_KEY = settings.last_opened_cat_key;

    let $elCategories = $('#category');
    let htmlCategories = $('#template-category-index').html();
    let htmlAddCategory = $('#template-add-category').html();
    let templateCategoryItem = Handlebars.compile($('#template-category-item').html());
    let templateRenameCategory = Handlebars.compile($('#template-rename-category').html());


    const display = function (categories) {
        //Remove loader
        $elCategories.html('');
        //Display categories
        categories.forEach(function (category) {
            $elCategories.append(templateCategoryItem({
                category: category
            }));
        });
    };

    this.find = function (categoryId) {
        for (let i = 0; i < categoryLists.length; i++) {
            if (categoryId === categoryLists[i].id) {
                return {
                    category: categoryLists[i],
                    key: i
                }
            }
        }

        return undefined;
    };

    this.fetch = function (rememberLastOpened = false) {
        $elRoot.html(htmlCategories);
        $elCategories = $('#categories');
        $.ajax({
            url: apiUrl(ENDPOINT),
            error: function (error) {
                alert('Error occurred');
                console.log(error);
            }
        }).then(function (response) {
            let categories = response.data;
            categoryLists = categories;

            let lastOpenedCat = localStorage.getItem(LAST_OPENED_CAT_KEY);
            let lastOpenedData = localStorage.getItem(LAST_OPENED_CAT_KEY);

            if (lastOpenedCat && rememberLastOpened && categories.length > 0) {
                _this.open(
                    parseInt(lastOpenedCat),
                    function () {
                        if (lastOpenedData && settings.viewData) {
                            settings.viewData(parseInt(lastOpenedData));
                        }
                    },
                    function (error) {
                        display(categoryLists);
                        console.log(error)
                    }
                );
                return;
            } else {
                localStorage.removeItem(LAST_OPENED_CAT_KEY);
            }

            display(categories);
        });
    };

    this.add = function () {
        $modal.find('.modal-title').html('Add Category');
        $modal.find('.modal-footer').hide();
        $modal.find('.modal-body').html(htmlAddCategory);
        $modal.one('shown.bs.modal', function () {
            let $formAddCategory = $('#form-add-category');

            $formAddCategory.find('input[name="category-name"]').focus();

            $formAddCategory.off('submit').submit(function (event) {
                event.preventDefault();

                $formAddCategory.find('button[type="submit"]')
                    .text('Adding...')
                    .attr('disabled', 'disabled');

                $.ajax({
                    url: apiUrl(ENDPOINT),
                    method: 'POST',
                    data: {
                        name: $formAddCategory.find('input[name="category-name"]').val(),
                    },
                    error: function (error) {
                        alert('Error occurred');
                        console.log(error);
                    }
                }).then(function (response) {
                    let category = response.data;
                    //Add to category lists
                    categoryLists.push(category);
                    //Append to categories
                    $elCategories.append(templateCategoryItem({
                        category: category
                    }));
                    $modal.modal('hide');
                });


            })
        });
        $modal.modal('show');
    };

    this.open = function (categoryId, successCallback, errorCallback) {
        if (!categoryId) {
            if (errorCallback) errorCallback();
            return;
        }

        $.ajax({
            url: apiUrl(ENDPOINT + categoryId + '/open'),
            method: 'GET',
        }).then(function (response) {
            if (!response.success) {
                if (errorCallback) errorCallback(response);
                return;
            }

            //
            let foundCategory = _this.find(categoryId);

            if (!foundCategory) {
                if (errorCallback) errorCallback();
            }

            let category = foundCategory.category;

            if (parseInt(categoryId) === parseInt(localStorage.getItem(LAST_OPENED_CAT_KEY))) {
                localStorage.removeItem(LAST_OPENED_CAT_KEY);
            }

            currentCategory = category;
            console.log(category)
            localStorage.setItem(LAST_OPENED_CAT_KEY, category.id);

            settings.initData(category, response.data);

            if (successCallback) successCallback();
        })
    }

    this.delete = function (categoryId) {
        if (confirm('All notes in this directory will be deleted with it\n Do you really want to delete?')) {
            $.ajax({
                url: apiUrl(ENDPOINT + categoryId),
                method: 'DELETE',
                error: function (error) {
                    alert('Error occurred');
                    console.log(error);
                }
            }).then(function (response) {
                let category = response.data;
                //Remove category from list
                delete categoryLists[_this.find(categoryId).key];
                //Append to categories
                let $categoryItem = $('#category-item-' + categoryId);
                $categoryItem.addClass('border-danger').fadeOut(200, function () {
                    $categoryItem.remove();
                });

                fetchCategories();

                $modal.modal('hide');
            });
        }
    };

    this.rename = function (categoryId) {
        let category = this.find(categoryId);
        $modal.find('.modal-title').html('Rename category');
        $modal.find('.modal-footer').hide();
        $modal.find('.modal-body').html(templateRenameCategory({
            category: category.category
        }));
        $modal.one('shown.bs.modal', function () {
            let $form = $('#form-rename-category');
            $form.submit(function (event) {
                event.preventDefault();

                let newCategoryName = $('input[name="category-name"]').val();

                $form.find('button[type="submit"]')
                    .addClass('disabled')
                    .attr('disabled', 'disabled')
                    .html('Saving...');

                $.ajax({
                    url: apiUrl(ENDPOINT + categoryId),
                    method: 'PUT',
                    data: {
                        name: newCategoryName
                    },
                    error: function (error) {
                        alert('Error occurred');
                        console.log(error);
                    }
                }).then(function (response) {
                    let category = _this.find(categoryId);

                    categoryLists[category.key].name = newCategoryName;

                    //Add to category lists
                    $('#breadcrumb-item-' + categoryId).html(newCategoryName);

                    $modal.modal('hide');
                });
            });
        });

        $modal.modal('show');
    };
});