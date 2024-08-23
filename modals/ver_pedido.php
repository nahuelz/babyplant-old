<div id="ModalVerPedido" class="modal">
    <div class="modal-content-verpedido">
        <div class='box box-primary'>
            <div class='box-header with-border'>
                <h4 class='box-title'>Ver Pedido Nº <b class='num_pedido'></b></h4>
                <button style="float:right;" class="btn btn-modal-top fa fa-close"
                    onClick="$('#ModalVerPedido').modal('hide')"></button>
                <button style='float:right;' class="btn btn-modal-top fa fa-print" onClick="printInterno1(1)">
                    INTERNO</button>
                <button style='float:right;' class="btn btn-modal-top fa fa-print" onClick="printCliente_verpedidos(1)">
                    CLIENTE</button>
            </div>
            <div id="tablita">
                <div class='box-body'>
                    <h4>Nº Pedido: <span class="num_pedido"></span></h4>
                    <h4 id="nombre_cliente">Cliente:</h4>
                    <h4>Fecha: <span class="fecha_pedido"></span></h4>

                    <table id="tabla_detallepedido" class="table table-responsive w-100 d-block d-md-table" role="grid">
                        <thead>
                            <tr role="row">
                                <th>ID</th>
                                <th>Producto</th>
                                <th class='text-center'>Cantidad de Bandejas/Plantas</th>
                                <th class='text-center'>Fecha Siembra Estimada</th>
                                <th class='text-center'>Fecha Entrega Solicitada</th>
                                <th><button class="btn btn-success btn-round fa fa-plus-square btn-modal-top"
                                        onclick="pone_tiposdeproducto();modalAgregarProducto();"></button></th>

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