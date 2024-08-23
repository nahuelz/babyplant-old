<!-- MODAL AGREGAR PRODUCTO -->
<div id="modalAgregarProducto" class="modal">
  <div class="modal-content" style="height: 98%">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Agregar Producto al Pedido</h3>
      </div>
    </div>
    <div class="box-body">
      <div class="form-group">
        <div class="row">
          <div class="col-md-6">
            <label for="select_tipo" class="control-label"
              >Tipo de Producto:</label
            >
            <select
              id="select_tipo"
              title="Selecciona Producto"
              class="selectpicker"
              data-style="btn-info"
              data-live-search="true"
              data-width="100%"
            ></select>
          </div>
          <div class="col-md-6">
            <label for="select_subtipo" class="control-label">Subtipo:</label>
            <select
              id="select_subtipo"
              title="Subtipo"
              class="selectpicker"
              data-style="btn-info"
              data-width="100%"
            ></select>
          </div>
        </div>
      </div>
      <div class="form-group">
        <div class="row">
          <div class="col-md-8">
            <label for="select_variedad" class="control-label">Variedad:</label>
            <select
              id="select_variedad"
              title="Selecciona Variedad"
              class="selectpicker"
              data-style="btn-info"
              data-live-search="true"
              data-width="100%"
            ></select>
          </div>
          <div class="col-md-4">
            <label for="select_bandeja" class="control-label">Bandeja:</label>
            <select
              id="select_bandeja"
              title="Bandeja"
              class="selectpicker"
              data-style="btn-info"
              data-width="100%"
            ></select>
          </div>
        </div>
      </div>
      <div class="form-group">
        <div class="row">
          <div class="col-md-6">
            <label for="cantidad_plantas" class="control-label">Plantas:</label>
            <input
              type="number"
              min="0"
              step="1"
              id="cantidad_plantas"
              class="form-control text-right"
              onkeyup="setCantidadSemillas(this.value)"
              onpaste="this.onkeyup();"
            />
          </div>
          <div class="col-md-6">
            <label for="cantidad_semillas" class="control-label"
              >Semillas:</label
            >
            <input
              type="text"
              id="cantidad_semillas"
              class="form-control text-right"
              onkeyup="setPlantasconSemillas(this.value);"
            />
          </div>
        </div>
      </div>
      <div class="form-group">
        <div class="row">
          <div class="col-md-6">
            <label for="cantidad_band" class="label-control"
              >Cant. Bandejas:</label
            >
            <input
              type="number"
              min="0"
              step="1"
              id="cantidad_band"
              class="form-control text-right"
              onkeyup="setPlantas(this.value);"
            />
          </div>
          <div class="col-md-6">
            <label for="check_semilla" class="control-label">Con Semilla</label>
            <input
              class="ml-2"
              type="checkbox"
              id="check_semilla"
              value="first_checkbox"
            />
          </div>
        </div>
      </div>
      <div class="form-group">
        <div class="row">
          <div class="col-md-4">
            <label class="label-control" for="fechasiembra_txt"
              >Fecha Siembra:</label
            >
            <div class="input-group">
              <input
                type="text"
                data-date-format="dd/mm/yy"
                disabled="disabled"
                value="DD/MM/YYYY"
                class="datepicker form-control"
                id="fechasiembra_txt"
                placeholder="DD-MM-AAAA"
              />
            </div>
          </div>
          <div class="col-md-2">
            <label class="label-control" for="dias_produccion">Dias</label>
            <div class="select-editable">
              <select
                class="form-control"
                onchange="this.nextElementSibling.value=this.value;setFechaEntrega(this.value)"
              >
                <option value="20">28</option>
                <option value="20">20</option>
                <option value="30">30</option>
                <option value="40">40</option>
                <option value="50">50</option>
                <option value="60">60</option>
                <option value="70">70</option>
                <option value="80">80</option>
                <option value="90">90</option>
              </select>
              <input
                input
                type="text"
                class="form-control"
                id="dias_produccion"
                name="dias_produccion1"
                value="28"
                type="text"
                onchange="setFechaEntrega(this.value)"
                onkeyup="this.onchange();"
                onpaste="this.onchange();"
                oninput="this.onchange();"
              />
            </div>
          </div>
          <div class="col-md-6">
            <label class="label-control" for="fecha_entrega"
              >Fecha Entrega:</label
            >
            <br />
            <input
              type="text"
              data-date-format="dd/mm/yy"
              value="DD/MM/YYYY"
              class="datepicker form-control"
              id="fechaentrega_picker"
            />
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col text-right">
          <button
            type="button"
            class="btn fa fa-close"
            style="font-size: 2em"
            id="btn_cancel"
            onClick="$('#modalAgregarProducto').modal('hide');"
          ></button>
          <button
            type="button"
            class="btn fa fa-save"
            style="font-size: 2em; margin-left: 0.5em"
            id="btn_guardarcliente"
            onClick="addToPedido();"
          ></button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- FIN MODAL AGREGAR PRODUCTO -->
