<div id="ModalCambioCantidad" class="modal">
    <div class="modal-content3">
        <div class='box box-primary'>
            <div class='box-header with-border'>
                <h3 class='box-title'>Modificar Cantidad de Bandejas <span style="display: none;"
                        id="id_artpedidohide3"></span></h3>
            </div>
            <div class='box-body'>
                <div class='form-group'>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="cantidadbandejas_txt" class="control-label">Nueva Cantidad:</label>
                            <input type="number" min="0" step="1" id="cantidadbandejas_txt"
                                class="form-control text-right" value="0">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="d-flex flex-row justify-content-end">
                        <button type="button" class="btn fa fa-close btn-modal-bottom"
                            onClick="CerrarModalCantidad();"></button>
                        <button type="button" class="btn fa fa-save btn-modal-bottom"
                            onClick="GuardarCambioCantidad();"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> <!-- MODAL CAMBIAR CANTIDAD FIN -->