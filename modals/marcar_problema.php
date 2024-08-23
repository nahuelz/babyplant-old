<div id="ModalProblema" class="modal">
    <div class="modal-content3">
        <div class='box box-primary'>
            <div class='box-header with-border'>
                <h3 class='box-title'>Marcar Problema</span></h3>
            </div>
            <div class='box-body'>
                <div class='form-group'>
                    <div class="row">
                        <div class="col">
                            <label for="obsproblema_txt" class="control-label">Observaciones:</label>
                            <input type="text" maxlength="200" id="obsproblema_txt" class="form-control"
                                style="font-size:1.2em;text-transform: uppercase;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="d-flex flex-row justify-content-end">
                    <button type="button" class="btn btn-modal-bottom fa fa-close" id="btn_cancel"
                        onClick="$('#ModalProblema').css({'display':'none'});"></button>
                    <button type="button" class="btn fa fa-save btn-modal-bottom ml-2" id="btn_guardarcliente"
                        onClick="GuardarProblema();"></button>
                </div>
            </div>
            <span id="id_artpedidoproblema" style="display:none"></span>
        </div>
    </div>
</div> <!-- MODAL PROBLEMA FIN -->