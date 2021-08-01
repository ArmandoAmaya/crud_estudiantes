<template>
  <div>
    <b-container class="mt-5">
      <b-card border-variant="dark" header="Gestión de estudiantes" align="left">

          <b-button id="show-btn" @click="openModal()">Agregar nuevo</b-button>

          <b-card-text>
            <b-table responsive :fields="table.fields" :items="table.items">
              

              <template #cell(carrera_id)="data">
                {{ cursoById(data.item.carrera_id) }}
              </template>


              <template #cell(status)="data">
                <b-badge v-if="data.item.status == '1'" variant="success">Activo</b-badge>
                <b-badge v-if="data.item.status == '0'" variant="danger">Inactivo</b-badge><b></b>
              </template>
              
              <template #cell(action)="data">
                <b-button @click="openModal(data.item)" variant="primary">Editar</b-button>
                &nbsp;
                <b-button @click="deleteEstudiante(data.item.action)" variant="danger">Eliminar</b-button>
              </template>
            </b-table>
          </b-card-text>
        </b-card>       
    </b-container>

     <b-modal id="bv-modal-example" hide-footer>
        <template #modal-title>
          Agregar estudiante nuevo
        </template>
        <div class="d-block text-center">
          <form id="estudiante_form" @submit.prevent="action ? save() : edit()" align="left">
            <b-row>
              <b-col md="12" sm="12">
                <div class="form-group" align="left">
                  <label align="left">Nombre:</label>
                  <b-form-input
                    name="name" 
                    v-model="estudiante.name"
                    aria-describedby="input-live-help input-live-feedback"
                    placeholder="Nombre del estudiante"
                    trim
                  ></b-form-input>
                  <small class="text-danger">{{ errors.name }}</small>
                </div>
              </b-col>

              <b-col md="12" sm="12">
                <div class="form-group" align="left">
                  <label align="left">Apellidos:</label>
                  <b-form-input
                    name="last_name" 
                    v-model="estudiante.last_name"
                    aria-describedby="input-live-help input-live-feedback"
                    placeholder="Apellido del estudiante"
                    trim
                  ></b-form-input>
                  <small class="text-danger">{{ errors.nalast_nameme }}</small>
                </div>
              </b-col>

              <b-col md="12" sm="12">
                <div class="form-group" align="left">
                  <label align="left">Email:</label>
                  <b-form-input
                    name="email" 
                    v-model="estudiante.email"
                    aria-describedby="input-live-help input-live-feedback"
                    placeholder="Correo electrónico"
                    trim
                  ></b-form-input>
                  <small class="text-danger">{{ errors.email }}</small>
                </div>
              </b-col>

              <b-col md="12" sm="12">
                <div class="form-group" align="left">
                  <label align="left">Curso:</label>
                  <b-form-select 
                    class="w-100" 
                    v-model="estudiante.carrera_id" 
                    :options="carreras"
                  ></b-form-select>
                  <small class="text-danger">{{ errors.carrera_id }}</small>
                </div>
              </b-col>


              <b-col md="12" sm="12">
                <div class="form-group" align="left">
                  <label align="left">Estado:</label>
                  <b-form-select 
                    class="w-100" 
                    v-model="estudiante.status" 
                    :options="[
                      {value: '', text: 'Seleccione'},
                      {value: '0', text: 'Inactivo'},
                      {value: '1', text: 'Activo'}
                    ]"
                  ></b-form-select>
                  <small class="text-danger">{{ errors.status }}</small>
                </div>
              </b-col>

              
              
            </b-row>

            <b-button class="mt-3" type="submit" :disabled="disabled" variant="primary">Guardar</b-button>
          </form>
          
        </div>
        
      </b-modal>
  </div>
</template>

<script>
import axios from "axios";
import { mapGetters } from "vuex";
import { commons } from "../mixins/commons";

