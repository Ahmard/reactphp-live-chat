<?php require(view_path('layout/header.php')); ?>

<style>
    .btn-circle {
        border-radius: 80px;
        padding-left: 5px;
    }
</style>

<div class="container mt-5" id="root"></div>

<template id="template-category-index">
    <div class="card" id="card-category">
        <div class="card-header text-uppercase">Categories</div>
        <div class="card-body">
            <div class="list-group" id="categories">
                <div class="text-center"><i class="fa fa-spinner fa-pulse fa-3x"></i></div>
            </div>
            <div class="mt-2 text-right">
                <button class="btn btn-md btn-primary rounded" id="btn-add-category" onclick="categories.add();">
                    <i class="fa fa-plus fa-1x"></i> Category
                </button>
            </div>
        </div>
    </div>
</template>

<template id="template-category-item">
    <a id="category-item-{{category.id}}" onclick="categories.open({{category.id}});"
       class="list-group-item list-group-item-action font-weight-bold">
        <i class="fa fa-folder"></i>
        <span class="list-title">{{category.name}}</span>
    </a>
</template>

<template id="template-add-category">
    <form id="form-add-category">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Name</span>
            </div>
            <input type="text" name="category-name" class="form-control" placeholder="Category Name">
        </div>
        <div class="mt-2">
            <button type="submit" class="btn btn-md btn-block btn-primary ">Add</button>
        </div>
    </form>
</template>

<template id="template-rename-category">
    <form id="form-rename-category">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Name</span>
            </div>
            <input type="text" name="category-name" class="form-control" placeholder="Category Name"
                   value="{{category.name}}">
        </div>
        <div class="mt-2">
            <button type="submit" class="btn btn-md btn-block btn-primary ">
                Rename
            </button>
        </div>
    </form>
</template>

<template id="template-open-category">
    <div class="list-group" id="category-lists"></div>
</template>


<template id="template-list-index">
    <div class="d-flex justify-content-between">
        <div class="bc-icons-1">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a class="black-text" href="#" onclick="categories.fetch();">Categories</a>
                    </li>
                    <li class="breadcrumb-item active" id="breadcrumb-item-{{category.id}}">{{category.name}}</li>
                </ol>
            </nav>
        </div>

        <div class="">
            <button class="btn btn-md btn-primary" onclick="categories.rename({{category.id}});">
                <i class="fa fa-pen-alt"></i> Rename
            </button>
            <button class="btn btn-md btn-danger" onclick="categories.delete({{category.id}});">
                <i class="fa fa-trash-alt"></i> Delete
            </button>
        </div>
    </div>

    <div class="card" id="card-list">
        <div class="card-header text-uppercase">List Items</div>
        <div class="card-body">
            <div class="list-group" id="lists"></div>
            <div class="mt-2 text-right">
                <button class="btn btn-md btn-primary rounded" id="btn-add-list" onclick="addList();">
                    <i class="fa fa-plus fa-1x"></i> Add Item
                </button>
            </div>
        </div>
    </div>
</template>

<template id="template-add-list">
    <form id="form-add-list">
        <div class="input-group">
            <input type="text" name="list-content" class="form-control" placeholder="List Title">
            <div class="input-group-append">
                <button type="submit" class="btn btn-md btn-block z-depth-0 m-0 btn-primary ">Save List</button>
            </div>
        </div>

    </form>
</template>

<template id="template-edit-list">
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">Content</span>
        </div>
        <input type="text" name="list-content" class="form-control" placeholder="List Content" value="{{list.content}}">
        <div class="input-group-append">
            <button onclick="updateList({{list.id}}, this)" type="submit"
                    class="btn btn-md btn-block z-depth-0 m-0 btn-primary ">Save Item
            </button>
        </div>
    </div>
</template>

<template id="template-list-item">
    <div id="list-item-{{list.id}}"
         class="list-group-item d-flex justify-content-between">
        <div style="word-wrap: anywhere;width: 55rem;" class="mr-5">
            <i class="fa fa-caret-right"></i>
            <span class="list-content text-wrap" >{{{list.content}}}</span>
        </div>
        <div>
            <div class="dropdown">
                <button class="btn btn-link font-weight-bold z-depth-0 btn-circle m-0 dropdown-toggle" type="button"
                        style="border-radius: 50px;padding: 5px;"
                        id="dropdown-{{list.id}}" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                    Actions
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdown-{{list.id}}">
                    <h6 class="dropdown-header text-uppercase">Actions</h6>
                    <div class="dropdown-divider"></div>
                    <button onclick="moveList({{list.id}})" class="dropdown-item text-primary" type="button">
                        <i class="fa fa-sync-alt"></i> Move
                    </button>
                    <div class="dropdown-divider"></div>
                    <button onclick="editList({{list.id}})" class="dropdown-item text-primary" type="button">
                        <i class="fa fa-pen-alt"></i> Update
                    </button>
                    <div class="dropdown-divider"></div>
                    <button onclick="deleteList({{list.id}})" class="dropdown-item text-danger" type="button">
                        <i class="fa fa-trash-alt"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<template id="template-move-list">
    <div class="font-weight-bolder mb-2">CHOOSE CATEGORY TO MOVE NOTE INTO</div>
    <div class="" id="move-list-cats"></div>
</template>

<template id="template-move-list-item">
    <button class="btn btn-md btn-dark mr-2" onclick="doMoveList({{category.id}}, {{list.id}});">{{category.name}}
    </button>
</template>


<div class="modal fade" id="modal_general" data-backdrop="static" data-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<?php require(view_path('layout/footer.php')); ?>
<script>
    let $elRoot = $('#root');
    let $modal = $('#modal_general');

    $(function () {
        categories.fetch(true);
    })
</script>
<script src="/assets/js/user/note.category.js"></script>
<script src="/assets/js/user/list-taking.js"></script>
<script src="/assets/js/linkify.min.js"></script>
<script src="/assets/js/linkify-string.min.js"></script>
