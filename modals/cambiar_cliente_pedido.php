<div id="ModalCambioCliente" class="modal">
    <div class="modal-contentcambio">
        <div class='box box-primary'>
            <div class='box-header with-border'>
                <h3 class='box-title'>Modificar Cliente/Enviar a Stock</h3>
            </div>
            <div class='box-body'>
                <div class="row">
                    <div class="col-md-1">
                        <label for="select_modocambio" class="control-label">Modo:</label>
                    </div>
                    <div class="col">
                        <select id="select_modocambio" title="Selecciona Tipo Modificación" onChange="setModoCambio()"
                            data-style="btn-info" class="selectpicker" data-width="100%">
                        </select>
                    </div>
                </div>
            </div>
            <div id="modo_permutar" class="row" style="display:none;">
                <div class="col">
                    <table id="tabla_ordenes_similares" class="table w-100 d-md-table table-responsive" role="grid">
                        <thead>
                            <tr role="row">
                                <th class="text-center" style="width:30px;">Ord</th>
                                <th class="text-center" style="width:220px;">Producto</th>
                                <th class="text-center" style="width:80px;">Bandejas en<br>Invernáculo</th>
                                <th class="text-center" style="width:140px;">Cliente</th>
                                <th class="text-center" style="width:20px;">Fecha Entrega</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="modo_asignarcliente" class="row mt-2" style="display:none;">
                <div class="col">
                    <div class="row">
                        <div class="col text-center">
                            <h5>Cliente:</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <select id="select_cliente" class="selectpicker mobile-device" title="Selecciona Cliente"
                                data-container="ModalCambioCliente" data-style="btn-info" data-live-search="true"
                                data-dropup-auto="false" data-width="100%"></select>
                        </div>
                    </div>
                </div>
            </div>

            <div id="modo_enviar_stock_total" class="d-none">
                <div class="row mt-2">
                    <div class="col text-center">
                        <h5>Selecciona Mesada: <span class="label-mesada-actual"></span></h5>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <select id="select_mesada_stock" data-size="10" class="selectpicker mobile-device"
                            title="Selecciona Mesada" data-container="ModalCambioCliente" data-style="btn-info"
                            data-dropup-auto="false" data-width="100%"></select>
                    </div>
                </div>
            </div>

            <div id="modo_enviar_stock_sobrante" class="d-none">
                <div class="row mt-2">
                    <div class="col text-center">
                        <h5>Selecciona Mesada: <span class="label-mesada-actual"></span></h5>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <select id="select_mesada_stock_sobrante" data-size="10" class="selectpicker mobile-device"
                            title="Selecciona Mesada" data-container="ModalCambioCliente" data-style="btn-info"
                            data-dropup-auto="false" data-width="100%"></select>
                    </div>
                </div>
            </div>


            <div class="row" style="margin-top: 80px;"> </div>
            <div class="row">
                <div class="col">
                    <div class="d-flex flex-row justify-content-end">
                        <button type="button" class="btn btn-modal-bottom fa fa-close"
                            onClick="$('#ModalCambioCliente').modal('hide');"></button>
                        <button type="button" class="btn btn-modal-bottom ml-2 fa fa-save"
                            onClick="GuardarCambioCliente();"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> <!-- MODAL CAMBIO CLIENTEFIN -->