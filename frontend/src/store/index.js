import { createStore } from "vuex";
import states from "@/store/modules/states";
import mutations from "@/store/modules/mutations";
import getters from "@/store/modules/getters";
import actions from "@/store/modules/actions";
// import localStoragePlugin from "@/store/modules/plugins";

const store = createStore({
  states,
  mutations,
  getters,
  actions,
  // plugins: [localStoragePlugin],
});

export default store;
