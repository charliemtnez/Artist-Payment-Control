<?php

    getLayouts('header','login',['css'=>['style']]);

?>

<section>
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header"><h3 class="text-center font-weight-light my-4">Login</h3></div>
                                <div class="card-body">
                                    <div id="msg_error" class="small mb-3 alert alert-danger" role="alert" hidden></div>
                                    <form name="login" action="auth/act/act_auth.php" method="post">
                                        <div class="form-group">
                                            <label class="small mb-1" for="user">Usuario / email</label>
                                            <input class="form-control" id="user" type="text" placeholder="Entre Usuario o correo" />
                                        </div>
                                        <div class="form-group">
                                            <label class="small mb-1" for="password">Password</label>
                                            <!-- <input class="form-control py-4" id="password" type="password" placeholder="Entrar contraseña" /> -->
                                            <div class="input-group mb-3">
                                                <input class="form-control" aria-describedby="button-addon2" id="password" type="password" placeholder="Entrar contraseña">
                                                <button class="btn btn-outline-secondary" type="button" data-type="show" id="button-addon2" onclick="show_pass(this);"><i class="fa fa-eye"></i></button>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input" id="remenberme" type="checkbox" />
                                                <label class="custom-control-label" for="remenberme">Recordar</label>
                                            </div>
                                        </div>
                                        <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <a class="small" href="<?php echo getPathUriVar('URI') ;?>/repass">Olvidó su contraseña?</a>
                                            <!-- <a class="btn btn-primary" onclick="formhash(this.form);" href="index.html">Login</a> -->
                                            <button type="button" onclick="userauth(this.form);" class="btn btn-primary btn-user">
                                                    Login
                                            </button>
                                        </div>
                                    </form>
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

    getLayouts('footer',null, ['js'=>['sha512','userauth']]);

?>