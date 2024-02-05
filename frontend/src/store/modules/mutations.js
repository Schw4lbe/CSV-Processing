export default {
  setUploadSuccessMsg(state, message) {
    state.uploadSuccessMsg = message;
    localStorage.setItem("uploadSuccessMsg", message);
  },

  setTableName(state, name) {
    state.tableName = name;
    localStorage.setItem("tableName", name);
  },

  storeChartData(state, data) {
    state.chartData = data;
    localStorage.setItem("chartData", JSON.stringify(data));
  },

  unsetSessionData(state) {
    state.chartData = null;
    state.tableName = null;
    state.uploadSuccessMsg = null;
    localStorage.removeItem("chartData");
    localStorage.removeItem("tableName");
    localStorage.removeItem("uploadSuccessMsg");
  },

  // mutations properties to handle errors, warnings, success msg
  setErrorCode(state, code) {
    state.errorCode = code;
  },
  setWarningCode(state, code) {
    state.warningCode = code;
  },
  setSuccessCode(state, code) {
    console.log(code);
    state.successCode = code;
  },

  unsetErrorCode(state) {
    state.errorCode = null;
  },
  unsetWarningCode(state) {
    state.warningCode = null;
  },
  unsetSuccessCode(state) {
    state.successCode = null;
  },

  // loading animation upon table creation:
  triggerLoadingAnimation(state) {
    state.isLoading = true;
  },
  unsetLoadingAnimation(state) {
    state.isLoading = false;
  },
};
