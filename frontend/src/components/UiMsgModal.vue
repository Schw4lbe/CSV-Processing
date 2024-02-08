<template>
  <div class="ui-update-container">
    <!-- UI Message Container -->
    <div v-if="isError || isWarning || isSuccess" class="msg-wrapper">
      <div v-if="isError || isWarning" class="msg-container">
        <p v-if="isError" class="errorMsg">
          {{ errorMsg }}<i class="fa-solid fa-circle-exclamation"></i>
        </p>
        <p v-if="isWarning" class="warningMsg">{{ warningMsg }}</p>
        <button @click="confirmMsg" class="btn-confirm-msg">OK</button>
      </div>
      <div v-if="isSuccess" class="msg-container-fade">
        <p class="successMsg">{{ successMsg }}</p>
      </div>
    </div>

    <!-- Loading Animation Container -->
    <div v-if="isLoading" class="animation-wrapper">
      <div class="spinnerContainer">
        <div class="ball1"></div>
        <div class="ball2"></div>
        <div class="ball3"></div>
        <div class="ball4"></div>
        <div class="ball5"></div>
        <div class="ball6"></div>
        <div class="ball7"></div>
        <div class="ball8"></div>
      </div>
      <h2 class="animation-header">Tabelle wird erstellt...</h2>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapMutations } from "vuex";
import messageMap from "../store/mapping/msgMap";

export default {
  name: "UiMsgModal",

  computed: {
    ...mapGetters([
      "getErrorCode",
      "getSuccessCode",
      "getWarningCode",
      "getLoadingState",
    ]),
  },

  watch: {
    getErrorCode(nCode, oCode) {
      if (nCode === null || nCode === undefined) {
        return;
      }

      if (nCode !== oCode) {
        this.isError = true;
        this.errorMsg = messageMap[nCode];
      }
    },

    getWarningCode(nCode, oCode) {
      if (nCode === null || nCode === undefined) {
        return;
      }

      if (nCode !== oCode) {
        this.isWarning = true;
        this.warningMsg = messageMap[nCode];
      }
    },

    getSuccessCode(newCode, oldCode) {
      if (newCode === null || newCode === undefined) {
        return;
      }

      if (newCode !== oldCode) {
        this.isSuccess = true;
        this.successMsg = messageMap[newCode];
        this.successFadeTimer();
      }
    },

    getLoadingState(newState, oldState) {
      if (newState !== oldState) {
        if (newState === true) {
          this.isLoading = true;
        } else {
          this.isLoading = false;
        }
      }
    },
  },

  data() {
    return {
      errorMsg: null,
      warningMsg: null,
      successMsg: null,

      isError: false,
      isWarning: false,
      isSuccess: false,

      isLoading: false,
    };
  },

  methods: {
    ...mapMutations(["unsetErrorCode", "unsetWarningCode", "unsetSuccessCode"]),

    confirmMsg() {
      this.isError = false;
      this.isWarning = false;
      this.errorMsg = null;
      this.warningMsg = null;
      this.unsetErrorCode();
      this.unsetWarningCode();
    },

    successFadeTimer() {
      setTimeout(() => {
        this.isSuccess = false;
        this.successMsg = null;
        this.unsetSuccessCode();
      }, "1500");
    },
  },
};
</script>
