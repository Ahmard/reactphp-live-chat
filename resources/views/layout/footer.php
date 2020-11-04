</div>
</main>
<!-- Main layout -->

<!-- Footer -->
<footer class="page-footer mt-5 mdb-color">

    <!-- Copyright -->
    <div class="footer-copyright py-3 text-center">
        <div class="container-fluid">
            © <?= date('Y') ?> Copyright: <a href="/" target="_blank"><?= $_ENV['APP_NAME'] ?></a>
        </div>
    </div>
    <!-- Copyright -->

</footer>
<!-- Footer -->

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

<script src="/assets/js/jquery-3.5.1.min.js"></script>
<script src="/assets/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/mdb.min.js"></script>
<script src="/assets/js/moment.min.js"></script>
<script src="/assets/js/handlebars.min-v4.7.6.js"></script>
<script src="/assets/js/EventEmitter.min.js"></script>
<script src="/assets/js/howler.min.js"></script>
<script src="/assets/js/socket.js"></script>

<?php if (request()->auth()->check()): ?>
    <script> const privateChatSocketPrefix = '<?=$_ENV['PRIVATE_CHAT_SOCKET_URL_PREFIX']?>'; </script>
    <script src="/assets/js/user/private-connection.js"></script>
<?php endif; ?>

<script>
    $(function () {
        // Tooltips Initialization
        $('[data-toggle="tooltip"]').tooltip();

        $('#dark-mode').on('click', function (e) {
            e.preventDefault();
            $('footer').toggleClass('mdb-color lighten-4 dark-card-admin');
            $('body, .navbar').toggleClass('white-skin navy-blue-skin');
            $(this).toggleClass('white text-dark btn-outline-black');
            $('body').toggleClass('dark-bg-admin');
            $('.card').toggleClass('dark-card-admin');
            $('h6, .card, p, td, th, i, li a, h4, input, label').not(
                '#slide-out i, #slide-out a, .dropdown-item i, .dropdown-item').toggleClass('text-white');
            $('.btn-dash').toggleClass('grey blue').toggleClass('lighten-3 darken-3');
            $('.gradient-card-header').toggleClass('white dark-card-admin');
        });
    });
</script>
</body>
</html>