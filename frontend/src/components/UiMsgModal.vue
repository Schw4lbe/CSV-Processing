<template>
  <div v-if="isError || isWarning || isSuccess" class="msg-wrapper">
    <div v-if="isError || isWarning" class="msg-container">
      <p v-if="isError" class="errorMsg">{{ errorMsg }}</p>
      <p v-if="isWarning" class="warningMsg">TEST WARNING</p>
      <button @click="confirmMsg" class="btn-confirm-msg">OK</button>
    </div>
    <div v-if="isSuccess" class="msg-container-fade">
      <p class="successMsg">{{ successMsg }}</p>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapMutations } from "vuex";
import messageMap from "../store/mapping/msgMap";

export default {
  name: "UiMsgModal",

  computed: {
    ...mapGetters(["getErrorCode", "getSuccessCode"]),
  },

  watch: {
    getErrorCode(newErrCode, oldErrCode) {
      // guard to prevent error on local storage variable remove on exit
      if (newErrCode === null || newErrCode === undefined) {
        return;
      }

      if (newErrCode !== oldErrCode) {
        this.isError = true;
        this.errorMsg = messageMap[newErrCode];
      }
    },

    // warning watcher here

    getSuccessCode(newSucCode, oldSucCode) {
      console.log("test");

      if (newSucCode === null || newSucCode === undefined) {
        return;
      }

      if (newSucCode !== oldSucCode) {
        this.isSuccess = true;
        this.successMsg = messageMap[newSucCode];
        // set a timer to reset success state due to no user input
        this.successFadeTimer();
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

<style scode>
.msg-wrapper {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 3;
  display: flex;
  justify-content: center;
  align-items: center;
}

.msg-container,
.msg-container-fade {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  background: rgba(0, 0, 0, 0.9);
  width: 50%;
  height: max-content;
  padding: 1rem;
}

.msg-container-fade {
  animation: test 1s ease-in 0.5s forwards;
}

@keyframes test {
  0% {
    opacity: 1;
  }
  100% {
    opacity: 0;
  }
}

.errorMsg {
  color: lightcoral;
  padding: 1rem;
  text-transform: uppercase;
}

.warningMsg {
  color: rgb(255, 255, 168);
  padding: 1rem;
  text-transform: uppercase;
}

.successMsg {
  color: lightgreen;
  padding: 1rem;
  text-transform: uppercase;
}

.btn-confirm-msg {
  padding: 0.5rem 1rem;
  margin: 1rem;
  background: #444;
  text-transform: uppercase;
  width: 100px;
  transition: all 0.3s;
}

.btn-confirm-msg:hover {
  background: #222;
  color: #2194f0;
}
</style>
