{strip}
<link href="layouts/v7/modules/Users/resources/Login.css" rel="stylesheet" type="text/css"/>
<span class="app-nav">
</span>
<div class="container-fluid loginPageContainer">
    <div>
        <div class="loginDiv widgetHeight">
            <img class="img-responsive user-logo" src="test/logo/logo.svg" alt="BPS" />
            <div>
                <span class="{if !$ERROR}hide{/if} failureMessage" id="validationMessage">
                    {$MESSAGE}
                </span>
                <span class="{if !$MAIL_STATUS}hide{/if} successMessage">
                    {$MESSAGE}
                </span>
            </div>
            <div id="loginFormDiv">
                <form action="index.php" class="form-horizontal" method="POST">
                    <input name="module" type="hidden" value="Users"/>
                    <input name="action" type="hidden" value="Login"/>
                    <div class="group">
                        <input autocomplete="username" id="username" name="username" placeholder="Usuario" type="text"/>
                        <span class="bar">
                        </span>
                        <label>
                            Usuario
                        </label>
                    </div>
                    <div class="group">
                        <input autocomplete="current-password" id="password" name="password" placeholder="Contraseña" type="password"/>
                        <span class="bar">
                        </span>
                        <label>
                            Contraseña
                        </label>
                    </div>
                    <div class="group">
                        <button class="button buttonBlue" type="submit">
                            Ingresar
                        </button>
                        <br/>
                        <a class="forgotPasswordLink" style="color: #15c;">
                            Olvido de contraseña
                        </a>
                    </div>
                </form>
            </div>
            <div class="hide" id="forgotPasswordDiv">
                <form action="forgotPassword.php" class="form-horizontal" method="POST">
                    <div class="group">
                        <input autocomplete="username" id="fusername" name="username" placeholder="Usuario" type="text"/>
                        <span class="bar">
                        </span>
                        <label>
                            Usuario
                        </label>
                    </div>
                    <div class="group">
                        <input id="email" name="emailId" placeholder="Email" type="email"/>
                        <span class="bar">
                        </span>
                        <label>
                            Email
                        </label>
                    </div>
                    <div class="group">
                        <button class="button buttonBlue forgot-submit-btn" type="submit">
                            Enviar
                        </button>
                        <br/>
                        <span>
                            Ingrese los datos y presione enviar
                            <a class="forgotPasswordLink pull-right" style="color: #15c;">
                                Volver
                            </a>
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="layouts/v7/modules/Users/resources/Login.js" type="text/javascript">
    </script>
</div>
{/strip}
