<div id="ModalRevision" class="modal">
    <div class="modal-content3">
        <div class='box box-primary'>
            <div class='box-header with-border'>
                <h3 class='box-title'>Enviar a Revisión</h3>
            </div>
            <div class='box-body'>
                <div class='form-group'>
                    <div class="row">
                        <div class="col-sm-8">
                            <label for="preciou" class="control-label">Tipo:</label>
                            <select id="select_tiporevision" class="selectpicker mobile-device" data-dropup-auto="false"
                                title="Tipo de Revisión" data-style="btn-info" data-width="100%">
                                <option value="1">Falla de Germinación</option>
                                <option value="2">Golpe</option>
                                <option value="3">Pájaro</option>
                                <option value="4">Rata</option>
                                <option value="5">Realizar Despunte</option>
                                <option value="6">Uso para Injerto</option>
                                <option value="7">D1 Realizado</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="d-flex flex-row justify-content-end">
                    <button type="button" class="btn fa fa-close btn-modal-bottom" id="btn_cancel"
                        onClick="$('#ModalRevision').css({'display':'none'});"></button>
                    <button type="button" class="btn fa fa-save btn-modal-bottom ml-2"
                        onClick="sendToRevision();"></button>
                </div>
            </div>
            <span id="id_artpedidorevision" style="display:none"></span>
        </div>
    </div>
</div>