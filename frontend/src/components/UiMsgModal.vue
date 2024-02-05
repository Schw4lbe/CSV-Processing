<template>
  <div class="msg-wrapper">
    <div class="msg-container">
      <p class="msg"></p>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import errorMsg from "../store/mapping/errorMsg";

export default {
  name: "UiMsgModal",

  computed: {
    ...mapGetters(["getErrorMsg"]),
  },

  watch: {
    getErrorMsg(newVal, oldVal) {
      // guard to prevent error on local storage variable remove on exit
      if (newVal === null || newVal === undefined) {
        return;
      }

      if (newVal !== oldVal) {
        console.log("error code received: ", newVal);
        console.log(errorMsg[newVal]);
      }
    },
  },

  data() {
    return {
      errorMsg: null,
      warningMsg: null,
      successMsg: null,
    };
  },
};
</script>
