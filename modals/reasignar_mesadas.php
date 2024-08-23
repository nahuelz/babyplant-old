<div id="ModalEnviaraMesadas" class="modal">
    <div class="modal-content-verpedido">
        <div class='box box-primary'>
            <div class='box-header with-border'>
                <h4 class="box-title">Reasignar Mesadas</h4>
                <button class="btn fa fa-close btn-modal-top pull-right ml-5" style="font-size: 2em"
                    onClick="$('#ModalEnviaraMesadas').modal('hide')"></button>
                <button type="button" class="btn fa fa-save btn-modal-top pull-right" 
                    onClick="GuardarReasignacionMesadas();"> GUARDAR</button>
            </div>
            <div class='row'>
                <div class='col-md-7'>
                    <div class='box-body'>
                        <div id='box_info2'>
                            <h4 id='bandejas_pendientes'></h4>
                            <div class="contenedor">
                                <div class="row">
                                    <div class="col text-center">
                                        <div class="row row-reasignar">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class='col-md-4 order-first order-md-last'>
                    <div id="contenedor_cantidades">
                        <h4>Faltan asignar: <span id='quedan_bandejas'></span></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>