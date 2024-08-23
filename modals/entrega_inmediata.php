<div id="ModalEntregaInmediata" class="modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-content-verpedido">
        <div class='box box-primary'>
            <div class='box-header with-border'>
                <h4 class='box-title'>Entrega Inmediata</h4>
                <button style="float:right;" class="btn fa fa-close btn-modal-top"
                    onClick="$('#ModalEntregaInmediata').modal('hide')"></button>
                <button style='float:right;' class="btn btn-success btn-modal-top ml-5 mr-5"
                    onclick="guardarEntregaInmediata();"><i class='fa fa-save'></i> <span class='btn-entregar-label'>ENTREGAR</span></button>
            </div>

            <div class='box-body'>
                <div class="row">
                    <div class="col text-center">
                        <div class="d-flex flex-row" style="justify-content: center; align-items: center;">
                            <h5 class="label-nombrecliente"></h5>
                            <button onClick="" class="btn btn-warning btn-sm ml-3">Entregar a otro
                                Cliente</button>
                        </div>
                    </div>
                </div>
                <table id="tabla_entregainmediata" class="table table-responsive w-100 d-block d-md-table" role="grid">
                    <thead>
                        <tr role="row">
                            <th class="text-center">Ord</th>
                            <th class="text-center">Producto</th>
                            <th class="text-center">Faltan Entregar</th>
                            <th class="text-center">Band. Sembradas</th>
                            <th class="text-center">Mesada Nº</th>
                            <th class="text-center">Precio ($)</th>
                            <th class="text-center">Cantidad a Entregar</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <div class="modal-footer d-block">
                    <div class="row">
                        <div class="col pt-3">
                            <div class="d-flex flex-row">
                                <h6>APLICAR DESCUENTO:</h6>
                                <select class="form-control ml-2 select-descuento" style="max-width: 150px;">
                                    <option value="porcentual" selected>Porcentual</option>
                                    <option value="fijo">Suma Fija</option>
                                </select>
                                <input type="text" style="font-size: 1.0em; max-width: 150px"
                                    onkeyup="calcularSubtotal(this)" onpaste="calcularSubtotal(this);"
                                    class="form-control ml-2 input-descuento font-weight-bold text-center">
                            </div>
                        </div>
                        <div class="col">
                            <div class="d-flex flex-row pull-right">
                                <div class="pt-3">
                                    <h4>SUBTOTAL:</h4>
                                </div>
                                <input type="text" style="font-size: 1.6em; max-width: 150px"
                                    class="form-control ml-2 input-subtotal font-weight-bold text-center">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--FIN MODAL ENTREGA INMEDIATA -->