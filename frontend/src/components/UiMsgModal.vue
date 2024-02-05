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
</template>

<script>
import { mapGetters, mapMutations } from "vuex";
import messageMap from "../store/mapping/msgMap";

export default {
  name: "UiMsgModal",

  computed: {
    ...mapGetters(["getErrorCode", "getSuccessCode", "getLoadingState"]),
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

    // warning watcher here or all in one? TBD TODO!

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

<style scode>
.msg-wrapper,
.animation-wrapper {
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

.animation-wrapper {
  backdrop-filter: blur(10px);
  background: rgba(0, 0, 0, 0.7);
  flex-direction: column;
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

/* animation */
.animation-header {
  margin: 2rem;
  color: #eee;
  text-transform: uppercase;
  animation: pulse 1.6s infinite ease-in-out;
  padding-left: 20px;
}

@keyframes pulse {
  0% {
    filter: brightness(100%);
  }
  50% {
    filter: brightness(80%);
  }
  100% {
    filter: brightness(100%);
  }
}

.spinnerContainer {
  width: 80px;
  height: 80px;
  position: relative;
  margin: 100px auto 0 auto;
}
.spinnerContainer div {
  border-radius: 50%;
  background: #fff;
  height: 20px;
  width: 20px;
  position: absolute;
  animation: grow 1.6s infinite ease-in-out;
  transform: scale(0);
}
.ball1 {
  top: 0;
  left: 30px;
}
.spinnerContainer .ball2 {
  top: 9px;
  right: 9px;
  -webkit-animation-delay: 0.2s;
  animation-delay: 0.2s;
}
.spinnerContainer .ball3 {
  right: 0;
  top: 30px;
  -webkit-animation-delay: 0.4s;
  animation-delay: 0.4s;
}
.spinnerContainer .ball4 {
  bottom: 9px;
  right: 9px;
  -webkit-animation-delay: 0.6s;
  animation-delay: 0.6s;
}
.spinnerContainer .ball5 {
  bottom: 0;
  left: 30px;
  -webkit-animation-delay: 0.8s;
  animation-delay: 0.8s;
}
.spinnerContainer .ball6 {
  bottom: 9px;
  left: 9px;
  -webkit-animation-delay: 1s;
  animation-delay: 1s;
}
.spinnerContainer .ball7 {
  left: 0;
  top: 30px;
  -webkit-animation-delay: 1.2s;
  animation-delay: 1.2s;
}
.spinnerContainer .ball8 {
  top: 9px;
  left: 9px;
  -webkit-animation-delay: 1.4s;
  animation-delay: 1.4s;
}

@keyframes grow {
  0% {
    -moz-transform: scale(0);
  }
  50% {
    -moz-transform: scale(1);
    background: #2194f0;
  }
  100% {
    -moz-transform: scale(0);
    background: #2194f0;
  }
}
</style>
