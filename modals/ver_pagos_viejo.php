<div id="ModalPagos" class="modal">
    <div class="modalpago-content" style="overflow-y: scroll;overflow-x: hidden">
        <div class='box box-primary'>
            <div class='box-header with-border'>
                <div class="col-md-10">
                    <h3 class='box-title'>Ver Pagos (En desuso)<span style="display: none" id='id_pedidoref'></span>
                    </h3>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn fa fa-close btn-modal-top" id="btn_cancel"
                        onClick="CerrarModalPago();"></button>
                </div>
            </div>
            <div class='box-body'>

                <div id="contenedor_pagos" class="row">
                    <div class="col">
                        <span style="font-weight: bold;font-size: 14px;">Pagos Anteriores</span>
                        <table style='width: 100%' id="tabla_pagos" class="table2 table-bordered table-hover"
                            role="grid">
                            <thead>
                                <tr role="row">
                                    <th class="text-center">Fecha</th>
                                    <th class="text-center">Concepto</th>
                                    <th class="text-center">Monto ($)</th>
                                    <th class="text-center" style="width: 30px"></th>
                                    <th class="text-center" style="width: 30px"></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> <!-- MODAL PAGOS FIN -->