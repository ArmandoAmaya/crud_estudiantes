import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    /*
    * Api php
    */
    api: 'http://localhost/test-crud/backend/public/api/'
  },
  getters: {
     api: (state) => state.api
  },
  mutations: {
  },
  actions: {
  },
  modules: {
  }
})
