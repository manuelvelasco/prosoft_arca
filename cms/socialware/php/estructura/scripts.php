        <script>
            $(function() {
                $("#toggle_nav_btn").click();
            });


            $("#link_cerrarSesion").click(function () {
                $.ajax({
                    type: "post",
                    url: "socialware/php/ajax/finalizaSesion.php",
                    success: function() {
                        window.location.replace("acceso.php");
                    }
                });

                return false;
            });
        </script>
