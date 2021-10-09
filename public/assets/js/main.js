const ajaxErrorHandler = function (error) {
    console.log(error);
    alert('Ajax error occurred, check your console for more details.');
};

const apiUrl = (url) => '/api/' + url + '/' + TOKEN;

const formatLink = (content) => content.linkify();

$(function (){
    $('[data-toggle="tooltip"]').tooltip({
        html: true
    });
});