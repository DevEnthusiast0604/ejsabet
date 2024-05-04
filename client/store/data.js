import axios from 'axios'
import Cookies from 'js-cookie'

// state
export const state = () => ({
    companies: [],
    users: [],
    sidebar_collapsed: false,
    layout_mode: 'dark',
    balance: [],
    progress: 0,
})

// getters
export const getters = {
    companies: state => state.companies,
    users: state => state.users,
    sidebar_collapsed: state => state.sidebar_collapsed,
    balance: state => state.balance,
    layout_mode: state => state.layout_mode,
    progress: state => state.progress,
}

// mutations
export const mutations = {
    GET_COMPANIES(state, data) {
        state.companies = data
    },
    GET_USERS(state, data) {
        state.users = data
    },
    TOGGLE_SIDEBAR(state, data) {
        if (data == 'toggle') {
            state.sidebar_collapsed = !state.sidebar_collapsed
        } else {
            state.sidebar_collapsed = data
        }
    },
    GET_BALANCE(state, data) {
        state.balance = data
    },
    SET_LAYOUT_MODE(state, data) {
        if (data) {
            state.layout_mode = data;
        } else {
            state.layout_mode = state.layout_mode === 'dark' ? 'light' : 'dark';
        }
    },
    SET_PROGRESS(state, data) {
        state.progress = data
    },
}

// actions
export const actions = {
    async getCompanies ({ commit }) {
        const { data } = await axios.get('/company/search')
        commit('GET_COMPANIES', data.data)
    },
    async getUsers ({ commit }) {
        const { data } = await axios.get('/user/search')
        commit('GET_USERS', data.data)
    },
    toggleSidebar({commit}, data = 'toggle') {
        commit('TOGGLE_SIDEBAR', data)
    },
    async getBalance ({ commit }) {
        const { data } = await axios.get('/get_balance')
        commit('GET_BALANCE', data.data)
    },
    setLayoutMode({ commit, state }, data = null) {
        commit('SET_LAYOUT_MODE', data);
        Cookies.set('layout_mode', state.layout_mode, { expires: 365 })
    },
    setProgress({ commit }, data) {
        commit('SET_PROGRESS', data);
    }
}
