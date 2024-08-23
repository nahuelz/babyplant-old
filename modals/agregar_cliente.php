<div id="ModalAgregarCliente" class="modal">
  <div class="modal-content2">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Agregar Cliente</h3>
      </div>
      <div class="box-body">
        <div class="form-group">
          <label for="nombrecliente_txt" class="control-label">Nombre:</label>
          <input
            type="search"
            autocomplete="off"
            id="nombrecliente_txt"
            style="text-transform: uppercase"
            class="form-control"
          />
        </div>

        <div class="form-group">
          <label for="domiciliocliente_txt" class="control-label"
            >Domicilio:</label
          >
          <input
            type="search"
            autocomplete="off"
            id="domiciliocliente_txt"
            style="text-transform: uppercase"
            class="form-control"
          />
        </div>

        <div class="form-group">
          <label for="telcliente_txt" class="control-label">Teléfono:</label>
          <input
            type="search"
            autocomplete="off"
            id="telcliente_txt"
            style="text-transform: uppercase"
            class="form-control"
          />
        </div>

        <div class="form-group">
          <label for="mailcliente_txt" class="control-label">E-Mail:</label>
          <input
            type="search"
            autocomplete="off"
            id="mailcliente_txt"
            style="text-transform: lowercase !important"
            class="form-control"
          />
        </div>

        <div class="form-group">
          <label for="cuitcliente_txt" class="control-label">CUIT:</label>
          <input
            class="form-control"
            type="search"
            autocomplete="off"
            id="cuitcliente_txt"
            oninput="this.value = this.value.replace(/[^0-9-]/g, ''); this.value = this.value.replace(/(\..*)\-/g, '$1');"
          />
        </div>

        <div class="row">
          <div class="col">
            <div class="d-flex flex-row justify-content-end">
              <button
                type="button"
                class="btn btn-modal-bottom fa fa-close"
                id="btn_cancel"
                onClick="$('#ModalAgregarCliente').modal('hide')"
              ></button>
              <button
                type="button"
                class="btn btn-modal-bottom fa fa-save ml-3"
                id="btn_guardarpedido"
                onClick="guardarCliente();"
              ></button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
