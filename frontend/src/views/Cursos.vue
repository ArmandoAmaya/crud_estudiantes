<template>
  <div>
    <b-container class="mt-5">
      <b-card border-variant="dark" header="Gestión de cursos" align="left">

          <b-button id="show-btn" @click="openModal()">Agregar nuevo</b-button>

          <b-card-text>
            <b-table responsive :fields="table.fields" :items="table.items">

              <template #cell(status)="data">
                <b-badge v-if="data.item.status == '1'" variant="success">Activo</b-badge>
                <b-badge v-if="data.item.status == '0'" variant="danger">Inactivo</b-badge><b></b>
              </template>
              
              <template #cell(action)="data">
                <b-button @click="openModal(data.item)" variant="primary">Editar</b-button>
                &nbsp;
                <b-button @click="deleteCarrera(data.item.action)" variant="danger">Eliminar</b-button>
              </template>
            </b-table>
          </b-card-text>
        </b-card>       
    </b-container>

     <b-modal id="bv-modal-example" hide-footer>
        <template #modal-title>
          Agregar curso nuevo
        </template>
        <div class="d-block text-center">
          <form id="cursos_form" @submit.prevent="action ? save() : edit()" align="left">
            <b-row>
              <b-col md="12" sm="12">
                <div class="form-group" align="left">
                  <label align="left">Nombre:</label>
                  <b-form-input
                    name="name" 
                    v-model="curso.name"
                    aria-describedby="input-live-help input-live-feedback"
                    placeholder="Nombre del curso"
                    trim
                  ></b-form-input>
                  <small class="text-danger">{{ errors.name }}</small>
                </div>
              </b-col>

              <b-col md="12" sm="12">
                <div class="form-group" align="left">
                  <label align="left">Descripción:</label>
                   <b-form-textarea
                      name="long_description"
                      v-model="curso.long_description"
                      placeholder="Descripción larga"
                      rows="3"
                      max-rows="6"
                    ></b-form-textarea>
                    <small class="text-danger">{{ errors.long_description }}</small>
                </div>
              </b-col>


              <b-col md="12" sm="12">
                <div class="form-group" align="left">
                  <label align="left">Descripción:</label>
                  <b-form-select 
                    class="w-100" 
                    v-model="curso.status" 
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
      curso: {
        name: '',
        long_description: '',
        status: '',
        curso_id: ''
      },
      errors: {
        name: '',
        long_description: '',
        status: '',
      },
      table: {
        /*
        * Campos de la tabla
        */
        fields: [
          { key: 'name', label: 'Nombre' },
          { key: 'long_description', label: 'Descripción larga' },
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
     * Abre el modal en editar o guardar
     */
    openModal(data = null){
        this.disabled = false;
        this.clearValues(this.curso);
        this.clearValues(this.errors);
        if(data != null) {
            this.curso = {
                name: data.name,
                long_description: data.long_description,
                status: data.status,
                curso_id: data.action
            };
            this.action = false;
        }
        else{
            this.action = true;
        }
        this.$bvModal.show('bv-modal-example');
    },
    /*
    * Guarda un curso
    */
    save(){
      this.disabled = true;
      let __data = new FormData();
      __data.append('name', this.curso.name);
      __data.append('long_description', this.curso.long_description);
      __data.append('status', this.curso.status);
      var $this = this;
      this.clearValues(this.errors);
      
      axios
      .post(this.api + "carreras", __data, {
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
              $this.clearValues($this.curso);
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
     * Edita un registro de una carrera
     */
    edit(){
        this.disabled = true;
        this.clearValues(this.errors);
        var $this = this;
        let __data = {
          name: this.curso.name,
          long_description: this.curso.long_description,
          status: this.curso.status
        };


        axios
        .put(this.api + "carreras/"+this.curso.curso_id, __data, {
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
                $this.clearValues($this.curso);
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
     * Obtiene todas las carreras
     */
    getData() {
        var self = this;
        axios
        .get(this.api + "carreras")
        .then(resp => {
            let data = resp.data;
            let parsedData = [];
            data.forEach(d => {

              parsedData.push({
                  'name': d.name,
                  'long_description': d.long_description,
                  'status': d.status,
                  'action': d.id,
              })
            });
            self.table.items = parsedData;
        })
    },

    /**
     * Elimina una carrera
     * 
     * @param id: Identificador de la carrera
     */
    deleteCarrera(id){
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
                .delete(this.api + 'carreras/'+id)
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
  }

}
</script>
