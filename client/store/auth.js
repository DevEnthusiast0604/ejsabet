import axios from 'axios'
import Cookies from 'js-cookie'

// state
export const state = () => ({
  user: null,
  token: null,
  allowed_ips: [],
  ip: '',
})

// getters
export const getters = {
  user: state => state.user,
  token: state => state.token,
  check: state => state.user !== null,
  allowed_ips: state => state.allowed_ips,
  ip: state => state.ip,
}

// mutations
export const mutations = {
  SET_TOKEN (state, token) {
    state.token = token
  },

  FETCH_USER_SUCCESS (state, user) {
    state.user = user
  },

  FETCH_USER_FAILURE (state) {
    state.token = null
  },

  LOGOUT (state) {
    state.user = null
    state.token = null
  },

  UPDATE_USER (state, { user }) {
    state.user = user
  },

  SET_ALLOWED_IPS (state, data) {
    state.allowed_ips = data
  },

  SET_IP (state, { ip }) {
    state.ip = ip
  }
}

// actions
export const actions = {
  saveToken ({ commit, dispatch }, { token, remember }) {
    commit('SET_TOKEN', token)

    Cookies.set('token', token, { expires: remember ? 365 : null })
  },

  async fetchUser ({ commit }) {
    try {
      const { data } = await axios.get('/user')

      commit('FETCH_USER_SUCCESS', data)
    } catch (e) {
      Cookies.remove('token')

      commit('FETCH_USER_FAILURE')
    }
  },

  updateUser ({ commit }, payload) {
    commit('UPDATE_USER', payload)
  },

  async logout ({ commit }) {
    try {
      await axios.post('/logout')
    } catch (e) { }

    Cookies.remove('token')

    commit('LOGOUT')
  },

  async fetchAllowedIps({ commit }) {
    const response = await axios.get('/company_ip');
    commit('SET_ALLOWED_IPS', response.data.data);
  },

  async checkIp({ commit }) {
    const response = await axios.get('/get_client_ip');
    if (response.data.status === 'Success') {
      commit('SET_IP', response.data.data.ip);
    }
  }
}
