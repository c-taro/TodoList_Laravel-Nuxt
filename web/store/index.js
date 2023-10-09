// store/index.js
export const state = () => ({
  boards: false,
  successMessage: null,
})

export const mutations = {
  setBoards(state, payload){
    state.boards = payload;
  },
  clearSuccessMessage(state) {
    state.successMessage = null
  },
  setSuccessMessage(state, message) {
    state.successMessage = message
  },
}