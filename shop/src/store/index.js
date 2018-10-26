import Vue from 'vue'
import Vuex from 'vuex'
Vue.use(Vuex)
const store = new Vuex.Store({
    state: {
        title: ''
    },
    mutations: {
        set: (state,objs)=>{
            //JSON.stringify()
            var key = Object.keys(objs)[0]
            state[key] = objs[key]
        },
        title: (state,val)=>{
            state.title = val
        }
    }
})
export default store