        <script>
            $(function() {
                $("#toggle_nav_btn").click();
                inicializaSelect2();
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

            $("#boton_cerrar").click(function() {
                if (self !== top) {
                    parent.location.href = parent.location.href.split("?")[0];
                }
            });

            function inicializaSelect2() {
                $(".select2").select2({
                    allowClear: true,
                    minimumResultsForSearch: "",
                    placeholder: "Elige",
                    width: "100%"
                });

                $(".select2-show-search").select2({
                    allowClear: true,
                    minimumResultsForSearch: "",
                    placeholder: "Elige",
                    width: "100%"
                });

                $(".select2").on("click", () => {
                    let selectField = document.querySelectorAll(".select2-search__field");

                    selectField.forEach((element, index) => {
                        element.focus();
                    })
                });
            }
                    </script>
