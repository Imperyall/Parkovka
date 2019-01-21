import Vue from 'vue'
import Vuex from 'vuex'
import Axios from 'axios'
import { 
  TITLES,
  SHORT_TITLES,
  TRASH_TITLES,
  DATA,
  PERMISSION,
  SEARCH,
  ALERT,
  DEPARTMENTS,
  EDIT_DATA,
  DELETE_DATA,
  SET_DATA,
  SET_PERMISSION,
  SET_SEARCH,
  SET_ALERT,
  SET_ALERT_TIMER,
  GET_DATA,
  GET_PERMISSION,
  GET_REPORT,
  CONST_ALL_TITLES, 
  CONST_SHORT_TITLES, 
  CONST_TRASH_TITLES,
  CONST_ALERT_TYPE,
  CONST_ALERT_TIMER,
  CONST_ALERT_MSG,
  CONST_CONNECT_URL,
  CONST_DEPARTMENTS
} from '@/constants/index'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    titles: CONST_ALL_TITLES,
    short_titles: CONST_SHORT_TITLES,
    trash_titles: CONST_TRASH_TITLES,
    data: [],
    permission: 0,
    search: '',
    alert: {
      type: CONST_ALERT_TYPE,
      timer: 0,
      msg: CONST_ALERT_MSG
    },
    departments: CONST_DEPARTMENTS
  },
  getters: {
  	[TITLES]: state => state.titles,
  	[SHORT_TITLES]: state => state.short_titles,
    [TRASH_TITLES]: state => state.trash_titles,
  	[DATA]: state => state.data,
    [PERMISSION]: state => state.permission,
    [SEARCH]: state => state.search,
    [ALERT]: state => state.alert,
    [DEPARTMENTS]: state => state.departments
  },
  mutations: {
    [SET_PERMISSION]: (state, payload) => { state.permission = payload },
    [SET_DATA]: (state, payload) => { state.data = payload },
    [SET_SEARCH]: (state, payload) => { state.search = payload },
    [SET_ALERT]: (state, payload) => { state.alert = payload },
    [SET_ALERT_TIMER]: (state, payload) => { state.alert.timer = payload },
  },
  actions: {
    [GET_PERMISSION]: async (context, payload) => {
      let { data, status } = await Axios.get(CONST_CONNECT_URL, { params: { action: 'permission' } })
      if (status === 200 && +data < 3) context.commit(SET_PERMISSION, +data)
    },
  	[GET_DATA]: async (context, payload) => {
      let { data, status } = await Axios.get(CONST_CONNECT_URL, { params: { action: payload ? payload : 'get_short' } })
      if (status === 200 && data.length) context.commit(SET_DATA, data)
    },
    [EDIT_DATA]: async (context, payload) => {
      let { data, status } = await Axios.post(CONST_CONNECT_URL, { form: payload })
      if (status === 200 && 'data' in data) {
        context.commit(SET_DATA, data.data)
        context.commit(SET_ALERT, { 
          type: data.type,
          timer: CONST_ALERT_TIMER,
          msg: data.msg
        })
      }
    },
    [DELETE_DATA]: async (context, payload) => {
      let { data, status } = await Axios.post(CONST_CONNECT_URL, { delete: payload })
      if (status === 200 && 'data' in data) {
        context.commit(SET_DATA, data.data)
        context.commit(SET_ALERT, { 
          type: data.type,
          timer: CONST_ALERT_TIMER,
          msg: data.msg
        })
      }
    },
    [GET_REPORT]: async (context, payload) => {
      let { data, status } = await Axios.get(CONST_CONNECT_URL, { params: { action: 'get_report' } })
      if (status === 200 && data.length) {
        const link = document.createElement('a');
        link.href = data;
        link.setAttribute('download', '');
        document.body.appendChild(link);
        link.click();
      }
    }
  }
})
