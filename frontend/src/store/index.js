import { createStore } from "vuex";
import state from "@/store/modules/state";
import mutations from "@/store/modules/mutations";
import getters from "@/store/modules/getters";
import actions from "@/store/modules/actions";

const store = createStore({
  state,
  mutations,
  getters,
  actions,
});

export default store;
