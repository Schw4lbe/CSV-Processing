import { createStore } from "vuex";
// import state from "@/store/modules/state";
// import mutations from "@/store/modules/mutations";
// import getters from "@/store/modules/getters";
import actions from "@/store/modules/actions";
// import localStoragePlugin from "@/store/modules/plugins";

const store = createStore({
  // state,
  // mutations,
  // getters,
  actions,
  // plugins: [localStoragePlugin],
});

export default store;
