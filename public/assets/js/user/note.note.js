let viewNote;
let editNote;
let deleteNote;
let updateNote;
let initNotes;
let addNote;
let findNote;
let moveNote;
let doMoveNote;
let noteLists = [];

let currentCategory;

$(function () {
    let templateAddNote = $('#template-add-note').html();
    let templateEditNote = Handlebars.compile($('#template-edit-note').html());
    let templateNoteItem = Handlebars.compile($('#template-note-item').html());
    let templateViewNote = Handlebars.compile($('#template-view-note').html());
    let templateViewNoteFooter = Handlebars.compile($('#template-view-note-footer').html());
    let templateNotesBlock = Handlebars.compile($('#template-note-index').html());
    let templateMoveNote = Handlebars.compile($('#template-move-note').html());
    let templateMoveNoteItem = Handlebars.compile($('#template-move-note-item').html());

    findNote = function (noteId) {
        for (let i = 0; i < noteLists.length; i++) {
            if (noteId === noteLists[i].id) {
                return {
                    note: noteLists[i],
                    key: i
                }
            }
        }

        return undefined;
    }

    initNotes = function (category, notes) {
        noteLists = notes;

        $elRoot.html(templateNotesBlock({
            category: category
        }));

        notes.forEach(function (note) {
            $('#notes').append(templateNoteItem({
                note: note
            }));
        });

    };

    addNote = function () {
        $modal.find('.modal-title').html('Add note');
        $modal.find('.modal-footer').hide();
        $modal.find('.modal-body').html(templateAddNote);
        $modal.one('shown.bs.modal', function () {
            let $formAddNote = $('#form-add-note');

            $formAddNote.find('input[name="note-title"]').focus();

            $formAddNote.one('submit', function (event) {
                event.preventDefault();

                $formAddNote.find('button[type="submit"]')
                    .text('Adding...')
                    .attr('disabled', 'disabled');

                let noteData = {
                    category_id: currentCategory.id,
                    title: $formAddNote.find('input[name="note-title"]').val(),
                    note: $formAddNote.find('textarea[name="note-data"]').val(),
                };

                $.ajax({
                    url: '/api/notes' + '/' + TOKEN,
                    method: 'POST',
                    data: noteData,
                    error: function (error) {
                        alert('Error occurred');
                        console.log(error);
                    }
                }).then(function (response) {
                    let note = response.data;
                    //Add to note lists
                    noteLists.push(note);
                    //Append to notes
                    $('#notes').append(templateNoteItem({
                        note: note
                    }));
                    $modal.modal('hide');
                });


            })
        });
        $modal.modal('show');
    };

    moveNote = function (noteId){
        let note = findNote(noteId).note;

        $modal.find('.modal-title').html('Move note');
        $modal.find('.modal-body').html(templateMoveNote({
            note: note
        }));

        let $elItemDest = $('#move-note-cats');

        $elItemDest.html('');

        categoryLists.forEach(function (category) {
            if(category.id !== currentCategory.id){
                $elItemDest.append(templateMoveNoteItem({
                    note: note,
                    category: category
                }));
            }
        });

        $modal.find('.modal-footer').hide();
        $modal.modal('show');
    };

    doMoveNote = function(categoryId, noteId){
        $.ajax({
            url: '/api/notes/' + noteId + '/move/' + categoryId + '/' + TOKEN,
            method: 'GET',
            error: function (error) {
                alert('Error occurred');
                console.log(error);
            }
        }).then(function (response) {
            $modal.find('.modal-body').html('<div class="fa-2x text-success">Note Moved Successfully</div>');

            $('#note-item-'+noteId).remove();

            let timeout = setTimeout(function () {
                $modal.modal('hide');
            }, 5000);

            $modal.one('hidden.bs.modal', function () {
                clearTimeout(timeout);
            });
        });
    };

    viewNote = function (noteId) {
        let note = findNote(noteId).note;
        $modal.find('.modal-title').html('View note');
        $modal.find('.modal-body').html(templateViewNote({
            note: note
        }));
        $modal.find('.modal-footer')
            .show()
            .html(templateViewNoteFooter({
                note: note
            }));

        //Save last opened note history
        $modal.on('shown.bs.modal', function () {
            localStorage.setItem('last_opened_note', noteId);
        });
        //Remove last opened note from history
        $modal.on('hidden.bs.modal', function () {
            localStorage.removeItem('last_opened_note');
        });

        $modal.modal('show');
    }

    deleteNote = function (noteId) {
        $.ajax({
            url: '/api/notes/' + noteId + '/' + TOKEN,
            method: 'DELETE',
            error: function (error) {
                alert('Error occurred');
                console.log(error);
            }
        }).then(function (response) {
            let note = response.data;
            //Remove note from list
            delete noteLists[findNote(noteId).key];
            //Append to notes
            let $noteItem = $('#note-item-' + noteId);
            $noteItem.addClass('border-danger').fadeOut(200, function () {
                $noteItem.remove();
            });
            $modal.modal('hide');
        });
    };

    editNote = function (noteId) {
        let note = findNote(noteId);
        $modal.find('.modal-title').html('Update note');
        $modal.find('.modal-footer').hide();
        $modal.find('.modal-body').html(templateEditNote({
            note: note.note
        }));
        $modal.modal('show');
    };

    updateNote = function (noteId, button) {
        let noteTitle = $('input[name="note-title"]').val();
        let noteData = $('textarea[name="note-data"]').val();

        $(button).addClass('disabled')
            .attr('disabled', 'disabled')
            .html('Saving...');

        $.ajax({
            url: '/api/notes/' + noteId + '/' + TOKEN,
            method: 'PUT',
            data: {
                title: noteTitle,
                note: noteData,
            },
            error: function (error) {
                alert('Error occurred');
                console.log(error);
            }
        }).then(function (response) {
            let note = findNote(noteId);

            noteLists[note.key].title = noteTitle;
            noteLists[note.key].note = noteData;

            //Add to note lists
            $('#note-item-' + noteId)
                .find('.note-title')
                .html(noteTitle);

            $modal.modal('hide');
        });
    }
});