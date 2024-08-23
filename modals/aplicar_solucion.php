<div id="ModalSolucion" class="modal">
    <div class="modal-content3">
        <div class='box box-primary'>
            <div class='box-header with-border'>
                <h3 class='box-title'>Aplicar Solución</h3>
            </div>
            <div class='box-body'>
                <div class='form-group'>
                    <div class="row">
                        <div class="col-md-8">
                            <label for="preciou" class="control-label">Tipo:</label>
                            <select id="select_tiposolucion" class="selectpicker mobile-device" data-dropup-auto="false"
                                title="Tipo de Solución" data-style="btn-info" data-width="100%">
                                <option value="1">D1 Cancelado</option>
                                <option value="2">Clasificación</option>
                                <option value="3">Repique</option>
                                <option value="4">Resiembra</option>
                                <option value="5">Dejar Fallas 12</option>
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
                        onClick="$('#ModalSolucion').css({'display':'none'});"></button>
                    <button type="button" class="btn btn-modal-bottom ml-2 fa fa-save"
                        onClick="aplicarSolucion();"></button>
                </div>
            </div>
            <span id="id_artpedidosolucion" style="display:none"></span>
        </div>
    </div>
</div>