export default {
  name: 'Home',
  computed: Object.assign(mapGetters(["api"]), {  }),
  mixins: [commons],
  data() {
    return {
      actin: true,
      estudiante: {
        name: '',
        last_name: '',
        email: '',
        carrera_id: '',
        status: '',
        estudiante_id: ''
      },
      errors: {
        name: '',
        last_name: '',
        email: '',
        carrera_id: '',
        status: ''
      },
      carreras: [{
        value: '',
        text: 'Seleccione'
      }],
      table: {
        /*
        * Campos de la tabla
        */
        fields: [
          { key: 'name', label: 'Nombre' },
          { key: 'last_name', label: 'Apellidos' },
          { key: 'email', label: 'Correo electrónico' },
          { key: 'carrera_id', label: 'Carrera' },
          { key: 'status', label: 'Estado' },
          { key: 'action', label: 'Acción' }
        ],
        /*
        * Datos de la tabla
        */
        items:[]
      },

      disabled: false
    
    }
  },
  methods: {
    /**
     * Obtiene el curso mediante su id
     */
    cursoById(carrera_id) {
      let curso = this.carreras.find(c => carrera_id == c.value);
      if (typeof curso != 'undefined') {
        return curso.text;
      }
      return '';
    },
    /**
     * Abre el modal en editar o guardar
     */
    openModal(data = null){
        this.disabled = false;
        this.clearValues(this.estudiante);
        this.clearValues(this.errors);
        if(data != null) {
            this.estudiante = {
                name: data.name,
                last_name: data.last_name,
                email: data.email,
                carrera_id: data.carrera_id,
                status: data.status,
                estudiante_id: data.action,
            };
            this.action = false;
        }
        else{
            this.action = true;
        }
        this.$bvModal.show('bv-modal-example');
    },
    /*
    * Guarda un estudiante
    */
    save(){
      this.disabled = true;
      let __data = new FormData();
      __data.append('name', this.estudiante.name);
      __data.append('last_name', this.estudiante.last_name);
      __data.append('email', this.estudiante.email);
      __data.append('carrera_id', this.estudiante.carrera_id);
      __data.append('status', this.estudiante.status);

      var $this = this;
      this.clearValues(this.errors);
      
      axios
      .post(this.api + "estudiantes", __data, {
      headers: {
          "Content-Type": "application/x-www-form-urlencoded"
      }
      })
      .then(resp => {
          const data = resp.data;
          if (data.success) {
              
              this.$swal({
                  position: "center",
                  icon: "success",
                  title: "Guardado",
                  text: data.message,
                  showConfirmButton: false,
                  timer: 1500,
                  onDestroy: () => {}
              });

              $this.$bvModal.hide('bv-modal-example');
              $this.clearValues($this.estudiante);
              $this.disabled = false;
              $this.getData();
          } else {
              throw data;
          }
      })
      .catch(err => {
          if(!err.hasOwnProperty('errors')){
              $this.$swal({
                  position: "center",
                  icon: "error",
                  title: "Ups!",
                  text: err.message,
                  showConfirmButton: false,
                  timer: 2500,
                  onDestroy: () => {}
              });
              $this.disabled = false;
          }else{
              $this.disabled = false;
              $this.parseErrors($this.errors, err.errors);
          }
      });
    },

    /**
     * Edita un registro de una beca
     */
    edit(){
        this.disabled = true;
        this.clearValues(this.errors);
        var $this = this;


        let __data = {
          name: this.estudiante.name,
          last_name: this.estudiante.last_name,
          email: this.estudiante.email,
          carrera_id: this.estudiante.carrera_id,
          status: this.estudiante.status
        };

        axios
        .put(this.api + "estudiantes/"+this.estudiante.estudiante_id, __data, {
        headers: {
            "Content-Type": "application/json"
        }
        })
        .then(resp => {
            const data = resp.data;
            if (data.success) {
                
                $this.$swal({
                    position: "center",
                    icon: "success",
                    title: "Guardado",
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                });

                $this.$bvModal.hide('bv-modal-example');
                $this.clearValues($this.estudiante);
                $this.disabled = false;
                $this.getData();
            } else {
                throw data;
            }
        })
        .catch(err => {
            if(!err.hasOwnProperty('errors')){
                $this.$swal({
                    position: "center",
                    icon: "error",
                    title: "Ups!",
                    text: err.message,
                    showConfirmButton: false,
                    timer: 2500
                });
                $this.disabled = false;
            }else{
                $this.disabled = false;
                $this.parseErrors($this.errors, err.errors);
            }
        });
    },
    /**
     * Obtiene todos los becados
     */
    getData() {
        var self = this;
        axios
        .get(this.api + "estudiantes")
        .then(resp => {
            let data = resp.data;
            let parsedData = [];
            data.forEach(d => {

              parsedData.push({
                  name: d.name,
                  last_name: d.last_name,
                  email: d.email,
                  carrera_id: d.carrera_id,
                  status: d.status,
                  action: d.id
              })
            });
            self.table.items = parsedData;
        })
    },

    /**
     * Obtiene todos los becados
     */
    getCarreras() {
        var self = this;
        axios
        .get(this.api + "carreras")
        .then(resp => {
            let data = resp.data;
            data.forEach(d => {
              self.carreras.push({
                  value: d.id,
                  text: d.name
              })
            });
        })
    },

    /**
     * Elimina un estudiante
     * 
     * @param id: Identificador del estudiante
     */
    deleteEstudiante(id){
        var $this = this;
        this.$swal({
            title: '¿Estás seguro?',
            text: "¡Usted no podrá revertir ésto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, Eliminar',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                axios
                .delete(this.api + 'estudiantes/'+id)
                .then(resp => {
                    if (resp.status == '200') {
                        this.$swal({
                            position: "center",
                            icon: "success",
                            title: "Eliminado",
                            text: 'Eliminado correctamente',
                            showConfirmButton: false,
                            timer: 1500
                        });

                        $this.getData();
                    } 
                })
                .catch(err => {
                    this.$swal({
                        position: "center",
                        icon: "error",
                        title: "Ups!",
                        text: err.message,
                        showConfirmButton: false,
                        timer: 2500,
                        onDestroy: () => { }
                    });
                });
            }
        });
    },
  },
  mounted(){
    // Llenar la tabla
    this.getData();
    // Carreras
    this.getCarreras();
  }

}
</script>
