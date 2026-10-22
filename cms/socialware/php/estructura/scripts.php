        <script>
            $(function() {
                //$("#toggle_nav_btn").click();
                inicializaSelect2();
            });


            window.addEventListener('load', (event) => {
                var script = document.createElement('script');
                script.src = "assets/plugins/sidemenu/sidemenu.js";
                document.body.appendChild(script);

                if ($(".app-sidebar__overlay").css("visibility") == "visible") {
                    setTimeout(function () {
                        $(".app-sidebar__toggle").trigger("click");
                    }, 300);
                }

                $(".profile-1").click(function(){
                    $(".perfilUsuario").toggleClass("perfilUsuarioVisible");
                });
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
