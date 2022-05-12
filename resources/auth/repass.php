<?php
    getLayouts('header','login',['css'=>['style']]);

    
    // echo '<pre>';
    // print_r($db->check_database());
    // print_r($db->get_response());
    
    // print_r($_ENV);
    // var_dump(getPathUriVar('URI'));
    // var_dump(getPathUriVar('URI_VAR'));
    // var_dump(getPathUriVar('PATH_RESOURCES'));
    // echo '</pre>';
?>

<section>
<div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header"><h3 class="text-center font-weight-light my-4">Recuperar la Contraseña</h3></div>
                                <div class="card-body">
                                    <div class="small mb-3 text-muted">Entre su correo y se le enviará un enlace para configurar nuevamente la contraseña.</div>
                                    <div id="msg_error" class="small mb-3 alert alert-danger" role="alert" hidden></div>
                                    <form name="password">
                                        <div class="form-group">
                                            <label class="small mb-1" for="email">Email</label>
                                            <input class="form-control py-4" id="email" type="email" placeholder="Entre su correo" />
                                        </div>
                                        
                                        <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <a class="small" href="/login">Regresar a login</a>
                                            <button type="button" onclick="formhash(this.form);" class="btn btn-primary btn-user">
                                                Enviar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center">
                                    <!-- <div class="small"><a href="register.html">Need an account? Sign up!</a></div> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</section>

<?php
    getLayouts('footer',null, ['js'=>['sha512']]);
?>