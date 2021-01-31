<?php require(view_path('layout/header.php')); ?>

<div class="container mt-5" id="root"></div>

<template id="template-category-index">
    <div class="card" id="card-category">
        <div class="card-header">Categories</div>
        <div class="card-body">
            <div class="list-group" id="category">
                <div class="text-center"><i class="fa fa-spinner fa-pulse fa-3x"></i></div>
            </div>
            <div class="mt-2 text-right">
                <button class="btn btn-md btn-primary rounded" id="btn-add-category" onclick="addCategory();">
                    <i class="fa fa-plus fa-1x"></i> Category
                </button>
            </div>
        </div>
    </div>
</template>

<template id="template-category-item">
    <a id="category-item-{{category.id}}" onclick="openCategory({{category.id}});"
       class="list-group-item list-group-item-action font-weight-bold">
        <i class="fa fa-folder"></i>
        <span class="note-title">{{category.name}}</span>
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
    <div class="list-group" id="category-notes"></div>
</template>


<template id="template-note-index">
    <div class="d-flex justify-content-between">
        <div class="bc-icons-1">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a class="black-text" href="#" onclick="fetchCategories();">Categories</a>
                    </li>
                    <li class="breadcrumb-item active" id="breadcrumb-item-{{category.id}}">{{category.name}}</li>
                </ol>
            </nav>
        </div>

        <div class="">
            <button class="btn btn-md btn-primary" onclick="renameCategory({{category.id}});">
                <i class="fa fa-pen-alt"></i> Rename
            </button>
            <button class="btn btn-md btn-danger" onclick="deleteCategory({{category.id}});">
                <i class="fa fa-trash-alt"></i> Delete
            </button>
        </div>
    </div>

    <div class="card" id="card-note">
        <div class="card-header">Notes</div>
        <div class="card-body">
            <div class="list-group" id="notes"></div>
            <div class="mt-2 text-right">
                <button class="btn btn-md btn-primary rounded" id="btn-add-note" onclick="addNote();">
                    <i class="fa fa-plus fa-1x"></i> Note
                </button>
            </div>
        </div>
    </div>
</template>

<template id="template-add-note">
    <form id="form-add-note">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Title</span>
            </div>
            <input type="text" name="note-title" class="form-control" placeholder="Note Title">
        </div>
        <div class="input-group mt-2">
            <textarea rows="5" name="note-data" placeholder="Note Data" class="form-control"></textarea>
        </div>
        <div class="mt-2">
            <button type="submit" class="btn btn-md btn-block btn-primary ">Save Note</button>
        </div>
    </form>
</template>

<template id="template-edit-note">
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">Title</span>
        </div>
        <input type="text" name="note-title" class="form-control" placeholder="Note Title" value="{{note.title}}">
    </div>
    <div class="input-group mt-2">
        <textarea rows="7" name="note-data" placeholder="Note Data" class="form-control">{{note.note}}</textarea>
    </div>
    <div class="mt-2">
        <button onclick="updateNote({{note.id}}, this);" type="submit" class="btn btn-md btn-block btn-primary ">Update
            Note
        </button>
    </div>
</template>

<template id="template-note-item">
    <a id="note-item-{{note.id}}" onclick="viewNote({{note.id}});"
       class="list-group-item list-group-item-action font-weight-bold">
        <i class="fa fa-file-alt"></i>
        <span class="note-title">{{note.title}}</span>
    </a>
</template>

<template id="template-view-note">
    <div class="list-group">
        <div class="list-group-item">
            <i class="font-weight-bold">Title:</i> {{note.title}}
        </div>
        <div class="list-group-item">
            <i class="font-weight-bold">Note:</i>
            <div class="text-wrap" style="word-wrap:break-word">{{note.note}}</div>
        </div>
    </div>
</template>

<template id="template-view-note-footer">
    <div class="d-flex justify-content-between">
        <div>
            <button onclick="moveNote({{note.id}});" class="btn btn-md btn-primary mr-3">
                <i class="fa fa-sync-alt"></i> Move
            </button>
        </div>
        <div>
            <button onclick="editNote({{note.id}});" class="btn btn-md btn-primary"><i class="fa fa-edit"></i> Edit
            </button>
            <button onclick="deleteNote({{note.id}});" class="ml-2 btn btn-md btn-danger"><i class="fa fa-trash"></i>
                Delete
            </button>
        </div>
    </div>
</template>

<template id="template-move-note">
    <div class="font-weight-bolder mb-2">CHOOSE CATEGORY TO MOVE NOTE INTO</div>
    <div class="" id="move-note-cats"></div>
</template>

<template id="template-move-note-item">
    <button class="btn btn-md btn-dark mr-2" onclick="doMoveNote({{category.id}}, {{note.id}});">{{category.name}}</button>
</template>


<div class="modal fade" id="modal_general" data-backdrop="static" data-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
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
</script>
<script src="/assets/js/user/note.note.js"></script>
<script src="/assets/js/linkify.min.js"></script>
<script src="/assets/js/linkify-jquery.min.js"></script>
<script src="/assets/js/user/note.category.js"></script>
